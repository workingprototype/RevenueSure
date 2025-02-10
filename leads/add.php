<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $category_id = $_POST['category_id'];
    $assigned_to = isset($_POST['assigned_to']) && !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null; // Get the assigned_to value. Default to NULL if not set.


    // Validate inputs
    if (empty($name) || empty($phone) || empty($email) || empty($category_id)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT id FROM leads WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "A lead with this email already exists.";
        } else {
             // Insert the lead
            $stmt = $conn->prepare("INSERT INTO leads (name, phone, email, category_id, assigned_to) VALUES (:name, :phone, :email, :category_id, :assigned_to)");
             $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
             $stmt->bindParam(':email', $email);
            $stmt->bindParam(':category_id', $category_id);
             $stmt->bindParam(':assigned_to', $assigned_to);

            if ($stmt->execute()) {
                $success = "Lead added successfully!";
            } else {
                $error = "Error adding lead.";
            }
        }
    }
}

// Fetch categories for the dropdown
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Add Lead</h1>

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

    <!-- Add Lead Form -->
    <div class="bg-white p-6 rounded-2xl shadow-xl">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name</label>
                <input type="text" name="name" id="name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-gray-700">Phone</label>
                <input type="text" name="phone" id="phone" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
              <div class="mb-4">
                <label for="category_id" class="block text-gray-700">Category</label>
                <select name="category_id" id="category_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="mb-4">
                <label for="assigned_to" class="block text-gray-700">Assign To</label>
               <select name="assigned_to" id="assigned_to" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                    <option value="">Unassigned</option>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY name ASC");
                    $stmt->execute();
                    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Add Lead</button>
        </form>
     </div>
</div>