# Implementation Plan

- [x] 1. Set up project structure and database schema





  - Create directory structure: css/, js/, images/, php/, server/, sql/
  - Create database setup script (setup.sql) with all table definitions
  - Insert sample data for regions, room_types, hotels, and room_inventory
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [x] 2. Create database configuration and connection




  - [x] 2.1 Implement PHP database configuration file


    - Write php/config.php with PDO connection setup
    - Configure session management with 30-minute timeout
    - Set error handling mode for PDO
    - _Requirements: 7.5_
  
- [x] 3. Implement introduction page




  - [x] 3.1 Create HTML structure for introduction page


    - Write index.html with XHTML-compliant syntax
    - Add organization description section (max 600 words)
    - Create navigation buttons to registration and login pages
    - _Requirements: 6.1, 6.2, 6.3, 9.1, 9.2, 9.3, 9.4, 9.5_
  - [x] 3.2 Design and add custom logo


    - Create custom hotel logo image
    - Add logo to images/ directory and display on introduction page
    - _Requirements: 6.2_
  - [x] 3.3 Style introduction page


    - Write CSS for professional hotel theme with hero image
    - Implement responsive layout
    - Style navigation buttons
    - _Requirements: 6.1_

- [x] 4. Implement user registration functionality






  - [x] 4.1 Create registration page HTML

    - Write register.html with form fields: last name, first name, address, phone, email, password
    - Add Register, Clear, and Back to Home buttons
    - Ensure XHTML compliance
    - _Requirements: 1.1, 1.5, 6.4, 9.1, 9.2, 9.3, 9.4, 9.5_

  - [x] 4.2 Implement client-side validation for registration

    - Write js/register.js with jQuery validation
    - Validate all required fields, email format, phone format (10 digits)
    - Validate password strength (min 8 chars, 1 uppercase, 1 number)
    - Implement Clear button functionality
    - _Requirements: 1.2, 7.3_


  - [x] 4.3 Implement server-side registration processing





    - Write php/register.php to handle form submission
    - Validate POST data on server side
    - Hash password using password_hash() with bcrypt
    - Insert user record using prepared statements
    - Handle duplicate email constraint violation
    - Return JSON response with success/error message
    - _Requirements: 1.2, 1.3, 1.4, 1.6, 7.1, 7.2, 7.4_
  - [ ]* 4.4 Write unit tests for registration
    - Test password hashing functionality
    - Test duplicate email handling
    - Test input validation
    - _Requirements: 1.4, 1.6, 7.4_

- [x] 5. Implement user authentication functionality





  - [x] 5.1 Create login page HTML


    - Write login.html with email and password fields
    - Add Login and Back to Home buttons
    - Ensure XHTML compliance
    - _Requirements: 2.1, 6.4, 9.1, 9.2, 9.3, 9.4, 9.5_
  - [x] 5.2 Implement client-side validation for login


    - Write js/login.js with jQuery validation
    - Validate email format and password not empty
    - _Requirements: 2.2, 7.3_
  - [x] 5.3 Implement server-side login processing


    - Write php/login.php to handle authentication
    - Query user by email using prepared statements
    - Verify password with password_verify()
    - Create PHP session with user_id and email on success
    - Return JSON response with success/error and redirect URL
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 7.1, 7.4_
  - [x] 5.4 Create login failure page


    - Generate HTML response for invalid credentials
    - Display "sorry, login failed" message
    - Add button to return to introduction page
    - _Requirements: 2.4_
  - [ ]* 5.5 Write unit tests for authentication
    - Test password verification logic
    - Test session creation
    - Test invalid credential handling
    - _Requirements: 2.3, 2.4, 2.5_

- [ ] 6. Implement reservation page structure and search functionality
  - [ ] 6.1 Create reservation page HTML
    - Write reserve.html with search criteria section
    - Add date pickers for check-in and check-out dates
    - Add number inputs for adults (min 1) and children (min 0)
    - Add region filter dropdown
    - Add containers for dynamic hotel and room lists
    - Add Reserve, Clear, and Cancel buttons
    - Ensure XHTML compliance
    - _Requirements: 3.1, 3.2, 4.1, 4.8, 4.9, 9.1, 9.2, 9.3, 9.4, 9.5_
  - [ ] 6.2 Implement client-side date and occupancy validation
    - Write js/reserve.js with date validation logic
    - Validate check-out date is at least 1 day after check-in
    - Set minimum date for check-in to today
    - Implement Clear button to reset search criteria
    - Implement Cancel button to redirect to introduction page
    - _Requirements: 3.3, 4.8, 4.9, 7.3_
  - [ ] 6.3 Implement session validation
    - Add session check at top of reserve.html
    - Redirect to login page if session is invalid or expired
    - _Requirements: 2.5, 7.5_

