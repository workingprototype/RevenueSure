<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';


$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch customer details
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = :id");
$stmt->bindParam(':id', $customer_id);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: " . BASE_URL . "customers/manage");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_company_preferences'])) {
         $company = trim($_POST['company']);
          $preferences = trim($_POST['preferences']);
         $stmt = $conn->prepare("UPDATE customers SET company = :company, preferences = :preferences WHERE id = :id");
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':preferences', $preferences);
        $stmt->bindParam(':id', $customer_id);

        if($stmt->execute()){
           $success = "Customer details updated successfully!";
            header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
             exit();
        }else {
           $error =  "There was an error updating customer info.";
         }
     }elseif (isset($_POST['add_preference'])) {
        $preference = trim($_POST['preference']);

         if(!empty($preference)){
             $stmt = $conn->prepare("INSERT INTO customer_preferences (customer_id, preference) VALUES (:customer_id, :preference)");
            $stmt->bindParam(':customer_id', $customer_id);
             $stmt->bindParam(':preference', $preference);
             if ($stmt->execute()) {
                $success = "Preference added successfully!";
                    header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
                     exit();
                } else {
                      $error = "Error adding preference.";
                   }
          } else {
                $error = "Preference cannot be empty!";
         }

    } elseif (isset($_POST['add_interaction'])) {
           $interaction_type = trim($_POST['interaction_type']);
           $details = trim($_POST['details']);

          $stmt = $conn->prepare("INSERT INTO customer_interactions (customer_id, interaction_type, details) VALUES (:customer_id, :interaction_type, :details)");
            $stmt->bindParam(':customer_id', $customer_id);
             $stmt->bindParam(':interaction_type', $interaction_type);
             $stmt->bindParam(':details', $details);
             if ($stmt->execute()) {
                   $success = "Interaction added successfully!";
                    header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
                    exit();
                } else {
                      $error = "Error adding interaction.";
                  }
        }  elseif (isset($_POST['add_custom_field'])) {
         $field_name = trim($_POST['field_name']);
            $field_value = trim($_POST['field_value']);
         if(!empty($field_name) && !empty($field_value)){
             $stmt = $conn->prepare("INSERT INTO customer_custom_fields (customer_id, field_name, field_value) VALUES (:customer_id, :field_name, :field_value)");
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':field_name', $field_name);
             $stmt->bindParam(':field_value', $field_value);
             if ($stmt->execute()) {
                $success = "Custom field added successfully!";
                    header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
                     exit();
             } else {
                $error = "Error adding custom field.";
             }
        } else {
            $error = "Custom field cannot be empty.";
         }
    }  elseif(isset($_POST['add_tag'])){
         $tag = trim($_POST['tag']);
         $color = trim($_POST['color']);
         if(!empty($tag)){
                $stmt = $conn->prepare("INSERT INTO customer_tags (customer_id, tag, color) VALUES (:customer_id, :tag, :color)");
                $stmt->bindParam(':customer_id', $customer_id);
                $stmt->bindParam(':tag', $tag);
                $stmt->bindParam(':color', $color);
                 if ($stmt->execute()) {
                $success = "Tag added successfully!";
                 header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
                 exit();
            } else {
                $error = "Error adding tag.";
            }
         }else {
                $error = "Tag cannot be empty.";
         }
    }  elseif (isset($_POST['update_demographics'])) {
          $address = trim($_POST['address']);
          $social_media_profiles = trim($_POST['social_media_profiles']);
        $age = trim($_POST['age']);
        $gender = trim($_POST['gender']);
        $location = trim($_POST['location']);
        $job_title = trim($_POST['job_title']);
        $industry = trim($_POST['industry']);

         $stmt = $conn->prepare("UPDATE customers SET address = :address, social_media_profiles = :social_media_profiles, age = :age, gender = :gender, location = :location, job_title = :job_title, industry = :industry WHERE id = :id");
                $stmt->bindParam(':address', $address);
             $stmt->bindParam(':social_media_profiles', $social_media_profiles);
             $stmt->bindParam(':age', $age);
            $stmt->bindParam(':gender', $gender);
              $stmt->bindParam(':location', $location);
                $stmt->bindParam(':job_title', $job_title);
                 $stmt->bindParam(':industry', $industry);
                $stmt->bindParam(':id', $customer_id);

        if($stmt->execute()){
          $success = "Demographic data updated successfully!";
              header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
                 exit();
        } else {
           $error =  "There was an error updating customer info.";
         }
    }  elseif (isset($_POST['remove_profile_picture'])) {
        // Set profile_picture to NULL
        $stmt = $conn->prepare("UPDATE customers SET profile_picture = NULL WHERE id = :id");
        $stmt->bindParam(':id', $customer_id);
           if ($stmt->execute()) {
                    $success = "Profile picture removed successfully!";
                       header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true"); // Redirect back to profile page
                        exit();
                } else {
                    $error = "Error removing profile picture.";
                      header("Location: " . BASE_URL . "customers/view?id=$customer_id"); // Redirect back to profile page
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
        $stmt = $conn->prepare("UPDATE customers SET profile_picture = :profile_picture WHERE id = :id");
        $stmt->bindParam(':profile_picture', $file_path);
        $stmt->bindParam(':id', $customer_id);
          if ($stmt->execute()) {
                $success = "Profile picture uploaded successfully!";
                header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true"); // Redirect back to profile page
                exit();
            } else {
                $error = "Error updating profile.";
                 header("Location: " . BASE_URL . "customers/view?id=$customer_id");
                  exit();
            }
    } else {
        $error = "Error moving profile picture.";
         header("Location: " . BASE_URL . "customers/view?id=$customer_id");
         exit();
    }
}
 if(isset($_GET['success']) && $_GET['success'] == 'true'){
       $success = "Customer details updated successfully!";
  }

