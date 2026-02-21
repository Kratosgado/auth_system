<?php
/**
 * Reset Password - Using Token
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
$validToken = false;
$userId = null;
$token = $_GET['token'] ?? '';

// Verify token exists and is valid
if (!empty($token)) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Check if token exists, is unused, and not expired
        $stmt = $db->prepare(
            "SELECT pr.user_id, pr.expires_at, u.email 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.token = :token 
            AND pr.used = 0 
            AND pr.expires_at > NOW()"
        );
        $stmt->execute(['token' => $token]);
        $resetData = $stmt->fetch();
        
        if ($resetData) {
            $validToken = true;
            $userId = $resetData['user_id'];
        } else {
            $errors[] = "Invalid or expired reset token. Please request a new one.";
        }
    } catch (Exception $e) {
        error_log("Token Verification Error: " . $e->getMessage());
        $errors[] = "An error occurred. Please try again later.";
    }
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Utils::verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate password
        if (empty($password)) {
            $errors[] = "Password is required";
        } else {
            $passwordValidation = Utils::validatePassword($password);
            if (!$passwordValidation['valid']) {
                $errors[] = $passwordValidation['message'];
            }
        }
        
        // Confirm password match
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        // If validation passes, update password
        if (empty($errors)) {
            try {
                $db = Database::getInstance()->getConnection();
                
                // Start transaction
                $db->beginTransaction();
                
                // Update user password
                $hashedPassword = Utils::hashPassword($password);
                $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                $stmt->execute([
                    'password' => $hashedPassword,
                    'user_id' => $userId
                ]);
                
                // Mark token as used
                $stmt = $db->prepare("UPDATE password_resets SET used = 1 WHERE token = :token");
                $stmt->execute(['token' => $token]);
                
                // Commit transaction
                $db->commit();
                
                Utils::setFlashMessage('success', 'Password reset successful! Please login with your new password.');
                header('Location: login.php');
                exit();
                
            } catch (Exception $e) {
                // Rollback on error
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                error_log("Password Reset Error: " . $e->getMessage());
                $errors[] = "An error occurred while resetting your password. Please try again.";
            }
        }
    }
}

// Generate CSRF token for the form
$csrfToken = Utils::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Auth System</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h1>Reset Password</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($validToken): ?>
                <p>Enter your new password below.</p>
                
                <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            autofocus
                            placeholder="Enter your new password"
                        >
                        <small>At least 8 characters with uppercase, lowercase, number, and special character</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required
                            placeholder="Confirm your new password"
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            <?php else: ?>
                <div class="alert alert-error">
                    <p>This password reset link is invalid or has expired.</p>
                    <p><a href="forgot-password.php">Request a new password reset link</a></p>
                </div>
            <?php endif; ?>
            
            <p class="auth-link">
                Remember your password? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>
