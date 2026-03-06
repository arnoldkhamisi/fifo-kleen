<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once 'db_connect.php';

$result = $conn->query('SELECT rating, note FROM feedback ORDER BY rating DESC LIMIT 6');
if (!$result) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rating = (int) ($row['rating'] ?? 0);
    $note = trim((string) ($row['note'] ?? ''));
    if ($rating < 1 || $rating > 5 || $note === '') {
        continue;
    }
    $rows[] = [
        'rating' => $rating,
        'note' => htmlspecialchars($note, ENT_QUOTES, 'UTF-8'),
    ];
}

echo json_encode($rows);
