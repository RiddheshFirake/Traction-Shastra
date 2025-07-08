<?php
require '../includes/auth.php';
require '../config/db.php';

if (!isset($_GET['room_id'])) {
    die("No chat room selected.");
}

$room_id = intval($_GET['room_id']);
$stmt = $pdo->prepare("SELECT * FROM chat_rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    die("Invalid chat room.");
}

$_SESSION['room_id'] = $room_id;
?>


<link rel="stylesheet" href="style.css">

<div class="chat-container">
    <div class="chat-header">
        <span>💬 Room: <?= htmlspecialchars($room['name']) ?></span>
        <div>
            <a href="index.php">← Change Room</a> |
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="users-list">
        <strong>🟢 Online Users:</strong>
        <ul id="online-users"></ul>
    </div>

    <div id="chat-box" class="chat-box"></div>
    <div id="typing-status" class="typing-status"></div>

    <div class="chat-input-container">
        <input type="text" id="message-input" placeholder="Type a message..." autocomplete="off" />
        <button id="send-btn">Send</button>
    </div>
</div>

<script>
    const currentUsername = "<?= $_SESSION['user']['username'] ?>";
</script>
<script src="script.js"></script>
