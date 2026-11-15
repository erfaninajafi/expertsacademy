<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? null;

if (!$userMessage) {
    http_response_code(400);
    echo json_encode(['error' => 'No message provided']);
    exit;
}

$apiUrl = "https://chat.binaexperts.com/api/chat/completions";
$headers = [
    "Authorization: Bearer sk-3f9b237d93f94e9a97f8c7f8248b03ca",
    "Content-Type: application/json"
];

$body = json_encode([
    "model" => "---",
    "messages" => [
        ["role" => "user", "content" => $userMessage]
    ]
]);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

curl_close($ch);

header('Content-Type: application/json');
echo $response;
