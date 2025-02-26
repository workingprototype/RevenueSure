<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

// Fetch conversations *before* determining the conversation ID
$stmt = $conn->prepare("SELECT id, title FROM ai_conversations WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine the conversation ID to display
$conversation_id = null; // Initialize

if (isset($_GET['conversation_id'])) {
    // Verify the conversation ID against the user's conversations
    $hashed_conversation_id = $_GET['conversation_id'];
    foreach ($conversations as $convo) {
        if (hash('sha256', (string)$convo['id']) === $hashed_conversation_id) {
            $conversation_id = $convo['id'];
            break;
        }
    }
    //If no conversations were found with the conversation ID and User ID, redirect to the index.
    if(empty($conversation_id)) {
        header("Location: /ai/"); //Important: Absolute URL
        exit;
    }

} elseif (!empty($conversations)) {
    // If no conversation ID is in the GET request, use the most recent.
    $conversation_id = $conversations[0]['id'];
}

// If no conversations exist for this user, create one.
if (empty($conversations) && !isset($_GET['conversation_id'])) {
        $stmt = $conn->prepare("INSERT INTO ai_conversations (user_id) VALUES (:user_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $conversation_id = $conn->lastInsertId();

        // Redirect to the new conversation *after* creation
        header("Location: ?conversation_id=" . hash('sha256', (string)$conversation_id));  // Cast to string
        exit;
}


// Fetch messages for the selected conversation
$messages = [];
if ($conversation_id) {
    $stmt = $conn->prepare("SELECT * FROM ai_messages WHERE conversation_id = :conversation_id ORDER BY sent_at ASC");
    $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Set current conversation ID.  Important for the JavaScript.
$_SESSION['conversation_id'] = $conversation_id;

?>

<style>
/* --- Chat Bubble Styles --- */

/* Container for the entire chat area */
/* Chat Bubble Styles */
.chat-container {
    overflow-y: auto;        /* Enable vertical scrolling */
    max-height: 60vh;        /* Limit height (adjust as needed) */
    padding-bottom: 1rem;    /* Add padding at the bottom */
}

.chat-message {
    margin-bottom: 1rem;
    clear: both; /* Prevent floating next to each other */
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* Align items to the start (left) */
}

.chat-message .message-content {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    max-width: 75%;         /* Limit message width */
    word-wrap: break-word;  /* Handle long words */
    margin-bottom: 0.25rem; /* Space below message content */
    position: relative;     /* For timestamp positioning */
}

/* User message styles */
.user .message-content {
    background-color: #DCF8C6; /* Light green */
    align-self: flex-end;      /* Align to the right */
    border-bottom-right-radius: 0; /* Remove bottom-right corner radius */
}

/* AI message styles */
.ai .message-content {
    background-color: #F0F0F0; /* Light gray */
    align-self: flex-start;     /* Align to the left */
    border-bottom-left-radius: 0;  /* Remove bottom-left corner radius */
}

/* Timestamp styling */
.chat-message .timestamp {
    font-size: 0.7rem;
    color: #888;
    align-self: flex-end; /* Align timestamp to the right for user messages */
    margin-top: 0.25rem;  /* Space above the timestamp */
}

.user .timestamp {
    align-self: flex-end; /* Right-align for user */
}

.ai .timestamp {
    align-self: flex-start;  /* Left-align for AI */
}

/* Typing indicator */
#typingIndicator {
    padding: 0.75rem 1rem;
    background: #e0e0e0;
    border-radius: 1rem;
}

.input-container {
    display: flex;
    flex-direction: column; /* Stack items vertically */
    align-items: flex-start; /* Align items to the start (left) */
    gap: 10px; /* Space between textarea and button */
}

#userInput {
    width: 100%;
    min-height: 40px; /* Minimum height */
    max-height: 200px; /* Maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
    transition: height 0.2s ease; /* Smooth height transition */
    resize: none; /* Disable manual resizing */
    padding: 10px; /* Add padding for better appearance */
    box-sizing: border-box; /* Include padding in the element's total width and height */
}

.send-button {
    align-self: flex-end; /* Align the button to the right */
    height: fit-content; /* Adjust height based on content */
}
</style>
</head>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">AI Chat</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Conversation List (Sidebar) -->
        <div class="md:col-span-1">
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Conversations</h2>
                <ul class="conversation-list">
                    <?php foreach ($conversations as $convo): ?>
                        <li>
                            <a href="?conversation_id=<?php echo hash('sha256', (string)$convo['id']); ?>" data-conversation-id="<?php echo hash('sha256', (string)$convo['id']); ?>">
                                <?php echo htmlspecialchars($convo['title'] ?: 'Conversation ' . $convo['id']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button onclick="startNewConversation()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">New Conversation</button>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="md:col-span-3">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="chat-container" id="chatBox">
                    <?php foreach ($messages as $message): ?>
                        <div class="chat-message <?php echo $message['sender'] === 'user' ? 'user' : 'ai'; ?>">
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                            </div>
                            <div class="timestamp">
                                <?php echo date('M j, g:i A', strtotime($message['sent_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="typingIndicator" class="hidden">AI is typing...</div>
                <div class="input-container mt-4">
                    <textarea id="userInput" placeholder="Type your message..." class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    <button onclick="sendMessage()" class="send-button bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition duration-300">Send</button>
                </div>
                <div class="mt-4">
                    <label for="model" class="block text-gray-700">AI Model:</label>
                    <select id="model" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="claude-3-5-sonnet">Claude 3.5 Sonnet</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.puter.com/v2/"></script>
<script>
// Use the *hashed* conversation ID from PHP.  Critical for consistency.
let currentConversationId = '<?php echo hash('sha256', (string)$conversation_id); ?>';

async function startNewConversation() {
    try {
        let response = await fetch('actions/new_conversation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: JSON.stringify({ user_id: <?php echo $_SESSION['user_id']; ?> })
        });

        let data = await response.json();
        if (data.status === 'success') {
            currentConversationId = data.conversation_id; // Update the global variable.  This is now the *hashed* ID.
            window.location.href = '?conversation_id=' + currentConversationId;
        } else {
            console.error("Could not create conversation", data.message);
            alert("Could not create conversation: " + data.message);
        }
    } catch (error) {
        console.error('Error during fetch:', error);
        alert("Failed to create conversation: " + error.message);
    }
}


// Update the conversation ID when a conversation is selected from the list
document.querySelectorAll('.conversation-list a').forEach(link => {
    link.addEventListener('click', event => {
        event.preventDefault();
        currentConversationId = link.getAttribute('data-conversation-id');
        window.location.href = '?conversation_id=' + currentConversationId;
    });
});


const textarea = document.getElementById('userInput');

textarea.addEventListener('input', function() {
    // Adjust the height based on the content
    this.style.height = 'auto'; // Reset height to auto
    let newHeight = this.scrollHeight;

    // Set the new height with a maximum limit
    if (newHeight > 200) {
        this.style.height = '200px';
        this.style.overflowY = 'scroll'; // Enable scrolling
    } else {
        this.style.height = newHeight + 'px';
        this.style.overflowY = 'hidden'; // Hide scrollbar if within limit
    }
});

// Trigger the resize on initial load in case there's pre-filled text
textarea.dispatchEvent(new Event('input'));

async function sendMessage() {
    const userInput = document.getElementById("userInput").value.trim();
    const chatBox = document.getElementById("chatBox");
    const model = document.getElementById("model").value;
    const typingIndicator = document.getElementById("typingIndicator");

    if (!userInput) return;

    // Use the *unhashed* conversation ID for database operations.
    const unhashedConversationId = <?php echo json_encode($conversation_id); ?>;


    // Display user's message
    appendMessageToChat('user', userInput);
    document.getElementById("userInput").value = '';

    // Show typing indicator
    typingIndicator.classList.remove("hidden");

    try {
        // Save user message
        const saveResponse = await fetch('actions/chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: JSON.stringify({
                message: userInput,
                conversation_id: unhashedConversationId, // Use unhashed ID
                model: model,
                action: 'save_user_message'
            })
        });

        if (!saveResponse.ok) {
            const errorData = await saveResponse.json();
            throw new Error("Failed to save user message: " + (errorData.message || "Unknown error"));
        }
        const saveData = await saveResponse.json(); //Get the json data.
        console.log("Save Response:", saveData); // Log save response


        // Get the AI response via Puter (streaming)
        const aiResponse = await puter.ai.chat(userInput, { model: model, stream: true });
        const aiMessageDiv = document.createElement("div");
        aiMessageDiv.classList.add("chat-message", "ai");
        const aiMessageContentDiv = document.createElement("div");
        aiMessageContentDiv.classList.add("message-content");
        aiMessageDiv.appendChild(aiMessageContentDiv);
        // Add timestamp div *inside* the main message div, but after the content
        const aiTimestampDiv = document.createElement("div");
        aiTimestampDiv.classList.add("timestamp");
        aiMessageDiv.appendChild(aiTimestampDiv);

        chatBox.appendChild(aiMessageDiv);
        let fullAiResponse = '';
        for await (const part of aiResponse) {
            if (part && part.text) {
                fullAiResponse += part.text;
                aiMessageContentDiv.innerText = fullAiResponse;
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }
        // Set the timestamp *after* the streaming is complete
        aiTimestampDiv.innerText = new Date().toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });

        // Send the complete AI response to the server
        const aiSaveResponse = await fetch('actions/chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: JSON.stringify({
                message: fullAiResponse,
                conversation_id: unhashedConversationId, // Use unhashed ID
                model: model,
                action: 'save_ai_response'
            })
        });


        if (!aiSaveResponse.ok) {
            const errorData = await aiSaveResponse.json();
            throw new Error("Failed to save AI response: " + (errorData.message || "Unknown error"));
        }
        const aiSaveData = await aiSaveResponse.json(); //Get the JSON.
        console.log("AI Save Response:", aiSaveData); // Log AI save response

        typingIndicator.classList.add("hidden");

    } catch (error) {
        console.error('Error:', error);
        alert("An error occurred: " + error.message);
        typingIndicator.classList.add("hidden"); // Hide on error too
    }
}

function appendMessageToChat(sender, message) {
    const chatBox = document.getElementById("chatBox");
    const messageDiv = document.createElement("div");
    messageDiv.classList.add("chat-message", sender);
    const messageContentDiv = document.createElement("div");
    messageContentDiv.classList.add("message-content");
    messageContentDiv.innerText = message;
    messageDiv.appendChild(messageContentDiv);

    // Add timestamp div *inside* the main message div
    const timestampDiv = document.createElement("div");
    timestampDiv.classList.add("timestamp");
    timestampDiv.innerText = new Date().toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });
    messageDiv.appendChild(timestampDiv);

    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}
</script>