<?php
session_start();
require_once("../controllers/product_controller.php");
require_once("../controllers/user_controller.php");
require_once("../helpers/encryption.php");

// Check if this is a vendor-specific storefront
$is_vendor_storefront = false;
$vendor_data = null;

if (isset($_GET['store'])) {
    $encrypted_slug = $_GET['store'];
    $vendor_slug = decrypt_slug($encrypted_slug);
    
    if ($vendor_slug) {
        $vendor_data = get_vendor_by_slug_ctr($vendor_slug);
        if ($vendor_data) {
            $is_vendor_storefront = true;
        }
    }
}

// Enforce store parameter
if (!$is_vendor_storefront) {
    include 'store_not_found.php';
    exit();
}

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    $store_param = isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : '';
    header('Location: products.php' . $store_param);
    exit();
}

// Fetch cart products for summary
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $all_products = get_all_products_ctr();
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        foreach ($all_products as $product) {
            if ($product['product_id'] == $product_id) {
                $item = $product;
                $item['quantity'] = $quantity;
                $item['item_total'] = $product['price'] * $quantity;
                $cart_items[] = $item;
                $subtotal += $item['item_total'];
                break;
            }
        }
    }
}

$cart_count = count($_SESSION['cart']);
$shipping = 50;
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <?php if ($is_vendor_storefront && $vendor_data): ?>
    <title><?php echo htmlspecialchars($vendor_data['business_name']); ?> - Checkout</title>
    <?php else: ?>
    <title>Checkout - PreOrda</title>
    <?php endif; ?>
    <style>
        :root {
            <?php if ($is_vendor_storefront && $vendor_data): ?>
            --primary: <?php echo htmlspecialchars($vendor_data['primary_color'] ?? '#2c3e50'); ?>;
            --secondary: <?php echo htmlspecialchars($vendor_data['secondary_color'] ?? '#2d3748'); ?>;
            --bg-main: <?php echo htmlspecialchars($vendor_data['background_color'] ?? '#f8f9fa'); ?>;
            --accent: <?php echo htmlspecialchars($vendor_data['accent_color'] ?? '#f7fafc'); ?>;
            --font-main: <?php echo $vendor_data['font_family'] ?? 'Outfit'; ?>, sans-serif;
            <?php else: ?>
            --primary: #2c3e50;
            --secondary: #2d3748;
            --bg-main: #f8f9fa;
            --accent: #3498db;
            --font-main: 'Outfit', sans-serif;
            <?php endif; ?>
            --text-dark: #1a202c;
            --text-gray: #718096;
            --border: #e2e8f0;
            --white: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-main);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Header */
        header {
            background-color: var(--white);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border);
        }

        nav {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo img {
            height: 40px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            font-size: 0.95rem;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .cart-icon {
            position: relative;
            cursor: pointer;
            color: var(--text-dark);
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #e53e3e;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 30px 60px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        /* Checkout Layout */
        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }

        /* Forms */
        .checkout-form {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            font-size: 1.2rem;
            color: var(--secondary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Order Summary */
        .order-summary {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 1.2rem;
            color: var(--secondary);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .summary-items {
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
        }

        .summary-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .summary-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-item-img {
            width: 60px;
            height: 60px;
            background: #f1f5f9;
            border-radius: 8px;
            overflow: hidden;
        }

        .summary-item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .summary-item-details {
            flex: 1;
        }

        .summary-item-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 4px;
        }

        .summary-item-price {
            font-size: 0.85rem;
            color: var(--text-gray);
        }

        .summary-totals {
            border-top: 2px solid var(--border);
            padding-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: var(--text-gray);
        }

        .summary-row.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .place-order-btn {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s;
        }

        .place-order-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-method {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }

        .payment-method.active {
            border-color: var(--primary);
            background: var(--accent);
            color: var(--primary);
            font-weight: 600;
        }

        /* Error Message */
        .error-message {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            font-size: 0.95rem;
        }

        .error-message.show {
            display: block;
        }

        /* Loading State */
        .place-order-btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
            position: relative;
        }

        .place-order-btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 900px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
                order: -1; /* Show summary first on mobile */
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <?php if ($is_vendor_storefront && !empty($vendor_data['logo_url'])): ?>
                <a href="products.php?store=<?php echo htmlspecialchars($_GET['store']); ?>" class="logo">
                    <img src="<?php echo htmlspecialchars($vendor_data['logo_url']); ?>" alt="<?php echo htmlspecialchars($vendor_data['business_name']); ?>">
                </a>
            <?php else: ?>
                <a href="../index.php" class="logo">
                    <img src="../images/logo_c.png" alt="PreOrda Logo">
                </a>
            <?php endif; ?>
            <ul class="nav-links">
                <li><a href="products.php<?php echo isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : ''; ?>">Products</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li>
                    <div class="cart-icon" onclick="window.location.href='cart.php<?php echo isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : ''; ?>'">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Checkout</h1>
            <p class="page-subtitle">Complete your pre-order</p>
        </div>

        <div class="checkout-layout">
            <!-- Checkout Form -->
            <div class="checkout-form">
                <div id="errorMessage" class="error-message"></div>
                <form id="checkoutForm">
                    <!-- Contact Info -->
                    <div class="form-section">
                        <h3>Contact Information</h3>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" required placeholder="john@example.com">
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="form-section">
                        <h3>Shipping Address</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-input" required>
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-input" required placeholder="123 Main St">
                            </div>
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-input" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h3>Payment Method</h3>
                        <div class="payment-methods">
                            <div class="payment-method active" onclick="selectPayment(this)">
                                Mobile Money
                            </div>
                            <div class="payment-method" onclick="selectPayment(this)">
                                Card Payment
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number / Card Details</label>
                            <input type="text" class="form-input" required placeholder="Enter details">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3 class="summary-title">Order Summary</h3>
                
                <div class="summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div class="summary-item-img">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#cbd5e0;">📦</div>
                                <?php endif; ?>
                            </div>
                            <div class="summary-item-details">
                                <div class="summary-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="summary-item-price">Qty: <?php echo $item['quantity']; ?> × GH₵ <?php echo number_format($item['price'], 2); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>GH₵ <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>GH₵ <?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>GH₵ <?php echo number_format($total, 2); ?></span>
                    </div>
                </div>

                <button type="submit" form="checkoutForm" class="place-order-btn">
                    Place Order
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: var(--secondary); color: white; text-align: center; padding: 25px 20px; margin-top: 60px;">
        <p style="margin: 0; font-size: 0.95rem;">Powered by <strong>PreOrda</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 0.85rem; opacity: 0.8;">&copy; <?php echo date('Y'); ?> PreOrda. All rights reserved.</p>
    </footer>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function selectPayment(element) {
            document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            
            // Update hidden input if we had one, or just track state
            const method = element.innerText.trim();
            // We could store this in a variable
        }

        const paymentForm = document.getElementById('checkoutForm');
        paymentForm.addEventListener("submit", payWithPaystack, false);

        function payWithPaystack(e) {
            e.preventDefault();

            const email = document.querySelector('input[type="email"]').value;
            const firstName = document.querySelectorAll('input[type="text"]')[0].value;
            const lastName = document.querySelectorAll('input[type="text"]')[1].value;
            const amount = <?php echo $total * 100; ?>; // Convert to kobo/pesewas
            
            const shippingAddress = document.querySelectorAll('input[type="text"]')[2].value + ', ' + 
                                  document.querySelectorAll('input[type="text"]')[3].value;
            
            // Validate inputs
            if (!email || !firstName || !lastName) {
                showError('Please fill in all required fields');
                return;
            }

            const handler = PaystackPop.setup({
                key: 'pk_test_1e1399c94c952ee54bbacfd2dcc4e1bbbcdd61f8', // Replace with your public key
                email: email,
                amount: amount,
                currency: 'GHS',
                firstname: firstName,
                lastname: lastName,
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Mobile Number",
                            variable_name: "mobile_number",
                            value: document.querySelector('input[type="tel"]').value
                        }
                    ]
                },
                callback: function(response) {
                    // On success, send reference to backend
                    const reference = response.reference;
                    const phone = document.querySelector('input[type="tel"]').value;
                    verifyTransaction(reference, email, amount, shippingAddress, firstName, lastName, phone);
                },
                onClose: function() {
                    // User closed the payment modal - no action needed
                }
            });

            handler.openIframe();
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
                errorDiv.classList.remove('show');
            }, 5000);
        }

        function verifyTransaction(reference, email, amount, shippingAddress, firstName, lastName, phone) {
            const btn = document.querySelector('.place-order-btn');
            btn.classList.add('loading');
            btn.disabled = true;
            btn.textContent = 'Processing...';

            const formData = new FormData();
            formData.append('reference', reference);
            formData.append('email', email);
            formData.append('amount', amount);
            formData.append('shipping_address', shippingAddress);
            formData.append('payment_method', 'paystack');
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('phone', phone);
            
            fetch('../actions/process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'payment_success.php?ref=' + reference;
                } else {
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    btn.textContent = 'Place Order';
                    showError('Payment successful but order creation failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.classList.remove('loading');
                btn.disabled = false;
                btn.textContent = 'Place Order';
                showError('An error occurred while processing your order. Please try again.');
            });
        }
    </script>
</body>
</html>
