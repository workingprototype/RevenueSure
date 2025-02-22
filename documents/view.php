<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$document_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch document details
$stmt = $conn->prepare("SELECT documents.*, users.username as creator_name FROM documents LEFT JOIN users ON documents.created_by = users.id WHERE documents.id = :id");
$stmt->bindParam(':id', $document_id);
$stmt->execute();
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    header("Location: " . BASE_URL . "documents/manage");
    exit();
}

// Check if the user has permission to view the document (creator or collaborator)
$isCollaboratorStmt = $conn->prepare("SELECT 1 FROM document_collaborators WHERE document_id = :document_id AND user_id = :user_id");
$isCollaboratorStmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
$isCollaboratorStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$isCollaboratorStmt->execute();
$isCollaborator = $isCollaboratorStmt->fetch();

if ($document['created_by'] != $_SESSION['user_id'] && !$isCollaborator) {
    echo "You do not have permission to view this document.";
    exit; // Or redirect to an error page
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">View Document: <?php echo htmlspecialchars($document['title']); ?></h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <p class="text-gray-600">
            <strong>Created By:</strong> <?php echo htmlspecialchars($document['creator_name']); ?> on <?php echo htmlspecialchars($document['created_at']); ?>
        </p>
        <div class="mt-4 border border-gray-200 p-4 rounded-lg">
            <?php echo $document['content']; ?>
        </div>
    </div>
    <div class="mt-6">
        <a href="<?php echo BASE_URL; ?>documents/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Documents</a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const documentId = <?php echo $document_id; ?>;
    const documentContentDiv = document.getElementById('document_content');

    const formData = new FormData();
    formData.append('action', 'get_document_content'); // API Action
    formData.append('document_id', documentId);
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>'); // Include CSRF token

    fetch('actions/document_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            documentContentDiv.innerHTML = data.content;
        } else {
            console.error('Error loading document content:', data.message);
            documentContentDiv.textContent = 'Error loading document content.';
        }
    })
    .catch(error => {
        console.error('Network error loading document content:', error);
        documentContentDiv.textContent = 'Network error loading document content.';
    });
});
</script>