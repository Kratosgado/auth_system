<?php
/**
 * User Dashboard
 */

require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/utils.php';

session_start();

// Require login to access this page
Utils::requireLogin();

// Get user information
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];

try {
    $db = Database::getInstance()->getConnection();
    
    // Get user details
    $stmt = $db->prepare(
        "SELECT username, email, created_at, updated_at 
        FROM users 
        WHERE id = :user_id"
    );
    $stmt->execute(['user_id' => $userId]);
    $user = $stmt->fetch();
    
    // Get recent login attempts
    $stmt = $db->prepare(
        "SELECT ip_address, attempted_at, successful 
        FROM login_attempts 
        WHERE email = :email 
        ORDER BY attempted_at DESC 
        LIMIT 5"
    );
    $stmt->execute(['email' => $email]);
    $recentLogins = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $user = null;
    $recentLogins = [];
}

// Get flash message if any
$flashMessage = Utils::getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Auth System</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <div class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <div class="user-actions">
                    <a href="change-password.php" class="btn btn-secondary">Change Password</a>
                    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
            
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?php echo $flashMessage['type']; ?>">
                    <?php echo htmlspecialchars($flashMessage['message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($user): ?>
                <div class="card">
                    <h2>Account Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Username:</strong>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Account Created:</strong>
                            <span><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Last Updated:</strong>
                            <span><?php echo date('M d, Y', strtotime($user['updated_at'])); ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($recentLogins)): ?>
                    <div class="card">
                        <h2>Recent Login Activity</h2>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>IP Address</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentLogins as $login): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($login['ip_address']); ?></td>
                                            <td><?php echo date('M d, Y H:i:s', strtotime($login['attempted_at'])); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $login['successful'] ? 'success' : 'failed'; ?>">
                                                    <?php echo $login['successful'] ? 'Success' : 'Failed'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
