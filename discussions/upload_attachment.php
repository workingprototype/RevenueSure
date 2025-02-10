<?php

require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $message_id = $_POST['message_id'];
     $discussion_id = $_POST['discussion_id'];
    $file_name = basename($_FILES['file']['name']);
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_path = "public/uploads/" . uniqid() . "_" . $file_name;

     if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

    if (move_uploaded_file($file_tmp, $file_path)) {
          $stmt = $conn->prepare("INSERT INTO discussion_attachments (message_id, file_name, file_path) VALUES (:message_id, :file_name, :file_path)");
        $stmt->bindParam(':message_id', $message_id);
           $stmt->bindParam(':file_name', $file_name);
           $stmt->bindParam(':file_path', $file_path);

        if ($stmt->execute()) {
           header("Location: " . BASE_URL . "discussions/view?id=$discussion_id&success=true");
            exit();
        } else {
           echo "<script>alert('Error uploading file.'); window.location.href='discussions/view?id=$discussion_id';</script>";
             exit();
        }
    } else {
        echo "<script>alert('Error moving file.'); window.location.href='discussions/view?id=$discussion_id';</script>";
        exit();
    }
} else {
    echo "<script>alert('No file uploaded or file error.'); window.location.href='discussions/view?id=$discussion_id';</script>";
     exit();
}
?>