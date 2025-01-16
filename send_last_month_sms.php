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

$oneMonthFromNow = (new DateTime())->modify('+1 month');

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE end_date = :oneMonthFromNow AND contract_status = 'UNAENDELEA'");
$stmt->execute(['oneMonthFromNow' => $oneMonthFromNow->format('Y-m-d')]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No contracts found expiring in one month.\n";
    exit;
}

echo "Found " . count($contracts) . " contracts expiring in one month.\n";

foreach ($contracts as $contract) {

    $today = new DateTime();


    $stmt = $pdo->prepare("SELECT tenant_name, phone_number FROM tenants WHERE id = :tenant_id");
    $stmt->execute(['tenant_id' => $contract['tenant_id']]);
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tenant && !empty($tenant['phone_number'])) {
        $startDate = new DateTime($contract['start_date']);
        $endDate = new DateTime($contract['end_date']);
        $intervalMonths = $contract['contract_interval'];

        $tenantMessage = sprintf(
            "Habari ndugu %s, mkataba wako wa kupangisha nyumba kwa muda wa miezi %d ulianza rasmi siku ya tarehe %s na utamalizika siku ya tarehe %s, yaani ndani ya mwezi mmoja kuanzia siku ya leo tarehe %s. Unakumbushwa kuwa bado kiasi cha Tsh %.0f ambacho bado hakijalipwa katika mkataba huu. Tafadhali fanya mawasiliano na mmiliki/msimamizi kwa maelezo zaidi.",
            $tenant['tenant_name'],
            $intervalMonths,
            $startDate->format('Y-m-d'),
            (new DateTime($contract['end_date']))->format('Y-m-d'),
            $today->format('Y-m-d'),
            $contract['amount_remaining']
        );


        $tenantRecipient = [
            'recipient_id' => $contract['tenant_id'],
            'dest_addr' => $tenant['phone_number'],
        ];

        $tenantResponse = sendSms($api_key, $secret_key, $tenantMessage, [$tenantRecipient]);

        if ($tenantResponse) {
            echo "Sent SMS to tenant " . $tenant['tenant_name'] . " about contract expiring in one month.\n";
        } else {
            echo "Failed to send SMS to tenant " . $tenant['tenant_name'] . ".\n";
        }
    } else {
        echo "No valid phone number found for tenant ID " . $contract['tenant_id'] . ".\n";
    }
}