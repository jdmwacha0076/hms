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
        'source_addr' => 'Saron-4G',
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

$oneMonthFromNow = (new DateTime())->modify('+1 month');

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE end_date = :oneMonthFromNow");
$stmt->execute(['oneMonthFromNow' => $oneMonthFromNow->format('Y-m-d')]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No contracts found expiring in one month.\n";
    exit;
}

echo "Found " . count($contracts) . " contracts expiring in one month.\n";

foreach ($contracts as $contract) {
    $today = new DateTime();

    $stmt = $pdo->prepare("
        SELECT 
            s.supervisor_name, s.phone_number,
            t.tenant_name,
            h.house_name,
            r.room_name
        FROM contracts c
        INNER JOIN houses h ON c.house_id = h.id
        INNER JOIN supervisors s ON h.supervisor_id = s.id
        INNER JOIN tenants t ON c.tenant_id = t.id
        INNER JOIN rooms r ON c.room_id = r.id
        WHERE c.id = :contract_id
    ");
    $stmt->execute(['contract_id' => $contract['id']]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($details && !empty($details['phone_number'])) {
        $startDate = new DateTime($contract['start_date']);
        $endDate = new DateTime($contract['end_date']);
        $intervalMonths = $contract['contract_interval'];

        $supervisorMessage = sprintf(
            "Habari ndugu %s, mkataba wa mpangaji %s anayekaa kwenye nyumba %s, chumba %s unakaribia kumalizika (Utamalizika mwezi mmoja kutokea tarehe ya leo). Mkataba huu ulianza tarehe %s na utafikia ukomo tarehe %s. Tafadhali fuatilia hali ya mkataba huu na kuhakikisha kuwa malipo yaliyosalia ya Tsh %.0f yanalipwa kwa wakati.",
            $details['supervisor_name'],
            $details['tenant_name'],
            $details['house_name'],
            $details['room_name'],
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $contract['amount_remaining']
        );

        $supervisorRecipient = [
            'recipient_id' => $contract['id'],
            'dest_addr' => $details['phone_number'],
        ];

        $supervisorResponse = sendSms($api_key, $secret_key, $supervisorMessage, [$supervisorRecipient]);

        if ($supervisorResponse && isset($supervisorResponse['code']) && $supervisorResponse['code'] === 100) {
            echo "Sent SMS to supervisor " . $details['supervisor_name'] . " about contract expiring in one month.\n";
        } else {
            echo "Failed to send SMS to supervisor " . $details['supervisor_name'] . ". Response: " . json_encode($supervisorResponse) . "\n";
        }
    } else {
        echo "No valid phone number found for the supervisor of house ID " . $contract['house_id'] . ".\n";
    }
}
