<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];


// Fetch user details
$stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $user['username'];
$email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (isset($_POST['update_profile'])) {
          $username = trim($_POST['username']);
            $email = trim($_POST['email']);

        if (empty($username) || empty($email)) {
            $error = "Name and email are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        }else {
             // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $error = "A user with this email already exists.";
            }else {
                $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email WHERE id = :user_id");
                 $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':user_id', $user_id);

                if ($stmt->execute()) {
                    $success = "Profile updated successfully!";
                      header("Location: " . BASE_URL . "profile/view?success=true"); // Redirect back to profile page
                        exit();
                } else {
                    $error = "Error updating profile.";
                }
           }
        }
    }  elseif (isset($_POST['update_password'])) {
         $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All password fields are required.";
        } else if ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } else {
              $stmt = $conn->prepare("SELECT password FROM users WHERE id = :user_id");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $current_password_hash = $stmt->fetch(PDO::FETCH_ASSOC)['password'];


            if (password_verify($old_password, $current_password_hash)) {
                  $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
                $stmt->bindParam(':new_password', $new_password_hash);
                 $stmt->bindParam(':user_id', $user_id);
                if ($stmt->execute()) {
                       $success = "Password updated successfully!";
                        header("Location: " . BASE_URL . "profile/view?success=true"); // Redirect back to profile page
                         exit();
                } else {
                     $error = "Error updating password.";
               }
            } else {
                $error = "Incorrect old password.";
            }
        }

    } elseif (isset($_POST['remove_profile_picture'])) {
        // Set profile_picture to NULL
        $stmt = $conn->prepare("UPDATE users SET profile_picture = NULL WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
           if ($stmt->execute()) {
                    $success = "Profile picture removed successfully!";
                       header("Location: " . BASE_URL . "profile/view?success=true"); // Redirect back to profile page
                        exit();
                } else {
                    $error = "Error removing profile picture.";
                      header("Location: " . BASE_URL . "profile/view"); // Redirect back to profile page
                        exit();
           }

    }

}

// Handle profile picture upload
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
                 header("Location: " . BASE_URL . "profile/view?success=true"); // Redirect back to profile page
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
 if(isset($_GET['success']) && $_GET['success'] == 'true'){
       $success = "Profile updated successfully!";
  }


?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">User Profile</h1>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-xl fade-in">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Profile Information</h2>
               <div class="mb-4 flex justify-center relative">
                     <?php if($user['profile_picture']): ?>
                          <img src="<?php echo BASE_URL . $user['profile_picture']; ?>" alt="Profile Picture" class="rounded-full w-32 h-32 object-cover">
                           <form method="post" action="" class="absolute top-0 right-0">
                           <?php echo csrfTokenInput(); ?>
                                 <button type="submit" name="remove_profile_picture" class="remove_item bg-red-500 text-white px-2 py-1 rounded-lg hover:bg-red-700 transition duration-300">
                                      <i class="fas fa-trash-alt"></i></button>
                                  </button>
                           </form>
                      <?php else: ?>
                         <div class="rounded-full w-32 h-32 bg-gray-200 flex items-center justify-center">
                             <i class="fas fa-user fa-3x text-gray-500"></i>
                         </div>
                      <?php endif; ?>
                </div>
             <form method="POST" action="" class="mb-6">
             <?php echo csrfTokenInput(); ?>
                    <div class="mb-4">
                       <label for="username" class="block text-gray-700">Name</label>
                        <input type="text" name="username" id="username" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($username); ?>" required>
                   </div>
                  <div class="mb-4">
                      <label for="email" class="block text-gray-700">Email</label>
                       <input type="email" name="email" id="email" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($email); ?>" required>
                   </div>
                    <button type="submit" name="update_profile" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Update Profile</button>
         </form>
                  <form method="POST" action="" enctype="multipart/form-data">
                  <?php echo csrfTokenInput(); ?>
                    <div class="mb-4">
                       <label for="profile_picture" class="block text-gray-700">Profile Picture</label>
                       <input type="file" name="profile_picture" id="profile_picture" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"
                        onchange="loadFile(event)">
                         <div id="preview-profile" class="hidden mt-2">
                                <img id="preview_image" src="#" alt="Preview" class="rounded-full w-32 h-32 object-cover"/>
                             </div>
                    </div>
                     <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Upload Picture</button>
                 </form>

             </div>
        <div class="bg-white p-6 rounded-lg shadow-md fade-in">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Change Password</h2>
            <form method="POST" action="" class="fade-in">
            <?php echo csrfTokenInput(); ?>
                <div class="mb-4">
                    <label for="old_password" class="block text-gray-700">Old Password</label>
                    <input type="password" name="old_password" id="old_password" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block text-gray-700">Confirm New Password</label>
                      <input type="password" name="confirm_password" id="confirm_password" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                 </div>
                  <button type="submit" name="update_password" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Change Password</button>
              </form>
        </div>
    </div>
    <script>
        var loadFile = function(event) {
          var previewImage = document.getElementById('preview_image');
          var previewContainer = document.getElementById('preview-profile');
            previewImage.src = URL.createObjectURL(event.target.files[0]);
            previewContainer.classList.remove('hidden');

        };
      </script>

