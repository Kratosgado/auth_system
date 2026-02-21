<?php
/**
 * Logout Handler
 */

require_once __DIR__ . '/../../includes/utils.php';

session_start();

// Clear all session variables
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Set flash message and redirect
session_start(); // Restart session to set flash message
Utils::setFlashMessage('success', 'You have been logged out successfully.');
header('Location: login.php');
exit();
