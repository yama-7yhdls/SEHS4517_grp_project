<?php
/**
 * Reservation Processing Handler
 * Hotel Booking System
 * 
 * This script handles reservation requests by:
 * - Validating user session
 * - Validating check-in/check-out dates
 * - Checking room availability
 * - Storing reservation in database
 * - Forwarding data to Node.js/Express server for confirmation page
 */

// Include database configuration
require_once __DIR__ . '/config.php';

// Set response header
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to generate unique booking reference
function generateBookingReference() {
    return 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

// Function to send data to Node.js server
function sendToNodeServer($bookingData) {
    $nodeServerUrl = 'http://localhost:3000/api/confirmation';
    
    $ch = curl_init($nodeServerUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bookingData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("Node.js server communication error: " . $error);
        return false;
    }
    
    if ($httpCode !== 200) {
        error_log("Node.js server returned HTTP " . $httpCode);
        return false;
    }
    
    return json_decode($response, true);
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', null, 405);
    }
    
    // Verify user session is valid
    if (!isSessionValid()) {
        sendResponse(false, 'Session expired or invalid. Please login again.', null, 401);
    }
    
    // Get user ID from session
    $userId = $_SESSION['user_id'];
    $userEmail = $_SESSION['email'];
    $userName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
    
    // Get JSON input
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);
    
    // Check if JSON is valid
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(false, 'Invalid JSON data', null, 400);
    }
    
    // Validate required fields
    $requiredFields = ['roomId', 'checkInDate', 'checkOutDate', 'adultsCount'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            sendResponse(false, 'Missing required field: ' . $field, null, 400);
        }
    }
    
    // Sanitize and extract inputs
    $roomId = (int)$data['roomId'];
    $checkInDate = sanitizeInput($data['checkInDate']);
    $checkOutDate = sanitizeInput($data['checkOutDate']);
    $adultsCount = (int)$data['adultsCount'];
    $childrenCount = isset($data['childrenCount']) ? (int)$data['childrenCount'] : 0;
    
    // Validate date formats
    $checkInDateTime = DateTime::createFromFormat('Y-m-d', $checkInDate);
    $checkOutDateTime = DateTime::createFromFormat('Y-m-d', $checkOutDate);
    
    if (!$checkInDateTime || !$checkOutDateTime) {
        sendResponse(false, 'Invalid date format. Use YYYY-MM-DD', null, 400);
    }
    
    // Validate check-in date is not in the past
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if ($checkInDateTime < $today) {
        sendResponse(false, 'Check-in date cannot be in the past', null, 400);
    }
    
    // Validate check-out date is after check-in date
    if ($checkOutDateTime <= $checkInDateTime) {
        sendResponse(false, 'Check-out date must be after check-in date', null, 400);
    }
    
    // Validate guest counts
    if ($adultsCount < 1) {
        sendResponse(false, 'At least one adult is required', null, 400);
    }
    
    if ($childrenCount < 0) {
        sendResponse(false, 'Invalid children count', null, 400);
    }
    
    // Get room details and verify it exists
    $roomStmt = $pdo->prepare("
        SELECT 
            ri.rm_id,
            ri.room_number,
            ri.price_per_night,
            rt.room_type_name,
            rt.max_occupancy,
            h.hotel_name,
            h.address as hotel_address
        FROM room_inventory ri
        JOIN room_type rt ON ri.room_type_id = rt.room_type_id
        JOIN hotels h ON ri.hotel_id = h.hotel_id
        WHERE ri.rm_id = ?
    ");
    
    $roomStmt->execute([$roomId]);
    $room = $roomStmt->fetch();
    
    if (!$room) {
        sendResponse(false, 'Room not found', null, 404);
    }
    
    // Verify guest count doesn't exceed room capacity
    $totalGuests = $adultsCount + $childrenCount;
    if ($totalGuests > $room['max_occupancy']) {
        sendResponse(false, "Room capacity exceeded. Maximum occupancy: {$room['max_occupancy']}", null, 400);
    }
    
    // Check room availability for the requested dates
    $availabilityStmt = $pdo->prepare("
        SELECT COUNT(*) as booking_count
        FROM bookings
        WHERE rm_id = ?
        AND status IN ('confirmed', 'pending')
        AND (
            (check_in_date <= ? AND check_out_date > ?)
            OR (check_in_date < ? AND check_out_date >= ?)
            OR (check_in_date >= ? AND check_out_date <= ?)
        )
    ");
    
    $availabilityStmt->execute([
        $roomId,
        $checkInDate, $checkInDate,
        $checkOutDate, $checkOutDate,
        $checkInDate, $checkOutDate
    ]);
    
    $availability = $availabilityStmt->fetch();
    
    if ($availability['booking_count'] > 0) {
        sendResponse(false, 'Room is not available for the selected dates', null, 409);
    }
    
    // Calculate total price
    $nights = $checkInDateTime->diff($checkOutDateTime)->days;
    $totalPrice = $nights * $room['price_per_night'];
    
    // Generate unique booking reference
    $bookingReference = generateBookingReference();
    
    // Insert booking record
    $insertStmt = $pdo->prepare("
        INSERT INTO bookings (
            booking_reference,
            user_id,
            rm_id,
            check_in_date,
            check_out_date,
            adults_count,
            children_count,
            total_price,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')
    ");
    
    $result = $insertStmt->execute([
        $bookingReference,
        $userId,
        $roomId,
        $checkInDate,
        $checkOutDate,
        $adultsCount,
        $childrenCount,
        $totalPrice
    ]);
    
    if (!$result) {
        sendResponse(false, 'Failed to create reservation. Please try again.', null, 500);
    }
    
    // Prepare booking data to send to Node.js server
    $bookingData = [
        'bookingReference' => $bookingReference,
        'userEmail' => $userEmail,
        'userName' => $userName,
        'hotelName' => $room['hotel_name'],
        'hotelAddress' => $room['hotel_address'],
        'roomType' => $room['room_type_name'],
        'roomNumber' => $room['room_number'],
        'checkInDate' => $checkInDate,
        'checkOutDate' => $checkOutDate,
        'nights' => $nights,
        'adultsCount' => $adultsCount,
        'childrenCount' => $childrenCount,
        'totalPrice' => number_format($totalPrice, 2),
        'createdAt' => date('Y-m-d H:i:s')
    ];
    
    // Send booking data to Node.js/Express server
    $nodeResponse = sendToNodeServer($bookingData);
    
    if ($nodeResponse === false) {
        // Log warning but don't fail the reservation
        error_log("Warning: Failed to send booking data to Node.js server");
    }
    
    // Return success response with booking details
    sendResponse(
        true,
        'Reservation created successfully!',
        [
            'bookingReference' => $bookingReference,
            'confirmationUrl' => 'http://localhost:3000/confirmation/' . $bookingReference
        ],
        201
    );
    
} catch (PDOException $e) {
    // Log error for debugging
    error_log('Reservation error: ' . $e->getMessage());
    
    sendResponse(false, 'Database error occurred. Please try again later.', null, 500);
    
} catch (Exception $e) {
    // Log error for debugging
    error_log('Reservation error: ' . $e->getMessage());
    
    sendResponse(false, 'An unexpected error occurred. Please try again.', null, 500);
}
?>
