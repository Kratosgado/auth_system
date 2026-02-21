<?php
/**
 * Utility Functions for Security and Validation
 */

class Utils {
    
    /**
     * Sanitize user input
     * @param string $data
     * @return string
     */
    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Validate email address
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate password strength
     * Requirements: At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character
     * @param string $password
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        
        if (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match("/[a-z]/", $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match("/[0-9]/", $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        return [
            'valid' => empty($errors),
            'message' => implode(". ", $errors)
        ];
    }
    
    /**
     * Validate username
     * Requirements: 3-50 characters, alphanumeric and underscores only
     * @param string $username
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateUsername($username) {
        if (strlen($username) < 3 || strlen($username) > 50) {
            return [
                'valid' => false,
                'message' => 'Username must be between 3 and 50 characters'
            ];
        }
        
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            return [
                'valid' => false,
                'message' => 'Username can only contain letters, numbers, and underscores'
            ];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    
    /**
     * Hash password using bcrypt
     * @param string $password
     * @return string
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify password against hash
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate secure random token
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Get user IP address
     * @return string
     */
    public static function getIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }
    
    /**
     * Redirect to a page
     * @param string $url
     */
    public static function redirect($url) {
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Set flash message in session
     * @param string $type (success, error, warning, info)
     * @param string $message
     */
    public static function setFlashMessage($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get and clear flash message
     * @return array|null
     */
    public static function getFlashMessage() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        
        return null;
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Require login (redirect to login page if not logged in)
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            self::setFlashMessage('warning', 'Please login to access this page');
            self::redirect('login.php');
        }
    }
    
    /**
     * Prevent CSRF attacks
     * @return string
     */
    public static function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = self::generateToken(32);
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     * @param string $token
     * @return bool
     */
    public static function verifyCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
