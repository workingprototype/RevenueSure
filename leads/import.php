<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

// Handle CSV Import
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($file, 'r')) !== FALSE) {
        fgetcsv($handle); // Skip header row
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $name = $data[0];
            $email = $data[1];
            $phone = $data[2];
            $category_id = $data[3];

            $stmt = $conn->prepare("INSERT INTO leads (name, email, phone, category_id) VALUES (:name, :email, :phone, :category_id)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
        }
        fclose($handle);
        echo "<script>alert('Leads imported successfully!');</script>";
    } else {
        echo "<script>alert('Error importing CSV file.');</script>";
    }
}

// Fetch all categories for the add lead form
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="container mx-auto p-6 fade-in">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Import Leads</h1>

        <!-- Import Leads Section -->
        <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
                <i class="fas fa-file-import absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Import Leads
            </h2>
            <form method="POST" action="" enctype="multipart/form-data">
            <?php echo csrfTokenInput(); ?>
                <input type="file" name="csv_file" accept=".csv" class="mb-4 w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Import CSV</button>
            </form>
        </div>
</div>