// Fetch customer preferences
$stmt = $conn->prepare("SELECT * FROM customer_preferences WHERE customer_id = :customer_id ORDER BY created_at ASC");
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$preferences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer interactions
$stmt = $conn->prepare("SELECT * FROM customer_interactions WHERE customer_id = :customer_id ORDER BY interaction_at DESC");
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch custom fields
$stmt = $conn->prepare("SELECT * FROM customer_custom_fields WHERE customer_id = :customer_id ORDER BY created_at ASC");
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$custom_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch customer tags
$stmt = $conn->prepare("SELECT * FROM customer_tags WHERE customer_id = :customer_id ORDER BY created_at ASC");
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container mx-auto p-6 fade-in">
<h1 class="text-4xl font-bold text-gray-900 mb-6">Customer Details: <?php echo htmlspecialchars($customer['name']); ?></h1>
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

    <div class="bg-white p-6 rounded-2xl shadow-xl mb-8 border-l-4 border-blue-500 transition hover:shadow-2xl">
         <h2 class="text-2xl font-semibold text-gray-900 mb-4 relative">
           <i class="fas fa-id-card absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Contact Information
        </h2>
        <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Name:</span> <?php echo htmlspecialchars($customer['name']); ?></p>
        <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Email:</span> <?php echo htmlspecialchars($customer['email']); ?></p>
        <p class="text-gray-700 mb-2"> <span class="font-semibold text-gray-800">Phone:</span> <?php echo htmlspecialchars($customer['phone']); ?></p>
         <div class="mb-4 flex justify-center relative mt-4">
                     <?php if($customer['profile_picture']): ?>
                          <img src="<?php echo BASE_URL . $customer['profile_picture']; ?>" alt="Profile Picture" class="rounded-full w-32 h-32 object-cover">
                           <form method="post" action="" class="absolute top-0 right-0">
                           <?php echo csrfTokenInput(); ?>
                                 <button type="submit" name="remove_profile_picture" class="remove_item bg-red-500 text-white px-2 py-1 rounded-lg hover:bg-red-700 transition duration-300">
                                      <i class="fas fa-trash-alt"></i>
                                  </button>
                           </form>
                      <?php else: ?>
                         <div class="rounded-full w-32 h-32 bg-gray-200 flex items-center justify-center">
                             <i class="fas fa-user fa-3x text-gray-500"></i>
                         </div>
                      <?php endif; ?>
                </div>
         <form method="POST" action="" enctype="multipart/form-data" class="mt-4">
         <?php echo csrfTokenInput(); ?>
                    <div class="mb-4">
                       <label for="profile_picture" class="block text-gray-700">Profile Picture</label>
                       <input type="file" name="profile_picture" id="profile_picture" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Upload Picture</button>
         </form>
    </div>
     <form method="post" action="">
     <?php echo csrfTokenInput(); ?>
         <div class="bg-white p-6 rounded-2xl shadow-md mb-8 border-l-4 border-green-500 transition hover:shadow-2xl">
              <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
                 <i class="fas fa-building absolute left-[-20px] top-[-5px] text-green-500 text-sm"></i> Company Profile
            </h2>
                 <div class="mb-4">
                   <label for="company" class="block text-gray-700">Company</label>
                       <input type="text" name="company" id="company" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($customer['company'] ?? ''); ?>">
                </div>
        </div>
           <div class="bg-white p-6 rounded-2xl shadow-md mb-8 border-l-4 border-yellow-500 transition hover:shadow-2xl">
            <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
               <i class="fas fa-thumbs-up absolute left-[-20px] top-[-5px] text-yellow-500 text-sm"></i> Preferences
            </h2>
             <ul class="mb-4">
                 <?php if($preferences): ?>
                     <?php foreach ($preferences as $preference): ?>
                            <li class="flex justify-between items-center mb-2">
                                 <?php echo htmlspecialchars($preference['preference']); ?>
                                  <div class="flex gap-2">
                                    <a href="<?php echo BASE_URL; ?>customers/edit_preference?id=<?php echo $preference['id']; ?>&customer_id=<?php echo $customer_id; ?>" class="text-blue-600 hover:underline">Edit</a>
                                  <a href="<?php echo BASE_URL; ?>customers/delete_preference?id=<?php echo $preference['id']; ?>&customer_id=<?php echo $customer_id; ?>" class="text-red-600 hover:underline">Delete</a>
                                   </div>
                            </li>
                        <?php endforeach; ?>
                   <?php else: ?>
                     <p>No Preferences added.</p>
                  <?php endif; ?>
               </ul>
             <div class="mb-4">
                <input type="text" name="preference" id="preference" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Add Preference">
                </div>
                <button type="submit" name="add_preference" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Preference</button>
        </div>
         <div class="bg-white p-6 rounded-2xl shadow-md mb-8 border-l-4 border-purple-500 transition hover:shadow-2xl">
             <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
              <i class="fas fa-user-friends absolute left-[-20px] top-[-5px] text-purple-500 text-sm"></i> Demographic Information
            </h2>
               <div class="mb-4">
                    <label for="address" class="block text-gray-700">Address</label>
                        <input type="text" name="address" id="address" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"  value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>">
                   </div>
               <div class="mb-4">
                   <label for="social_media_profiles" class="block text-gray-700">Social Media Profiles</label>
                       <textarea name="social_media_profiles" id="social_media_profiles" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($customer['social_media_profiles'] ?? ''); ?></textarea>
                 </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                      <label for="age" class="block text-gray-700">Age</label>
                       <input type="number" name="age" id="age" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"  value="<?php echo htmlspecialchars($customer['age'] ?? ''); ?>">
                    </div>
                     <div class="mb-4">
                          <label for="gender" class="block text-gray-700">Gender</label>
                           <select name="gender" id="gender" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                              <option value="" <?php if(empty($customer['gender'])) echo 'selected'; ?>>Select</option>
                               <option value="Male" <?php if($customer['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if($customer['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if($customer['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                     </div>
                     <div class="mb-4">
                       <label for="location" class="block text-gray-700">Location</label>
                      <input type="text" name="location" id="location" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"  value="<?php echo htmlspecialchars($customer['location'] ?? ''); ?>">
                 </div>
                <div class="mb-4">
                   <label for="job_title" class="block text-gray-700">Job Title</label>
                   <input type="text" name="job_title" id="job_title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"  value="<?php echo htmlspecialchars($customer['job_title'] ?? ''); ?>">
             </div>
             <div class="mb-4">
                  <label for="industry" class="block text-gray-700">Industry</label>
                  <input type="text" name="industry" id="industry" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($customer['industry'] ?? ''); ?>">
                </div>
        </div>
           <button type="submit" name="update_demographics" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4">Update Demographics</button>
        </div>
       
        <div class="bg-white p-6 rounded-2xl shadow-md mb-8 border-l-4 border-teal-500 transition hover:shadow-2xl">
         <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
               <i class="fas fa-history absolute left-[-20px] top-[-5px] text-teal-500 text-sm"></i> Past Interactions
              </h2>
             <ul>
                   <?php if($interactions): ?>
                         <?php foreach ($interactions as $interaction): ?>
                            <li class="mb-4">
                                 <p class="text-gray-600 text-sm">
                                    <strong><?php echo htmlspecialchars($interaction['interaction_type']); ?> on:</strong> <?php echo date('Y-m-d H:i', strtotime($interaction['interaction_at'])); ?>
                                </p>
                                 <p class="text-gray-800"><?php echo htmlspecialchars($interaction['details']); ?></p>
                             </li>
                         <?php endforeach; ?>
                    <?php else: ?>
                      <p class="text-gray-600">No interactions found.</p>
                  <?php endif; ?>
             </ul>
              <form method="POST" action="">
              <?php echo csrfTokenInput(); ?>
                <div class="mb-4">
                    <label for="interaction_type" class="block text-gray-700">Interaction Type</label>
                        <select name="interaction_type" id="interaction_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                           <option value="Call">Call</option>
                            <option value="Email">Email</option>
                           <option value="Meeting">Meeting</option>
                           <option value="Other">Other</option>
                      </select>
                 </div>
                <div class="mb-4">
                     <label for="details" class="block text-gray-700">Details</label>
                        <textarea name="details" id="details" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                </div>
                 <button type="submit" name="add_interaction" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Interaction</button>
           </form>
    </div>
          <div class="bg-white p-6 rounded-lg shadow-md mb-8 border-l-4 border-indigo-500 transition hover:shadow-2xl">
         <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
            <i class="fas fa-tags absolute left-[-20px] top-[-5px] text-indigo-500 text-sm"></i> Tags
         </h2>
             <div class="flex gap-2 mb-4">
                    <?php if($tags): ?>
                       <?php foreach($tags as $tag): ?>
                          <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php if(!empty($tag['color'])) echo 'bg-' . $tag['color'] . '-100 text-' . $tag['color'] . '-800' ?>"><?php echo $tag['tag'] ?></span>
                        <?php endforeach; ?>
                     <?php else: ?>
                     <p class="text-gray-600">No tags added.</p>
                    <?php endif; ?>
                </div>
                  <form method="POST" action="" class="flex gap-2 items-end">
                  <?php echo csrfTokenInput(); ?>
                    <div class="mb-4 flex-1">
                       <label for="tag" class="block text-gray-700">Tag Name</label>
                          <input type="text" name="tag" id="tag" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                 </div>
                    <div class="mb-4">
                       <label for="color" class="block text-gray-700">Tag Color</label>
                             <select name="color" id="color" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                                   <option value="gray">Gray</option>
                                   <option value="red">Red</option>
                                   <option value="green">Green</option>
                                   <option value="blue">Blue</option>
                                   <option value="yellow">Yellow</option>
                                   <option value="purple">Purple</option>
                                </select>
                    </div>
                        <button type="submit" name="add_tag" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Tag</button>
            </form>
        </div>
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>customers/manage"  class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Back To Customers</a>
   </div>
</div>