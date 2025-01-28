<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

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

// Include header
require 'header.php';
?>

        <h1 class="text-3xl font-bold text-gray-800 mb-6">Import Leads</h1>

        <!-- Import Leads Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Import Leads</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="file" name="csv_file" accept=".csv" class="mb-4" required>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Import CSV</button>
            </form>
        </div>
<?php
// Include footer
require 'footer.php';
?>