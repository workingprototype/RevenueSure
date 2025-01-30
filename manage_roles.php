<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Fetch all roles
$stmt = $conn->prepare("SELECT * FROM team_roles ORDER BY created_at DESC");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Team Roles</h1>

    <!-- Add Role Button -->
    <div class="flex justify-between items-center mb-8">
      <a href="add_role.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
           <i class="fas fa-plus-circle mr-2"></i> Add Role
         </a>
    </div>

    <!-- Roles Table -->
    <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
         <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($roles): ?>
                    <?php foreach ($roles as $role): ?>
                        <tr class="border-b border-gray-300">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($role['name']); ?></td>
                             <td class="px-4 py-3 flex gap-2">
                                    <a href="edit_role.php?id=<?php echo $role['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete_role.php?id=<?php echo $role['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-600">No roles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>