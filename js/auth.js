// Tab Switching
document.querySelectorAll('.form-tab').forEach(tab => {
    tab.addEventListener('click', function () {
        const targetTab = this.dataset.tab;

        // Update tabs
        document.querySelectorAll('.form-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        // Update forms
        document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
        document.getElementById(`${targetTab}Form`).classList.add('active');

        // Update subtitle
        const subtitle = document.getElementById('formSubtitle');
        if (targetTab === 'login') {
            subtitle.textContent = 'Welcome back! Please enter your details.';
        } else {
            subtitle.textContent = 'Create your account to start pre-ordering amazing products.';
        }
    });
});

// Password Toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Password Strength Checker
document.getElementById('signupPassword')?.addEventListener('input', function () {
    const password = this.value;
    const bars = [
        document.getElementById('bar1'),
        document.getElementById('bar2'),
        document.getElementById('bar3'),
        document.getElementById('bar4')
    ];
    const strengthText = document.getElementById('strengthText');

    // Reset bars
    bars.forEach(bar => {
        bar.classList.remove('filled', 'medium', 'strong');
    });

    if (password.length === 0) {
        strengthText.textContent = '';
        return;
    }

    let strength = 0;
    let strengthLevel = 'weak';

    // Check criteria
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    // Determine level
    if (strength >= 4) {
        strengthLevel = 'strong';
        strengthText.textContent = '✓ Strong password';
        strengthText.style.color = '#38a169';
    } else if (strength >= 2) {
        strengthLevel = 'medium';
        strengthText.textContent = '⚡ Medium strength - add special characters';
        strengthText.style.color = '#f6ad55';
    } else {
        strengthLevel = 'weak';
        strengthText.textContent = '⚠️ Weak password - use 8+ characters, mix cases & numbers';
        strengthText.style.color = '#e53e3e';
    }

    // Fill bars
    const fillCount = Math.min(strength, 4);
    for (let i = 0; i < fillCount; i++) {
        bars[i].classList.add('filled', strengthLevel);
    }
});

// Validation Functions
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePhone(phone) {
    return /^\+?[0-9]{10,15}$/.test(phone.replace(/\s/g, ''));
}

function showError(inputId, errorId, message) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);
    const errorText = document.getElementById(errorId + 'Text');

    if (!input || !error) {
        // If elements don't exist, show alert as fallback
        console.warn(`Error display elements not found: ${inputId}, ${errorId}`);
        alert(message);
        return;
    }

    input.classList.add('error');
    input.classList.remove('success');
    error.classList.add('show');
    if (errorText) {
        errorText.textContent = message;
    }
}

function showSuccess(inputId) {
    const input = document.getElementById(inputId);
    input.classList.remove('error');
    input.classList.add('success');
}

function clearError(inputId, errorId) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);

    input.classList.remove('error');
    error.classList.remove('show');
}

// Real-time Validation
const inputs = ['loginEmail', 'loginPassword', 'signupName', 'signupEmail', 'phoneNumber', 'signupPassword', 'confirmPassword'];
inputs.forEach(inputId => {
    const input = document.getElementById(inputId);
    if (input) {
        input.addEventListener('input', function () {
            clearError(inputId, inputId + 'Error');
        });

        input.addEventListener('blur', function () {
            const value = this.value.trim();
            if (!value) return;

            if (inputId.includes('Email') && !validateEmail(value)) {
                showError(inputId, inputId + 'Error', 'Please enter a valid email address');
            } else if (inputId.includes('Phone') && !validatePhone(value)) {
                showError(inputId, inputId + 'Error', 'Please enter a valid phone number');
            } else if (inputId === 'confirmPassword') {
                const passwordInput = document.getElementById('signupPassword');
                if (passwordInput) {
                    const password = passwordInput.value;
                    if (value !== password) {
                        showError(inputId, inputId + 'Error', 'Passwords do not match');
                    } else {
                        showSuccess(inputId);
                    }
                }
            } else if (value) {
                showSuccess(inputId);
            }
        });
    }
});


