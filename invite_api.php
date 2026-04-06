<?php
require_once __DIR__ . '/autoload.php';
session_start();
header('Content-Type: application/json');

// Alleen POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Alleen POST toegestaan']);
    exit;
}

// Login controleren
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Niet ingelogd']);
    exit;
}

$user = new User();
$user->loadById($_SESSION['user_id']);

$data = json_decode(file_get_contents('php://input'), true);

$recipient_email = $data['recipient_email'] ?? '';
$activity_id = (int)($data['activity_id'] ?? 0);

// Validatie
if (empty($recipient_email) || $activity_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Ongeldig verzoek']);
    exit;
}

// Controleer of activiteit bestaat
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM bookings WHERE id = :id LIMIT 1");
$stmt->bindParam(':id', $activity_id);
$stmt->execute();
$activity = $stmt->fetch();

if (!$activity) {
    http_response_code(404);
    echo json_encode(['error' => 'Activiteit niet gevonden']);
    exit;
}

// Controleer of ontvanger bestaat
$stmt = $db->prepare("SELECT id, username FROM users WHERE email = :email LIMIT 1");
$stmt->bindParam(':email', $recipient_email);
$stmt->execute();
$recipient = $stmt->fetch();

if (!$recipient) {
    http_response_code(404);
    echo json_encode(['error' => 'Gebruiker niet gevonden']);
    exit;
}

// Voeg uitnodiging toe
$stmt = $db->prepare("
    INSERT INTO invites (from_email, to_email, activity_id, created_at)
    VALUES (:from_email, :to_email, :activity_id, NOW())
");
$stmt->bindParam(':from_email', $user->email);
$stmt->bindParam(':to_email', $recipient_email);
$stmt->bindParam(':activity_id', $activity_id);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => "Uitnodiging verzonden naar " . htmlspecialchars($recipient['username']),
        'activity' => [
            'id' => $activity['id'],
            'naam' => $activity['naam'],
            'datum' => $activity['datum']
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Fout bij verzenden']);
}
?>
