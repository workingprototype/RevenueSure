<?php
require_once ROOT_PATH . 'helper/core.php'; //Load required data & set Session
redirectIfUnauthorized(true); // Requires user to be logged in *and* an admin


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(400);
            echo "CSRF token invalid";
            exit;
        }

    $name = sanitizeString($_POST['name']);
    $email = sanitizeString($_POST['email']);
    $phone = sanitizeString($_POST['phone']);

    // Validate inputs
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!validateEmail($email)) {
        $error = "Invalid email format.";
    }  elseif (!validateAlphanumeric($name)) {
         $error = "Invalid characters in name";
    } elseif (!validatePhone($phone)) {
          $error = "Invalid phone number.";
    } else {
        try {
            // Check if email already exists using prepared statement
            $stmt = $conn->prepare("SELECT id FROM customers WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $error = "A customer with this email already exists.";
            } else {
                // Insert customer using prepared statement
                $stmt = $conn->prepare("INSERT INTO customers (name, email, phone) VALUES (:name, :email, :phone)");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $success = "Customer added successfully!";
                } else {
                    $error = "Error adding customer.";
                }
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Log the error
            $error = "Database error. Please try again later.";
        }
    }
}
?>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add Customer</h1>

    <!-- Display error or success message -->
    <?php if ($error): ?>
        <?php echo displayAlert($error, 'error'); ?>
    <?php endif; ?>

    <?php if ($success): ?>
        <?php echo displayAlert($success, 'success'); ?>
    <?php endif; ?>

    <!-- Add Customer Form -->
    <form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
    <?php echo csrfTokenInput(); ?>
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Customer</button>
    </form>