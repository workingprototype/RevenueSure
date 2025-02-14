<?php
    require_once ROOT_PATH . 'helper/core.php';
    require_once ROOT_PATH . 'mail/includes/email_functions.php';
    redirectIfUnauthorized(true);

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = $_SESSION['user_id'];
        $to = trim($_POST['to']);
        $subject = trim($_POST['subject']);
        $body = $_POST['body'];
        $altBody = $_POST['altBody'];

        // Validate inputs (at least basic validation)
        if (empty($to) || empty($subject) || empty($body)) {
            $error = "Recipient, subject, and body are required.";
        } else {
            // Fetch user's email settings
            $settings = getUserEmailSettings($conn, $user_id);

            if ($settings) {
                // Send email using SMTP
                if (smtpSendEmail($settings, $to, $subject, $body, $altBody)) {
                    $success = "Email sent successfully!";
                } else {
                    $error = "Failed to send email.";
                }
            } else {
                $error = "Email settings not found. Please configure your email settings.";
            }
        }
    }
?>