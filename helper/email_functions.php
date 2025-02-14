<?php
//Use your credentials
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ROOT_PATH . 'vendor/autoload.php';
/**
 * Retrieves user's email settings from the database.
 *
 * @param PDO     $conn  Database connection object.
 * @param int     $user_id User ID.
 * @return array|false  Associative array of settings or false on failure.
 */
function getUserEmailSettings(PDO $conn, int $user_id): array|false {
    $stmt = $conn->prepare("SELECT imap_server, imap_port, imap_username, imap_password, smtp_server, smtp_port, smtp_username, smtp_password, smtp_security FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    try {
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getUserEmailSettings: " . $e->getMessage());
        return false;
    }
}

/**
 * Establishes an IMAP connection.
 *
 * @param array $settings Array of email settings (from getUserEmailSettings).
 * @return resource|false IMAP stream on success, false on failure.
 */
function imapConnect(array $settings) {
    if (!$settings || empty($settings['imap_server']) || empty($settings['imap_port']) || empty($settings['imap_username']) || empty($settings['imap_password'])) {
        error_log("Missing or incomplete IMAP settings.");
        return false;
    }

    $server = $settings['imap_server'];
    $port = $settings['imap_port'];
    $username = $settings['imap_username'];

    try {
        $password = decrypt($settings['imap_password']);
    } catch (Exception $e){
       error_log("Decryption error on IMAP password: " . $e->getMessage());
        return false;
    }

    $security = $settings['smtp_security'] ?? 'tls';  //Default
    // Construct the connection string
    $connectionString = "{{$server}:{$port}/imap/$security";

    // ADD CERT VERIFICATION HANDLING
    if (IS_DEVELOPMENT) {
        $connectionString .= "/novalidate-cert";  // ONLY in development
    }

    $connectionString .= "}INBOX";

    // Attempt to connect
    $imap = @imap_open($connectionString, $username, $password); //Use the @ sign here

    if (!$imap) {
        $errors = imap_errors();
        $errorMessage = $errors ? implode(", ", $errors) : "Unknown IMAP error.";
        error_log("IMAP connection failed: " . $errorMessage);
        return false;
    }

    return $imap;
}

/**
 * Sends email using SMTP.
 *
 * @param array $settings Array of email settings.
 * @param string $to Recipient email address.
 * @param string $subject Email subject.
 * @param string $body Email body.
 * @param string $altBody Optional plain text body for non-HTML clients.
 * @return bool True on success, false on failure.
 */
function smtpSendEmail(array $settings, string $to, string $subject, string $body, string $altBody = null): bool {
    $mail = new PHPMailer(true); // `true` enables exceptions

    try {
        //Server settings
        $mail->SMTPDebug = 0; // Enable verbose debugging output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = $settings['smtp_server'];                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $settings['smtp_username'];                     // SMTP username
        $mail->Password   = decrypt($settings['smtp_password']);                               // SMTP password, decrypt if necessary
        $mail->SMTPSecure = $settings['smtp_security'];         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = $settings['smtp_port'];                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom($settings['smtp_username'], 'RevenueSure Mailer');
        $mail->addAddress($to);     // Add a recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: 'This is a plain-text message alternative.';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Synchronizes emails from IMAP to the database.
 * @param PDO      $conn Database connection
 * @param resource $imap IMAP stream
 * @param int      $user_id Current User ID
 * @param int      $max_emails Max Number of emails to fetch
 * @return bool Success or failure
 */
function syncEmails(PDO $conn, $imap, int $user_id, int $max_emails = 10): bool {
      try{
          $emails = imap_search($imap, 'ALL', SE_UID); //search all unqiue emails.

          if($emails){
            rsort($emails); //reverse order of emails
               foreach ($emails as $email_uid) { // For now, use just 10 for demonstration: change as needed.
                  $overview = @imap_fetch_overview($imap, $email_uid, FT_UID);

                 if(empty($overview)) continue; //Skip if overview is empty.

                    $overview = $overview[0];
                      $messageId = $overview->message_id;

                    if(emailExists($conn, $messageId)){
                         continue; // If a particular email exists, skip.
                    }
                  $structure = @imap_fetchstructure($imap, $email_uid, FT_UID);
                   if(!$structure) continue;
                     $body = getEmailBody($imap, $email_uid, $structure, FT_UID);

                   $sentDate = date("Y-m-d H:i:s", strtotime($overview->date));
                    $from = $overview->from; //Sanitize later
                    $to = $overview->to[0]->mailbox . "@" . $overview->to[0]->host; //Sanitize later
                  $subject = $overview->subject; //Sanitize later.

                  $stmt = $conn->prepare("INSERT INTO emails (user_id, message_id, sender, recipient, subject, body, sent_date) VALUES (:user_id, :message_id, :sender, :recipient, :subject, :body, :sent_date)");
                    $stmt->bindParam(':user_id', $user_id);
                      $stmt->bindParam(':message_id', $messageId);
                      $stmt->bindParam(':sender', $from);
                       $stmt->bindParam(':recipient', $to);
                     $stmt->bindParam(':subject', $subject);
                      $stmt->bindParam(':body', $body);
                      $stmt->bindParam(':sent_date', $sentDate);

                     if ($stmt->execute()) {
                          continue;
                     } else {
                           return false;
                     }
               }
            } else {
                   // echo "Could not load!";
           }
           return true;
      } catch(Exception $e){
         return false;
      }

}

/**
* Check if the provided email exists in the database, based on message ID.
*/
function emailExists(PDO $conn, string $messageId): bool {
        $stmt = $conn->prepare("SELECT 1 FROM emails WHERE message_id = :message_id");
          $stmt->bindParam(':message_id', $messageId);
          $stmt->execute();
          return (bool) $stmt->fetchColumn();
}

/**
 * Extracts and decodes the body of an email.
 * @param resource $imap IMAP stream
 * @param int      $email_number Email number/UID
 * @param object   $structure Structure of the email (from imap_fetchstructure)
 * @param int $flag value to send the correct flag.
 * @return string Cleaned email body
 */
function getEmailBody($imap, int $email_number, object $structure, int $flag = 0): string {
    $body = '';

    if (!isset($structure->parts) || empty($structure->parts)) {
        // No attachments, just a simple email
        $body = @imap_body($imap, $email_number, $flag);
    } else {
        // Email has attachments, iterate over the parts
        foreach ($structure->parts as $partNumber => $part) {
            $data = @imap_fetchbody($imap, $email_number, $partNumber + 1, $flag);
             $encoding = $part->encoding;
            switch ($encoding) {
                case ENCQUOTEDPRINTABLE:
                     $data = quoted_printable_decode($data);
                   break;
                  case ENCBASE64:
                    $data = base64_decode($data);
                        break;
                   default:
                       // Assume no encoding and leave as is
                        break;
             }

            if ($part->type == 0 && $part->ifdisposition == 0) {
                // Text body part
                $body .= $data;
            }  elseif ($part->disposition == 'attachment') {
                   //Handle for attachment.
                }

        }
    }

    return cleanEmailBody($body);
}

/**
 * Cleans an email body by removing HTML tags and excessive whitespace.
 *
 * @param string $body The email body to clean.
 * @return string The cleaned email body.
 */
function cleanEmailBody(string $body): string {
    // Remove HTML tags
    $body = strip_tags($body);
    // Replace multiple spaces with a single space
    $body = preg_replace('/\s+/', ' ', $body);
    // Trim whitespace
    $body = trim($body);
    return $body;
}

function encrypt(string $data, string $key = "secretkey"): string {
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decrypt(string $data, string $key = "secretkey"): string {
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}