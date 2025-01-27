<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lead_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch lead details
$stmt = $conn->prepare("SELECT * FROM leads WHERE id = :id");
$stmt->bindParam(':id', $lead_id);
$stmt->execute();
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch attachments for the lead
$stmt = $conn->prepare("SELECT * FROM attachments WHERE lead_id = :lead_id");
$stmt->bindParam(':lead_id', $lead_id);
$stmt->execute();
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Lead Details: <?php echo htmlspecialchars($lead['name']); ?></h1>

<!-- Lead Details -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <p><strong>Email:</strong> <?php echo htmlspecialchars($lead['email']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($lead['phone']); ?></p>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($lead['category_id']); ?></p>
</div>

<!-- Attachments Section -->
<h2 class="text-2xl font-bold text-gray-800 mb-4">Attachments</h2>

<!-- Upload Attachment Form -->
<form method="POST" action="upload_attachment.php" enctype="multipart/form-data" class="mb-8">
    <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">
    <div class="mb-4">
        <label for="file_type" class="block text-gray-700">Attachment Type</label>
        <select name="file_type" id="file_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            <option value="contract">Contract</option>
            <option value="proposal">Proposal</option>
            <option value="notes">Notes</option>
        </select>
    </div>
    <div class="mb-4">
        <label for="file" class="block text-gray-700">Choose File</label>
        <input type="file" name="file" id="file" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Upload</button>
</form>

<!-- Attachments List -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">File Name</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($attachments): ?>
                <?php foreach ($attachments as $attachment): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($attachment['file_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($attachment['file_type']); ?></td>
                        <td class="px-4 py-2">
                            <a href="<?php echo $attachment['file_path']; ?>" class="text-blue-600 hover:underline" download>Download</a>
                            <a href="delete_attachment.php?id=<?php echo $attachment['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="px-4 py-2 text-center text-gray-600">No attachments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require 'footer.php';
?>