<?php
/**
 * Generates a hidden input field containing the CSRF token.
 *
 * @return string The HTML for the CSRF token hidden input.
 */
function csrfTokenInput(): string {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $token = htmlspecialchars($_SESSION['csrf_token'] ?? '');
    return "<input type='hidden' name='csrf_token' value='$token'>";
}

/**
 * Creates a URL with appended parameters.
 *
 * @param string $path The base URL.
 * @param array  $params An associative array of parameters to append to the URL.
 * @return string The URL with appended parameters.
 */
function createURLWithParams(string $path, array $params = []): string
{
    $queryString = http_build_query($params);
    return $path . (!empty($queryString) ? '?' . $queryString : '');
}
/**
 * Redirects to URL with base and parameters.
 *
 * @param string $path
 * @param array $params
 * @return void
 */
function redirectWithURL(string $path, array $params = []): void{
    header("Location: ". createURLWithParams(baseURL() . $path, $params));
    exit();
}
/**
 * Sanitizes a string input to prevent XSS.
 *
 * @param string $data The input string.
 * @return string The sanitized string.
 */
function sanitizeString(string $data): string {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // More robust escaping
    return $data;
}

/**
 * Validates an email address.
 *
 * @param string $email The email address to validate.
 * @return bool True if the email is valid, false otherwise.
 */
function validateEmail(string $email): bool {
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validates a phone number (basic format).
 *  You might want to use a more robust library for phone number validation.
 * @param string $phone The phone number to validate.
 * @return bool True if the phone number is valid, false otherwise.
 */
function validatePhone(string $phone): bool {
    // Allow only digits, spaces, plus signs, and hyphens
     return (bool)preg_match('/^[0-9\s\+]+$/', $phone);
}

/**
 * Validates a string for alphanumeric characters, spaces, underscores, and hyphens
 * @param string $input The string to validate
 * @return bool True if string is valid, false otherwise
 */
function validateAlphanumeric(string $input): bool {
    return (bool)preg_match('/^[a-zA-Z0-9\s\-_]+$/', $input);
}


/**
 * Displays an alert message.
 *
 * @param string $message The message to display.
 * @param string $type The type of alert (success, error, warning, info).
 * @return string The HTML for the alert message.
 */
function displayAlert(string $message, string $type = 'error'): string {
    $bgColor = 'red-100';
    $borderColor = 'red-400';
    $textColor = 'red-700';

    if ($type === 'success') {
        $bgColor = 'green-100';
        $borderColor = 'green-400';
        $textColor = 'green-700';
    } elseif ($type === 'warning') {
        $bgColor = 'yellow-100';
        $borderColor = 'yellow-400';
        $textColor = 'yellow-700';
    } elseif ($type === 'info') {
        $bgColor = 'blue-100';
        $borderColor = 'blue-400';
        $textColor = 'blue-700';
    }

    return "<div class=\"bg-$bgColor border border-$borderColor text-$textColor px-4 py-3 rounded-lg mb-6\">" . htmlspecialchars($message) . "</div>";
}
/**
 * Checks if a string contains HTML
 */
function containsHTML(string $input): bool {
    return (bool) preg_match('/<[^>]*>/', $input);
  }
  
  /**
   * Sanitizes HTML content by stripping unwanted tags and attributes.
   */
  function sanitizeHTML(string $input, array $allowedTags = ['p', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'a', 'img']): string {
    $allowedTagsString = '<' . implode('><', $allowedTags) . '>'; // Convert allowed tags array to string
    return strip_tags($input, $allowedTagsString);
  }

  
?>