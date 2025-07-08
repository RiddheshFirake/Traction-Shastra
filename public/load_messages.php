<?php
require '../config/db.php';
session_start();

$room_id = intval($_GET['room_id']);

$stmt = $pdo->prepare("
    SELECT m.message_text, m.timestamp, u.username
    FROM messages m
    JOIN users u ON m.user_id = u.id
    WHERE m.room_id = ?
    ORDER BY m.timestamp ASC
");
$stmt->execute([$room_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
