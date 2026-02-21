<?php
/**
 * Installation Test Script
 * Run this file to verify your setup is correct
 * Access via: http://localhost/auth_system/test.php
 */

// Prevent running in production
if ($_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1') {
    die('This test script should only be run in development environment.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Test - Auth System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .test-item {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .test-pass {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }
        .test-fail {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .test-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .status {
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
        }
        .status-pass { background: #28a745; }
        .status-fail { background: #dc3545; }
        .status-warning { background: #ffc107; color: #333; }
        .message {
            flex: 1;
        }
        .details {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 5px;
            text-align: center;
        }
        .next-steps {
            margin-top: 20px;
            padding: 20px;
            background: #d1ecf1;
            border-radius: 5px;
            border-left: 4px solid #0c5460;
        }
        .next-steps h3 {
            margin-top: 0;
            color: #0c5460;
        }
        .next-steps ol {
            margin: 10px 0;
        }
        .next-steps li {
            margin: 8px 0;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>Installation Test Results</h1>
        
        <?php
        $tests = [];
        $passCount = 0;
        $failCount = 0;
        $warningCount = 0;
        
        // Test 1: PHP Version
        $phpVersion = phpversion();
        $phpTest = version_compare($phpVersion, '7.4.0', '>=');
        $tests[] = [
            'name' => 'PHP Version',
            'status' => $phpTest ? 'pass' : 'fail',
            'message' => "PHP $phpVersion" . ($phpTest ? ' (OK)' : ' (Minimum 7.4 required)'),
            'details' => $phpTest ? 'Your PHP version is compatible.' : 'Please upgrade to PHP 7.4 or higher.'
        ];
        
        // Test 2: PDO MySQL Extension
        $pdoTest = extension_loaded('pdo_mysql');
        $tests[] = [
            'name' => 'PDO MySQL Extension',
            'status' => $pdoTest ? 'pass' : 'fail',
            'message' => $pdoTest ? 'PDO MySQL is enabled' : 'PDO MySQL is not enabled',
            'details' => $pdoTest ? 'Database connectivity is available.' : 'Enable pdo_mysql extension in php.ini'
        ];
        
        // Test 3: Config File
        $configTest = file_exists('config.php');
        $tests[] = [
            'name' => 'Configuration File',
            'status' => $configTest ? 'pass' : 'fail',
            'message' => $configTest ? 'config.php exists' : 'config.php not found',
            'details' => $configTest ? 'Configuration file is present.' : 'Copy config.template.php to config.php'
        ];
        
        // Test 4: Database Connection
        if ($configTest) {
            require_once 'config.php';
            require_once 'database.php';
            
            try {
                $db = Database::getInstance()->getConnection();
                $dbTest = true;
                $dbMessage = 'Database connection successful';
                $dbDetails = 'Connected to: ' . DB_NAME;
            } catch (Exception $e) {
                $dbTest = false;
                $dbMessage = 'Database connection failed';
                $dbDetails = 'Error: ' . $e->getMessage();
            }
            
            $tests[] = [
                'name' => 'Database Connection',
                'status' => $dbTest ? 'pass' : 'fail',
                'message' => $dbMessage,
                'details' => $dbDetails
            ];
            
            // Test 5: Database Tables
            if ($dbTest) {
                $requiredTables = ['users', 'password_resets', 'login_attempts'];
                $missingTables = [];
                
                foreach ($requiredTables as $table) {
                    try {
                        $stmt = $db->query("SHOW TABLES LIKE '$table'");
                        if ($stmt->rowCount() === 0) {
                            $missingTables[] = $table;
                        }
                    } catch (Exception $e) {
                        $missingTables[] = $table;
                    }
                }
                
                $tablesTest = empty($missingTables);
                $tests[] = [
                    'name' => 'Database Tables',
                    'status' => $tablesTest ? 'pass' : 'fail',
                    'message' => $tablesTest ? 'All required tables exist' : 'Missing tables: ' . implode(', ', $missingTables),
                    'details' => $tablesTest ? 'Tables: ' . implode(', ', $requiredTables) : 'Run database.sql to create tables'
                ];
            }
        }
        
        // Test 6: Session Support
        $sessionTest = function_exists('session_start');
        $tests[] = [
            'name' => 'Session Support',
            'status' => $sessionTest ? 'pass' : 'fail',
            'message' => $sessionTest ? 'Sessions are supported' : 'Sessions are not supported',
            'details' => $sessionTest ? 'Session handling is available.' : 'Enable session support in PHP'
        ];
        
        // Test 7: Required Files
        $requiredFiles = ['utils.php', 'database.php', 'register.php', 'login.php', 'dashboard.php', 'style.css'];
        $missingFiles = [];
        
        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                $missingFiles[] = $file;
            }
        }
        
        $filesTest = empty($missingFiles);
        $tests[] = [
            'name' => 'Required Files',
            'status' => $filesTest ? 'pass' : 'fail',
            'message' => $filesTest ? 'All required files present' : 'Missing files: ' . implode(', ', $missingFiles),
            'details' => $filesTest ? count($requiredFiles) . ' files checked' : 'Please restore missing files'
        ];
        
        // Test 8: File Permissions
        $writableTest = is_writable('.');
        $tests[] = [
            'name' => 'File Permissions',
            'status' => $writableTest ? 'warning' : 'pass',
            'message' => $writableTest ? 'Directory is writable (check in production)' : 'Directory permissions OK',
            'details' => $writableTest ? 'Ensure proper permissions in production' : 'Files are readable'
        ];
        
        // Display results
        foreach ($tests as $test) {
            $statusClass = 'test-' . $test['status'];
            $statusBadge = 'status-' . $test['status'];
            $statusText = strtoupper($test['status']);
            
            if ($test['status'] === 'pass') $passCount++;
            elseif ($test['status'] === 'fail') $failCount++;
            elseif ($test['status'] === 'warning') $warningCount++;
            
            echo "<div class='test-item $statusClass'>";
            echo "<div class='message'>";
            echo "<strong>{$test['name']}</strong><br>";
            echo "{$test['message']}";
            echo "<div class='details'>{$test['details']}</div>";
            echo "</div>";
            echo "<span class='status $statusBadge'>$statusText</span>";
            echo "</div>";
        }
        
        // Summary
        $totalTests = count($tests);
        echo "<div class='summary'>";
        echo "<h3>Summary</h3>";
        echo "<p><strong>Total Tests:</strong> $totalTests</p>";
        echo "<p style='color: #28a745;'><strong>Passed:</strong> $passCount</p>";
        if ($failCount > 0) {
            echo "<p style='color: #dc3545;'><strong>Failed:</strong> $failCount</p>";
        }
        if ($warningCount > 0) {
            echo "<p style='color: #ffc107;'><strong>Warnings:</strong> $warningCount</p>";
        }
        
        if ($failCount === 0) {
            echo "<p style='color: #28a745; font-size: 18px; margin-top: 20px;'><strong>✓ System is ready to use!</strong></p>";
        } else {
            echo "<p style='color: #dc3545; font-size: 18px; margin-top: 20px;'><strong>✗ Please fix the failed tests above</strong></p>";
        }
        echo "</div>";
        
        if ($failCount === 0) {
            echo "<div class='next-steps'>";
            echo "<h3>Next Steps</h3>";
            echo "<ol>";
            echo "<li>Delete or secure this test file (<code>test.php</code>)</li>";
            echo "<li>Visit <a href='register.php'>register.php</a> to create your first account</li>";
            echo "<li>Test the login functionality at <a href='login.php'>login.php</a></li>";
            echo "<li>Review <a href='SETUP.md'>SETUP.md</a> for detailed configuration</li>";
            echo "<li>Read <a href='README.md'>README.md</a> for complete documentation</li>";
            echo "</ol>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
