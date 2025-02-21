<?php
require_once ROOT_PATH . 'helper/core.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Clear anonymous cache on login
            clearCache('header_anonymous');
            clearCache('footer_anonymous');

            header("Location: " . BASE_URL . "dashboard/index");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = "Error logging in. Please try again later.";
    }
}
?>

    <div class="min-h-screen flex items-center justify-center">
        <div class="login-container">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Sign in</h1>
            <p>Enter your account details or use QR code</p>
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <?php echo csrfTokenInput(); ?>
                <div>
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div>
                    <label for="password" class="block text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="remember-me flex items-center justify-between">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <div class="recover-password">
                        <a href="#">Recover password</a>
                    </div>
                </div>
                <button type="submit">Sign in</button>
            </form>

            <div class="separator">
                or
            </div>

            <div class="qr-code-login">
                <button>
                    <i class="fa-solid fa-qrcode mr-2"></i> Log in with QR code
                </button>
            </div>

            <div class="create-account">
                You don't have an account? <a href="<?php echo BASE_URL; ?>auth/register">Create an account</a>
            </div>
        </div>
    </div>