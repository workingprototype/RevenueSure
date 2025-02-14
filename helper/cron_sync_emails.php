<?php
// helper/cron.php
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/email_functions.php'; // Include path fix

try {
    // Fetch all users - you might want to run this for a limited number at a time
    $stmt = $conn->prepare("SELECT id FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $user_id = $user['id'];
        $settings = getUserEmailSettings($conn, $user_id);

        if ($settings) {
            if ($imap = imapConnect($settings)) {
                $syncResult = syncEmails($conn, $imap, $user_id, 5); // Process 5 emails at a time
                if (!$syncResult) {
                    error_log("Sync failed for user: " . $user_id);
                }
                imap_close($imap);
            } else {
                 error_log("Connection failed for user: " . $user_id);
           }
        }
    }
     error_log("Cron job finished.");
} catch (Exception $e) {
    error_log("Cron job error: " . $e->getMessage());
}