# Server-Side Functions - Implementation Guide

## ✅ Created Files Summary

### PHP Functions (3 + 1 utility)

1. **`php/register.php`** ✓ (Already exists)
   - Handles user registration
   - Validates input, checks for duplicate emails
   - Hashes passwords with bcrypt
   - Stores user data in MySQL

2. **`php/login.php`** ✓ (Already exists)
   - Authenticates users
   - Verifies email and password
   - Creates PHP session on success
   - Returns JSON with redirect URL

3. **`php/reserve.php`** ✓ (New)
   - Validates user session
   - Checks room availability
   - Creates booking in database
   - Forwards data to Node.js server via cURL

4. **`php/logout.php`** ✓ (New - bonus)
   - Destroys user session
   - Clears session cookies

5. **`php/config.php`** ✓ (Already exists - shared utility)
   - Database connection
   - Session management
   - Helper functions

### Node.js/Express Function (1)

6. **`server.js`** ✓ (New)
   - Express server on port 3000
   - POST `/api/confirmation` - receives booking data from PHP
   - GET `/confirmation/:bookingReference` - generates 5th web page
   - In-memory storage for booking confirmations

### Supporting Files

7. **`package.json`** ✓ (New)
   - Node.js dependencies: express, cors
   - Start scripts

8. **`.gitignore`** ✓ (New)
   - Ignores node_modules, logs, etc.

---

## 🚀 Setup Instructions

### 1. Install Node.js Dependencies

```bash
npm install
```

### 2. Start Node.js Server

```bash
npm start
# Or for development with auto-reload:
npm run dev
```

Server will run on: `http://localhost:3000`

### 3. Configure Apache/PHP

Ensure Apache is running with PHP and MySQL enabled.

### 4. Database Setup

Run the SQL setup script:
```bash
mysql -u root -p < sql/setup.sql
```

---

## 📋 API Endpoints

### PHP Endpoints (Apache)

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/php/register.php` | POST | User registration |
| `/php/login.php` | POST | User authentication |
| `/php/reserve.php` | POST | Create reservation |
| `/php/logout.php` | POST | User logout |

### Node.js Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/confirmation` | POST | Receive booking from PHP |
| `/confirmation/:ref` | GET | Display confirmation page (5th page) |
| `/health` | GET | Server health check |

---

## 🔄 Data Flow

1. **Registration Flow**: `register.html` → `register.js` → `php/register.php` → MySQL
2. **Login Flow**: `login.html` → `login.js` → `php/login.php` → MySQL → Session
3. **Reservation Flow**: `reserve.html` → `reserve.js` → `php/reserve.php` → MySQL → Node.js → Confirmation Page

---

## 🔐 Security Features

- ✓ Password hashing with bcrypt
- ✓ Prepared statements (SQL injection prevention)
- ✓ Input sanitization (XSS prevention)
- ✓ Session timeout (30 minutes)
- ✓ Email validation
- ✓ CORS enabled for Node.js

---

## 📝 Next Steps

1. Install Node.js dependencies: `npm install`
2. Test each endpoint individually
3. Create/update HTML forms to connect to these endpoints
4. Test the complete flow end-to-end
