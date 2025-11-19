-- Hotel Booking System Database Setup Script
-- Drop existing database if it exists and create fresh
DROP DATABASE IF EXISTS hotel_booking;
CREATE DATABASE hotel_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel_booking;

-- Table 1: users
-- Stores registered user information with hashed passwords
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Table 2: regions
-- Stores geographical regions where hotels are located
CREATE TABLE regions (
    region_id INT AUTO_INCREMENT PRIMARY KEY,
    region_name VARCHAR(100) NOT NULL UNIQUE
);

-- Table 3: hotels
-- Stores hotel information linked to regions
CREATE TABLE hotels (
    hotel_id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(200) NOT NULL,
    region_id INT NOT NULL,
    address TEXT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES regions(region_id),
    INDEX idx_region (region_id)
);

-- Table 4: room_type
-- Stores room type categories with maximum occupancy
CREATE TABLE room_type (
    room_type_id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_name VARCHAR(100) NOT NULL,
    max_occupancy INT NOT NULL,
    UNIQUE KEY uk_room_type (room_type_name)
);

-- Table 5: room_inventory
-- Each physical room in the hotel system (one row per room)
CREATE TABLE room_inventory (
    rm_id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    room_type_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id),
    FOREIGN KEY (room_type_id) REFERENCES room_type(room_type_id),
    UNIQUE KEY uk_hotel_room (hotel_id, room_number),
    INDEX idx_hotel (hotel_id)
);

-- Table 6: bookings
-- Stores reservation information with unique booking references
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    rm_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    adults_count INT NOT NULL,
    children_count INT NOT NULL DEFAULT 0,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (rm_id) REFERENCES room_inventory(rm_id),
    INDEX idx_user (user_id),
    INDEX idx_room_dates (rm_id, check_in_date, check_out_date),
    INDEX idx_booking_ref (booking_reference)
);

-- ============================================
-- SAMPLE DATA INSERTION
-- ============================================

-- Insert regions
INSERT INTO regions (region_name) VALUES
('Downtown'),
('Beachfront'),
('Airport'),
('Suburban');

-- Insert room types
INSERT INTO room_type (room_type_name, max_occupancy) VALUES
('Single', 1),
('Double', 2),
('Queen', 2),
('King', 2),
('Suite', 4),
('Family Suite', 6);

-- Insert hotels
-- Downtown hotels
INSERT INTO hotels (hotel_name, region_id, address) VALUES
('Grand Plaza Hotel', 1, '123 Main Street, Downtown District'),
('Metropolitan Inn', 1, '456 Business Avenue, Downtown District'),
('City Center Suites', 1, '789 Commerce Road, Downtown District');

-- Beachfront hotels
INSERT INTO hotels (hotel_name, region_id, address) VALUES
('Ocean View Resort', 2, '100 Seaside Boulevard, Beachfront'),
('Coastal Paradise Hotel', 2, '250 Beach Drive, Beachfront'),
('Sunset Bay Inn', 2, '500 Marina Way, Beachfront');

-- Airport hotels
INSERT INTO hotels (hotel_name, region_id, address) VALUES
('Airport Express Hotel', 3, '10 Terminal Road, Airport District'),
('Sky Lodge', 3, '25 Aviation Way, Airport District');

-- Suburban hotels
INSERT INTO hotels (hotel_name, region_id, address) VALUES
('Garden View Hotel', 4, '300 Park Lane, Suburban Area'),
('Quiet Retreat Inn', 4, '450 Maple Street, Suburban Area');

-- Insert room inventory
-- Grand Plaza Hotel (Downtown) - hotel_id: 1
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(1, 1, '101', 89.99),
(1, 1, '102', 89.99),
(1, 2, '201', 129.99),
(1, 2, '202', 129.99),
(1, 3, '301', 149.99),
(1, 3, '302', 149.99),
(1, 4, '401', 179.99),
(1, 5, '501', 299.99);

-- Metropolitan Inn (Downtown) - hotel_id: 2
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(2, 2, '101', 119.99),
(2, 2, '102', 119.99),
(2, 3, '201', 139.99),
(2, 3, '202', 139.99),
(2, 4, '301', 169.99),
(2, 5, '401', 279.99);

-- City Center Suites (Downtown) - hotel_id: 3
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(3, 3, '101', 159.99),
(3, 4, '201', 189.99),
(3, 5, '301', 319.99),
(3, 6, '401', 449.99);

-- Ocean View Resort (Beachfront) - hotel_id: 4
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(4, 2, '101', 159.99),
(4, 2, '102', 159.99),
(4, 3, '201', 189.99),
(4, 3, '202', 189.99),
(4, 4, '301', 229.99),
(4, 4, '302', 229.99),
(4, 5, '401', 399.99),
(4, 6, '501', 599.99);

-- Coastal Paradise Hotel (Beachfront) - hotel_id: 5
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(5, 2, '101', 149.99),
(5, 3, '201', 179.99),
(5, 4, '301', 219.99),
(5, 5, '401', 379.99);

-- Sunset Bay Inn (Beachfront) - hotel_id: 6
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(6, 2, '101', 139.99),
(6, 2, '102', 139.99),
(6, 3, '201', 169.99),
(6, 4, '301', 209.99),
(6, 5, '401', 359.99);

-- Airport Express Hotel (Airport) - hotel_id: 7
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(7, 1, '101', 79.99),
(7, 1, '102', 79.99),
(7, 2, '201', 109.99),
(7, 2, '202', 109.99),
(7, 3, '301', 129.99),
(7, 4, '401', 159.99);

-- Sky Lodge (Airport) - hotel_id: 8
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(8, 2, '101', 99.99),
(8, 3, '201', 119.99),
(8, 4, '301', 149.99),
(8, 5, '401', 249.99);

-- Garden View Hotel (Suburban) - hotel_id: 9
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(9, 2, '101', 99.99),
(9, 2, '102', 99.99),
(9, 3, '201', 119.99),
(9, 4, '301', 149.99),
(9, 5, '401', 259.99),
(9, 6, '501', 399.99);

-- Quiet Retreat Inn (Suburban) - hotel_id: 10
INSERT INTO room_inventory (hotel_id, room_type_id, room_number, price_per_night) VALUES
(10, 2, '101', 89.99),
(10, 3, '201', 109.99),
(10, 4, '301', 139.99),
(10, 5, '401', 239.99);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Display summary of inserted data
SELECT 'Regions' as Table_Name, COUNT(*) as Row_Count FROM regions
UNION ALL
SELECT 'Room Types', COUNT(*) FROM room_type
UNION ALL
SELECT 'Hotels', COUNT(*) FROM hotels
UNION ALL
SELECT 'Room Inventory', COUNT(*) FROM room_inventory;

-- Display hotels by region
SELECT r.region_name, COUNT(h.hotel_id) as hotel_count
FROM regions r
LEFT JOIN hotels h ON r.region_id = h.region_id
GROUP BY r.region_id, r.region_name
ORDER BY r.region_name;

-- Display room inventory summary
SELECT h.hotel_name, rt.room_type_name, COUNT(ri.rm_id) as room_count
FROM hotels h
JOIN room_inventory ri ON h.hotel_id = ri.hotel_id
JOIN room_type rt ON ri.room_type_id = rt.room_type_id
GROUP BY h.hotel_id, h.hotel_name, rt.room_type_id, rt.room_type_name
ORDER BY h.hotel_name, rt.room_type_name;
