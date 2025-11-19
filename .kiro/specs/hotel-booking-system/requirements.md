# Requirements Document

## Introduction

This document specifies the requirements for a web-based hotel room booking system. The system enables users to register, log in, search for available hotel rooms based on date ranges and regions, make reservations, and receive booking confirmations. The system consists of five interconnected web pages with client-side validation and server-side processing using PHP and Node.js/Express with MySQL database storage.

## Glossary

- **Booking System**: The complete hotel room reservation web application
- **User**: A person who registers and makes hotel room reservations
- **Reservation**: A confirmed booking of a specific hotel room for a date range
- **Booking Reference**: A unique identifier assigned to each reservation (format: BK + YYYYMMDD + sequence number)
- **Room Inventory**: The collection of all available hotel rooms across all properties
- **Region**: A geographical area containing one or more hotels
- **Check-in Date**: The date when the guest arrives at the hotel
- **Check-out Date**: The date when the guest departs from the hotel
- **Occupancy**: The number of guests (adults and children) staying in a room
- **Available Room**: A room with no overlapping confirmed bookings for the requested date range
- **Session**: An authenticated user's active connection to the system (30-minute timeout)

## Requirements

### Requirement 1: User Registration

**User Story:** As a new visitor, I want to register for an account, so that I can make hotel room reservations.

#### Acceptance Criteria

1. WHEN a user accesses the registration page, THE Booking System SHALL display input fields for last name, first name, mailing address, contact phone number, email address, and password.
2. WHEN a user clicks the "Register" button, THE Booking System SHALL validate that all required fields contain data.
3. WHEN a user clicks the "Register" button with valid data, THE Booking System SHALL send the registration data to a PHP program using prepared statements.
4. WHEN the PHP program receives valid registration data, THE Booking System SHALL hash the password using bcrypt and store the user record in the MySQL users table.
5. WHEN a user clicks the "Clear" button, THE Booking System SHALL reset all input fields to empty values.
6. THE Booking System SHALL prevent registration with duplicate email addresses by enforcing a unique constraint on the email field.

### Requirement 2: User Authentication

**User Story:** As a registered user, I want to log in with my credentials, so that I can access the reservation functionality.

#### Acceptance Criteria

1. WHEN a user accesses the login page, THE Booking System SHALL display input fields for email address and password.
2. WHEN a user submits login credentials, THE Booking System SHALL send the credentials to a PHP program for validation.
3. WHEN the PHP program receives login credentials, THE Booking System SHALL verify the email and password against the MySQL database using prepared statements and password verification.
4. IF the credentials are invalid, THEN THE Booking System SHALL display a failure message with a button to return to the introduction page.
5. WHEN credentials are valid, THE Booking System SHALL create a session with a 30-minute timeout and display the reservation page.

### Requirement 3: Hotel Search and Room Availability

**User Story:** As an authenticated user, I want to search for available hotel rooms by date range and region, so that I can find suitable accommodations.

#### Acceptance Criteria

1. WHEN a user accesses the reservation page, THE Booking System SHALL display a date range picker for check-in and check-out dates.
2. WHEN a user accesses the reservation page, THE Booking System SHALL display input fields for number of adults (minimum 1) and number of children (minimum 0).
3. WHEN a user selects dates and guest counts, THE Booking System SHALL validate that the check-out date is at least one day after the check-in date.
4. WHEN a user selects a region filter, THE Booking System SHALL query the PHP program to retrieve hotels in that region with available rooms for the specified date range.
5. WHEN the PHP program calculates availability, THE Booking System SHALL exclude rooms with overlapping confirmed bookings using the formula: NOT (check_out_date <= check_in OR check_in_date >= check_out).
6. WHEN hotels are displayed, THE Booking System SHALL show only hotels with at least one available room.

### Requirement 4: Room Selection and Reservation

**User Story:** As an authenticated user, I want to select a specific room and complete my reservation, so that I can secure my accommodation.

#### Acceptance Criteria

