# Server-Side Functions blueprint

## Overview
This document summarizes the server-side functions required for the hotel booking reservation system based on the project specification.

## PHP Functions Required: 3

### 1. User Registration Handler (`register.php`)
- **Purpose**: Process member registration form submission
- **Inputs**: 
  - Last name
  - First name
  - Mailing address
  - Contact phone number
  - Email address
  - Password
- **Processing**:
  - Validate user input
  - Hash password for security
  - Store user data in MySQL `users` table
- **Output**: Success/error response to client

### 2. Login Authentication Handler (`login.php`)
- **Purpose**: Validate user credentials for system access
- **Inputs**:
  - Email address
  - Password
- **Processing**:
  - Query MySQL database to verify email and password
  - Check if credentials match
- **Output**:
  - If incorrect: Generate "login failed" page with error message
  - If correct: Generate/redirect to reservation page (4th web page)

### 3. Reservation Processing Handler (`reserve.php`)
- **Purpose**: Process facility reservation and forward to Node.js server
- **Inputs**:
  - Date and time slot
  - Chosen facility item (room/facility ID)
  - User information
- **Processing**:
  - Store reservation information in MySQL `bookings` table
  - Forward reservation data to Node.js/Express server via HTTP request
- **Output**: Trigger Node.js response generation

## Node.js/Express Functions Required: 1

### 1. Reservation Confirmation Handler (Express route)
- **Purpose**: Generate "thank you" confirmation page
- **Inputs**: 
  - User email address
  - Reservation details (date, time, facility)
- **Processing**:
  - Receive data from PHP reservation handler
  - Format confirmation message
- **Output**: Generate 5th web page showing:
  - "Thank you" message
  - User's email address
  - Reservation information
  - "OK" button to return to homepage

## Total Server-Side Functions: 4
- **PHP**: 3 functions
- **Node.js/Express**: 1 function

## Technology Stack
- **Frontend**: HTML5 (XHTML syntax), JavaScript, jQuery
- **Backend**: PHP, Node.js with Express.js
- **Database**: MySQL
- **Web Server**: Apache (for PHP), Node.js server (for Express)
