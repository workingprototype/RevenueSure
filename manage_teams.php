<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$filter_role = isset($_GET['filter_role']) ? $_GET['filter_role'] : '';
$filter_department = isset($_GET['filter_department']) ? $_GET['filter_department'] : '';

// Build the base query with JOIN
$query = "SELECT users.*, team_roles.name as role_name
          FROM users LEFT JOIN team_roles ON users.role_id = team_roles.id WHERE users.role != 'admin'";

$params = [];

// Add filters to the query
if (!empty($filter_role)) {
     $query .= " AND users.role_id = :role_id";
     $params[':role_id'] = $filter_role;

}
if (!empty($filter_department)) {
     $query .= " AND users.department LIKE :department";
     $params[':department'] = "%$filter_department%";

}
$query .= " ORDER BY users.created_at DESC";
// Fetch all users excluding admins
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all roles
$stmt = $conn->prepare("SELECT * FROM team_roles");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Team Management</h1>

     <div class="flex justify-between items-center mb-8">
         <a href="add_team_member.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide"><i class="fas fa-user-plus mr-2"></i>Add Team Member</a>
          <div class="flex flex-wrap gap-2">
           <form method="GET" action="" class="flex gap-2">
               <select name="filter_role" id="filter_role" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                   <option value="">Select Role</option>
                     <?php foreach ($roles as $role): ?>
                           <option value="<?php echo $role['id']; ?>" <?php if(isset($_GET['filter_role']) && $_GET['filter_role'] == $role['id']) echo "selected"; ?> ><?php echo htmlspecialchars($role['name']); ?></option>
                      <?php endforeach; ?>
               </select>
                <input type="text" name="filter_department" id="filter_department" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Filter by Department" value="<?php echo isset($_GET['filter_department']) ? $_GET['filter_department'] : '' ?>">
                <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md">Filter</button>
             </form>
            <a href="manage_teams.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 shadow-md">Clear Filter</a>
       </div>
      </div>
    <!-- Members Table -->
    <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Department</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($members): ?>
                        <?php foreach ($members as $member): ?>
                            <tr class="border-b border-gray-300">
                                <td class="px-4 py-3"><?php echo htmlspecialchars($member['username']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($member['email']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($member['role_name'] ? $member['role_name'] : 'N/A'); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($member['department']); ?></td>
                                 <td class="px-4 py-3 flex gap-2">
                                       <a href="edit_team_member.php?id=<?php echo $member['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                         <a href="delete_team_member.php?id=<?php echo $member['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</a>
                                   </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-600">No team members found.</td>
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