function toggleCheckbox() {
    const checkbox = document.getElementById('agreeTerms');
    checkbox.checked = !checkbox.checked;
}


// Sign Up Form Submission
const signupFormElement = document.getElementById('signupForm');
if (signupFormElement) {
    signupFormElement.addEventListener('submit', async function (e) {
        e.preventDefault();

        let isValid = true;
        const nameInput = document.getElementById('signupName');
        const emailInput = document.getElementById('signupEmail');
        const phoneInput = document.getElementById('phoneNumber');
        const passwordInput = document.getElementById('signupPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const agreeTermsInput = document.getElementById('agreeTerms');

        if (!nameInput || !emailInput || !phoneInput || !passwordInput || !confirmPasswordInput || !agreeTermsInput) {
            return; // Exit if any required elements are missing
        }

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const agreeTerms = agreeTermsInput.checked;

        // Clear errors
        ['signupName', 'signupEmail', 'phoneNumber', 'signupPassword', 'confirmPassword'].forEach(id => {
            clearError(id, id + 'Error');
        });

        // Validate name
        if (!name || name.length < 3) {
            showError('signupName', 'signupNameError', 'Please enter your full name (minimum 3 characters)');
            isValid = false;
        }

        // Validate email
        if (!email || !validateEmail(email)) {
            showError('signupEmail', 'signupEmailError', 'Please enter a valid email address');
            isValid = false;
        }

        // Validate phone
        if (!phone || !validatePhone(phone)) {
            showError('phoneNumber', 'phoneNumberError', 'Please enter a valid phone number (10-15 digits)');
            isValid = false;
        }

        // Validate password
        if (!password || password.length < 8) {
            showError('signupPassword', 'signupPasswordError', 'Password must be at least 8 characters long');
            isValid = false;
        }

        // Validate confirm password
        if (password !== confirmPassword) {
            showError('confirmPassword', 'confirmPasswordError', 'Passwords do not match');
            isValid = false;
        }

        // Validate terms
        if (!agreeTerms) {
            alert('⚠️ Please agree to the Terms & Conditions to continue');
            isValid = false;
        }

        if (isValid) {
            const btn = this.querySelector('.btn');
            btn.classList.add('loading');
            btn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                btn.classList.remove('loading');
                btn.disabled = false;

                alert('✓ Account created successfully!\n\nPlease check your email to verify your account.');
                // In production: window.location.href = '/verify-email';
            }, 2000);
        }
    });
}


// Add to js/register.js
const businessTypeSelect = document.getElementById('businessType');
if (businessTypeSelect) {
    businessTypeSelect.addEventListener('change', function () {
        const regGroup = document.getElementById('registrationNumberGroup');
        const regNumber = document.getElementById('registrationNumber');
        if (regGroup && regNumber) {
            if (this.value === 'registered') {
                regGroup.style.display = 'block';
                regNumber.required = true;
            } else {
                regGroup.style.display = 'none';
                regNumber.required = false;
            }
        }
    });
}

// Password Recovery
const forgotPasswordLink = document.getElementById('forgotPasswordLink');
if (forgotPasswordLink) {
    forgotPasswordLink.addEventListener('click', function (e) {
        e.preventDefault();
        const recoveryModal = document.getElementById('recoveryModal');
        if (recoveryModal) {
            recoveryModal.classList.add('active');
        }
    });
}

function closeRecoveryModal() {
    const recoveryModal = document.getElementById('recoveryModal');
    if (recoveryModal) {
        recoveryModal.classList.remove('active');
    }
}

const recoveryModal = document.getElementById('recoveryModal');
if (recoveryModal) {
    recoveryModal.addEventListener('click', function (e) {
        if (e.target === this) {
            closeRecoveryModal();
        }
    });
}

