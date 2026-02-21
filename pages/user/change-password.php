<?php
/**
 * Change Password (for logged-in users)
 */

require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/utils.php';

session_start();

// Require login to access this page
Utils::requireLogin();

$errors = [];
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Utils::verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate current password
        if (empty($currentPassword)) {
            $errors[] = "Current password is required";
        }
        
        // Validate new password
        if (empty($newPassword)) {
            $errors[] = "New password is required";
        } else {
            $passwordValidation = Utils::validatePassword($newPassword);
            if (!$passwordValidation['valid']) {
                $errors[] = $passwordValidation['message'];
            }
        }
        
        // Check if new password matches confirmation
        if ($newPassword !== $confirmPassword) {
            $errors[] = "New passwords do not match";
        }
        
        // Check if new password is different from current
        if ($currentPassword === $newPassword) {
            $errors[] = "New password must be different from current password";
        }
        
        // If validation passes, update password
        if (empty($errors)) {
            try {
                $db = Database::getInstance()->getConnection();
                
                // Verify current password
                $stmt = $db->prepare("SELECT password FROM users WHERE id = :user_id");
                $stmt->execute(['user_id' => $userId]);
                $user = $stmt->fetch();
                
                if ($user && Utils::verifyPassword($currentPassword, $user['password'])) {
                    // Update password
                    $hashedPassword = Utils::hashPassword($newPassword);
                    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                    $stmt->execute([
                        'password' => $hashedPassword,
                        'user_id' => $userId
                    ]);
                    
                    Utils::setFlashMessage('success', 'Password changed successfully!');
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $errors[] = "Current password is incorrect";
                }
            } catch (Exception $e) {
                error_log("Change Password Error: " . $e->getMessage());
                $errors[] = "An error occurred while changing your password. Please try again.";
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
    <title>Change Password - Auth System</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h1>Change Password</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="change-password.php">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                        autofocus
                        placeholder="Enter your current password"
                    >
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required
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
                
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
