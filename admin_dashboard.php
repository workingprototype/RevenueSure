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

// Handle Mass Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_selected'])) {
    $selected_leads = $_POST['selected_leads'];
    if (!empty($selected_leads)) {
        $placeholders = implode(',', array_fill(0, count($selected_leads), '?'));
        $stmt = $conn->prepare("DELETE FROM leads WHERE id IN ($placeholders)");
        $stmt->execute($selected_leads);
        echo "<script>alert('Selected leads deleted successfully!');</script>";
    } else {
        echo "<script>alert('No leads selected for deletion.');</script>";
    }
}

// Handle Sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Fetch all leads with sorting
$stmt = $conn->prepare("SELECT * FROM leads ORDER BY $sort_by $order");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for the add lead form
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

        <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

        <!-- Import Leads Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Import Leads</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="file" name="csv_file" accept=".csv" class="mb-4" required>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Import CSV</button>
            </form>
        </div>

        <!-- Add Lead Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Add Lead</h2>
            <form method="POST" action="add_lead.php">
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
                    <input type="text" name="phone" id="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Lead</button>
            </form>
        </div>

        <!-- Leads Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Leads</h2>
            <form method="POST" action="">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <button type="submit" name="delete_selected" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Delete Selected</button>
                    </div>
                    <div>
                        <label for="sort_by" class="text-gray-700">Sort By:</label>
                        <select name="sort_by" id="sort_by" onchange="window.location.href = '?sort_by=' + this.value + '&order=<?php echo $order; ?>'" class="px-4 py-2 border rounded-lg">
                            <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name</option>
                            <option value="email" <?php echo $sort_by === 'email' ? 'selected' : ''; ?>>Email</option>
                            <option value="phone" <?php echo $sort_by === 'phone' ? 'selected' : ''; ?>>Phone</option>
                            <option value="category_id" <?php echo $sort_by === 'category_id' ? 'selected' : ''; ?>>Category</option>
                        </select>
                        <label for="order" class="text-gray-700 ml-4">Order:</label>
                        <select name="order" id="order" onchange="window.location.href = '?sort_by=<?php echo $sort_by; ?>&order=' + this.value" class="px-4 py-2 border rounded-lg">
                            <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                            <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                        </select>
                    </div>
                </div>
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="px-4 py-2"><input type="checkbox" id="select_all"></th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Phone</th>
                            <th class="px-4 py-2">Category</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><input type="checkbox" name="selected_leads[]" value="<?php echo $lead['id']; ?>"></td>
                                <td class="px-4 py-2"><?php echo $lead['name']; ?></td>
                                <td class="px-4 py-2"><?php echo $lead['email']; ?></td>
                                <td class="px-4 py-2"><?php echo $lead['phone']; ?></td>
                                <td class="px-4 py-2"><?php echo $lead['category_id']; ?></td>
                                <td class="px-4 py-2">
                                    <a href="edit_lead.php?id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                    <a href="delete_lead.php?id=<?php echo $lead['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <script>
        // Select All Checkbox
        document.getElementById('select_all').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_leads[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>

<?php
// Include footer
require 'footer.php';
?>