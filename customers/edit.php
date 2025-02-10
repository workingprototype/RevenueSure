<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';




$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Fetch customer details
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        header("Location: " . BASE_URL . "manage");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Database error. Please try again later.";
    $customer = null;  // Ensure customer is null to prevent further errors
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(400);
            echo "CSRF token invalid";
            exit;
        }

    $name = sanitizeString($_POST['name']);
    $email = sanitizeString($_POST['email']);
    $phone = sanitizeString($_POST['phone']);
    $company = sanitizeString($_POST['company']);

    // Validate inputs
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if email already exists (excluding current customer)
            $stmt = $conn->prepare("SELECT id FROM customers WHERE email = :email AND id != :id");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $error = "A customer with this email already exists.";
            } else {
                // Update customer
                $stmt = $conn->prepare("UPDATE customers SET name = :name, email = :email, phone = :phone, company = :company WHERE id = :id");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
                $stmt->bindParam(':company', $company, PDO::PARAM_STR);
                $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Update leads
                    $stmt = $conn->prepare("UPDATE leads SET name = :name, email = :email, phone = :phone WHERE customer_id = :customer_id");
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':customer_id', $customer_id);
                    $stmt->execute();
                    $success = "Customer updated successfully!";
                } else {
                    $error = "Error updating customer.";
                }
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "Database error. Please try again later.";
        }
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Customer</h1>

<!-- Display error or success message -->
<?php if ($error): ?>
    <?php echo displayAlert($error, 'error'); ?>
<?php endif; ?>

<?php if ($success): ?>
    <?php echo displayAlert($success, 'success'); ?>
<?php endif; ?>

<!-- Edit Customer Form -->
<?php if ($customer): ?>
    <form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
    <?php echo csrfTokenInput(); ?>
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($customer['phone']); ?>">
        </div>
        <div class="mb-4">
            <label for="company" class="block text-gray-700">Company</label>
            <input type="text" name="company" id="company" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($customer['company'] ?? ''); ?>">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Customer</button>
    </form>
<?php else: ?>
    <p>Customer not found.</p>
<?php endif; ?>