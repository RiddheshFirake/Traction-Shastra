# 💬 PHP Real-Time Chat App

This is a **real-time chat application** built using **PHP**, **WebSockets (Ratchet)**, **MySQL**, **HTML/CSS/JS**, and **Vanilla JavaScript**. Users can register, log in, join chat rooms, send messages, see who's online, and get real-time typing indicators with optional desktop notifications.

---

## 📁 Folder Structure

```
├── config/
│   └── db.php               # Database connection
├── database/
│   └── chat_app.sql         # SQL dump to initialize database
├── includes/
│   └── auth.php             # Session handling
├── public/
│   ├── index.php            # Login page
│   ├── register.php         # Registration page
│   ├── chat.php             # Main chat interface
│   ├── load_messages.php    # Load chat history (AJAX)
│   ├── logout.php           # Logout handler
│   ├── script.js            # WebSocket & chat logic
│   └── style.css            # Basic chat UI styles
├── websocket/
│   └── chat-server.php      # WebSocket server using Ratchet
├── vendor/                  # Composer dependencies (Ratchet)
├── composer.json
└── README.md
```

---

## 🔧 Features

- ✅ User Authentication (Register & Login)
- 💬 Real-Time Messaging (WebSocket powered)
- 👀 Online Users Indicator
- ✍️ Typing Indicator
- 🔔 Desktop Notifications
- 💾 Message Persistence (MySQL)
- 🛡️ Basic XSS Protection

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/RiddheshFirake/Traction-Shastra.git
cd chat-app
```

### 2. Setup MySQL Database

- Import the SQL dump:

```sql
Import `database/chat_app.sql` into your MySQL database (via phpMyAdmin or CLI)
```

- Update your DB credentials in `config/db.php`.

---

### 3. Install PHP Dependencies (Ratchet WebSocket)

Make sure [Composer](https://getcomposer.org/) is installed:

```bash
composer install
```

---

### 4. Run the WebSocket Server

```bash
php websocket/chat-server.php
```

> Make sure port `8080` is open on your machine.

---

### 5. Start PHP Server (if testing locally)

```bash
php -S localhost:8000 -t public
```

Then visit:  
👉 [http://localhost:8000/index.php](http://localhost:8000/index.php)

---

## 🛡️ Security

- XSS Prevention via `htmlspecialchars()` in PHP and DOM sanitization in JS.
- Session management using PHP native sessions.
- Inputs are trimmed and validated before processing.

---

## 📸 Screenshots

Register Screen:-
![image](https://github.com/user-attachments/assets/ba6ee85e-b104-4914-b454-0d776882d787)

Login Screen:- 
![image](https://github.com/user-attachments/assets/43b7ada7-7c37-4b38-bb61-41863a5c69b7)

Chats-room:-
![image](https://github.com/user-attachments/assets/abaa7095-c95a-44a9-b47f-956710241d6b)

Chat:-
![image](https://github.com/user-attachments/assets/6979c74a-5f81-45af-ae57-127f7183852c)

---

## 🙌 Credits

Developed by **[Riddhesh Firake](https://github.com/RiddheshFirake)** as part of a Web Developer Internship Assignment.
