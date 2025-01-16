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

$updateStmt = $pdo->prepare("UPDATE contracts SET contract_status = 'UMEISHA' WHERE end_date = :today AND contract_status != 'UMEISHA'");
$updateStmt->execute(['today' => $today->format('Y-m-d')]);

$stmt = $pdo->prepare("SELECT contracts.*, tenants.tenant_name, houses.house_name, houses.house_owner, rooms.room_name FROM contracts
                        JOIN tenants ON contracts.tenant_id = tenants.id
                        JOIN houses ON contracts.house_id = houses.id
                        JOIN rooms ON contracts.room_id = rooms.id
                        WHERE contracts.end_date = :today AND contracts.contract_status = 'UMEISHA'");
$stmt->execute(['today' => $today->format('Y-m-d')]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No contracts ended today.\n";
    exit;
}

echo "Found " . count($contracts) . " contracts that ended today.\n";

$userStmt = $pdo->query("SELECT id, name, phone_number FROM users WHERE phone_number IS NOT NULL");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($contracts as $contract) {
    $tenantMessage = sprintf(
        "Habari mwenye nyumba %s\n\n" .
        "Leo tarehe %s mkataba wa mpangaji wako %s, kutoka nyumba %s na chumba %s amemaliza mkataba wake wa miezi %d ambao ulianza tarehe %s. Tafadhali zingatia kufuata hatua zinazohitajika. Asante.",
        $contract['house_owner'],
        $today->format('Y-m-d'),
        $contract['tenant_name'],
        $contract['house_name'],
        $contract['room_name'],
        $contract['contract_interval'],
        (new DateTime($contract['start_date']))->format('Y-m-d')
    );

    foreach ($users as $user) {
        $postData = [
            'source_addr' => 'BOBTechWave',
            'encoding' => 0,
            'schedule_time' => '',
            'message' => $tenantMessage,
            'recipients' => [
                [
                    'recipient_id' => (string)($user['id']),
                    'dest_addr' => $user['phone_number']
                ]
            ]
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
            echo "Error sending message for contract ID " . $contract['id'] . ": " . curl_error($ch) . "\n";
        } else {
            echo "Message sent for contract ID " . $contract['id'] . ". Response: " . $response . "\n";
        }

        curl_close($ch);
    }
}

//Completed