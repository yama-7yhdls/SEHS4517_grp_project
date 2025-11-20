---
inclusion: always
---
<!------------------------------------------------------------------------------------
   Add rules to this file or a short description and have Kiro refine them for you.
   
   Learn about inclusion modes: https://kiro.dev/docs/steering/#inclusion-modes
-------------------------------------------------------------------------------------> 
---
inclusion: always
---

# Hotel Room Booking System - Steering Guide

## System Overview

Web-based hotel room booking system: registration → login → booking → confirmation

**Stack**: HTML5, jQuery, PHP, Node.js/Express, MySQL  
**Pattern**: PHP (auth + bookings) → MySQL → Express (confirmation page)

## Core Features

**Booking Flow**:
- Date range picker: check-in → check-out (calendar popup)
- Guest inputs: adults (min 1) + children (min 0)
- Region filter → hotel list → room selection
- Price calculation: price_per_night × nights
- Unique booking reference: BK20251119001

## Database Schema (3NF)

```
users: user_id (PK), email (UNIQUE), password_hash, name, contact
regions: region_id (PK), region_name
hotels: hotel_id (PK), hotel_name, region_id (FK), address
room_type: room_type_id (PK), room_type_name, max_occupancy
room_inventory: rm_id (PK), hotel_id (FK), room_type_id (FK), room_number, price_per_night
bookings: booking_id (PK), booking_reference (UNIQUE), user_id (FK), rm_id (FK), 
          check_in_date, check_out_date, adults_count, children_count, total_price, status
```

**Key Points**:
- Each room = one row in room_inventory (enables room-level tracking)
- Each booking reserves ONE specific room (rm_id)
- Same room type can have different prices per hotel

## Critical Business Rules

**Availability Logic**:
- Room available if NO overlapping confirmed bookings exist
- Overlap check: `NOT (check_out_date <= check_in OR check_in_date >= check_out)`
- Only show hotels with available_rooms >= 1

**Overbooking Prevention**:
- Use transactions with `SELECT ... FOR UPDATE` on rm_id
- Lock room → check conflicts → insert booking (atomic operation)
- Only count status='confirmed' bookings

**Validation**:
- Check-out must be after check-in (min 1 night)
- Guest count must not exceed room max_occupancy
- Booking reference must be unique

## Code Conventions

- XHTML syntax (self-closing tags, lowercase, quoted attributes)
- Prepared statements for ALL SQL queries (no raw SQL)
- Hash passwords with `password_hash()` (bcrypt)
- Sanitize all inputs/outputs (XSS prevention)
- Session management with 30-min timeout
- Client + server validation on all forms

## File Structure

```
/css, /js, /images
/php: config.php, register.php, login.php, reserve.php, get-hotels.php, get-rooms.php
/server: confirmation.js, /views/confirmation.ejs
index.html, register.html, login.html, reserve.html
```

## Key PHP Endpoints

- `get-hotels.php`: Query hotels by region + count available rooms for date range
- `get-rooms.php`: Get available rooms for specific hotel (with types/prices)
- `reserve.php`: Create booking with transaction (lock → validate → insert → forward to Express)

## Testing Focus

- Concurrent bookings on same room (race conditions)
- Overbooking prevention with simultaneous requests
- Date overlap detection accuracy
- Guest count vs max_occupancy validation
- Booking reference uniqueness
- Session timeout behavior
