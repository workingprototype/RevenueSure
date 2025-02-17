<?php

require_once __DIR__ . '/functions.php'; // Use absolute path
define('ROOT_PATH', __DIR__ . '/../'); // Absolute path to project root (one level up from helper)
define('IS_DEVELOPMENT', ($_ENV['APP_ENV'] ?? 'production') === 'development');
define('MAILDIR_BASE', ROOT_PATH . 'maildir/');  // Adjust path if needed

// Load caching flag from .env
$enableCache = filter_var($_ENV['ENABLE_CACHE'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
define('ENABLE_CACHE', $enableCache);

try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'lead_platform';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASS'] ?? '';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

if (IS_DEVELOPMENT) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * Returns the base URL of the application.
 *
 * @return string The base URL.
 */
function baseURL(): string {
    return BASE_URL;
}

/**
 * Checks if the current user has admin privileges.
 *
 * @return bool True if the user is an admin, false otherwise.
 */
function isAdmin(): bool {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirects the user to the appropriate dashboard after login based on their role.
 *
 * @param string $role The user's role (e.g., 'admin', 'user').
 */
function loginRedirect(string $role): void {
    header("Location: " . BASE_URL . "dashboard"); // All users go to the dashboard route
    exit();
}

/**
 * Redirects the user to the login page if they are not authorized.
 * @param bool $admin_only If true, only admins are allowed.
 */
function redirectIfUnauthorized(bool $admin_only = false) {
        if (session_status() == PHP_SESSION_NONE) {
           // echo 'Starting a new session';
           session_start();
        } else {
           // echo 'Session already started';
        }

    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "auth/login"); // Use BASE_URL here!
        exit();
    }

    if ($admin_only && !isAdmin()) { // Use isAdmin() helper
        header("Location: " . BASE_URL . "dashboard"); // Use BASE_URL here!
        exit();
    }
}

function createNoteCard(array $note): string {
    // Construct the path to the note view page
    $noteViewUrl = BASE_URL . "notes/view?id=" . urlencode($note['id']);

    return '<div class="bg-white rounded-lg shadow-md p-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-2"><a href="' . $noteViewUrl . '">' . htmlspecialchars($note['title']) . '</a></h2>
               <p class="text-gray-600 text-sm">Category: ' . htmlspecialchars($note['category_name'] ?: 'Uncategorized') . '</p>
               <p class="text-gray-600 text-sm mt-2">Created: ' . htmlspecialchars($note['created_at']) . '</p>
           </div>';
}