1. WHEN a user selects a hotel, THE Booking System SHALL display available rooms with room type, room number, maximum occupancy, and price per night.
2. WHEN a user selects a room, THE Booking System SHALL validate that the total guest count does not exceed the room's maximum occupancy.
3. WHEN a user clicks the "Reserve" button, THE Booking System SHALL calculate the total price as price_per_night multiplied by the number of nights.
4. WHEN the reservation is submitted, THE Booking System SHALL use a database transaction with SELECT FOR UPDATE to lock the room record and prevent overbooking.
5. WHEN the room is locked, THE Booking System SHALL verify no conflicting bookings exist before inserting the reservation record.
6. WHEN the reservation is confirmed, THE Booking System SHALL generate a unique booking reference in the format BK + YYYYMMDD + sequence number.
7. WHEN the reservation is stored, THE Booking System SHALL send the user's email address and reservation details to the Node.js Express server.
8. WHEN a user clicks the "Clear" button, THE Booking System SHALL reset the date and guest count inputs.
9. WHEN a user clicks the "Cancel" button, THE Booking System SHALL redirect to the introduction page.

### Requirement 5: Booking Confirmation

**User Story:** As a user who completed a reservation, I want to see my booking confirmation, so that I have proof of my reservation.

#### Acceptance Criteria

1. WHEN the Express server receives reservation data, THE Booking System SHALL generate a confirmation page displaying a thank you message.
2. WHEN the confirmation page is displayed, THE Booking System SHALL show the user's email address and complete reservation details including booking reference, hotel name, room details, dates, guest counts, and total price.
3. WHEN a user clicks the "OK" button on the confirmation page, THE Booking System SHALL redirect to the introduction page.

### Requirement 6: Introduction and Navigation

**User Story:** As a visitor, I want to view information about the hotel organization and navigate to registration or login, so that I can understand the service and access it.

#### Acceptance Criteria

1. WHEN a user accesses the introduction page, THE Booking System SHALL display an organization description not exceeding 600 words.
2. WHEN a user accesses the introduction page, THE Booking System SHALL display a custom logo for the organization.
3. THE Booking System SHALL provide navigation links or buttons to access the registration page and login page from the introduction page.
4. THE Booking System SHALL provide navigation links to return to the introduction page from the registration and login pages.

### Requirement 7: Data Security and Validation

**User Story:** As a system administrator, I want all user inputs to be validated and sanitized, so that the system is protected from security vulnerabilities.

#### Acceptance Criteria

1. THE Booking System SHALL use prepared statements for all SQL queries to prevent SQL injection attacks.
2. THE Booking System SHALL sanitize all user inputs and outputs to prevent cross-site scripting (XSS) attacks.
3. THE Booking System SHALL perform validation on both client-side (JavaScript/jQuery) and server-side (PHP).
4. THE Booking System SHALL store passwords using bcrypt hashing with password_hash function.
5. THE Booking System SHALL enforce session timeout after 30 minutes of inactivity.

### Requirement 8: Database Schema Compliance

**User Story:** As a database administrator, I want the database schema to follow third normal form, so that data integrity is maintained.

#### Acceptance Criteria

1. THE Booking System SHALL maintain separate tables for users, regions, hotels, room_type, room_inventory, and bookings.
2. THE Booking System SHALL enforce foreign key relationships between hotels and regions, room_inventory and hotels, room_inventory and room_type, and bookings and users.
3. THE Booking System SHALL enforce a unique constraint on the email field in the users table.
4. THE Booking System SHALL enforce a unique constraint on the booking_reference field in the bookings table.
5. THE Booking System SHALL store each physical room as a separate row in the room_inventory table with a unique rm_id.

### Requirement 9: XHTML Compliance

**User Story:** As a web developer, I want all HTML to follow XHTML syntax rules, so that the markup is standards-compliant.

#### Acceptance Criteria

1. THE Booking System SHALL use self-closing tags for void elements (e.g., `<br />`, `<img />`).
2. THE Booking System SHALL use lowercase for all HTML element names and attributes.
3. THE Booking System SHALL quote all attribute values.
4. THE Booking System SHALL properly nest all HTML elements.
5. THE Booking System SHALL declare the HTML5 doctype at the beginning of each page.
