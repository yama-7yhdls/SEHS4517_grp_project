<?php
/**
 * User Logout Handler
 * Hotel Booking System
 * 
 * This script handles user logout by:
 * - Destroying the PHP session
 * - Clearing session cookies
 * - Redirecting to homepage
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set response header
header('Content-Type: application/json');

try {
    // Unset all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    // Send success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'redirect' => 'index.html'
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Logout error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Logout failed. Please try again.'
    ]);
}
?>
