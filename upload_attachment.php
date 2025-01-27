<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lead_id = $_POST['lead_id'];
    $file_type = $_POST['file_type'];

    // Validate file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = basename($_FILES['file']['name']);
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_path = "uploads/" . uniqid() . "_" . $file_name;

        // Create uploads directory if it doesn't exist
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert file details into the database
            $stmt = $conn->prepare("INSERT INTO attachments (lead_id, file_name, file_path, file_type) VALUES (:lead_id, :file_name, :file_path, :file_type)");
            $stmt->bindParam(':lead_id', $lead_id);
            $stmt->bindParam(':file_name', $file_name);
            $stmt->bindParam(':file_path', $file_path);
            $stmt->bindParam(':file_type', $file_type);

            if ($stmt->execute()) {
                echo "<script>alert('File uploaded successfully!'); window.location.href='view_lead.php?id=$lead_id';</script>";
            } else {
                echo "<script>alert('Error uploading file.');</script>";
            }
        } else {
            echo "<script>alert('Error moving file.');</script>";
        }
    } else {
        echo "<script>alert('No file uploaded or file error.');</script>";
    }
}
?>