$(document).ready(function() {
    // Validation patterns
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // Clear all error messages
    function clearErrors() {
        $('.error-message').text('').hide();
        $('.form-group input').removeClass('input-error');
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
                }
                break;
        }
        
        return isValid;
    }
    
    // Validate all fields
    function validateForm() {
        clearErrors();
        let isValid = true;
        
        const fields = ['email', 'password'];
        fields.forEach(function(fieldId) {
            if (!validateField(fieldId)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Real-time validation on blur
    $('#email, #password').on('blur', function() {
        validateField($(this).attr('id'));
    });
    
    // Back to Home button
    $('#backHomeBtn').on('click', function() {
        window.location.href = 'index.html';
    });
    
    // Form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return false;
        }
        
        // Prepare form data
        const formData = {
            email: $('#email').val().trim(),
            password: $('#password').val()
        };
        
        // Show loading state
        const submitBtn = $('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Logging in...');
        $('#formMessage').text('').removeClass('success error');
        
        // Send AJAX request
        $.ajax({
            url: 'php/login.php',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                submitBtn.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    $('#formMessage')
                        .text(response.message || 'Login successful! Redirecting...')
                        .addClass('success')
                        .show();
                    
                    // Redirect to reservation page or specified URL
                    setTimeout(function() {
                        window.location.href = response.redirect || 'reserve.html';
                    }, 1000);
                } else {
                    $('#formMessage')
                        .text(response.message || 'Login failed. Please check your credentials.')
                        .addClass('error')
                        .show();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text(originalText);
                
                let errorMessage = 'An error occurred. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('#formMessage')
                    .text(errorMessage)
                    .addClass('error')
                    .show();
            }
        });
        
        return false;
    });
});
