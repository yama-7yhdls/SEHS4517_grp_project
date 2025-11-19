<?php
/**
 * Login Failed Page
 * Hotel Booking System
 * 
 * This page displays a "sorry, login failed" message
 * when user authentication fails.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Failed - Hotel Booking System</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/responsive.css" />
</head>
<body>
    <div class="container">
        <div class="registration-container">
            <div class="logo-section">
                <img src="../images/logo.svg" alt="Hotel Logo" class="logo" />
            </div>
            
            <h1>Login Failed</h1>
            
            <div class="error-box">
                <p class="error-message-large">Sorry, login failed.</p>
                <p>The email address or password you entered is incorrect.</p>
                <p>Please check your credentials and try again.</p>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-primary" onclick="window.location.href='../index.html'">Back to Home</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='../login.html'">Try Again</button>
            </div>
        </div>
    </div>
</body>
</html>
