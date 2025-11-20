<?php
/**
 * Database Configuration and Connection Setup
 * Hotel Booking System
 * 
 * This file establishes the database connection using PDO,
 * configures session management with 30-minute timeout,
 * and sets up error handling for the application.
 */

// Prevent direct access to this file
if (!defined('DB_CONFIG_LOADED')) {
    define('DB_CONFIG_LOADED', true);
}

// Database Configuration Constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session Configuration - 30 minute timeout (1800 seconds)
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.cookie_lifetime', 1800);
session_set_cookie_params(1800);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
try {
    // Create PDO instance with connection parameters
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        // Set error mode to exceptions for better error handling
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Return associative arrays by default
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Disable emulated prepared statements for true prepared statements
        PDO::ATTR_EMULATE_PREPARES => false,
        
        // Set persistent connection to false (better for shared hosting)
        PDO::ATTR_PERSISTENT => false
    ];
    
    // Create PDO connection
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Log error (in production, log to file instead of displaying)
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Display user-friendly error message
    die("Database connection failed. Please try again later.");
}

/**
 * Validate if user session is active and not expired
 * 
 * @return bool True if session is valid, false otherwise
 */
function isSessionValid() {
    // Check if user_id exists in session
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        return false;
    }
    
    // Check if session has expired (30 minutes of inactivity)
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        
        // If inactive for more than 30 minutes (1800 seconds)
        if ($inactive_time > 1800) {
            // Destroy session
            session_unset();
            session_destroy();
            return false;
        }
    }
    
    // Update last activity timestamp
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * Sanitize output to prevent XSS attacks
 * 
 * @param string $data The data to sanitize
 * @return string Sanitized data
 */
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and sanitize input data
 * 
 * @param string $data The data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Set timezone (adjust as needed)
date_default_timezone_set('UTC');

// Disable error display in production (enable for development)
// Uncomment the following line for production:
// ini_set('display_errors', 0);

// Enable error display for development (comment out for production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
