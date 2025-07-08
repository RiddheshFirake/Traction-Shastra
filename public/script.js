const roomId = new URLSearchParams(window.location.search).get('room_id');
const chatBox = document.getElementById("chat-box");
const socket = new WebSocket('ws://localhost:8080');
if ("Notification" in window && Notification.permission !== "granted") {
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            console.log("🔔 Notification permission granted");
        }
    });
}

socket.onopen = () => {
    console.log("✅ WebSocket connected");
};

socket.onerror = (error) => {
    console.error("❌ WebSocket error:", error);
};

socket.onclose = () => {
    console.log("🔌 WebSocket connection closed");
};

// Load message history
let isTabActive = true;

window.addEventListener("focus", () => isTabActive = true);
window.addEventListener("blur", () => isTabActive = false);


fetch(`load_messages.php?room_id=${roomId}`)
    .then(res => res.json())
    .then(messages => {
        messages.forEach(msg => {
            chatBox.innerHTML += `<div>${msg.username}: ${msg.message_text} <span style="font-size:10px; color:gray">(${msg.timestamp})</span></div>`;
        });
        chatBox.scrollTop = chatBox.scrollHeight; // auto scroll to bottom
    });



console.log("Current Username:", currentUsername); // Check if defined

// Handle connection open

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    const currentRoom = parseInt(roomId);

    //console.log("📩 Received WebSocket message:", data);

     // 🔔 Desktop notification logic
    if (data.room_id !== currentRoom) return;

    if (data.type === "message") {
        const msgClass = data.username === currentUsername ? 'you' : '';
        function escapeHTML(str) {
            return str.replace(/[&<>"']/g, function (tag) {
                const chars = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                };
                return chars[tag] || tag;
            });
        }

        chatBox.innerHTML += `<div>${escapeHTML(data.username)}: ${escapeHTML(data.message)}</div>`;



        // ✅ Only notify if: 1) not from me, 2) tab not focused
        if (data.username !== currentUsername && !isTabActive && Notification.permission === "granted") {
            new Notification(`New message from ${data.username}`, {
                body: data.message,
                icon: "https://cdn-icons-png.flaticon.com/512/727/727399.png"
            });
        }

        chatBox.scrollTop = chatBox.scrollHeight;
    }

    else if (data.type === "user_list") {
        const userList = data.users;
        const listElement = document.getElementById("online-users");

        listElement.innerHTML = ""; // Clear previous
        userList.forEach(user => {
            const li = document.createElement("li");
            li.textContent = user;
            listElement.appendChild(li);
        });
    }


    if (data.room_id !== currentRoom) return;

    if (data.type === "typing") {
        if (data.username !== currentUsername) {
            document.getElementById("typing-status").innerText = `${data.username} is typing...`;
        }
    } else if (data.type === "stop_typing") {
        if (data.username !== currentUsername) {
            document.getElementById("typing-status").innerText = '';
        }
    }
};




document.getElementById("message-input").addEventListener("keypress", function(e) {
    if (e.key === 'Enter' && this.value.trim() !== '') {
        const msg = {
            type: "message",
            room_id: parseInt(roomId),
            username: currentUsername,
            message: this.value
        };

        socket.send(JSON.stringify(msg));
        this.value = '';
    }
});




// Handle typing indication

let typingTimeout;

document.getElementById("message-input").addEventListener("input", function () {
    clearTimeout(typingTimeout);

    const typingData = {
        room_id: parseInt(roomId),
        username: currentUsername,
        type: "typing"
    };
    socket.send(JSON.stringify(typingData));
    //console.log("📤 Sent typing:", typingData);


    typingTimeout = setTimeout(() => {
        const stopTypingData = {
            room_id: parseInt(roomId),
            username: currentUsername,
            type: "stop_typing"
        };
        socket.send(JSON.stringify(stopTypingData));
        //console.log("📤 Sent typing:", typingData);

    }, 1500); // 1.5s delay to detect stop
});


document.getElementById("send-btn").addEventListener("click", function () {
    const input = document.getElementById("message-input");
    if (input.value.trim() !== '') {
        const msg = {
            room_id: parseInt(roomId),
            username: currentUsername,
            message: input.value
        };
        socket.send(JSON.stringify(msg));
        input.value = '';
    }
});
