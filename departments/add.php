<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    // Validate input
    if (empty($name)) {
        $error = "Department name is required.";
    } else {
        // Check if the department already exists
        $stmt = $conn->prepare("SELECT id FROM team_departments WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "A department with this name already exists.";
        } else {
            // Insert the department
            $stmt = $conn->prepare("INSERT INTO team_departments (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            if ($stmt->execute()) {
                $success = "Department added successfully!";
                  header("Location: " . BASE_URL . "departments/manage?success=true");
                    exit();
            } else {
                $error = "Error adding department.";
            }
        }
    }
}

?>

<div class="container mx-auto p-6 fade-in">
   <h1 class="text-4xl font-bold text-gray-900 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Add Team Department</h1>
     <!-- Display error or success message -->
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

      <!-- Add Department Form -->
    <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
           <div class="mb-4">
                <label for="name" class="block text-gray-700">Department Name</label>
                <input type="text" name="name" id="name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
              <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">Add Department</button>
        </form>
    </div>
</div>
