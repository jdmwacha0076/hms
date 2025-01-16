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

function sendSms($api_key, $secret_key, $message, $recipients)
{
    $postData = [
        'source_addr' => 'BOBTechWave',
        'encoding' => 0,
        'schedule_time' => '',
        'message' => $message,
        'recipients' => $recipients,
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
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($postData),
    ]);

    $response = curl_exec($ch);

    if ($response === FALSE) {
        echo "Error: " . curl_error($ch) . "\n";
        return null;
    }

    return json_decode($response, true);
}

$today = new DateTime();

$stmt = $pdo->prepare("
    SELECT 
        c.*, 
        t.tenant_name, 
        r.room_name, 
        h.house_name, 
        h.supervisor_id,
        h.id as house_id 
    FROM contracts c
    JOIN tenants t ON c.tenant_id = t.id
    JOIN rooms r ON c.room_id = r.id
    JOIN houses h ON c.house_id = h.id
    WHERE c.start_date <= :today AND c.end_date >= :today
    AND contract_status = 'UNAENDELEA'
");
$stmt->execute(['today' => $today->format('Y-m-d')]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No active contracts found.\n";
    exit;
}

echo "Found " . count($contracts) . " active contracts.\n";

foreach ($contracts as $contract) {

    $stmt = $pdo->prepare("SELECT supervisor_name, phone_number FROM supervisors WHERE id = :supervisor_id");
    $stmt->execute(['supervisor_id' => $contract['supervisor_id']]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($supervisor && !empty($supervisor['phone_number'])) {
        $startDate = new DateTime($contract['start_date']);
        $endDate = new DateTime($contract['end_date']);
        $intervalMonths = $contract['contract_interval'];

        $daysDifference = $today->diff($startDate)->days;

        if ($daysDifference > 0 && $daysDifference % 30 === 0) {
            $completedMonths = $daysDifference / 30;
            $remainingMonths = $intervalMonths - $completedMonths;

            $message = sprintf(
                "Habari %s, Mpangaji %s kutokea chumba %s katika nyumba ya %s ametimiza muda wa mwezi/miezi %d katika mkataba wake ambao ulianza tarehe %s na unatarajiwa kumalizika tarehe %s. Kwa sasa amebakiza kipindi cha mwezi/miezi %d. Tafadhali hakikisha kufuatilia kuwa amekamilisha malipo yake ya kodi yaliyobakia, kiasi cha Tsh %.0f. Asante.",
                $supervisor['supervisor_name'],
                $contract['tenant_name'],
                $contract['room_name'],
                $contract['house_name'],
                $completedMonths,
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                $remainingMonths,
                $contract['amount_remaining']
            );

            $supervisorRecipient = [
                'recipient_id' => $contract['supervisor_id'],
                'dest_addr' => $supervisor['phone_number'],
            ];
            $supervisorResponse = sendSms($api_key, $secret_key, $message, [$supervisorRecipient]);

            if ($supervisorResponse) {
                echo "Sent SMS to supervisor " . $supervisor['supervisor_name'] . " for completed month " . $completedMonths . "\n";
            } else {
                echo "Failed to send SMS to supervisor " . $supervisor['supervisor_name'] . ".\n";
            }
        }
    } else {
        echo "No valid phone number found for supervisor ID " . $contract['supervisor_id'] . ".\n";
    }
}
