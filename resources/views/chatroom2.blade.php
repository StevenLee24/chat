<!DOCTYPE html>
<html>
<head>
    <title>Chat Room</title>
    <script>
        let ws;
        let username;

        function connect() {
            ws = new WebSocket('ws://localhost:8080');

            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);

                if (data.action === 'message') {
                    document.getElementById('messages').innerHTML += `<p><strong>${data.sender}:</strong> ${data.message}</p>`;
                } else if (data.action === 'notification') {
                    document.getElementById('messages').innerHTML += `<p><em>${data.message}</em></p>`;
                }
            };

            ws.onopen = function() {
                document.getElementById('status').innerText = 'Connected';
                if (username) {
                    ws.send(JSON.stringify({ action: 'join', username: username }));
                }
            };

            ws.onclose = function() {
                document.getElementById('status').innerText = 'Disconnected';
            };
        }

        function sendMessage() {
            const recipient = document.getElementById('recipient').value;
            const message = document.getElementById('message').value;

            ws.send(JSON.stringify({ action: 'message', recipient: recipient, message: message }));
            document.getElementById('message').value = '';
        }

        function setUsername() {
            username = document.getElementById('username').value;
            connect();
        }
    </script>
</head>
<body>
    <h1>Chat Room</h1>
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" />
        <button onclick="setUsername()">Set Username</button>
    </div>
    <div id="status">Disconnected</div>
    <div id="messages"></div>
    <div>
        <label for="recipient">Recipient:</label>
        <input type="text" id="recipient" />
        <label for="message">Message:</label>
        <input type="text" id="message" />
        <button onclick="sendMessage()">Send</button>
    </div>
</body>
</html>
