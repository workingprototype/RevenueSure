<?php
require_once ROOT_PATH . 'helper/core.php';
require_once ROOT_PATH . 'mail/includes/email_functions.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';
$mailbox = isset($_GET['mailbox']) ? $_GET['mailbox'] : 'inbox'; // Default mailbox
$user_id = $_SESSION['user_id'];

// Initialize search term and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';

// Pagination variables
$totalEmails = 0;
$emails = [];

// Fetch email settings
$settings = getUserEmailSettings($conn, $user_id);
$isThere = !empty($settings['imap_server']) && !empty($settings['imap_port']) && !empty($settings['imap_username']) && !empty($settings['imap_password']);

// If "removeemail" is posted (to empty trash)
if (isset($_POST['removeemail'])) {
    $target_id = $_POST["removeemail"];
    $stmt = $conn->prepare("DELETE FROM emails WHERE id = :target_id");
    $stmt->bindParam(':target_id', $target_id);
    try {
        if ($stmt->execute()) {
            $success = "Trash has been emptied. Email with target ID {$target_id} was successfully erased.";
        }
    } catch(Exception $e) {
        $error = "Unable to delete.";
    }
}

if (!$settings) {
    $error = "Please configure your email settings first.";
} else {
    // Connect to IMAP
    if ($imap = imapConnect($settings)) {
        $criteria = 'ALL';
        switch ($mailbox) {
            case "inbox":
                $criteria = 'ALL';
                break;
            case "sent":
                $criteria = 'ALL';
                break;
            case "drafts":
                $criteria = 'ALL';
                break;
            case "trash":
                $criteria = 'DELETED';
                break;
            case "important":
                $criteria = 'FLAGGED';
                break;
            default:
                $criteria = 'ALL';
                break;
        }
        if ($filter == "unread") {
            $criteria = 'UNSEEN';
        } else if ($filter == "important") {
            $criteria = 'FLAGGED';
        }
        // Get total emails in the mailbox
        $totalEmails = imap_num_msg($imap);
        // Pagination setup
        $perPage = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;
        $emails = [];
        $start = max(1, $totalEmails - $offset - $perPage + 1);
        $end = min($totalEmails, $totalEmails - $offset);
        for ($i = $end; $i >= $start; $i--) {
            $overview = @imap_fetch_overview($imap, $i, 0);
            if (empty($overview)) continue;
            $emails[] = $overview[0];
        }
    } else {
        $error = "Failed to connect to IMAP server.";
    }
}

// Update buildURL to use BASE_URL
function buildURL($base, $params = []) {
    $url = BASE_URL . $base;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}
$nextPage = ($end < $totalEmails && $totalEmails > 0) ? $page + 1 : null;
$prevPage = ($page > 1) ? $page - 1 : null;
?>

