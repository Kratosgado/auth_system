<?php
/**
 * Forgot Password - Request Reset Token
 */

require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/utils.php';

session_start();

// Redirect if already logged in
if (Utils::isLoggedIn()) {
    header('Location: ../user/dashboard.php');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Utils::verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        // Sanitize input
        $email = Utils::sanitizeInput($_POST['email'] ?? '');
        
        // Validate email
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!Utils::validateEmail($email)) {
            $errors[] = "Invalid email format";
        }
        
        // If validation passes, process reset request
        if (empty($errors)) {
            try {
                $db = Database::getInstance()->getConnection();
                
                // Check if user exists
                $stmt = $db->prepare("SELECT id, username FROM users WHERE email = :email AND is_active = 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Generate reset token
                    $token = Utils::generateToken(32);
                    $expiresAt = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY);
                    
                    // Delete any existing unused tokens for this user
                    $stmt = $db->prepare("DELETE FROM password_resets WHERE user_id = :user_id AND used = 0");
                    $stmt->execute(['user_id' => $user['id']]);
                    
                    // Insert new reset token
                    $stmt = $db->prepare(
                        "INSERT INTO password_resets (user_id, token, expires_at) 
                        VALUES (:user_id, :token, :expires_at)"
                    );
                    $stmt->execute([
                        'user_id' => $user['id'],
                        'token' => $token,
                        'expires_at' => $expiresAt
                    ]);
                    
                    // In a real application, send this link via email
                    // For now, we'll just show it (NEVER do this in production!)
                    $resetLink = SITE_URL . "/pages/auth/reset-password.php?token=" . $token;
                    
                    // For demonstration purposes only - remove in production
                    $_SESSION['demo_reset_link'] = $resetLink;
                    
                    // NOTE: In production, you should send an email here using PHPMailer or similar
                    
                    $success = true;
                }
                
                // Always show success message (security best practice - don't reveal if email exists)
                Utils::setFlashMessage('success', 
                    'If an account exists with that email, a password reset link has been sent.');
                header('Location: forgot-password.php?sent=1');
                exit();
                
            } catch (Exception $e) {
                error_log("Password Reset Request Error: " . $e->getMessage());
                $errors[] = "An error occurred. Please try again later.";
            }
        }
    }
}

// Get flash message if any
$flashMessage = Utils::getFlashMessage();

// Generate CSRF token for the form
$csrfToken = Utils::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Auth System</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h1>Forgot Password</h1>
            
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?php echo $flashMessage['type']; ?>">
                    <?php echo htmlspecialchars($flashMessage['message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['sent']) && isset($_SESSION['demo_reset_link'])): ?>
                <div class="alert alert-info">
                    <strong>DEMO MODE:</strong> In production, this link would be sent via email.<br>
                    <strong>Reset Link:</strong><br>
                    <a href="<?php echo htmlspecialchars($_SESSION['demo_reset_link']); ?>">
                        <?php echo htmlspecialchars($_SESSION['demo_reset_link']); ?>
                    </a>
                </div>
                <?php unset($_SESSION['demo_reset_link']); ?>
            <?php endif; ?>
            
            <p>Enter your email address and we'll send you a link to reset your password.</p>
            
            <form method="POST" action="forgot-password.php">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required 
                        autofocus
                        placeholder="Enter your email"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
            </form>
            
            <p class="auth-link">
                Remember your password? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>
