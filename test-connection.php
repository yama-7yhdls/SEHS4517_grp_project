<?php
/**
 * Database Connection Test Script
 * This file tests the database connection and displays system information
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Hotel Booking System - Connection Test</h1>";
echo "<hr>";

// Test 1: PHP Version
echo "<h2>1. PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Required: 7.4+<br>";
echo "<strong>Status: " . (version_compare(phpversion(), '7.4.0', '>=') ? "✓ PASS" : "✗ FAIL") . "</strong>";
echo "<hr>";

// Test 2: Required Extensions
echo "<h2>2. Required PHP Extensions</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'session'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "$ext: " . ($loaded ? "✓ Loaded" : "✗ Not loaded") . "<br>";
}
echo "<hr>";

// Test 3: Include config.php
echo "<h2>3. Loading config.php</h2>";
try {
    require_once 'php/config.php';
    echo "✓ config.php loaded successfully<br>";
    echo "Database Host: " . DB_HOST . "<br>";
    echo "Database Name: " . DB_NAME . "<br>";
    echo "Database User: " . DB_USER . "<br>";
    echo "Database Charset: " . DB_CHARSET . "<br>";
} catch (Exception $e) {
    echo "✗ Error loading config.php: " . $e->getMessage() . "<br>";
    die();
}
echo "<hr>";

// Test 4: Database Connection
echo "<h2>4. Database Connection</h2>";
try {
    if (isset($pdo)) {
        echo "✓ PDO connection established<br>";
        
        // Test query
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        echo "MySQL Version: " . $result['version'] . "<br>";
    } else {
        echo "✗ PDO connection not found<br>";
    }
} catch (Exception $e) {
    echo "✗ Database connection error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 5: Database Tables
echo "<h2>5. Database Tables</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✓ Found " . count($tables) . " tables:<br>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Check for required tables
        $required_tables = ['users', 'regions', 'hotels', 'room_type', 'room_inventory', 'bookings'];
        $missing_tables = array_diff($required_tables, $tables);
        
        if (empty($missing_tables)) {
            echo "<strong>✓ All required tables exist</strong><br>";
        } else {
            echo "<strong>✗ Missing tables: " . implode(', ', $missing_tables) . "</strong><br>";
            echo "<em>Run sql/setup.sql to create tables</em><br>";
        }
    } else {
        echo "✗ No tables found in database<br>";
        echo "<em>Run sql/setup.sql to create tables</em><br>";
    }
} catch (Exception $e) {
    echo "✗ Error checking tables: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 6: Sample Data Count
echo "<h2>6. Sample Data</h2>";
try {
    $tables = ['users', 'regions', 'hotels', 'room_type', 'room_inventory', 'bookings'];
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Table</th><th>Row Count</th></tr>";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $result = $stmt->fetch();
            echo "<tr><td>$table</td><td>" . $result['count'] . "</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>$table</td><td>N/A</td></tr>";
        }
    }
    echo "</table>";
} catch (Exception $e) {
    echo "✗ Error checking data: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 7: Session Management
echo "<h2>7. Session Management</h2>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "✓ Active" : "✗ Not Active") . "<br>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "Session ID: " . session_id() . "<br>";
    echo "Session Timeout: " . ini_get('session.gc_maxlifetime') . " seconds (30 min = 1800)<br>";
}
echo "<hr>";

// Test 8: Helper Functions
echo "<h2>8. Helper Functions</h2>";
$functions = ['isSessionValid', 'sanitizeOutput', 'sanitizeInput'];
foreach ($functions as $func) {
    echo "$func(): " . (function_exists($func) ? "✓ Exists" : "✗ Not found") . "<br>";
}
echo "<hr>";

// Test 9: File Permissions
echo "<h2>9. File Structure</h2>";
$files = [
    'php/register.php',
    'php/login.php',
    'php/reserve.php',
    'php/logout.php',
    'server.js',
    'sql/setup.sql'
];
foreach ($files as $file) {
    echo "$file: " . (file_exists($file) ? "✓ Exists" : "✗ Not found") . "<br>";
}
echo "<hr>";

// Summary
echo "<h2>Summary</h2>";
echo "<strong style='color: green;'>If all tests show ✓, your system is ready!</strong><br><br>";
echo "Next steps:<br>";
echo "<ol>";
echo "<li>If database tables are missing, run: <code>mysql -u root -p hotel_booking < sql/setup.sql</code></li>";
echo "<li>Start Node.js server: <code>npm install && npm start</code></li>";
echo "<li>Access the application: <a href='index.html'>index.html</a></li>";
echo "</ol>";

?>
