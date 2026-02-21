<?php
/**
 * User Registration Handler
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
        // Sanitize inputs
        $username = Utils::sanitizeInput($_POST['username'] ?? '');
        $email = Utils::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate username
        $usernameValidation = Utils::validateUsername($username);
        if (!$usernameValidation['valid']) {
            $errors[] = $usernameValidation['message'];
        }
        
        // Validate email
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!Utils::validateEmail($email)) {
            $errors[] = "Invalid email format";
        }
        
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
        
        // If no validation errors, proceed with registration
        if (empty($errors)) {
            try {
                $db = Database::getInstance()->getConnection();
                
                // Check if username already exists
                $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                if ($stmt->fetch()) {
                    $errors[] = "Username already exists";
                }
                
                // Check if email already exists
                $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetch()) {
                    $errors[] = "Email already registered";
                }
                
                // If no duplicate errors, create user
                if (empty($errors)) {
                    $hashedPassword = Utils::hashPassword($password);
                    
                    $stmt = $db->prepare(
                        "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)"
                    );
                    
                    $result = $stmt->execute([
                        'username' => $username,
                        'email' => $email,
                        'password' => $hashedPassword
                    ]);
                    
                    if ($result) {
                        $success = true;
                        Utils::setFlashMessage('success', 'Registration successful! Please login.');
                        header('Location: login.php');
                        exit();
                    } else {
                        $errors[] = "Registration failed. Please try again.";
                    }
                }
            } catch (Exception $e) {
                error_log("Registration Error: " . $e->getMessage());
                $errors[] = "An error occurred during registration. Please try again later.";
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
    <title>Register - Auth System</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h1>Create Account</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required 
                        autofocus
                        placeholder="Enter your username"
                    >
                    <small>3-50 characters, letters, numbers, and underscores only</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                        placeholder="Enter your email"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Enter your password"
                    >
                    <small>At least 8 characters with uppercase, lowercase, number, and special character</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        placeholder="Confirm your password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            
            <p class="auth-link">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>