const recoveryForm = document.getElementById('recoveryForm');
if (recoveryForm) {
    recoveryForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const email = document.getElementById('recoveryEmail').value.trim();
        clearError('recoveryEmail', 'recoveryEmailError');

        if (!email || !validateEmail(email)) {
            showError('recoveryEmail', 'recoveryEmailError', 'Please enter a valid email address');
            return;
        }

        const btn = this.querySelector('.btn');
        btn.classList.add('loading');
        btn.disabled = true;

        setTimeout(() => {
            btn.classList.remove('loading');
            btn.disabled = false;
            alert('✓ Password reset link sent!\n\nPlease check your email inbox and spam folder.');
            closeRecoveryModal();
            document.getElementById('recoveryForm').reset();
        }, 1500);
    });
}

// Phone number formatting
function formatPhoneNumber(input) {
    if (!input) return;
    input.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 0 && !value.startsWith('233')) {
            if (value.startsWith('0')) {
                value = '233' + value.substring(1);
            }
        }
        this.value = value ? '+' + value : '';
    });
}

formatPhoneNumber(document.getElementById('phoneNumber'));
formatPhoneNumber(document.getElementById('payoutAccount'));

// Auto-fill detection
window.addEventListener('load', function () {
    setTimeout(() => {
        document.querySelectorAll('input').forEach(input => {
            if (input.value) {
                input.classList.add('success');
            }
        });
    }, 100);
});


// Example: Vendor registration AJAX
// Example: Vendor registration AJAX
const vendorSignupForm = document.getElementById('signupForm');
if (vendorSignupForm && document.getElementById('storeName')) {
    vendorSignupForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Collect values
        const data = {
            fullName: document.getElementById('fullName').value.trim(),
            email: document.getElementById('email').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            password: document.getElementById('password').value,
            storeName: document.getElementById('storeName').value.trim(),
            businessType: document.getElementById('businessType').value.trim()
        };

        // Send to PHP
        const response = await fetch('../actions/register_vendor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.status === 'otp_sent') {
            // Check if OTP is provided in response (development mode)
            if (result.otp) {
                alert(`Email service not configured.\n\nYour verification code is: ${result.otp}\n\nPlease enter this code on the next page.`);
            }
            // Redirect to OTP verification page
            window.location.href = 'verify_otp.php';
        } else if (result.status === 'success') {
            document.getElementById('successMessage').style.display = 'block';
            setTimeout(() => {
                // Redirect to dashboard or login
                window.location.href = '../vendor/dashboard.php';
            }, 2000);
        } else if (result.status === 'duplicate') {
            // Show alert for duplicate since we may not have proper error elements in vendor form
            alert(result.message);
        } else {
            alert(result.message || 'Registration failed. Please try again.');
        }
    });
}


// Login Form Submission
document.getElementById('loginForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();

    let isValid = true;
    const login = document.getElementById('loginEmail').value.trim(); // can be email or phone
    const password = document.getElementById('loginPassword').value;

    clearError('loginEmail', 'loginEmailError');
    clearError('loginPassword', 'loginPasswordError');

    if (!login) {
        showError('loginEmail', 'loginEmailError', 'Email or phone is required');
        isValid = false;
    }

    if (!password) {
        showError('loginPassword', 'loginPasswordError', 'Password is required');
        isValid = false;
    }

    if (isValid) {
        const btn = this.querySelector('.btn');
        if (btn) {
            btn.classList.add('loading');
            btn.disabled = true;
        }

        // Send login data to PHP
        const response = await fetch('../actions/login_vendor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ login, password })
        });
        const result = await response.json();

        if (btn) {
            btn.classList.remove('loading');
            btn.disabled = false;
        }

        if (result.status === 'success') {
            // Show success message
            document.getElementById('successMessage').style.display = 'block';
            setTimeout(() => {
                if (result.role === 'admin') {
                    window.location.href = '../admin/dashboard.php';
                } else {
                    window.location.href = '../vendor/dashboard.php';
                }
            }, 1000);
        } else if (result.status === 'invalid_password') {
            showError('loginPassword', 'loginPasswordError', 'Incorrect password');
        } else if (result.status === 'user_not_found') {
            showError('loginEmail', 'loginEmailError', 'Account not found');
        } else {
            showError('loginEmail', 'loginEmailError', result.message || 'Login failed. Please try again.');
        }
    }
});
