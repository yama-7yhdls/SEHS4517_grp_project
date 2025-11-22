<?php
// Include database configuration
require_once __DIR__ . '/config.php';

// Set response header
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Function to validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone format (10 digits)
function validatePhone($phone) {
    return preg_match('/^\d{10}$/', $phone);
}

// Function to validate password strength
function validatePassword($password) {
    // Min 8 chars, at least 1 uppercase, at least 1 number
    return preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', 405);
    }
    
    // Get JSON input
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);
    
    // Check if JSON is valid
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(false, 'Invalid JSON data', 400);
    }
    
    // Validate required fields
    $requiredFields = ['lastName', 'firstName', 'address', 'phone', 'email', 'password'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            sendResponse(false, 'All fields are required', 400);
        }
    }
    
    // Sanitize inputs
    $lastName = sanitizeInput($data['lastName']);
    $firstName = sanitizeInput($data['firstName']);
    $address = sanitizeInput($data['address']);
    $phone = sanitizeInput($data['phone']);
    $email = sanitizeInput($data['email']);
    $password = $data['password']; // Don't sanitize password, will be hashed
    
    // Validate email format
    if (!validateEmail($email)) {
        sendResponse(false, 'Invalid email format', 400);
    }
    
    // Validate phone format
    if (!validatePhone($phone)) {
        sendResponse(false, 'Phone must be exactly 10 digits', 400);
    }
    
    // Validate password strength
    if (!validatePassword($password)) {
        sendResponse(false, 'Password must be at least 8 characters with 1 uppercase and 1 number', 400);
    }
    
    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetch()) {
        sendResponse(false, 'Email already registered. Please login.', 409);
    }
    
    // Hash password using bcrypt
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user record using prepared statement
    $insertStmt = $pdo->prepare("
        INSERT INTO users (last_name, first_name, address, phone, email, password_hash)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $result = $insertStmt->execute([
        $lastName,
        $firstName,
        $address,
        $phone,
        $email,
        $passwordHash
    ]);
    
    if ($result) {
        sendResponse(true, 'Registration successful! Redirecting to login...', 201);
    } else {
        sendResponse(false, 'Registration failed. Please try again.', 500);
    }
    
} catch (PDOException $e) {
    // Check for duplicate email constraint violation
    if ($e->getCode() == 23000) {
        sendResponse(false, 'Email already registered. Please login.', 409);
    }
    
    // Log error for debugging (in production, log to file)
    error_log('Registration error: ' . $e->getMessage());
    
    sendResponse(false, 'Database error occurred. Please try again later.', 500);
    
} catch (Exception $e) {
    // Log error for debugging
    error_log('Registration error: ' . $e->getMessage());
    
    sendResponse(false, 'An unexpected error occurred. Please try again.', 500);
}
?>
