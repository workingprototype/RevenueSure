<?php 
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat</title>
    <script src="https://js.puter.com/v2/"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f8;
            margin: 0;
            padding: 0;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: flex-end;
            padding: 20px;
        }

        .chat-box {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            flex-grow: 1;
            padding: 15px;
            max-height: calc(100vh - 120px);
        }

        .chat-message {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .chat-message.user {
            align-items: flex-end;
        }

        .chat-message.ai {
            align-items: flex-start;
        }

        .message-content {
            background-color: #e1e1e1;
            border-radius: 10px;
            padding: 10px 15px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .message-content.user {
            background-color: #0078d4;
            color: white;
        }

        .message-content.ai {
            background-color: #f1f1f1;
        }

        .input-container {
            display: flex;
            margin-top: 10px;
            gap: 10px;
            align-items: flex-end;
        }

        .input-container textarea {
            flex-grow: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            font-size: 16px;
            resize: none; /* Prevents manual resizing */
            min-height: 40px;
            max-height: 200px; /* Adjust max height as per preference */
            overflow-y: auto;
            line-height: 1.4;
            white-space: pre-wrap;
        }

        .input-container button {
            padding: 10px 15px;
            background-color: #0078d4;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .input-container button:hover {
            background-color: #005a9e;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-box" id="chatBox">
            <!-- Messages will be displayed here -->
        </div>
        <div class="input-container">
            <textarea id="userInput" placeholder="Type your message..." oninput="adjustHeight(this)" onkeydown="handleKeyDown(event)"></textarea>
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        // Function to adjust the height of the textarea dynamically as the user types
        function adjustHeight(textarea) {
            textarea.style.height = 'auto';  // Reset height to auto
            textarea.style.height = (textarea.scrollHeight) + 'px';  // Adjust height based on content
        }

        // Handle "Enter" key press to send message or insert a new line
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();  // Prevent the default "Enter" action of creating a new line
                sendMessage();  // Send message on Enter press
            }
        }

        async function sendMessage() {
            const userInput = document.getElementById("userInput").value.trim();

            if (!userInput) {
                return; // Do nothing if input is empty
            }

            // Display user's message in the chat box
            const chatBox = document.getElementById("chatBox");
            const userMessage = document.createElement("div");
            userMessage.classList.add("chat-message", "user");
            const userMessageContent = document.createElement("div");
            userMessageContent.classList.add("message-content", "user");
            userMessageContent.innerText = userInput;
            userMessage.appendChild(userMessageContent);
            chatBox.appendChild(userMessage);
            chatBox.scrollTop = chatBox.scrollHeight;

            document.getElementById("userInput").value = ''; // Clear input field

            // Call the API with user input
            const response = await puter.ai.chat(userInput, { model: 'claude-3-5-sonnet', stream: true });

            // Display AI's streaming response
            const aiMessage = document.createElement("div");
            aiMessage.classList.add("chat-message", "ai");
            const aiMessageContent = document.createElement("div");
            aiMessageContent.classList.add("message-content", "ai");
            aiMessageContent.innerText = ''; // Initially empty content
            aiMessage.appendChild(aiMessageContent);
            chatBox.appendChild(aiMessage);
            chatBox.scrollTop = chatBox.scrollHeight;

            // Stream the response from the AI and update the chat box
            for await (const part of response) {
                aiMessageContent.innerText += part?.text;
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }
    </script>
</body>
</html>
