<?php
/**
 * User Login Handler
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Utils::verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        // Sanitize inputs
        $email = Utils::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validate inputs
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!Utils::validateEmail($email)) {
            $errors[] = "Invalid email format";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        }
        
        // If validation passes, attempt login
        if (empty($errors)) {
            try {
                $db = Database::getInstance()->getConnection();
                $ipAddress = Utils::getIpAddress();
                
                // Check for too many failed login attempts
                $stmt = $db->prepare(
                    "SELECT COUNT(*) as attempt_count 
                    FROM login_attempts 
                    WHERE email = :email 
                    AND ip_address = :ip 
                    AND successful = 0 
                    AND attempted_at > DATE_SUB(NOW(), INTERVAL :window SECOND)"
                );
                $stmt->execute([
                    'email' => $email,
                    'ip' => $ipAddress,
                    'window' => LOGIN_ATTEMPT_WINDOW
                ]);
                $result = $stmt->fetch();
                
                if ($result['attempt_count'] >= MAX_LOGIN_ATTEMPTS) {
                    $errors[] = "Too many failed login attempts. Please try again in " . 
                               (LOGIN_ATTEMPT_WINDOW / 60) . " minutes.";
                } else {
                    // Fetch user from database
                    $stmt = $db->prepare(
                        "SELECT id, username, email, password, is_active 
                        FROM users 
                        WHERE email = :email"
                    );
                    $stmt->execute(['email' => $email]);
                    $user = $stmt->fetch();
                    
                    // Verify password
                    if ($user && Utils::verifyPassword($password, $user['password'])) {
                        // Check if account is active
                        if (!$user['is_active']) {
                            $errors[] = "Your account has been deactivated. Please contact support.";
                            
                            // Log failed attempt
                            $stmt = $db->prepare(
                                "INSERT INTO login_attempts (email, ip_address, successful) 
                                VALUES (:email, :ip, 0)"
                            );
                            $stmt->execute(['email' => $email, 'ip' => $ipAddress]);
                        } else {
                            // Successful login
                            // Regenerate session ID to prevent session fixation
                            session_regenerate_id(true);
                            
                            // Set session variables
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['login_time'] = time();
                            
                            // Set session lifetime
                            if ($remember) {
                                // Remember for 30 days
                                ini_set('session.gc_maxlifetime', 30 * 24 * 3600);
                                session_set_cookie_params(30 * 24 * 3600);
                            } else {
                                // Session expires when browser closes
                                ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
                                session_set_cookie_params(0);
                            }
                            
                            // Log successful attempt
                            $stmt = $db->prepare(
                                "INSERT INTO login_attempts (email, ip_address, successful) 
                                VALUES (:email, :ip, 1)"
                            );
                            $stmt->execute(['email' => $email, 'ip' => $ipAddress]);
                            
                            // Redirect to dashboard
                            Utils::setFlashMessage('success', 'Welcome back, ' . $user['username'] . '!');
                            header('Location: ../user/dashboard.php');
                            exit();
                        }
                    } else {
                        // Invalid credentials
                        $errors[] = "Invalid email or password";
                        
                        // Log failed attempt
                        $stmt = $db->prepare(
                            "INSERT INTO login_attempts (email, ip_address, successful) 
                            VALUES (:email, :ip, 0)"
                        );
                        $stmt->execute(['email' => $email, 'ip' => $ipAddress]);
                    }
                }
            } catch (Exception $e) {
                error_log("Login Error: " . $e->getMessage());
                $errors[] = "An error occurred during login. Please try again later.";
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
    <title>Login - Auth System</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h1>Login</h1>
            
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
            
            <form method="POST" action="login.php">
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
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Enter your password"
                    >
                </div>
                
                <div class="form-group-inline">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        Remember me
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p class="auth-link">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>
