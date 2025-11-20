$(document).ready(function() {
    // Validation patterns
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phonePattern = /^\d{10}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
    
    // Clear all error messages
    function clearErrors() {
        $('.error-message').text('').hide();
        $('.form-group input, .form-group textarea').removeClass('input-error');
    }
    
    // Show error message for a field
    function showError(fieldId, message) {
        $('#' + fieldId + 'Error').text(message).show();
        $('#' + fieldId).addClass('input-error');
    }
    
    // Validate individual field
    function validateField(fieldId) {
        const field = $('#' + fieldId);
        const value = field.val().trim();
        let isValid = true;
        
        // Clear previous error
        $('#' + fieldId + 'Error').text('').hide();
        field.removeClass('input-error');
        
        switch(fieldId) {
            case 'lastName':
            case 'firstName':
                if (value === '') {
                    showError(fieldId, 'This field is required');
                    isValid = false;
                }
                break;
                
            case 'address':
                if (value === '') {
                    showError(fieldId, 'Mailing address is required');
                    isValid = false;
                }
                break;
                
            case 'phone':
                if (value === '') {
                    showError(fieldId, 'Contact phone is required');
                    isValid = false;
                } else if (!phonePattern.test(value)) {
                    showError(fieldId, 'Phone must be exactly 10 digits');
                    isValid = false;
                }
                break;
                
            case 'email':
                if (value === '') {
                    showError(fieldId, 'Email address is required');
                    isValid = false;
                } else if (!emailPattern.test(value)) {
                    showError(fieldId, 'Please enter a valid email address');
                    isValid = false;
                }
                break;
                
            case 'password':
                if (value === '') {
                    showError(fieldId, 'Password is required');
                    isValid = false;
                } else if (!passwordPattern.test(value)) {
                    showError(fieldId, 'Password must be at least 8 characters with 1 uppercase and 1 number');
                    isValid = false;
                }
                break;
        }
        
        return isValid;
    }
    
    // Validate all fields
    function validateForm() {
        clearErrors();
        let isValid = true;
        
        const fields = ['lastName', 'firstName', 'address', 'phone', 'email', 'password'];
        fields.forEach(function(fieldId) {
            if (!validateField(fieldId)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Real-time validation on blur
    $('#lastName, #firstName, #address, #phone, #email, #password').on('blur', function() {
        validateField($(this).attr('id'));
    });
    
    // Clear button functionality
    $('#clearBtn').on('click', function() {
        $('#registrationForm')[0].reset();
        clearErrors();
        $('#formMessage').text('').removeClass('success error');
    });
    
    // Back to Home button
    $('#backHomeBtn').on('click', function() {
        window.location.href = 'index.html';
    });
    
    // Form submission
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return false;
        }
        
        // Prepare form data
        const formData = {
            lastName: $('#lastName').val().trim(),
            firstName: $('#firstName').val().trim(),
            address: $('#address').val().trim(),
            phone: $('#phone').val().trim(),
            email: $('#email').val().trim(),
            password: $('#password').val()
        };
        
        // Show loading state
        const submitBtn = $('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Registering...');
        $('#formMessage').text('').removeClass('success error');
        
        // Send AJAX request
        $.ajax({
            url: 'php/register.php',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                submitBtn.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    $('#formMessage')
                        .text(response.message || 'Registration successful! Redirecting to login...')
                        .addClass('success')
                        .show();
                    
                    // Clear form
                    $('#registrationForm')[0].reset();
                    clearErrors();
                    
                    // Redirect to login page after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    $('#formMessage')
                        .text(response.message || 'Registration failed. Please try again.')
                        .addClass('error')
                        .show();
                }
            },
            error: function(xhr, status, error) {
                submitBtn.prop('disabled', false).text(originalText);
                
                let errorMessage = 'An error occurred. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('#formMessage')
                    .text(errorMessage)
                    .addClass('error')
                    .show();
                
                console.error('Registration error:', error);
            }
        });
        
        return false;
    });
});
