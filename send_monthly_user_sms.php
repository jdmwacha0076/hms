<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$api_key = $_ENV['API_KEY'];
$secret_key = $_ENV['SECRET_KEY'];

$host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

$today = new DateTime();

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE start_date <= :today AND end_date >= :today");
$stmt->execute(['today' => $today->format('Y-m-d')]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No active contracts found.\n";
    exit;
}

echo "Found " . count($contracts) . " active contracts.\n";


$consolidatedMessage = "";
$recipients = [];

foreach ($contracts as $index => $contract) {

    $stmt = $pdo->prepare("SELECT tenant_name, phone_number FROM tenants WHERE id = :tenant_id");
    $stmt->execute(['tenant_id' => $contract['tenant_id']]);
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT house_name, house_owner, phone_number FROM houses WHERE id = :house_id");
    $stmt->execute(['house_id' => $contract['house_id']]);
    $house = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tenant && $house && !empty($house['phone_number'])) {
        $startDate = new DateTime($contract['start_date']);
        $daysDifference = $today->diff($startDate)->days;

        if ($daysDifference > 0 && $daysDifference % 30 === 0) {
            $monthsElapsed = (int)($daysDifference / 30);
            $remainingMonths = $contract['contract_interval'] - $monthsElapsed;

            $tenantMessage = sprintf(
                "Habari mwenye nyumba, ndugu %s.\n\n" .
                    "Mpangaji wako, ndugu %s, kutoka nyumba ya %s, amekamilisha mwezi wa %d katika mkataba wake wa kuishi, " .
                    "ambao ulianza tarehe %s na utaisha tarehe %s. Kwa sasa amebakiza mwezi wa %d na kiasi baki cha kodi " .
                    "anachodaiwa ni Tsh %.0f.\n\nTafadhali zingatia kufuatilia hili kabla ya tarehe ya mwisho wa mkataba. Asante.",
                $house['house_owner'],
                $tenant['tenant_name'],
                $house['house_name'],
                $monthsElapsed,
                $startDate->format('Y-m-d'),
                (new DateTime($contract['end_date']))->format('Y-m-d'),
                $remainingMonths,
                $contract['amount_remaining']
            );

            $consolidatedMessage .= $tenantMessage . "\n\n";

            $recipients[] = [
                'recipient_id' => (string)($index + 1),
                'dest_addr' => $house['phone_number']
            ];
        }
    } else {
        echo "No valid phone number found for tenant ID " . $contract['tenant_id'] . " or house info.\n";
    }
}

if (empty(trim($consolidatedMessage))) {
    echo "No messages to send.\n";
    exit;
}

$postData = [
    'source_addr' => 'Saron-4G',
    'encoding' => 0,
    'schedule_time' => '',
    'message' => $consolidatedMessage,
    'recipients' => $recipients
];

$url = 'https://apisms.beem.africa/v1/send';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt_array($ch, [
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($postData)
]);

$response = curl_exec($ch);

if ($response === FALSE) {
    echo "Error: " . curl_error($ch) . "\n";
} else {
    var_dump($response);
}

curl_close($ch);
