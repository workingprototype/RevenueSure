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
.chat-container {
    overflow-y: auto;
    max-height: 60vh;
    padding-bottom: 1rem;
    transition: max-height 0.3s ease; /* Smooth transition for height changes */
}

.chat-message {
    margin-bottom: 1rem;
    clear: both;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.5s forwards; /* Fade in and move up animation */
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Edit textarea styles */
.edit-textarea {
    width: 100%;
    min-height: 80px;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    border: 1px solid #ccc;
    resize: none;
    box-sizing: border-box;
    transition: height 0.3s ease; /* Smooth height transition */
}

/* Ensure the message content container maintains size */
.message-content-container {
    position: relative;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    transition: all 0.3s ease; /* Smooth transition for all properties */
}

.user .message-content-container {
    align-items: flex-end;
}


.message-content {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    max-width: 75%;
    word-wrap: break-word;
    margin-bottom: 0.25rem;
    position: relative; /* For action buttons */
    transition: background-color 0.3s ease; /* Smooth background color transition */
}

/* User message styles */
.user .message-content {
    background-color: #DCF8C6; /* Light green */
    align-self: flex-end;
    border-bottom-right-radius: 0;
    border-top-left-radius: 1rem;
    border-bottom-left-radius: 1rem;
}

/* AI message styles */
.ai .message-content {
    background-color: #F0F0F0;
    align-self: flex-start;
    border-bottom-left-radius: 0;
    border-top-right-radius: 1rem;
    border-bottom-right-radius: 1rem;
}

/* Timestamp styling */
.chat-message .timestamp {
    font-size: 0.7rem;
    color: #888;
    align-self: flex-end;
    margin-top: 0.25rem;
    opacity: 0;
    transition: opacity 0.3s ease; /* Smooth opacity transition */
}

.chat-message:hover .timestamp {
    opacity: 1; /* Show timestamp on hover */
}

.user .timestamp {
    align-self: flex-end;
}

.ai .timestamp {
    align-self: flex-start;
}

/* Typing indicator */
#typingIndicator {
    padding: 0.75rem 1rem;
    background: #e0e0e0;
    border-radius: 1rem;
    opacity: 0;
    transition: opacity 0.3s ease; /* Smooth opacity transition */
}

#typingIndicator.visible {
    opacity: 1; /* Show typing indicator */
}

.input-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
}

#userInput {
    width: 100%;
    min-height: 40px;
    max-height: 200px;
    overflow-y: auto;
    transition: height 0.3s ease, background-color 0.3s ease; /* Smooth height and background color transition */
    resize: none;
    padding: 10px;
    box-sizing: border-box;
    border-radius: 0.5rem;
}

#userInput:focus {
    background-color: #f0f8ff; /* Light blue background on focus */
}

.send-button {
    align-self: flex-end;
    height: fit-content;
    transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth background color and transform transition */
}

