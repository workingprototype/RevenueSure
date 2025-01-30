<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = $_POST['ticket_id'];

     if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file_name = basename($_FILES['file']['name']);
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_path = "uploads/" . uniqid() . "_" . $file_name;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $file_path)) {
                $stmt = $conn->prepare("INSERT INTO support_ticket_attachments (ticket_id, file_name, file_path) VALUES (:ticket_id, :file_name, :file_path)");
                $stmt->bindParam(':ticket_id', $ticket_id);
                $stmt->bindParam(':file_name', $file_name);
                $stmt->bindParam(':file_path', $file_path);


                if ($stmt->execute()) {
                    echo "<script>alert('File uploaded successfully!'); window.location.href='view_ticket.php?id=$ticket_id';</script>";
                     exit();
                } else {
                    echo "<script>alert('Error uploading file.'); window.location.href='view_ticket.php?id=$ticket_id';</script>";
                       exit();
                }
            } else {
                echo "<script>alert('Error moving file.'); window.location.href='view_ticket.php?id=$ticket_id';</script>";
                 exit();
            }
        } else {
            echo "<script>alert('No file uploaded or file error.'); window.location.href='view_ticket.php?id=$ticket_id';</script>";
             exit();
        }
}
?>