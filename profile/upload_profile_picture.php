<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $file_name = basename($_FILES['profile_picture']['name']);
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_path = "public/uploads/profile/" . uniqid() . "_" . $file_name;

     if (!is_dir('public/uploads/profile')) {
                mkdir('public/uploads/profile', 0777, true);
            }


    if (move_uploaded_file($file_tmp, $file_path)) {
        $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id");
        $stmt->bindParam(':profile_picture', $file_path);
        $stmt->bindParam(':user_id', $user_id);
          if ($stmt->execute()) {
                $success = "Profile picture uploaded successfully!";
                 header("Location: " . BASE_URL . "profile/view"); // Redirect back to profile page
                exit();
            } else {
                $error = "Error updating profile.";
                 header("Location: " . BASE_URL . "profile/view"); // Redirect back to profile page
                exit();
            }
    } else {
         $error = "Error moving profile picture.";
          header("Location: " . BASE_URL . "profile/view"); // Redirect back to profile page
                exit();
    }
}

?>