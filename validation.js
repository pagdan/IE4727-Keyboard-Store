// Form validation functions

// Validate checkout form
function validateCheckoutForm() {
    let isValid = true;
    
    // Clear previous errors
    clearErrors();
    
    // Get form values
    const name = document.getElementById('customer_name').value.trim();
    const email = document.getElementById('customer_email').value.trim();
    const phone = document.getElementById('customer_phone').value.trim();
    const address = document.getElementById('shipping_address').value.trim();
    
    // Validate name (at least 2 characters)
    if (name.length < 2) {
        showError('customer_name', 'Name must be at least 2 characters long');
        isValid = false;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('customer_email', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate phone (8-20 digits, can include spaces, dashes, parentheses)
    const phoneRegex = /^[0-9\s\-\+\(\)]{8,20}$/;
    if (!phoneRegex.test(phone)) {
        showError('customer_phone', 'Please enter a valid phone number');
        isValid = false;
    }
    
    // Validate address (at least 10 characters)
    if (address.length < 10) {
        showError('shipping_address', 'Please enter a complete shipping address');
        isValid = false;
    }
    
    return isValid;
}

// Show error message for a field
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error';
    errorDiv.textContent = message;
    
    // Add error styling to field
    field.style.borderColor = '#e74c3c';
    
    // Insert error message after the field
    field.parentNode.appendChild(errorDiv);
}

// Clear all error messages
function clearErrors() {
    const errors = document.querySelectorAll('.error');
    errors.forEach(error => error.remove());
    
    // Reset field borders
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.style.borderColor = '#ddd';
    });
}

// Validate quantity input (positive integers only)
function validateQuantity(input) {
    let value = parseInt(input.value);
    
    if (isNaN(value) || value < 1) {
        input.value = 1;
    } else if (value > 99) {
        input.value = 99;
    }
}

// Update cart quantity with AJAX (if needed in future)
function updateCartQuantity(productId, quantity) {
    // For now, just submit the form
    const form = document.getElementById('cart-form');
    if (form) {
        form.submit();
    }
}

// Confirm before removing item from cart
function confirmRemove(productName) {
    return confirm('Remove ' + productName + ' from cart?');
}

// Admin login validation
function validateLoginForm() {
    let isValid = true;
    clearErrors();
    
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    
    if (username.length < 3) {
        showError('username', 'Username must be at least 3 characters');
        isValid = false;
    }
    
    if (password.length < 6) {
        showError('password', 'Password must be at least 6 characters');
        isValid = false;
    }
    
    return isValid;
}

// Add event listeners when document loads
document.addEventListener('DOMContentLoaded', function() {
    // Checkout form validation
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (!validateCheckoutForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Login form validation
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Quantity input validation
    const quantityInputs = document.querySelectorAll('input[type="number"][name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            validateQuantity(this);
        });
        
        input.addEventListener('blur', function() {
            validateQuantity(this);
        });
    });
});

// Real-time email validation
function validateEmailRealtime(input) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const value = input.value.trim();
    
    if (value === '') {
        input.style.borderColor = '#ddd';
        return;
    }
    
    if (emailRegex.test(value)) {
        input.style.borderColor = '#27ae60';
    } else {
        input.style.borderColor = '#e74c3c';
    }
}

// Real-time phone validation
function validatePhoneRealtime(input) {
    const phoneRegex = /^[0-9\s\-\+\(\)]{8,20}$/;
    const value = input.value.trim();
    
    if (value === '') {
        input.style.borderColor = '#ddd';
        return;
    }
    
    if (phoneRegex.test(value)) {
        input.style.borderColor = '#27ae60';
    } else {
        input.style.borderColor = '#e74c3c';
    }
}