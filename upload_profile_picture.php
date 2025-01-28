<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $file_name = basename($_FILES['profile_picture']['name']);
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_path = "uploads/profile/" . uniqid() . "_" . $file_name;

     if (!is_dir('uploads/profile')) {
                mkdir('uploads/profile', 0777, true);
            }


    if (move_uploaded_file($file_tmp, $file_path)) {
        $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id");
        $stmt->bindParam(':profile_picture', $file_path);
        $stmt->bindParam(':user_id', $user_id);
          if ($stmt->execute()) {
                $success = "Profile picture uploaded successfully!";
                 header("Location: profile.php"); // Redirect back to profile page
                exit();
            } else {
                $error = "Error updating profile.";
                 header("Location: profile.php"); // Redirect back to profile page
                exit();
            }
    } else {
         $error = "Error moving profile picture.";
          header("Location: profile.php"); // Redirect back to profile page
                exit();
    }
}

?>