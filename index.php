<?php
/**
 * Index Page - Redirects to appropriate page
 */

require_once __DIR__ . '/includes/utils.php';

session_start();

// Redirect to dashboard if logged in, otherwise to login page
if (Utils::isLoggedIn()) {
    header('Location: pages/user/dashboard.php');
} else {
    header('Location: pages/auth/login.php');
}
exit();