- [ ] 7. Implement hotel search functionality
  - [ ] 7.1 Create endpoint to fetch hotels by region
    - Write php/get-hotels.php to handle AJAX requests
    - Validate session before processing
    - Query hotels in selected region with availability subquery
    - Use prepared statements to prevent SQL injection
    - Calculate available room count excluding overlapping bookings
    - Return JSON array of hotels with available_count > 0
    - _Requirements: 3.4, 3.5, 3.6, 7.1, 7.5_
  - [ ] 7.2 Implement client-side hotel display
    - Add AJAX call to get-hotels.php in js/reserve.js
    - Dynamically generate hotel cards with name, address, available count
    - Add "View Rooms" button for each hotel
    - Handle AJAX errors with user-friendly messages
    - _Requirements: 3.4, 3.6_
  - [ ]* 7.3 Write unit tests for hotel search
    - Test availability calculation logic
    - Test date overlap detection
    - Test region filtering
    - _Requirements: 3.5, 3.6_

- [ ] 8. Implement room selection functionality
  - [ ] 8.1 Create endpoint to fetch available rooms
    - Write php/get-rooms.php to handle AJAX requests
    - Validate session before processing
    - Query room_inventory with room_type details for selected hotel
    - Exclude rooms with overlapping confirmed bookings
    - Use prepared statements
    - Return JSON array of available rooms with details
    - _Requirements: 4.1, 7.1, 7.5_
  - [ ] 8.2 Implement client-side room display and selection
    - Add AJAX call to get-rooms.php when user clicks "View Rooms"
    - Dynamically generate room cards with type, number, occupancy, price
    - Calculate and display total price (price_per_night × nights)
    - Validate guest count against max_occupancy before enabling Reserve button
    - Handle AJAX errors
    - _Requirements: 4.1, 4.2_
  - [ ]* 8.3 Write unit tests for room availability
    - Test room filtering logic
    - Test occupancy validation
    - Test price calculation
    - _Requirements: 4.1, 4.2_

- [ ] 9. Implement booking reservation with transaction
  - [ ] 9.1 Create booking reservation endpoint
    - Write php/reserve.php to handle reservation submission
    - Validate session and POST data
    - Calculate nights and total_price
    - Generate unique booking_reference (BK + YYYYMMDD + sequence)
    - Start database transaction
    - Lock selected room with SELECT FOR UPDATE
    - Check for conflicting bookings
    - Validate guest count against max_occupancy
    - Insert booking record with status='confirmed'
    - Commit transaction on success, rollback on failure
    - Use prepared statements for all queries
    - _Requirements: 4.2, 4.3, 4.4, 4.5, 4.6, 7.1_
  - [ ] 9.2 Implement forward to Express server
    - Add cURL POST request to Express server in reserve.php
    - Send booking data as JSON (email, booking_reference, hotel details, dates, guests, price)
    - Return Express-generated HTML to client
    - Handle connection errors to Express server
    - _Requirements: 4.7_
  - [ ] 9.3 Handle booking errors
    - Implement error handling for overbooking scenario
    - Return user-friendly error messages for validation failures
    - Log errors for debugging
    - _Requirements: 4.4, 4.5_
  - [ ]* 9.4 Write unit tests for booking logic
    - Test booking reference generation
    - Test price calculation
    - Test transaction rollback on conflicts
    - _Requirements: 4.3, 4.4, 4.6_

- [ ] 10. Implement Express confirmation server
  - [ ] 10.1 Set up Express server
    - Create server/package.json with express and ejs dependencies
    - Write server/confirmation.js with Express setup
    - Configure EJS as view engine
    - Set views directory to server/views
    - Add JSON body parser middleware
    - _Requirements: 5.1_
  - [ ] 10.2 Create confirmation route
    - Implement POST /confirmation route
    - Extract booking data from request body
    - Render confirmation.ejs template with booking data
    - Add error handling middleware
    - _Requirements: 5.1, 5.2_
  - [ ] 10.3 Create confirmation page template
    - Write server/views/confirmation.ejs
    - Display thank you message
    - Show user email and all reservation details (booking reference, hotel, room, dates, guests, price)
    - Add OK button to redirect to introduction page
    - Ensure XHTML compliance
    - _Requirements: 5.2, 5.3, 9.1, 9.2, 9.3, 9.4, 9.5_
  - [ ]* 10.4 Write integration tests for Express server
    - Test POST /confirmation route
    - Test template rendering with sample data
    - Test error handling
    - _Requirements: 5.1, 5.2_

