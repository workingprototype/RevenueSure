<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lead_id = $_POST['lead_id'];
    $file_type = $_POST['file_type'];

    if ($file_type === 'notes') {
        // Handle notes
        $note = trim($_POST['note']);
        if (!empty($note)) {
            $stmt = $conn->prepare("INSERT INTO attachments (lead_id, file_name, file_type) VALUES (:lead_id, :file_name, :file_type)");
            $stmt->bindParam(':lead_id', $lead_id);
            $stmt->bindParam(':file_name', $note);
            $stmt->bindParam(':file_type', $file_type);

            if ($stmt->execute()) {
                echo "<script>alert('Note added successfully!'); window.location.href='<?php echo BASE_URL; ?>leads/view?id=$lead_id';</script>";
            } else {
                echo "<script>alert('Error adding note.'); window.location.href='<?php echo BASE_URL; ?>leads/view?id=$lead_id';</script>";
            }
        } else {
            echo "<script>alert('Note cannot be empty.'); window.location.href='<?php echo BASE_URL; ?>leads/view?id=$lead_id';</script>";
        }
    } else {
        // Handle file uploads
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file_name = basename($_FILES['file']['name']);
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_path = "public/uploads/" . uniqid() . "_" . $file_name;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $file_path)) {
                $stmt = $conn->prepare("INSERT INTO attachments (lead_id, file_name, file_path, file_type) VALUES (:lead_id, :file_name, :file_path, :file_type)");
                $stmt->bindParam(':lead_id', $lead_id);
                $stmt->bindParam(':file_name', $file_name);
                $stmt->bindParam(':file_path', $file_path);
                $stmt->bindParam(':file_type', $file_type);

                if ($stmt->execute()) {
                    echo "<script>alert('File uploaded successfully!'); window.location.href='leads/view?id=$lead_id';</script>";
                } else {
                    echo "<script>alert('Error uploading file.'); window.location.href='leads/view?id=$lead_id';</script>";
                }
            } else {
                echo "<script>alert('Error moving file.'); window.location.href='leads/view?id=$lead_id';</script>";
            }
        } else {
            echo "<script>alert('No file uploaded or file error.'); window.location.href='leads/view?id=$lead_id';</script>";
        }
    }
}
?>