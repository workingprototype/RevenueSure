  <?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];

// Fetch current theme
$stmt = $conn->prepare("SELECT theme FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$current_theme = $user['theme'] ?? 'default'; // Default theme

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_theme'])) {
    $theme = trim($_POST['theme']);

    // Validate theme
    $allowed_themes = ['default', 'material3', 'retro', 'dark-mode', 'office', 'light', 'nature', 'playful', 'cute']; // Define your theme options
    if (!in_array($theme, $allowed_themes)) {
        $error = "Invalid theme selected.";
    } else {
        // Update theme
        $stmt = $conn->prepare("UPDATE users SET theme = :theme WHERE id = :user_id");
        $stmt->bindParam(':theme', $theme);
        $stmt->bindParam(':user_id', $user_id);
        $cacheDir = ROOT_PATH . 'cache/';
        $files = glob($cacheDir . '*.cache');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        if ($stmt->execute()) {
          $_SESSION['success'] = "Theme updated successfully! You might need to clear your browser cache to see the changes."; //Store in session
            header("Location: " . BASE_URL . "settings/index?success=true");
            exit();
        } else {
            $error = "Error updating theme.";
        }
    }
}

// Fetch current USE_CDN_ASSETS value from .env
$useCDNAssets = filter_var($_ENV['USE_CDN_ASSETS'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_assets'])) {
        $useCDNAssets = isset($_POST['use_cdn_assets']) ? true : false;

        // Update .env file (This is a simplified example; use a robust .env library for production)
        $envFile = ROOT_PATH . '.env';
        $envContents = file_get_contents($envFile);
        $envContents = preg_replace('/^USE_CDN_ASSETS=.*/m', "USE_CDN_ASSETS=" . ($useCDNAssets ? 'true' : 'false'), $envContents);

        if (file_put_contents($envFile, $envContents) !== false) {
            // Reload environment variables
            try {
                $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
                $dotenv->load();
                //define('USE_CDN_ASSETS', filter_var($_ENV['USE_CDN_ASSETS'] ?? 'false', FILTER_VALIDATE_BOOLEAN)); //Removed the define function
                $_SESSION['success'] = "Asset settings updated successfully! You might need to clear your browser cache to see the changes."; // Store in session
                 header("Location: " . BASE_URL . "settings/index");
                   exit();
            } catch (Exception $e) {
                $error = "Error reloading .env file: " . $e->getMessage();
            }
        } else {
            $error = "Error writing to .env file. Please ensure the file is writable.";
        }
    }  elseif (isset($_POST['clear_cache'])) {
        // Clear all cache files (You might want to make this more selective)
        $cacheDir = ROOT_PATH . 'cache/';
        $files = glob($cacheDir . '*.cache');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $_SESSION['success'] = "Cache cleared successfully!";
        header("Location: " . BASE_URL . "settings/index?success=true");
        exit();
    }
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Settings</h1>
     <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
        <?php unset($_SESSION['error']); // Clear the session variable ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
         <?php unset($_SESSION['success']); // Clear the session variable ?>
    <?php endif; ?>
    <ul class="text-xl">
      <li class="mb-2"> <a href="<?php echo BASE_URL; ?>settings/invoice/index" class="text-blue-600 hover:underline">Invoice Settings</a></li>
       </ul>
       <ul class="text-xl">
      <li class="mb-2"> <a href="<?php echo BASE_URL; ?>mail/settings" class="text-blue-600 hover:underline">E-mail Settings</a></li>
       </ul>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="theme" class="block text-gray-700">Select Theme</label>
                <select name="theme" id="theme" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="default" <?php if ($current_theme === 'default') echo 'selected'; ?>>Default ( Brutalism )</option>
                    <option value="office" <?php if ($current_theme === 'office') echo 'selected'; ?>>Office</option>
                    <option value="cute" <?php if ($current_theme === 'cute') echo 'selected'; ?>>Felt-Cute</option>
                    <option value="nature" <?php if ($current_theme === 'nature') echo 'selected'; ?>>Nature</option>
                    <option value="dark-mode" <?php if ($current_theme === 'dark-mode') echo 'selected'; ?>>Dark Mode ( Beta )</option>
                    <option value="light" <?php if ($current_theme === 'light') echo 'selected'; ?>>Light ( Beta )</option>
                    <option value="playful" <?php if ($current_theme === 'playful') echo 'selected'; ?>>Playful Pink</option>
                    <option value="material3" <?php if ($current_theme === 'material3') echo 'selected'; ?>>Material 3</option>
                    <option value="retro" <?php if ($current_theme === 'retro') echo 'selected'; ?>>Retro ( Beta )</option>
                </select>
            </div>
            <button type="submit" name="update_theme" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Theme</button>
        </form>
    </div>
   <!-- CDN Settings Form -->
   <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="use_cdn_assets" id="use_cdn_assets" class="mr-2" <?php if (USE_CDN_ASSETS) echo 'checked'; ?>>
                    <span class="text-gray-700">Use CDN for Assets</span>
                </label>
            </div>
            <button type="submit" name="update_assets" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Save Asset Settings</button>
            <button type="submit" name="clear_cache" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Clear Cache</button>
        </form>
    </div>
</div>