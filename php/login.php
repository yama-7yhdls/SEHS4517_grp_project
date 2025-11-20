<?php
/**
 * User Login Authentication
 * Hotel Booking System
 * 
 * This script handles user authentication by:
 * - Validating email and password
 * - Querying user from database using prepared statements
 * - Verifying password with password_verify()
 * - Creating PHP session on successful authentication
 * - Returning JSON response with success/error and redirect URL
 */

// Include database configuration
require_once 'config.php';

// Set response header
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message, $redirect = null, $httpCode = 200) {
    http_response_code($httpCode);
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($redirect !== null) {
        $response['redirect'] = $redirect;
    }
    
    echo json_encode($response);
    exit;
}

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', null, 405);
    }
    
    // Get JSON input
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);
    
    // Check if JSON is valid
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(false, 'Invalid JSON data', null, 400);
    }
    
    // Validate required fields
    if (!isset($data['email']) || empty(trim($data['email']))) {
        sendResponse(false, 'Email address is required', null, 400);
    }
    
    if (!isset($data['password']) || empty($data['password'])) {
        sendResponse(false, 'Password is required', null, 400);
    }
    
    // Sanitize inputs
    $email = sanitizeInput($data['email']);
    $password = $data['password']; // Don't sanitize password
    
    // Validate email format
    if (!validateEmail($email)) {
        sendResponse(false, 'Invalid email format', null, 400);
    }
    
    // Query user by email using prepared statement
    $stmt = $pdo->prepare("
        SELECT user_id, email, password_hash, first_name, last_name
        FROM users
        WHERE email = ?
    ");
    
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Check if user exists
    if (!$user) {
        sendResponse(false, 'Invalid email or password', null, 401);
    }
    
    // Verify password with password_verify()
    if (!password_verify($password, $user['password_hash'])) {
        sendResponse(false, 'Invalid email or password', null, 401);
    }
    
    // Password is correct - create PHP session
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);
    
    // Store user information in session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['last_activity'] = time();
    
    // Send success response with redirect URL
    sendResponse(
        true,
        'Login successful! Redirecting to reservation page...',
        'reserve.html',
        200
    );
    
} catch (PDOException $e) {
    // Log error for debugging (in production, log to file)
    error_log('Login error: ' . $e->getMessage());
    
    sendResponse(false, 'Database error occurred. Please try again later.', null, 500);
    
} catch (Exception $e) {
    // Log error for debugging
    error_log('Login error: ' . $e->getMessage());
    
    sendResponse(false, 'An unexpected error occurred. Please try again.', null, 500);
}
?>
