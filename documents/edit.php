<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$document_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch document details
$stmt = $conn->prepare("SELECT * FROM documents WHERE id = :id");
$stmt->bindParam(':id', $document_id, PDO::PARAM_INT);
$stmt->execute();
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    header("Location: " . BASE_URL . "documents/manage");
    exit();
}

// Fetch collaborators
$stmt = $conn->prepare("SELECT users.id, users.username FROM document_collaborators LEFT JOIN users ON document_collaborators.user_id = users.id WHERE document_id = :document_id");
$stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
$stmt->execute();
$collaborators = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users for collaborator selection (except the current user)
$allUsers = getUserList($conn, $_SESSION['user_id']);

// Check if the user has permission to view the document
$isCollaboratorStmt = $conn->prepare("SELECT 1 FROM document_collaborators WHERE document_id = :document_id AND user_id = :user_id");
$isCollaboratorStmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
$isCollaboratorStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$isCollaboratorStmt->execute();
$isCollaborator = $isCollaboratorStmt->fetch();

if ($document['created_by'] != $_SESSION['user_id'] && !$isCollaborator) {
    echo "You do not have permission to edit this document.";
    exit;
}

// Get current user details for TogetherJS
$currentUserStmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = :user_id");
$currentUserStmt->bindParam(':user_id', $_SESSION['user_id']);
$currentUserStmt->execute();
$currentUser = $currentUserStmt->fetch(PDO::FETCH_ASSOC);

// Function to convert image to data URL
function getImageDataUrl($imagePath) {
    if (file_exists($imagePath)) {
        $type = pathinfo($imagePath, PATHINFO_EXTENSION);
        $data = file_get_contents($imagePath);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    // If file doesn't exist, return path to default image
    return 'data:image/png;base64,' . base64_encode(file_get_contents('public/uploads/profile/default_profile.png'));
}

// Get image data URL
$avatarUrl = getImageDataUrl($currentUser['profile_picture']);
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Collaborative Document: <?php echo htmlspecialchars($document['title']); ?></h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Document Content</h2>
        <div id="ckeditor"><?php echo $document['content']; ?></div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Collaborators</h2>

        <form id="add_collaborator_form" method="POST" action="actions/add_collaborator.php">
            <input type="hidden" name="document_id" value="<?php echo $document_id; ?>">
            <div class="mb-4">
                <label for="user_id" class="block text-gray-700">Add Collaborator:</label>
                <select name="user_id" id="user_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">Select a user</option>
                    <?php foreach ($allUsers as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Add</button>
        </form>

        <?php if ($collaborators): ?>
            <ul>
                <?php foreach ($collaborators as $collaborator): ?>
                    <li class="mb-2"><?php echo htmlspecialchars($collaborator['username']); ?>
                        <form method='POST' action='actions/drop_collaborator.php'>
                            <input type='hidden' name='document_id' value='<?php echo $document_id ?>'>
                            <input type='hidden' name='user_id' value='<?php echo $collaborator['id'] ?>'>
                            <button type='submit' class='bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300'>Drop</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">No collaborators for this document.</p>
        <?php endif; ?>
    </div>

    <div class="mt-6 flex justify-center">
        <a href="<?php echo BASE_URL; ?>documents/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Documents</a>
        <button id="startCollaboration" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 ml-4">Start Collaborating</button>
    </div>
</div>
<script src="https://insanelyelegant.com/WriteTogether/togetherjs.js"></script>

<script>
// TogetherJS Configuration
TogetherJSConfig_hubBase = "https://togetherjs-hub.glitch.me/";
TogetherJSConfig_getUserName = function () {
    return <?php echo json_encode($currentUser['username']); ?>;
};
TogetherJSConfig_getUserAvatar = function () {
    return <?php echo json_encode($avatarUrl); ?>;
};
TogetherJSConfig_suppressJoinConfirmation = true;
TogetherJSConfig_suppressInvite = true;

// Auto-start collaboration if URL contains the collaboration parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const startCollab = urlParams.get('collaborate');
    
    if (startCollab === 'true') {
        // Small delay to ensure TogetherJS is fully loaded
        setTimeout(() => {
            if (!TogetherJS.running) {
                TogetherJS();
            }
        }, 1000);
    }

    // Manual collaboration button
    document.getElementById('startCollaboration').addEventListener('click', function() {
        if (!TogetherJS.running) {
            TogetherJS();
        }
    });

    let editor;

    ClassicEditor
        .create(document.querySelector('#ckeditor'))
        .then(newEditor => {
            editor = newEditor;
            editor.setData(<?php echo json_encode($document['content']); ?>);

            // Autosave setup
            setInterval(function () {
                saveContent(editor.getData());
            }, 2000);
        })
        .catch(error => {
            console.error("Error initializing CKEditor:", error);
        });

    function saveContent(content) {
        fetch('actions/save.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'document_id=<?php echo $document_id; ?>&content=' + encodeURIComponent(content)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Save error:', data.message);
                alert('Error saving document: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving content:', error);
            alert('Error saving document.');
        });
    }
});
</script>