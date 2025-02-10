<?php
define('BASE_URL', '/revenuesure-lite/'); // Define the base URL
define('ROOT_PATH', __DIR__ . '/'); // Define the root path
require_once 'vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    // Handle .env loading error if needed.
    error_log("Error loading .env file: " . $e->getMessage());
}
require 'routes.php';
?>