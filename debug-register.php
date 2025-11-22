<?php
/**
 * Debug script for register.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing register.php</h2>";

echo "<h3>1. Loading config.php</h3>";
try {
    require_once 'config.php';
    echo "✓ config.php loaded<br>";
    echo "✓ PDO connection exists: " . (isset($pdo) ? "Yes" : "No") . "<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    die();
}

echo "<h3>2. Testing with sample data</h3>";

$testData = [
    'lastName' => 'Test',
    'firstName' => 'User',
    'address' => '123 Test Street',
    'phone' => '1234567890',
    'email' => 'test' . time() . '@example.com',
    'password' => 'Password123'
];

echo "Sample data: " . json_encode($testData) . "<br><br>";

// Simulate the registration process
try {
    // Check if email exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->execute([$testData['email']]);
    
    if ($checkStmt->fetch()) {
        echo "✗ Email already exists<br>";
    } else {
        echo "✓ Email is available<br>";
        
        // Hash password
        $passwordHash = password_hash($testData['password'], PASSWORD_BCRYPT);
        echo "✓ Password hashed<br>";
        
        // Insert user
        $insertStmt = $pdo->prepare("
            INSERT INTO users (last_name, first_name, address, phone, email, password_hash)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $result = $insertStmt->execute([
            $testData['lastName'],
            $testData['firstName'],
            $testData['address'],
            $testData['phone'],
            $testData['email'],
            $passwordHash
        ]);
        
        if ($result) {
            echo "✓ User inserted successfully!<br>";
            echo "✓ User ID: " . $pdo->lastInsertId() . "<br>";
        } else {
            echo "✗ Insert failed<br>";
        }
    }
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "<br>";
}

echo "<h3>3. Summary</h3>";
echo "If all checks passed, register.php should work. Try registering through the form now.";
?>
