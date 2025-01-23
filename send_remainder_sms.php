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
    $postData = array(
        'source_addr' => 'BOBTechWave',
        'encoding' => 0,
        'schedule_time' => '',
        'message' => $message,
        'recipients' => $recipients,
    );

    $url = 'https://apisms.beem.africa/v1/send';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
            'Content-Type: application/json',
        ),
        CURLOPT_POSTFIELDS => json_encode($postData),
    ));

    $response = curl_exec($ch);

    if ($response === FALSE) {
        echo "Error: " . curl_error($ch) . "\n";
        return null;
    }

    return json_decode($response, true);
}

$today = new DateTime();
$oneWeekLater = (clone $today)->modify('+7 days');

$stmt = $pdo->prepare("
    SELECT 
        c.id AS contract_id, 
        c.end_date, 
        c.start_date, 
        c.amount_remaining, 
        t.id AS tenant_id, 
        t.phone_number, 
        t.tenant_name, 
        h.house_name, 
        r.room_name
    FROM 
        contracts c
    JOIN tenants t ON c.tenant_id = t.id
    JOIN houses h ON c.house_id = h.id
    JOIN rooms r ON c.room_id = r.id
    WHERE 
        c.end_date BETWEEN :today AND :one_week_later 
        AND c.end_date > :today 
        AND c.contract_status = 'UNAENDELEA'
");
$stmt->execute([
    'today' => $today->format('Y-m-d'),
    'one_week_later' => $oneWeekLater->format('Y-m-d')
]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No contracts expiring in the next week.\n";
    exit;
}

echo "Found " . count($contracts) . " contracts expiring in the next week.\n";

$tenantPhoneNumbers = [];
$tenantMessagesForAdmin = [];

foreach ($contracts as $contract) {
    $endDate = new DateTime($contract['end_date']);
    $startDate = new DateTime($contract['start_date']);
    $daysLeft = $endDate->diff($today)->days;

    $tenantMessage = sprintf(
        "Ndugu %s, mkataba wako wa upangaji wa nyumba %s (chumba namba %s) ulianza tarehe %s na utafika kikomo tarehe %s. Umebakiwa na siku %d mpaka kufikia mwisho wa mkataba wako. Kiasi cha kodi ambacho bado unadaiwa ni Tsh %s. Tafadhali wasiliana na mmiliki/msimamizi kwa taratibu zaidi.",
        $contract['tenant_name'],
        $contract['house_name'],
        $contract['room_name'],
        $startDate->format('Y-m-d'),
        $endDate->format('Y-m-d'),
        $daysLeft,
        number_format($contract['amount_remaining'], 2)
    );

    $tenantMessagesForAdmin[] = sprintf(
        "%s (%s) kutoka nyumba ya %s, chumba namba %s, mkataba wake ulianza tarehe %s na utafika kikomo tarehe %s. Amebakiza idadi ya siku %d mpaka kufikia ukomo wa mkataba wake. Kiasi cha kodi ambacho anadaiwa ni Tsh %s.",
        $contract['tenant_name'],
        $contract['phone_number'],
        $contract['house_name'],
        $contract['room_name'],
        $startDate->format('Y-m-d'),
        $endDate->format('Y-m-d'),
        $daysLeft,
        number_format($contract['amount_remaining'], 2)
    );

    $recipient = [
        'recipient_id' => $contract['tenant_id'],
        'dest_addr' => $contract['phone_number'],
    ];

    $tenantPhoneNumbers[] = ['message' => $tenantMessage, 'recipient' => $recipient];
    echo "Prepared SMS for tenant ID " . $contract['tenant_id'] . ": " . $contract['phone_number'] . "\n";
}

if (!empty($tenantMessagesForAdmin)) {
    $adminMessage = "Habari ndugu, tunakutaarifu kuwa:\n" . implode("\n", $tenantMessagesForAdmin);
} else {
    $adminMessage = "Hakuna mikataba inayokaribia kuisha.";
}

$userPhoneNumbers = [];
$stmt = $pdo->prepare("SELECT id, phone_number, name FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    if (!empty($user['phone_number'])) {
        $recipient = [
            'recipient_id' => $user['id'],
            'dest_addr' => $user['phone_number'],
        ];

        $userPhoneNumbers[] = ['message' => $adminMessage, 'recipient' => $recipient];
    }
}

foreach ($tenantPhoneNumbers as $entry) {
    $response = sendSms($api_key, $secret_key, $entry['message'], [$entry['recipient']]);

    if ($response) {
        echo "SMS sent to tenant " . $entry['recipient']['dest_addr'] . "\n";
    } else {
        echo "Failed to send SMS to tenant " . $entry['recipient']['dest_addr'] . ".\n";
    }
}

foreach ($userPhoneNumbers as $entry) {
    $response = sendSms($api_key, $secret_key, $entry['message'], [$entry['recipient']]);

    if ($response) {
        echo "SMS sent to admin " . $entry['recipient']['dest_addr'] . "\n";
    } else {
        echo "Failed to send SMS to admin " . $entry['recipient']['dest_addr'] . ".\n";
    }
}

echo "Process completed.\n";
