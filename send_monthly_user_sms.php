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

// Fetch active contracts
$stmt = $pdo->prepare("
    SELECT * FROM contracts 
    WHERE start_date <= :today AND end_date >= :today AND contract_status = 'UNAENDELEA'
");
$stmt->execute(['today' => $today->format('Y-m-d')]);
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contracts)) {
    echo "No active contracts found.\n";
    exit;
}

echo "Found " . count($contracts) . " active contracts.\n";

// Fetch users with phone numbers
$userStmt = $pdo->query("
    SELECT id, name, phone_number FROM users 
    WHERE phone_number IS NOT NULL
");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($contracts as $contract) {
    // Fetch house, room, tenant, and tenant phone details for the current contract
    $stmt = $pdo->prepare("
        SELECT h.house_name, h.house_owner, r.room_name, t.tenant_name, t.phone_number
        FROM houses h
        JOIN rooms r ON r.id = :room_id
        JOIN tenants t ON t.id = :tenant_id
        WHERE h.id = :house_id
    ");
    $stmt->execute([
        'house_id' => $contract['house_id'],
        'room_id' => $contract['room_id'], // Use the specific room_id for accurate room data
        'tenant_id' => $contract['tenant_id']
    ]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($details) {
        $startDate = new DateTime($contract['start_date']);
        $endDate = new DateTime($contract['end_date']);
        $daysDifference = $today->diff($startDate)->days;

        if ($daysDifference > 0 && $daysDifference % 30 === 0) {
            $monthsElapsed = (int)($daysDifference / 30);
            $remainingMonths = max(0, $contract['contract_interval'] - $monthsElapsed);

            // Updated tenant message with phone number
            $tenantMessage = sprintf(
                "Habari,\n\n" .
                    "Mpangaji wako %s - %s kutoka nyumba ya %s chumba namba (%s) amekamilisha miezi %d katika mkataba wake " .
                    "ambao ulianza tarehe %s na utamalizika tarehe %s. Kwa sasa amebakiza miezi %d na kiasi baki cha kodi anachodaiwa ni Tsh %.0f.\n\n" .
                    "Tafadhali zingatia kufuatilia hili kabla ya tarehe ya mwisho wa mkataba. Asante.",
                $details['tenant_name'],
                $details['phone_number'],
                $details['house_name'],
                $details['room_name'],
                $monthsElapsed,
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                $remainingMonths,
                $contract['amount_remaining']
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
                    echo "Error sending message to " . $user['name'] . ": " . curl_error($ch) . "\n";
                } else {
                    echo "Message sent to " . $user['name'] . " (" . $user['phone_number'] . ").\n";
                }

                curl_close($ch);
            }
        }
    } else {
        echo "No valid details for contract ID " . $contract['id'] . ".\n";
    }
}

echo "All messages processed.\n";

//Completed2