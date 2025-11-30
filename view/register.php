<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Store - PreOrda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background: #ffffff;
            color: #1a1a1a;
            line-height: 1.6;
        }

        .page-container {
            display: grid;
            grid-template-columns: 45% 55%;
            min-height: 100vh;
        }

        .left-panel {
            background: #0a0a0a;
            color: #ffffff;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .branding {
            margin-bottom: 60px;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 20px;
        }

        .tagline {
            font-size: 18px;
            color: #a0a0a0;
            line-height: 1.6;
            max-width: 400px;
        }

        .features {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 32px;
        }

        .feature-item {
            display: flex;
            gap: 20px;
            align-items: start;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: #1a1a1a;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-content h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .feature-content p {
            font-size: 14px;
            color: #a0a0a0;
            line-height: 1.5;
        }

        .footer-text {
            font-size: 13px;
            color: #666;
            margin-top: 40px;
        }

        .right-panel {
            background: #ffffff;
            padding: 60px 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            width: 100%;
            max-width: 480px;
        }

        .form-header {
            margin-bottom: 48px;
        }

        .form-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: #666;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1a1a1a;
            font-weight: 500;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e5e5e5;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
            font-family: inherit;
            background: #ffffff;
        }

        input:hover, select:hover {
            border-color: #c0c0c0;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #0a0a0a;
            box-shadow: 0 0 0 3px rgba(10, 10, 10, 0.05);
        }

        .url-preview-container {
            margin-top: 12px;
        }

        .url-preview {
            background: #f8f8f8;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 14px;
            color: #666;
            font-family: 'Courier New', monospace;
            border: 1px solid #e5e5e5;
        }

        .url-preview .domain {
            color: #999;
        }

        .url-preview .slug {
            color: #0a0a0a;
            font-weight: 600;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: #0a0a0a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 32px;
        }

        .btn-primary:hover {
            background: #2a2a2a;
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 32px 0;
            color: #999;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e5e5;
        }

        .login-link {
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #0a0a0a;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1.5px solid #0a0a0a;
        }

        .login-link a:hover {
            opacity: 0.7;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            display: none;
        }

        .success-message {
            background: #f0fdf4;
            color: #166534;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: none;
            font-size: 14px;
            border: 1px solid #bbf7d0;
        }

        .helper-text {
            font-size: 13px;
            color: #999;
            margin-top: 6px;
        }

        @media (max-width: 1024px) {
            .page-container {
                grid-template-columns: 1fr;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 40px 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .right-panel {
                padding: 32px 20px;
            }

            .form-header h1 {
                font-size: 28px;
            }
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231a1a1a' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        input[type="password"] {
            letter-spacing: 0.1em;
        }

        input[type="password"]::placeholder {
            letter-spacing: normal;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="left-panel">
            <div class="branding">
                <div class="logo">
                    <img src="../images/logo.png" alt="PreOrda Logo" style="max-width: 200px; height: auto;">
                </div>
                <p class="tagline">Launch your personalized pre-order storefront in minutes. No technical skills required.</p>
            </div>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>Instant Setup</h3>
                        <p>Get your unique storefront URL and start accepting pre-orders immediately</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>Full Control</h3>
                        <p>Set your own pricing, deposit rules, and delivery timelines</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>Secure Payments</h3>
                        <p>Manage deposits and full payments with built-in payment tracking</p>
                    </div>
                </div>
            </div>

            <div class="footer-text">
                Trusted by micro-shippers and vendors across Ghana
            </div>
        </div>

        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <h1>Create your store</h1>
                    <p>Join hundreds of vendors managing pre-orders effortlessly</p>
                </div>

                <div class="success-message" id="successMessage">
                    Account created successfully! Redirecting to your dashboard...
                </div>

                <form id="signupForm">
                    <div class="form-group">
                        <label>Store Name</label>
                        <input type="text" id="storeName" placeholder="Enter your store name" required>
                        <div class="url-preview-container">
                            <div class="url-preview">
                                <span class="domain">PreOrda.com/</span><span class="slug" id="urlSlug">your-store</span>
                            </div>
                        </div>
                        <div class="error" id="storeNameError">Please enter a store name</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="fullName" placeholder="John Doe" required>
                            <div class="error" id="fullNameError">Full name is required</div>
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" id="phone" placeholder="+233 XX XXX XXXX" required>
                            <div class="error" id="phoneError">Phone number is required</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" id="email" placeholder="you@example.com" required>
                        <div class="error" id="emailError">Please enter a valid email</div>
                    </div>

                    <div class="form-group">
                        <label>Business Type</label>
                        <select id="businessType" required>
                            <option value="">Select your business category</option>
                            <option value="fashion">Fashion & Apparel</option>
                            <option value="electronics">Electronics & Gadgets</option>
                            <option value="beauty">Beauty & Cosmetics</option>
                            <option value="home">Home & Living</option>
                            <option value="food">Food & Beverages</option>
                            <option value="accessories">Accessories & Jewelry</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="error" id="businessTypeError">Please select a business type</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="password" placeholder="••••••••" required>
                            <div class="helper-text">Minimum 8 characters</div>
                            <div class="error" id="passwordError">Password must be at least 8 characters</div>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" id="confirmPassword" placeholder="••••••••" required>
                            <div class="error" id="confirmPasswordError">Passwords do not match</div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Create My Store</button>

                    <div class="divider">or</div>

                    <div class="login-link">
                        Already have an account? <a href="login.php">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/auth.js"></script>
</body>
</html>