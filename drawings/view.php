<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$drawing_board_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch drawing board details
$stmt = $conn->prepare("SELECT drawing_boards.*, users.username as creator_name FROM drawing_boards LEFT JOIN users ON drawing_boards.created_by = users.id WHERE drawing_boards.id = :id");
$stmt->bindParam(':id', $drawing_board_id);
$stmt->execute();
$drawing_board = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$drawing_board) {
header("Location: " . BASE_URL . "drawings/manage");
exit();
}
?>


<!-- React Dependencies -->
<script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

<!-- Excalidraw -->
<script src="https://unpkg.com/@excalidraw/excalidraw/dist/excalidraw.production.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/@excalidraw/excalidraw/dist/excalidraw.min.css" />
</head>
<body>
<div class="container mx-auto p-6 fade-in">
<h1 class="text-3xl font-bold text-gray-800 mb-6">Drawing Board: <?php echo htmlspecialchars($drawing_board['title']); ?></h1>

<div class="bg-white p-6rounded-lg shadow-md">
<p class="text-gray-600mb-4">
<strong>Created By:</strong> <?php echo htmlspecialchars($drawing_board['creator_name']); ?>
on <?php echo htmlspecialchars($drawing_board['created_at']); ?>
</p>
<div class="border border-gray-200 rounded-lg">
<div id="excalidraw-container" style="height: 500px; width: 100%;"></div>
</div>
</div>

<div class="mt-4">
<a href="<?php echo BASE_URL; ?>drawings/manage" class="bg-gray-600 text-white px-4 py-2rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Drawing Boards</a>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
const App = () => {
// Parse the initial data
let initialData = {};
try {
initialData = <?php echo $drawing_board['elements'] ? $drawing_board['elements'] : '{}'; ?>;
} catch (e) {
console.error('Error parsing initial data:', e);
initialData = {
elements: [],
appState: {
viewBackgroundColor: "#ffffff",
currentItemFontFamily:1
}
};
}

return React.createElement(ExcalidrawLib.Excalidraw, {
initialData: {
elements: initialData.elements || [],
appState: {
...initialData.appState,
viewBackgroundColor: "#ffffff",
currentItemFontFamily: 1
}
},
viewModeEnabled: true,
zenModeEnabled: false,
gridModeEnabled: false
});
};

const container = document.getElementById("excalidraw-container");
const root = ReactDOM.createRoot(container);
root.render(React.createElement(App));
});
</script>