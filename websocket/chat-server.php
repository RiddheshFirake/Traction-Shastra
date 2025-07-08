<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require dirname(__DIR__) . '/config/db.php'; // DB connection
require dirname(__DIR__) . '/vendor/autoload.php'; // Ratchet

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $usernames;

    public function __construct() {
        $this->clients = [];
        $this->usernames = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients[$conn->resourceId] = [
            'conn' => $conn,
            'room_id' => null
        ];
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "📩 Received raw message: $msg\n";
        $data = json_decode($msg, true);

        if (!isset($data['room_id']) || !isset($data['username'])) return;

        $room_id = (int)$data['room_id'];
        $username = $data['username'];

        $this->clients[$from->resourceId]['room_id'] = $room_id;
        $this->clients[$from->resourceId]['username'] = $username;
        $this->usernames[$from->resourceId] = $username;

        $this->broadcastOnlineUsers($room_id);




        // Typing events
        if (isset($data['type']) && $data['type'] === "typing") {
            $this->broadcastToRoom($room_id, [
                "type" => "typing",
                "username" => $username,
                "room_id" => $room_id
            ]);
            return;
        }

        if (isset($data['type']) && $data['type'] === "stop_typing") {
            $this->broadcastToRoom($room_id, [
                "type" => "stop_typing",
                "username" => $username,
                "room_id" => $room_id
            ]);
            return;
        }

        // Handle and save normal chat message
        $message = trim($data['message'] ?? '');
        if ($message === '') return;

        // Get user_id from DB
        global $pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();


        if ($user) {
        $user_id = $user['id'];

        try {
            $insert = $pdo->prepare("INSERT INTO messages (room_id, user_id, message_text) VALUES (?, ?, ?)");
            $insert->execute([$room_id, $user_id, $message]);
            echo "✅ Message inserted for user_id=$user_id in room_id=$room_id\n";
        } catch (PDOException $e) {
            echo "❌ DB Insert Error: " . $e->getMessage() . "\n";
        }
    }
    


        // Broadcast message to room
        $this->broadcastToRoom($room_id, [
            "type" => "message",
            "username" => $username,
            "message" => $message,
            "room_id" => $room_id
        ]);
    }

    private function broadcastOnlineUsers($room_id) {
        $usersInRoom = [];

        foreach ($this->clients as $client) {
            if ($client['room_id'] === $room_id && isset($client['username'])) {
                $usersInRoom[] = $client['username'];
            }
        }

        foreach ($this->clients as $client) {
            if ($client['room_id'] === $room_id) {
                $client['conn']->send(json_encode([
                    "type" => "user_list",
                    "room_id" => $room_id,
                    "users" => $usersInRoom
                ]));
            }
        }
    }


    private function broadcastToRoom($room_id, $payload) {
        foreach ($this->clients as $client) {
            if ($client['room_id'] === $room_id) {
                $client['conn']->send(json_encode($payload));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $room_id = $this->clients[$conn->resourceId]['room_id'] ?? null;
        unset($this->clients[$conn->resourceId]);
        unset($this->usernames[$conn->resourceId]);

        if ($room_id) {
            $this->broadcastOnlineUsers($room_id);
        }

    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "❌ WebSocket error: " . $e->getMessage() . "\n";
        $conn->close();
    }
}

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();
