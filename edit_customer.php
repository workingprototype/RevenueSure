<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch customer details
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = :id");
$stmt->bindParam(':id', $customer_id);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: manage_customers.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validate inputs
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email already exists (excluding current customer)
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = :email AND id != :id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $customer_id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "A customer with this email already exists.";
        } else {
            // Update customer
            $stmt = $conn->prepare("UPDATE customers SET name = :name, email = :email, phone = :phone WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':id', $customer_id);

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
    }
}

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Customer</h1>

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

<!-- Edit Customer Form -->
<form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
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
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Customer</button>
</form>

<?php
// Include footer
require 'footer.php';
?>