- [ ] 11. Implement security measures
  - [ ] 11.1 Add input sanitization
    - Implement htmlspecialchars() for all output in PHP
    - Sanitize user inputs before display
    - Add XSS prevention in JavaScript
    - _Requirements: 7.2_
  - [ ] 11.2 Verify prepared statements usage
    - Review all PHP files to ensure no raw SQL queries
    - Confirm all queries use PDO prepared statements with bound parameters
    - _Requirements: 7.1_
  - [ ] 11.3 Implement session timeout enforcement
    - Add session validity checks in all protected endpoints
    - Return appropriate error when session expires
    - _Requirements: 7.5_
  - [ ]* 11.4 Perform security testing
    - Test SQL injection attempts on all inputs
    - Test XSS attempts with script tags
    - Test session timeout behavior
    - _Requirements: 7.1, 7.2, 7.5_

- [ ] 12. Create styling and responsive design
  - [ ] 12.1 Implement base CSS styles
    - Write css/style.css with professional hotel theme
    - Style all form elements consistently
    - Create button styles for primary and secondary actions
    - Style error messages and validation feedback
    - _Requirements: 6.1_
  - [ ] 12.2 Implement responsive layout
    - Write css/responsive.css with media queries
    - Ensure mobile-friendly layouts for all pages
    - Test on various screen sizes
    - _Requirements: 6.1_
  - [ ] 12.3 Add visual assets
    - Create or source hero image for introduction page
    - Add icons for navigation and actions
    - Optimize images for web
    - _Requirements: 6.1, 6.2_

- [ ] 13. Implement common JavaScript utilities
  - [ ] 13.1 Create shared utility functions
    - Write js/common.js with reusable functions
    - Implement date formatting utilities
    - Add AJAX error handling helper
    - Create form validation helper functions
    - _Requirements: 7.3_

- [ ] 14. Set up Apache and Node.js servers
  - [ ] 14.1 Configure Apache virtual host
    - Create virtual host configuration for the application
    - Set document root and directory permissions
    - Enable mod_rewrite if needed
    - _Requirements: Project Implementation_
  - [ ] 14.2 Create .htaccess file
    - Add security headers (X-Content-Type-Options, X-Frame-Options, X-XSS-Protection)
    - Configure URL rewriting rules if needed
    - _Requirements: 7.2_
  - [ ] 14.3 Start Node.js server
    - Install Express and EJS dependencies (npm install)
    - Start Express server on port 3000
    - Verify server is accessible from PHP
    - _Requirements: Project Implementation_

- [ ] 15. Perform integration and functional testing
  - [ ] 15.1 Test complete user flow
    - Test registration → login → search → booking → confirmation flow
    - Verify all data is correctly stored and displayed
    - Test navigation between all pages
    - _Requirements: All requirements_
  - [ ] 15.2 Test error scenarios
    - Test duplicate email registration
    - Test invalid login attempts
    - Test booking unavailable room
    - Test session timeout during booking
    - _Requirements: 1.6, 2.4, 7.5_
  - [ ]* 15.3 Perform concurrency testing
    - Simulate multiple users booking same room simultaneously
    - Verify only one booking succeeds
    - Confirm transaction isolation works correctly
    - _Requirements: 4.4, 4.5_
  - [ ]* 15.4 Perform load testing
    - Simulate 50-100 concurrent users
    - Measure response times for search and booking
    - Identify performance bottlenecks
    - _Requirements: Performance considerations_

- [ ] 16. Final validation and deployment preparation
  - [ ] 16.1 Validate XHTML compliance
    - Run HTML validator on all pages
    - Fix any syntax errors
    - Verify self-closing tags, lowercase elements, quoted attributes
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_
  - [ ] 16.2 Review and optimize database queries
    - Use EXPLAIN on complex queries
    - Add indexes where needed for performance
    - Verify foreign key constraints are working
    - _Requirements: 8.2, 8.3, 8.4, 8.5_
  - [ ] 16.3 Create deployment documentation
    - Document installation steps for Apache, PHP, MySQL, Node.js
    - Document database setup procedure
    - Document server configuration requirements
    - _Requirements: Project Implementation_
