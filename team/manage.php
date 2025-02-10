<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';


$filter_role = isset($_GET['filter_role']) ? $_GET['filter_role'] : '';
$filter_department = isset($_GET['filter_department']) ? $_GET['filter_department'] : '';

// Build the base query with JOIN
$query = "SELECT users.*, team_roles.name as role_name, team_departments.name as department_name
          FROM users LEFT JOIN team_roles ON users.role_id = team_roles.id
          LEFT JOIN team_departments ON users.department_id = team_departments.id
          WHERE users.role != 'admin'";

$params = [];

// Add filters to the query
if (!empty($filter_role)) {
     $query .= " AND users.role_id = :role_id";
     $params[':role_id'] = $filter_role;

}
if (!empty($filter_department)) {
    $query .= " AND users.department_id = :department_id";
     $params[':department_id'] = $filter_department;

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

// Fetch all departments
$stmt = $conn->prepare("SELECT * FROM team_departments");
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Team Management</h1>

     <div class="flex justify-between items-center mb-8">
         <a href="<?php echo BASE_URL; ?>team/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide"><i class="fas fa-user-plus mr-2"></i>Add Team Member</a>
          <div class="flex flex-wrap gap-2">
           <form method="GET" action="" class="flex gap-2">
               <select name="filter_role" id="filter_role" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                   <option value="">Select Role</option>
                     <?php foreach ($roles as $role): ?>
                           <option value="<?php echo $role['id']; ?>" <?php if(isset($_GET['filter_role']) && $_GET['filter_role'] == $role['id']) echo "selected"; ?> ><?php echo htmlspecialchars($role['name']); ?></option>
                      <?php endforeach; ?>
               </select>
                <select name="filter_department" id="filter_department" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                   <option value="">Select Department</option>
                     <?php foreach ($departments as $department): ?>
                           <option value="<?php echo $department['id']; ?>" <?php if(isset($_GET['filter_department']) && $_GET['filter_department'] == $department['id']) echo "selected"; ?> ><?php echo htmlspecialchars($department['name']); ?></option>
                      <?php endforeach; ?>
               </select>
                <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md">Filter</button>
             </form>
            <a href="<?php echo BASE_URL; ?>team/manage" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 shadow-md">Clear Filter</a>
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
                                <td class="px-4 py-3"><?php echo htmlspecialchars($member['department_name'] ? $member['department_name'] : 'N/A'); ?></td>
                                 <td class="px-4 py-3 flex gap-2">
                                       <a href="<?php echo BASE_URL; ?>team/edit?id=<?php echo $member['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                         <a href="<?php echo BASE_URL; ?>team/delete?id=<?php echo $member['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</a>
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
