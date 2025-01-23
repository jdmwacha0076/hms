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

function sendSms($api_key, $secret_key, $message, $recipients, $reference_id)
{
    $postData = array(
        'source_addr' => 'BOBTechWave',
        'encoding' => 0,
        'schedule_time' => '',
        'message' => $message,
        'recipients' => $recipients,
        'reference_id' => $reference_id,
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

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE end_date BETWEEN :today AND :one_week_later AND end_date > :today AND contract_status = 'UNAENDELEA'");
$stmt->execute(['today' => $today->format('Y-m-d'), 'one_week_later' => $oneWeekLater->format('Y-m-d')]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No contracts expiring in the next week.\n";
    exit;
}

echo "Found " . count($contracts) . " contracts expiring in the next week.\n";

$tenantPhoneNumbers = [];
$tenantMessagesForAdmin = [];
$supervisorPhoneNumbers = [];

foreach ($contracts as $contract) {

    $stmt = $pdo->prepare("SELECT phone_number, tenant_name FROM tenants WHERE id = :tenant_id");
    $stmt->execute(['tenant_id' => $contract['tenant_id']]);
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT phone_number, supervisor_name FROM supervisors WHERE id = (SELECT supervisor_id FROM houses WHERE id = :house_id)");
    $stmt->execute(['house_id' => $contract['house_id']]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tenant && !empty($tenant['phone_number']) && $supervisor && !empty($supervisor['phone_number'])) {
        $endDate = new DateTime($contract['end_date']);
        $startDate = new DateTime($contract['start_date']);
        $daysLeft = $endDate->diff($today)->days;

        $tenantMessage = sprintf(
            "Ndugu %s, mkataba wako wa upangaji ulianza siku ya tarehe %s na utafika kikomo chake siku ya tarehe %s. Ikiwa tumebakiwa na siku %d, ujumbe huu unakukumbusha kuwasiliana na mmiliki/msimamizi wa nyumba kwa taratibu zaidi.",
            $tenant['tenant_name'],
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $daysLeft
        );

        $tenantMessagesForAdmin[] = sprintf(
            "%s mkataba wake ulianza siku ya tarehe %s, na utafika kikomo siku ya tarehe %s, kuna idadi ya siku %d kuweza kufunga mkataba huu. Tafadhali unakumbushwa kufuatilia hili.",
            $tenant['tenant_name'],
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $daysLeft
        );

        $recipient = [
            'recipient_id' => $contract['tenant_id'],
            'dest_addr' => $tenant['phone_number'],
        ];

        $tenantPhoneNumbers[] = ['message' => $tenantMessage, 'recipient' => $recipient];

        $supervisorMessage = sprintf(
            "Habari %s, Mkataba wa ndugu %s katika chumba namba %s ulianza tarehe %s na utafika kikomo tarehe %s. Tafadhali wasiliana na mmiliki/mpangishaji na mpangaji kwa hatua zaidi.",
            $supervisor['supervisor_name'],
            $tenant['tenant_name'],
            $contract['room_id'],
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        $supervisorRecipient = [
            'recipient_id' => $contract['house_id'],
            'dest_addr' => $supervisor['phone_number'],
        ];

        $supervisorPhoneNumbers[] = ['message' => $supervisorMessage, 'recipient' => $supervisorRecipient];
    }
}

if (!empty($tenantMessagesForAdmin)) {
    $adminMessage = "Habari ndugu, tunakutaarifa kuwa:\n" . implode("\n", $tenantMessagesForAdmin);
} else {
    $adminMessage = "No expiring contracts found.";
}

foreach ($tenantPhoneNumbers as $entry) {
    $reference_id = "tenant_" . $entry['recipient']['recipient_id'] . "_sms_" . time();
    $response = sendSms($api_key, $secret_key, $entry['message'], [$entry['recipient']], $reference_id);

    if ($response) {
        var_dump($response);
    } else {
        echo "Failed to send SMS to " . $entry['recipient']['dest_addr'] . ".\n";
    }
}

foreach ($supervisorPhoneNumbers as $entry) {
    $reference_id = "supervisor_" . $entry['recipient']['recipient_id'] . "_sms_" . time();
    $response = sendSms($api_key, $secret_key, $entry['message'], [$entry['recipient']], $reference_id);

    if ($response) {
        var_dump($response);
    } else {
        echo "Failed to send SMS to " . $entry['recipient']['dest_addr'] . ".\n";
    }
}

//Completed