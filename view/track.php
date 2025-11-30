<?php
session_start();
require_once("../settings/core.php");

// Get tracking parameter
$reference = $_GET['ref'] ?? '';
$order_id = $_GET['order_id'] ?? '';

// Fetch order details if order_id is provided
$order = null;
if ($order_id) {
    require_once("../classes/order_class.php");
    $order_obj = new order_class();
    $order = $order_obj->db_fetch_one("SELECT * FROM orders WHERE order_id = '$order_id'");
} elseif ($reference) {
    // Try to fetch by payment reference
    require_once("../classes/payment_class.php");
    $payment_obj = new Payment();
    $payment = $payment_obj->db_fetch_one("SELECT * FROM payments WHERE transaction_id LIKE '%$reference%' LIMIT 1");
    
    if ($payment) {
        require_once("../classes/order_class.php");
        $order_obj = new order_class();
        $order = $order_obj->db_fetch_one("SELECT * FROM orders WHERE order_id = '{$payment['order_id']}'");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - PreOrda</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #1a202c;
            --bg-main: #f8f9fa;
            --accent: #3498db;
            --font-main: 'Outfit', sans-serif;
            --text-dark: #1a202c;
            --text-gray: #718096;
            --border: #e2e8f0;
            --white: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --radius-lg: 20px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: var(--transition);
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
            transition: transform 0.2s;
        }
        
        .logo:hover {
            transform: scale(1.02);
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
            transition: var(--transition);
            font-size: 0.95rem;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: var(--accent);
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .cart-icon {
            position: relative;
            cursor: pointer;
            color: var(--text-dark);
            padding: 8px;
            border-radius: 50%;
            transition: var(--transition);
        }
        
        .cart-icon:hover {
            background-color: rgba(0,0,0,0.03);
            color: var(--accent);
        }

        .cart-badge {
            position: absolute;
            top: 0;
            right: 0;
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="1" cy="1" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 800;
            letter-spacing: -0.02em;
            position: relative;
        }

        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            font-weight: 300;
        }

        /* Main Content */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 30px 80px;
            flex: 1;
            width: 100%;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 50px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .search-box {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }

        .search-box input {
            flex: 1;
            padding: 18px 25px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 1.1rem;
            font-family: inherit;
            transition: var(--transition);
            background: var(--bg-main);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(44, 62, 80, 0.1);
        }

        .search-box button {
            padding: 18px 40px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
        }

        .search-box button:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(44, 62, 80, 0.25);
        }

        .order-status {
            text-align: center;
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--border);
        }

        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            margin-top: 15px;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dbeafe; color: #1e40af; }
        .status-shipped { background: #e0e7ff; color: #4338ca; }
        .status-delivered { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .order-details {
            background: var(--bg-main);
            padding: 30px;
            border-radius: var(--radius-md);
            margin: 40px 0;
            border: 1px solid var(--border);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--text-gray);
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-dark);
            font-weight: 700;
        }

        .timeline {
            margin-top: 50px;
            position: relative;
            padding-left: 20px;
        }

        .timeline-item {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 24px;
            top: 50px;
            width: 2px;
            height: calc(100% + 10px);
            background: var(--border);
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-dot {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
            z-index: 1;
            box-shadow: 0 0 0 8px var(--white);
            font-size: 1.2rem;
        }

        .timeline-dot.inactive {
            background: var(--bg-main);
            color: var(--text-gray);
            border: 2px solid var(--border);
            box-shadow: 0 0 0 8px var(--white);
        }
        
        .timeline-dot.active {
            background: var(--success, #16a34a);
            box-shadow: 0 0 0 8px var(--white), 0 0 0 12px rgba(22, 163, 74, 0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(22, 163, 74, 0); }
            100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
        }

        .timeline-content {
            flex: 1;
            padding-top: 10px;
        }

        .timeline-title {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 6px;
            font-size: 1.1rem;
        }

        .timeline-date {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-gray);
        }

        .empty-state svg {
            width: 100px;
            height: 100px;
            margin-bottom: 25px;
            opacity: 0.5;
            color: var(--text-gray);
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--secondary);
        }

        /* Footer */
        footer {
            background-color: var(--secondary);
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: auto;
        }
        
        footer p {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        footer strong {
            color: var(--white);
            opacity: 1;
        }

        @media (max-width: 600px) {
            .search-box {
                flex-direction: column;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .card {
                padding: 30px;
                margin-top: -40px;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <a href="../index.php" class="logo">
                <img src="../images/logo_c.png" alt="PreOrda Logo">
            </a>
            <ul class="nav-links">
                <li><a href="products.php">Products</a></li>
                <li><a href="my_orders.php">My Orders</a></li>
                <li>
                    <div class="cart-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="cart-badge"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Hero -->
    <div class="hero">
        <h1>Track Your Order</h1>
        <p>Enter your order ID or payment reference to check the status.</p>
    </div>

    <div class="container">
        <div class="card">
            <div class="search-box">
                <input type="text" id="trackingInput" placeholder="Enter Order ID or Payment Reference" 
                       value="<?php echo htmlspecialchars($reference ?: $order_id); ?>">
                <button onclick="trackOrder()">Track Order</button>
            </div>

            <?php if ($order): ?>
                <div class="order-status">
                    <h3 style="margin-bottom: 10px; color: var(--text-dark);">Order #<?php echo $order['order_id']; ?></h3>
                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>

                <div class="order-details">
                    <div class="detail-row">
                        <span class="detail-label">Order Date:</span>
                        <span class="detail-value"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">GH₵ <?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Shipping Address:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['shipping_address'] ?? 'N/A'); ?></span>
                    </div>
                    <?php if ($order['tracking_number']): ?>
                    <div class="detail-row">
                        <span class="detail-label">Tracking Number:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['tracking_number']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="timeline">
                    <h3 style="margin-bottom: 24px; color: var(--text-dark);">Order Timeline</h3>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot">✓</div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Placed</div>
                            <div class="timeline-date"><?php echo date('M d, Y - H:i', strtotime($order['order_date'])); ?></div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo in_array($order['status'], ['confirmed', 'shipped', 'delivered']) ? '' : 'inactive'; ?>">
                            <?php echo in_array($order['status'], ['confirmed', 'shipped', 'delivered']) ? '✓' : '2'; ?>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Confirmed</div>
                            <div class="timeline-date">
                                <?php echo $order['status'] != 'pending' ? 'Confirmed' : 'Waiting for confirmation'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo in_array($order['status'], ['shipped', 'delivered']) ? '' : 'inactive'; ?>">
                            <?php echo in_array($order['status'], ['shipped', 'delivered']) ? '✓' : '3'; ?>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Shipped</div>
                            <div class="timeline-date">
                                <?php echo $order['status'] == 'shipped' || $order['status'] == 'delivered' ? 'In transit' : 'Not yet shipped'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo $order['status'] == 'delivered' ? '' : 'inactive'; ?>">
                            <?php echo $order['status'] == 'delivered' ? '✓' : '4'; ?>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Delivered</div>
                            <div class="timeline-date">
                                <?php echo $order['status'] == 'delivered' ? 'Successfully delivered' : 'Delivery pending'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 16v-4M12 8h.01"></path>
                    </svg>
                    <h3 style="margin-bottom: 10px;">No Order Found</h3>
                    <p>Please check your order ID or payment reference and try again.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: var(--secondary); color: white; text-align: center; padding: 25px 20px; margin-top: auto;">
        <p style="margin: 0; font-size: 0.95rem;">Powered by <strong>PreOrda</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 0.85rem; opacity: 0.8;">&copy; <?php echo date('Y'); ?> PreOrda. All rights reserved.</p>
    </footer>

    <script>
        function trackOrder() {
            const input = document.getElementById('trackingInput');
            const value = input.value.trim();
            
            if (value) {
                window.location.href = 'track.php?ref=' + encodeURIComponent(value);
            }
        }

        // Allow Enter key to trigger search
        document.getElementById('trackingInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                trackOrder();
            }
        });
    </script>
</body>
</html>
