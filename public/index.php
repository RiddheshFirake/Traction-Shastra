<?php
require_once __DIR__ . '/../config/db.php';
session_start();

$stmt = $pdo->query("SELECT * FROM chat_rooms");
$rooms = $stmt->fetchAll();
?>

<link rel="stylesheet" href="style.css">

<div class="chat-container">
    <div class="chat-header">
        <span>Welcome, <?= $_SESSION['user']['username'] ?? 'Guest' ?></span>
        <div><a href="logout.php">Logout</a></div>
    </div>

    <div style="padding: 20px;">
        <h2>📂 Choose a Chat Room</h2>
        <ul>
            <?php foreach ($rooms as $room): ?>
                <li style="margin: 10px 0;">
                    <a href="chat.php?room_id=<?= $room['id'] ?>" style="font-size: 18px;">
                        <?= htmlspecialchars($room['name']) ?>
                    </a>
                    <div style="font-size: 13px; color: #555;"><?= htmlspecialchars($room['description']) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