<div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <!-- Toast Notification -->
        <?php if ($error || $success): ?>
            <div id="toast" class="fixed top-5 right-5 z-50 bg-<?php echo $error ? 'red' : 'green'; ?>-100 border border-<?php echo $error ? 'red' : 'green'; ?>-400 text-<?php echo $error ? 'red' : 'green'; ?>-700 px-4 py-3 rounded shadow-lg flex items-center" role="alert">
                <span class="mr-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?php if ($error): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        <?php endif; ?>
                    </svg>
                </span>
                <span><?php echo htmlspecialchars($error ? $error : $success); ?></span>
                <button onclick="closeToast()" class="ml-2 text-gray-600 hover:text-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <script>
                function closeToast() {
                    document.getElementById('toast').classList.add('hidden');
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
                document.addEventListener('DOMContentLoaded', function(){
                    setTimeout(closeToast, 3000);
                });
            </script>
        <?php endif; ?>

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-4xl font-bold text-gray-900 mb-4 md:mb-0">Mailbox: <?php echo ucfirst($mailbox); ?></h1>
            <div class="flex items-center space-x-4">
                <a href="<?php echo BASE_URL; ?>mail/compose" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Compose
                </a>
                <?php if ($mailbox == "trash"): ?>
                    <form method="POST" action="" class="flex">
                        <button type="submit" name="removeemail" value="1" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 flex items-center gap-2">
                            <i class="fas fa-trash-alt"></i> Empty Trash
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="<?php echo BASE_URL; ?>mail/actions/fetch_emails" class="flex">
                        <?php echo csrfTokenInput(); ?>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 flex items-center gap-2">
                            <i class="fas fa-sync-alt"></i> Sync Emails
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Search & Filter -->
        <section class="mb-6">
            <form method="GET" action="" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search Mails" class="flex-grow px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                <select name="filter" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">All</option>
                    <option value="unread" <?php if($filter=="unread") echo 'selected'; ?>>Unread</option>
                    <option value="important" <?php if($filter=="important") echo 'selected'; ?>>Important</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center gap-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </section>

        <!-- Mailbox Navigation Tabs -->
        <nav class="mb-6">
            <div class="flex space-x-4">
                <a href="<?php echo buildURL("mail/index", ['mailbox' => 'inbox', 'page' => 1]); ?>" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white transition duration-300">Inbox</a>
                <a href="<?php echo buildURL("mail/index", ['mailbox' => 'sent', 'page' => 1]); ?>" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white transition duration-300">Sent</a>
                <a href="<?php echo buildURL("mail/index", ['mailbox' => 'drafts', 'page' => 1]); ?>" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white transition duration-300">Drafts</a>
                <a href="<?php echo buildURL("mail/index", ['mailbox' => 'trash', 'page' => 1]); ?>" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white transition duration-300">Trash</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <?php if (empty($settings)) : ?>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <p class="text-gray-800">
                        Please connect to your E-Mail. Go to: <a href="<?php echo BASE_URL; ?>mail/settings" class="text-blue-600 hover:underline">E-mail Settings</a>
                    </p>
                </div>
            <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
                    <?php if (!empty($emails)): ?>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Snippet</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($emails as $email): ?>
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='<?php echo BASE_URL; ?>mail/view?id=<?php echo $email->uid; ?>&mailbox=<?php echo $mailbox; ?>'">
                                        <td class="px-4 py-2 truncate max-w-[120px]">
                                            <?php echo htmlspecialchars($email->from); ?>
                                        </td>
                                        <td class="px-4 py-2">
                                            <?php if(!$email->seen): ?>
                                                <strong class="text-red-600"><?php echo htmlspecialchars($email->subject); ?></strong>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($email->subject); ?>
                                            <?php endif; ?>
                                            <?php if(isset($email->flagged) && $email->flagged): ?>
                                                <span class="text-yellow-500 ml-1">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">
                                            <?php
                                                // Create a snippet from the subject as a placeholder (adjust as needed)
                                                $snippet = strlen($email->subject) > 50 ? substr($email->subject, 0, 50).'...' : $email->subject;
                                                echo htmlspecialchars($snippet);
                                            ?>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">
                                            <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($email->date))); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-600">No emails in this mailbox.</p>
                    <?php endif; ?>
                </div>
                <!-- Pagination -->
                <div class="flex justify-between items-center mt-4">
                    <?php if ($prevPage): ?>
                        <a href="<?php echo buildURL("mail/index", ['mailbox' => $mailbox, 'page' => $prevPage, 'filter' => $filter]); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-300">Previous</a>
                    <?php else: ?>
                        <span class="text-gray-500">Previous</span>
                    <?php endif; ?>
                    <?php if ($nextPage && $totalEmails > 0): ?>
                        <a href="<?php echo buildURL("mail/index", ['mailbox' => $mailbox, 'page' => $nextPage, 'filter' => $filter]); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-300">Next</a>
                    <?php else: ?>
                        <span class="text-gray-500">Next</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