.send-button:hover {
    background-color: #0056b3; /* Darker blue on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}

/* Edit and Copy button styles */
.message-actions {
    margin-top: 0.25rem;
    display: flex; /* Arrange buttons horizontally */
    gap: 5px; /* Space between buttons */
    align-self: flex-start; /* Align to the left by default */
    opacity: 0;
    transition: opacity 0.3s ease; /* Smooth opacity transition */
}

.chat-message:hover .message-actions {
    opacity: 1; /* Show actions on hover */
}

.user .message-actions {
    align-self: flex-end; /* Align to the right for user messages */
}

.message-actions button {
    background: none;
    border: none;
    cursor: pointer;
    color: #888; /* Gray color */
    font-size: 1em;
    transition: color 0.3s ease; /* Smooth color transition */
}

.message-actions button:hover {
    color: #333; /* Darker gray on hover */
}

/* Styling for the edit and delete buttons */
.edit-button {
    background-color: #4CAF50; /* Green */
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 5px; /* Add some space below the button */
    font-size: 0.8em;
    transition: background-color 0.3s ease; /* Smooth background color transition */
}

.edit-button:hover {
    background-color: #3e8e41; /* Darker green on hover */
}

/* Initially hide the delete icons */
.delete-icon {
    display: none; /* Hidden by default */
    cursor: pointer;
    color: red;
    margin-left: 5px;
    font-size: 1em; /* Adjust size as needed */
    transition: color 0.3s ease; /* Smooth color transition */
}

.delete-icon:hover {
    color: #ff3333; /* Darker red on hover */
}

.conversation-item {
    display: flex;
    align-items: center;
    padding: 5px 0;
    transition: background-color 0.3s ease; /* Smooth background color transition */
}

.conversation-item:hover {
    background-color: #f0f8ff; /* Light blue background on hover */
}

.conversation-list a {
    flex-grow: 1; /* Allow the link to take up remaining space */
}

/* Show delete icons when edit mode is active */
.edit-mode .delete-icon {
    display: inline-block; /* Show when in edit mode */
}

/* Code block styles */
pre {
    background-color: #f5f5f5;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto; /* Allow horizontal scrolling for long code */
    white-space: pre-wrap; /* Wrap long lines */
    font-family: monospace; /* Use a monospace font */
    margin: 1rem 0; /* Add some margin for spacing */
}

code {
    background-color: #f5f5f5;
    padding: 0.2rem 0.4rem;
    border-radius: 0.3rem;
    font-family: monospace;
}

/* Additional styling for inline code */
pre code {
    background-color: transparent;
    padding: 0;
    border-radius: 0;
}


/* Heading styles */
h1, h2, h3, h4, h5, h6 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    transition: color 0.3s ease; /* Smooth color transition */
}

h1 { font-size: 2em; }
h2 { font-size: 1.5em; }
h3 { font-size: 1.17em; }
h4 { font-size: 1em; }
h5 { font-size: 0.83em; }
h6 { font-size: 0.67em; }

/* Styles for save and cancel buttons during edit */
.save-edit-button, .cancel-edit-button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8em;
    margin-right: 5px; /* Add some space between buttons */
    transition: background-color 0.3s ease; /* Smooth background color transition */
}

.cancel-edit-button {
    background-color: #f44336; /* Red */
}

.save-edit-button:hover {
    background-color: #3e8e41;
}

