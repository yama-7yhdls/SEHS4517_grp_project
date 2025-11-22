/**
 * Node.js Express Server
 * Hotel Booking System - Confirmation Handler
 * 
 * This Express.js server handles reservation confirmations by:
 * - Receiving booking data from PHP
 * - Storing confirmation data temporarily
 * - Generating HTML confirmation page (5th web page)
 * - Serving the confirmation page to users
 */

const express = require('express');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// In-memory storage for booking confirmations (use database in production)
const bookingConfirmations = new Map();

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static('public'));

// Request logging middleware
app.use((req, res, next) => {
    console.log(`[${new Date().toISOString()}] ${req.method} ${req.url}`);
    next();
});

/**
 * API Endpoint: Receive booking confirmation from PHP
 * POST /api/confirmation
 */
app.post('/api/confirmation', (req, res) => {
    try {
        const bookingData = req.body;
        
        // Validate required fields
        const requiredFields = [
            'bookingReference',
            'userEmail',
            'userName',
            'hotelName',
            'roomType',
            'checkInDate',
            'checkOutDate',
            'totalPrice'
        ];
        
        for (const field of requiredFields) {
            if (!bookingData[field]) {
                return res.status(400).json({
                    success: false,
                    message: `Missing required field: ${field}`
                });
            }
        }
        
        // Store booking confirmation data
        bookingConfirmations.set(bookingData.bookingReference, {
            ...bookingData,
            timestamp: new Date().toISOString()
        });
        
        console.log(`✓ Booking confirmation received: ${bookingData.bookingReference}`);
        
        // Return success response
        res.status(200).json({
            success: true,
            message: 'Booking confirmation received',
            confirmationUrl: `/confirmation/${bookingData.bookingReference}`
        });
        
    } catch (error) {
        console.error('Error processing confirmation:', error);
        res.status(500).json({
            success: false,
            message: 'Failed to process booking confirmation'
        });
    }
});

/**
 * Web Page: Display booking confirmation (5th web page)
 * GET /confirmation/:bookingReference
 */
app.get('/confirmation/:bookingReference', (req, res) => {
    const { bookingReference } = req.params;
    
    // Retrieve booking data
    const booking = bookingConfirmations.get(bookingReference);
    
    if (!booking) {
        return res.status(404).send(`
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Booking Not Found</title>
                <link rel="stylesheet" href="/css/style.css" />
            </head>
            <body>
                <div class="container">
                    <h1>Booking Not Found</h1>
                    <p>The booking reference you're looking for does not exist.</p>
                    <button onclick="window.location.href='/'">Go to Homepage</button>
                </div>
            </body>
            </html>
        `);
    }
    
    // Generate confirmation HTML page
    const confirmationHTML = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Booking Confirmation - Hotel Booking System</title>
            <link rel="stylesheet" href="/css/style.css" />
        </head>
        <body>
            <div class="confirmation-container">
                <div class="success-icon">✓</div>
                
                <div class="confirmation-header">
                    <h1>Thank You for Your Reservation!</h1>
                    <p>Your booking has been confirmed successfully.</p>
                </div>
                
                <div class="booking-ref">
                    Booking Reference: ${booking.bookingReference}
                </div>
                
                <div class="booking-details">
                    <div class="detail-row">
                        <span class="detail-label">Guest Name:</span>
                        <span class="detail-value">${booking.userName}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">${booking.userEmail}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Hotel:</span>
                        <span class="detail-value">${booking.hotelName}</span>
                    </div>
                    
                    ${booking.hotelAddress ? `
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">${booking.hotelAddress}</span>
                    </div>
                    ` : ''}
                    
                    <div class="detail-row">
                        <span class="detail-label">Room Type:</span>
                        <span class="detail-value">${booking.roomType}</span>
                    </div>
                    
                    ${booking.roomNumber ? `
                    <div class="detail-row">
                        <span class="detail-label">Room Number:</span>
                        <span class="detail-value">${booking.roomNumber}</span>
                    </div>
                    ` : ''}
                    
                    <div class="detail-row">
                        <span class="detail-label">Check-in Date:</span>
                        <span class="detail-value">${formatDate(booking.checkInDate)}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Check-out Date:</span>
                        <span class="detail-value">${formatDate(booking.checkOutDate)}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Number of Nights:</span>
                        <span class="detail-value">${booking.nights}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Guests:</span>
                        <span class="detail-value">
                            ${booking.adultsCount} Adult${booking.adultsCount > 1 ? 's' : ''}
                            ${booking.childrenCount > 0 ? `, ${booking.childrenCount} Child${booking.childrenCount > 1 ? 'ren' : ''}` : ''}
                        </span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Total Price:</span>
                        <span class="detail-value total-price">$${booking.totalPrice}</span>
                    </div>
                </div>
                
                <p class="confirmation-note">
                    A confirmation email has been sent to <strong>${booking.userEmail}</strong>
                </p>
                
                <a href="/" class="btn-ok">OK</a>
            </div>
        </body>
        </html>
    `;
    
    res.send(confirmationHTML);
});

/**
 * Helper function to format dates
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

/**
 * Health check endpoint
 */
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        timestamp: new Date().toISOString(),
        activeBookings: bookingConfirmations.size
    });
});

/**
 * Homepage redirect
 */
app.get('/', (req, res) => {
    res.redirect('/index.html');
});

/**
 * 404 handler
 */
app.use((req, res) => {
    res.status(404).json({
        success: false,
        message: 'Endpoint not found'
    });
});

/**
 * Error handler
 */
app.use((err, req, res, next) => {
    console.error('Server error:', err);
    res.status(500).json({
        success: false,
        message: 'Internal server error'
    });
});

/**
 * Start server
 */
app.listen(PORT, () => {
    console.log('='.repeat(50));
    console.log(`Hotel Booking System - Node.js Server`);
    console.log('='.repeat(50));
    console.log(`Server running on: http://localhost:${PORT}`);
    console.log(`Confirmation API: http://localhost:${PORT}/api/confirmation`);
    console.log(`Health check: http://localhost:${PORT}/health`);
    console.log('='.repeat(50));
});

// Graceful shutdown
process.on('SIGINT', () => {
    console.log('\nShutting down server gracefully...');
    process.exit(0);
});
