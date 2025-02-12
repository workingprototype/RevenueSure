<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$issue_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch issue details (with user's username)
$stmt = $conn->prepare("SELECT project_issues.*, users.username as reported_by_name 
                        FROM project_issues 
                        LEFT JOIN users ON project_issues.reported_by = users.id 
                        WHERE project_issues.id = :issue_id");
$stmt->bindParam(':issue_id', $issue_id);
$stmt->execute();
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    header("Location: " . BASE_URL . "projects/manage");
    exit();
}
?>
    <style>
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        /* Custom button styling */
        .btn {
            background-color: #0071e3;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            transition: background-color 0.3s;
            text-decoration: none;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #005bb5;
        }
        /* Section header */
        .section-header {
            border-bottom: 1px solid #e5e5ea;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1c1c1e;
        }
        /* Detail rows */
        .detail-row {
            display: flex;
            margin-bottom: 1rem;
        }
        .detail-label {
            color: #636366;
            font-weight: 500;
            min-width: 140px;
        }
        .detail-value {
            color: #1c1c1e;
            font-weight: 400;
        }
        /* Alerts */
        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background-color: #ff3b30;
            color: white;
        }
        .alert-success {
            background-color: #34c759;
            color: white;
        }
    </style>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-semibold text-center text-gray-900 mb-8">Issue Details</h1>
            <div class="card">
                <div class="section-header">Issue Information</div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <div class="detail-row">
                    <div class="detail-label">Issue ID:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['issue_id']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Reported By:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['reported_by_name'] ? $issue['reported_by_name'] : 'N/A'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Title:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['issue_title']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Description:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['description']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Type:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['issue_type']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Priority:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['priority']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Steps to Reproduce:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['steps_to_reproduce'] ? $issue['steps_to_reproduce'] : 'N/A'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Environment/Version:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($issue['environment_version'] ?: 'N/A'); ?></div>
                </div>
                <?php if ($issue['resolution_date']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Resolution Date:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($issue['resolution_date']); ?></div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-6 text-center">
                <a href="<?php echo BASE_URL; ?>projects/issues/manage" class="btn">Back to Issues</a>
            </div>
        </div>
    </div>