.cancel-edit-button:hover {
    background-color: #da190b;
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
                 <button id="editConversations" class="edit-button">Edit Conversations</button>
                <ul class="conversation-list">
                    <?php foreach ($conversations as $convo): ?>
                      <li class="conversation-item">
                        <a href="?conversation_id=<?php echo hash('sha256', (string)$convo['id']); ?>" data-conversation-id="<?php echo hash('sha256', (string)$convo['id']); ?>">
                            <?php echo htmlspecialchars($convo['title'] ?: 'Conversation ' . $convo['id']); ?>
                        </a>
                         <i class="fas fa-trash-alt delete-icon" data-conversation-id="<?php echo hash('sha256', (string)$convo['id']); ?>"></i>
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
                            <div class="message-content-container">
                                <div class="message-content" data-message-id="<?php echo $message['id']; ?>">
                                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                </div>
                                <div class="message-actions">
                                    <?php if ($message['sender'] === 'user'): ?>
                                        <button class="edit-msg-btn" onclick="editMessage(<?php echo $message['id']; ?>)" title="Edit"><i class="fas fa-edit"></i></button>
                                    <?php endif; ?>
                                    <button onclick="copyMessage(<?php echo $message['id']; ?>)" title="Copy"><i class="fas fa-copy"></i></button>
                                </div>
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
let editMode = false; // Track edit mode state
let editingMessageId = null; // Track which message is being edited

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


// Edit button functionality
const editButton = document.getElementById('editConversations');
editButton.addEventListener('click', () => {
    editMode = !editMode; // Toggle edit mode
    const conversationList = document.querySelector('.conversation-list');
    conversationList.classList.toggle('edit-mode', editMode);
      if (editMode) {
        editButton.textContent = 'Done Editing';
        editButton.style.backgroundColor = '#ff6347'; // Change to red
    } else {
        editButton.textContent = 'Edit Conversations';
        editButton.style.backgroundColor = '#4CAF50';
    }
});

// Add event listener for delete icons (using event delegation)
document.querySelector('.conversation-list').addEventListener('click', async (event) => {
    if (event.target.classList.contains('delete-icon')) {
        const hashedConversationIdToDelete = event.target.dataset.conversationId;
        if (confirm('Are you sure you want to delete this conversation?')) {
            try {
                const response = await fetch('actions/delete_conversation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo $_SESSION["csrf_token"]; ?>'
                    },
                    body: JSON.stringify({ conversation_id: hashedConversationIdToDelete })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    // Remove the conversation list item from the DOM
                    event.target.closest('li').remove();

                    // If the deleted conversation is the current one, redirect to a new or existing conversation
                    if (hashedConversationIdToDelete === currentConversationId) {
                        const remainingConversations = document.querySelectorAll('.conversation-list a');
                        if (remainingConversations.length > 0) {
                            window.location.href = remainingConversations[0].getAttribute('href');
                        } else {
                            startNewConversation();
                        }
                    }
                } else {
                    alert('Error deleting conversation: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the conversation.');
            }
        }
    }
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

// Function to handle editing a message
async function editMessage(messageId) {
    const messageContentDiv = document.querySelector(`.message-content[data-message-id="${messageId}"]`);
    if (!messageContentDiv) return;

    // Prevent editing multiple messages at once
    if (editingMessageId !== null && editingMessageId !== messageId) {
        alert("Please finish editing the current message before editing another.");
        return;
    }

    editingMessageId = messageId;

    // Store original content
    const originalContent = messageContentDiv.innerHTML;
    const originalText = messageContentDiv.innerText;

    // Replace content with textarea
    const textarea = document.createElement('textarea');
    textarea.value = originalText;
    textarea.classList.add('edit-textarea');

    // Set the initial size of the textarea to match the message content
    textarea.style.height = messageContentDiv.offsetHeight + 'px';
    textarea.style.width = messageContentDiv.offsetWidth + 'px';

    messageContentDiv.innerHTML = '';
    messageContentDiv.appendChild(textarea);
    textarea.focus();

    // --- Button Handling (Corrected) ---
    const actionsDiv = messageContentDiv.nextElementSibling;
    actionsDiv.innerHTML = ''; // Clear existing buttons

    const saveButton = document.createElement('button');
    saveButton.textContent = 'Save';
    saveButton.classList.add('save-edit-button');
    saveButton.dataset.messageId = messageId; // Store messageId on the button!
    actionsDiv.appendChild(saveButton);

    const cancelButton = document.createElement('button');
    cancelButton.textContent = 'Cancel';
    cancelButton.classList.add('cancel-edit-button');
    actionsDiv.appendChild(cancelButton);

    // Named functions for event listeners
    const saveHandler = async () => {
        const newMessage = textarea.value.trim();
        const currentMessageId = saveButton.dataset.messageId; // Get from button

        if (newMessage) {
            try {
                // Update in database
                const updateResponse = await fetch('actions/chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo $_SESSION["csrf_token"]; ?>' },
                    body: JSON.stringify({ message_id: currentMessageId, message: newMessage, action: 'update_message' }) // Use currentMessageId
                });
                const updateData = await updateResponse.json();

                if (updateData.status === 'success') {
                    messageContentDiv.innerHTML = renderMarkdown(newMessage);
                    // Re-send the message
                    await resendMessage(newMessage, currentMessageId); // Pass message and ID
                } else {
                    alert('Error updating message: ' + updateData.message);
                }
            } catch (error) {
                console.error("Error:", error);
                alert('Error updating message: ' + error.message);
            }
        }

        // Remove listeners and clear editing state
        saveButton.removeEventListener('click', saveHandler);
        cancelButton.removeEventListener('click', cancelHandler);
        editingMessageId = null;
        actionsDiv.innerHTML = ''; // Remove the buttons
    };

    const cancelHandler = () => {
        messageContentDiv.innerHTML = originalContent;
        saveButton.removeEventListener('click', saveHandler);
        cancelButton.removeEventListener('click', cancelHandler);
        editingMessageId = null;
        actionsDiv.innerHTML = ''; // Remove the buttons
    };

    saveButton.addEventListener('click', saveHandler);
    cancelButton.addEventListener('click', cancelHandler);
}

// Function to copy a message to the clipboard
async function copyMessage(messageId) {
    const messageContentDiv = document.querySelector(`.message-content[data-message-id="${messageId}"]`);

    if (messageContentDiv) {
        try {
            // Use innerHTML to preserve formatting
            await navigator.clipboard.writeText(messageContentDiv.innerText);
            alert('Message copied to clipboard!');
        } catch (err) {
            console.error('Failed to copy message: ', err);
            alert('Failed to copy message.');
        }
    }
}


async function sendMessage() {
    const userInput = document.getElementById("userInput").value.trim();
    const chatBox = document.getElementById("chatBox");
    const model = document.getElementById("model").value;
    const typingIndicator = document.getElementById("typingIndicator");

    if (!userInput) return;

    // Use the *unhashed* conversation ID for database operations.
    const unhashedConversationId = <?php echo json_encode($conversation_id); ?>;


    // Display user's message (only if it's a new message)
    if (editingMessageId === null) {
        appendMessageToChat('user', userInput);
           // Build the conversation history for context.  Crucial for context.
        let conversationHistory = [];
        document.querySelectorAll('.chat-message').forEach(msg => {
        const sender = msg.classList.contains('user') ? 'user' : 'assistant'; // Use 'assistant' for Puter AI
        const messageText = msg.querySelector('.message-content').innerText;
        conversationHistory.push({ role: sender, content: messageText });
        });

        // Add the current user input to the history.
        conversationHistory.push({role: 'user', content: userInput});

        //Proceed
        await proceedToAIResponse(userInput, conversationHistory, unhashedConversationId, model);
    }

    document.getElementById("userInput").value = '';
}

//New function
async function resendMessage(message, messageId) {
    const chatBox = document.getElementById("chatBox");
    const model = document.getElementById("model").value;
    const typingIndicator = document.getElementById("typingIndicator");
     // Use the *unhashed* conversation ID for database operations.
    const unhashedConversationId = <?php echo json_encode($conversation_id); ?>;

      // Build the conversation history for context.  Crucial for context.
    let conversationHistory = [];
    document.querySelectorAll('.chat-message').forEach(msg => {
      const sender = msg.classList.contains('user') ? 'user' : 'assistant'; // Use 'assistant' for Puter AI
      const messageText = msg.querySelector('.message-content').innerText;

       // Skip the message being edited
      if (parseInt(msg.querySelector('.message-content').dataset.messageId) !== parseInt(messageId)) { //Important to parse.
           conversationHistory.push({ role: sender, content: messageText });
      }
    });

    // Add the current user input to the history.
      conversationHistory.push({role: 'user', content: message});
    await proceedToAIResponse(message, conversationHistory, unhashedConversationId, model);
}

async function proceedToAIResponse(userInput, conversationHistory, unhashedConversationId, model){
      const chatBox = document.getElementById("chatBox");
      const typingIndicator = document.getElementById("typingIndicator");

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
        const userMessageId = saveData.message_id; // Get the message ID from the response


       // Get the AI response via Puter (streaming), including context
        const aiResponse = await puter.ai.chat(conversationHistory, { model: model, stream: true }); // Pass the history
        const aiMessageDiv = document.createElement("div");
        aiMessageDiv.classList.add("chat-message", "ai");

        const aiMessageContentContainer = document.createElement("div");
        aiMessageContentContainer.classList.add("message-content-container");

        const aiMessageContentDiv = document.createElement("div");
        aiMessageContentDiv.classList.add("message-content");
        aiMessageContentContainer.appendChild(aiMessageContentDiv);

        // Add timestamp div *inside* the main message div, but after the content
        const aiTimestampDiv = document.createElement("div");
        aiTimestampDiv.classList.add("timestamp");
        aiMessageDiv.appendChild(aiTimestampDiv);

        const aiActionsDiv = document.createElement('div'); // Create the actions div
        aiActionsDiv.classList.add('message-actions');

        // Add the copy button (no edit button for AI messages)
        const aiCopyButton = document.createElement('button');
        aiCopyButton.innerHTML = '<i class="fas fa-copy"></i>';
        aiCopyButton.title = "Copy";
        aiCopyButton.addEventListener('click', () => {
            copyMessage(aiMessageId);
        });
        aiActionsDiv.appendChild(aiCopyButton);

        aiMessageContentContainer.appendChild(aiActionsDiv);
        aiMessageDiv.appendChild(aiMessageContentContainer);
        chatBox.appendChild(aiMessageDiv);


        let fullAiResponse = '';
        for await (const part of aiResponse) {
            if (part && part.text) {
                fullAiResponse += part.text;
                aiMessageContentDiv.innerHTML = renderMarkdown(fullAiResponse); // Use innerHTML and renderMarkdown
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
         // After successfully saving the AI response, set the message ID
        const aiMessageId = aiSaveData.message_id;
        aiMessageContentDiv.dataset.messageId = aiMessageId;
        console.log("AI Save Response:", aiSaveData); // Log AI save response

        typingIndicator.classList.add("hidden");

    } catch (error) {
        console.error('Error:', error);
        alert("An error occurred: " + error.message);
        typingIndicator.classList.add("hidden"); // Hide on error too
    }
}
// Simple Markdown-to-HTML conversion (for code blocks and headings)
function renderMarkdown(text) {
    // Code blocks (```)
    let html = text.replace(/```([\s\S]+?)```/g, '<pre><code>$1</code></pre>');

    // Headings (#, ##, ###)
    html = html.replace(/^#\s+(.+)$/gm, '<h1>$1</h1>');
    html = html.replace(/^##\s+(.+)$/gm, '<h2>$1</h2>');
    html = html.replace(/^###\s+(.+)$/gm, '<h3>$1</h3>');
    html = html.replace(/^####\s+(.+)$/gm, '<h4>$1</h4>');
    html = html.replace(/^#####\s+(.+)$/gm, '<h5>$1</h5>');
    html = html.replace(/^######\s+(.+)$/gm, '<h6>$1</h6>');

    return html;
}


// Example of appending a message to the chat with markdown rendering
function appendMessageToChat(sender, message) {
    const chatBox = document.getElementById("chatBox");
    const messageDiv = document.createElement("div");
    messageDiv.classList.add("chat-message", sender);

    const messageContentContainer = document.createElement("div");
    messageContentContainer.classList.add("message-content-container");

    const messageContentDiv = document.createElement("div");
    messageContentDiv.classList.add("message-content");
    messageContentDiv.innerHTML = renderMarkdown(message); // Use renderMarkdown to format the message

    messageContentContainer.appendChild(messageContentDiv);

    const actionsDiv = document.createElement('div');
    actionsDiv.classList.add('message-actions');

    // Only add the edit button for user messages
    if (sender === 'user') {
        const editButton = document.createElement('button');
        editButton.classList.add("edit-msg-btn"); // Add class for event delegation
        editButton.innerHTML = '<i class="fas fa-edit"></i>';
        editButton.title = "Edit";
        // *Don't* add the onclick here. We'll use event delegation below.
        actionsDiv.appendChild(editButton);
    }

    const copyButton = document.createElement('button');
    copyButton.innerHTML = '<i class="fas fa-copy"></i>';
    copyButton.title = "Copy";
    copyButton.addEventListener('click', () => {
        copyMessage(messageId);
    });
    actionsDiv.appendChild(copyButton);

    messageContentContainer.appendChild(actionsDiv);
    messageDiv.appendChild(messageContentContainer);

    // Add timestamp div *inside* the main message div
    const timestampDiv = document.createElement("div");
    timestampDiv.classList.add("timestamp");
    timestampDiv.innerText = new Date().toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });
    messageDiv.appendChild(timestampDiv);

    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;

    // We'll get the message ID from the server response after saving the message
    let messageId;
    if (sender === "user") {
        messageId = "temp-id-" + Date.now(); // Temporary ID until we get a real one from the server
        messageContentDiv.dataset.messageId = messageId; // Set a temporary data attribute
    }
}


// Event delegation for edit buttons
document.getElementById('chatBox').addEventListener('click', function(event) {
  if (event.target.closest('.edit-msg-btn')) { // Check if the click was on the edit button or its icon
    const messageId = event.target.closest('.message-content-container').querySelector('.message-content').dataset.messageId;
     editMessage(messageId);

  }
});
</script>