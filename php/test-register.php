<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: Testing basic PHP...\n";
echo "PHP Version: " . phpversion() . "\n\n";

echo "Step 2: Testing config.php path...\n";
$configPath = __DIR__ . '/config.php';
echo "Config path: $configPath\n";
echo "File exists: " . (file_exists($configPath) ? "Yes" : "No") . "\n\n";

if (file_exists($configPath)) {
    echo "Step 3: Loading config.php...\n";
    try {
        require_once $configPath;
        echo "✓ Config loaded successfully\n";
        echo "✓ PDO exists: " . (isset($pdo) ? "Yes" : "No") . "\n\n";
        
        if (isset($pdo)) {
            echo "Step 4: Testing database query...\n";
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch();
            echo "✓ Users table has " . $result['count'] . " records\n\n";
            
            echo "Step 5: Testing registration logic...\n";
            $testEmail = "test_" . time() . "@example.com";
            
            // Hash password
            $hash = password_hash("Password123", PASSWORD_BCRYPT);
            echo "✓ Password hashed\n";
            
            // Insert test user
            $stmt = $pdo->prepare("INSERT INTO users (last_name, first_name, address, phone, email, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute(['Test', 'User', '123 Test St', '1234567890', $testEmail, $hash]);
            
            if ($result) {
                echo "✓ Test user inserted successfully (ID: " . $pdo->lastInsertId() . ")\n";
                echo "✓ Email: $testEmail\n\n";
                echo "=== ALL TESTS PASSED ===\n";
                echo "Registration should work now. Try the form again.\n";
            } else {
                echo "✗ Failed to insert test user\n";
            }
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
} else {
    echo "✗ Config file not found!\n";
}
?>
