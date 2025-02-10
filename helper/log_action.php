<?php
function log_action($action) {
    $log = "[" . date('Y-m-d H:i:s') . "] User ID: {$_SESSION['user_id']} - Action: $action" . PHP_EOL;
    file_put_contents('logs.txt', $log, FILE_APPEND);
}
?>