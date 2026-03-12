<?php
/**
 * Database Configuration Template
 * 
 * INSTRUCTIONS:
 * 1. Copy this file and rename it to 'config.php'
 * 2. Update the values below with your actual database credentials
 * 3. Never commit the actual config.php file to version control
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'auth_system_db');
define('DB_USER', 'root');  
define('DB_PASS', '');     
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('SITE_URL', 'http://localhost:8000');
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('PASSWORD_RESET_EXPIRY', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_WINDOW', 900); // 15 minutes in seconds

// Security settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); //  Set to 1 in production if using HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Error reporting
//Set to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');
