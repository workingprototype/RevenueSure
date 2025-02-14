<?php
require_once ROOT_PATH . 'helper/core.php';
require_once ROOT_PATH . 'mail/includes/email_functions.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    // Fetch user's email settings
    $settings = getUserEmailSettings($conn, $user_id);

    if ($settings) {
        // Connect to IMAP
        if ($imap = imapConnect($settings)) {

            // Synchronize emails
            if (syncEmails($conn, $imap, $user_id)) {
                $success = "Emails synchronized successfully!";
                 header("Location: " . BASE_URL . "mail/index?success=true");
                exit();
            } else {
                $error = "Failed to synchronize emails.";
            }

            imap_close($imap);
        } else {
            $error = "Failed to connect to IMAP server. Check your settings.";
        }
    } else {
        $error = "Email settings not found. Please configure your email settings.";
    }
}
?>