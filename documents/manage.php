<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// --- Search and Sort Parameters ---
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'created_at'; // Default sort
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC'; // Default order

// --- Pagination ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10; // Number of documents per page
$offset = ($page - 1) * $per_page;

// --- Database Query ---
$sql = "SELECT
            d.id,
            d.title,
            d.created_at,
            u.username AS creator_name,
            GROUP_CONCAT(DISTINCT uc.username ORDER BY uc.username SEPARATOR ', ') AS collaborators
        FROM
            documents d
        INNER JOIN
            users u ON d.created_by = u.id
        LEFT JOIN
            document_collaborators dc ON d.id = dc.document_id
        LEFT JOIN
            users uc ON dc.user_id = uc.id
        WHERE d.title LIKE :search_term"; // Add search condition
$sql .= " GROUP BY d.id";
$sql .= " ORDER BY " . $sort_by . " " . $sort_order;
$sql .= " LIMIT :offset, :per_page";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':search_term', '%' . $search_term . '%', PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
$stmt->execute();
$documents = $stmt->fetchAll();

// --- Count Total Documents (for pagination) ---
$sql_count = "SELECT COUNT(*) FROM documents WHERE title LIKE :search_term";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bindValue(':search_term', '%' . $search_term . '%', PDO::PARAM_STR);
$stmt_count->execute();
$total_documents = $stmt_count->fetchColumn();
$total_pages = ceil($total_documents / $per_page);

// --- Available Sort Options ---
$sort_options = [
    'title' => 'Title',
    'created_at' => 'Created At',
    'creator_name' => 'Creator'
];

// --- Helper Functions ---
function sortURL($sort_field, $current_sort, $current_order) {
    $order = ($current_sort == $sort_field && $current_order == 'ASC') ? 'DESC' : 'ASC';
    return $_SERVER['PHP_SELF'] . '?search=' . urlencode($_GET['search'] ?? '') . '&sort=' . $sort_field . '&order=' . $order . '&page=1';
}

?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Documents</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- --- Search Bar --- -->
    <div class="mb-4">
        <form method="GET" action="">
            <div class="flex">
                <input type="text" name="search" placeholder="Search documents..." class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700 transition duration-300">Search</button>
            </div>
        </form>
    </div>

    <!-- --- Create Document Button --- -->
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>documents/add" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Create New Document</a>
    </div>

    <!-- --- Document List --- -->
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="<?php echo sortURL('title', $sort_by, $sort_order); ?>">Title</a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="<?php echo sortURL('creator_name', $sort_by, $sort_order); ?>">Creator</a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Collaborators
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="<?php echo sortURL('created_at', $sort_by, $sort_order); ?>">Created At</a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($documents): ?>
                    <?php foreach ($documents as $document): ?>
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($document['title']); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($document['creator_name']); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($document['collaborators'] ?: 'None'); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($document['created_at']); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="<?php echo BASE_URL; ?>documents/view?id=<?php echo $document['id']; ?>" class="text-blue-600 hover:text-blue-800">View</a> |
                                <a href="<?php echo BASE_URL; ?>documents/edit?id=<?php echo $document['id']; ?>" class="text-green-600 hover:text-green-800">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm">No documents found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- --- Pagination --- -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 flex justify-center">
            <?php if ($page > 1): ?>
                <a href="<?php echo $_SERVER['PHP_SELF'] . '?search=' . urlencode($search_term) . '&sort=' . $sort_by . '&order=' . $sort_order . '&page=' . ($page - 1); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="<?php echo $_SERVER['PHP_SELF'] . '?search=' . urlencode($search_term) . '&sort=' . $sort_by . '&order=' . $sort_order . '&page=' . $i; ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 <?php echo ($i == $page) ? 'bg-blue-500 text-white' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="<?php echo $_SERVER['PHP_SELF'] . '?search=' . urlencode($search_term) . '&sort=' . $sort_by . '&order=' . $sort_order . '&page=' . ($page + 1); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-r">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>