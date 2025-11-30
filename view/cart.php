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

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $store_param = isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : '';
        
        switch ($_POST['action']) {
            case 'update':
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
                break;
            
            case 'remove':
                $product_id = intval($_POST['product_id']);
                unset($_SESSION['cart'][$product_id]);
                break;
            
            case 'clear':
                $_SESSION['cart'] = [];
                break;
        }
        header('Location: cart.php' . $store_param);
        exit();
    }
}

// Fetch cart products
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
$shipping = 50; // Fixed shipping fee
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <?php if ($is_vendor_storefront && $vendor_data): ?>
    <title><?php echo htmlspecialchars($vendor_data['business_name']); ?> - Cart</title>
    <?php else: ?>
    <title>Shopping Cart - PreOrda</title>
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
            --secondary: #1a202c;
            --bg-main: #f8f9fa;
            --accent: #3498db;
            --font-main: 'Outfit', sans-serif;
            <?php endif; ?>
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

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 30px 80px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 40px;
            letter-spacing: -0.02em;
        }

        /* Cart Layout */
        .cart-container {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 40px;
        }

        /* Cart Items */
        .cart-items {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: var(--bg-main);
            border-bottom: 1px solid var(--border);
        }

        th {
            text-align: left;
            padding: 20px;
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 25px 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .product-col {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .product-img {
            width: 80px;
            height: 80px;
            border-radius: var(--radius-md);
            object-fit: cover;
            background-color: #f1f5f9;
            border: 1px solid var(--border);
        }

        .product-info h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .product-info p {
            font-size: 0.85rem;
            color: var(--text-gray);
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-main);
            padding: 5px;
            border-radius: var(--radius-md);
            width: fit-content;
            border: 1px solid var(--border);
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--white);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            color: var(--secondary);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover {
            background: var(--primary);
            color: white;
        }

        .qty-input {
            width: 40px;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-dark);
        }
        
        .qty-input:focus {
            outline: none;
        }

        .price {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.1rem;
        }

        .remove-btn {
            color: #e53e3e;
            background: rgba(229, 62, 62, 0.1);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-btn:hover {
            background: #e53e3e;
            color: white;
            transform: rotate(90deg);
        }

        /* Cart Summary */
        .cart-summary {
            background: var(--white);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .cart-summary h2 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 25px;
            color: var(--secondary);
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--text-gray);
            font-size: 1rem;
        }

        .summary-row.total {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed var(--border);
            font-weight: 800;
            color: var(--secondary);
            font-size: 1.4rem;
            align-items: center;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 18px;
            background: var(--primary);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 30px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
            border: none;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(44, 62, 80, 0.25);
        }

        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .continue-shopping:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 100px 20px;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px dashed var(--border);
        }

        .empty-cart-icon {
            font-size: 5rem;
            color: var(--border);
            margin-bottom: 20px;
            display: block;
        }

        .empty-cart h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: var(--secondary);
        }

        .empty-cart p {
            color: var(--text-gray);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .summary-card {
                position: static;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }

            .item-actions {
                grid-column: 2;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
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
                    <div class="cart-icon" style="color: var(--accent);">
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
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Shopping Cart</h1>
            <p class="page-subtitle"><?php echo $cart_count; ?> item<?php echo $cart_count !== 1 ? 's' : ''; ?> in your cart</p>
        </div>

        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Your cart is empty</h2>
                <p>Add some products to get started!</p>
                <a href="products.php<?php echo isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : ''; ?>" class="btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <!-- Cart Layout -->
            <div class="cart-layout">
                <!-- Cart Items -->
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <div class="no-image">📦</div>
                                <?php endif; ?>
                            </div>

                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-category"><?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?></div>
                                <div class="item-price">GH₵ <?php echo number_format($item['price'], 2); ?></div>
                            </div>

                            <div class="item-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <div class="quantity-control">
                                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="qty-btn">-</button>
                                        <span class="qty-value"><?php echo $item['quantity']; ?></span>
                                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="qty-btn">+</button>
                                    </div>
                                </form>

                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Summary Card -->
                <div class="summary-card">
                    <h2 class="summary-title">Order Summary</h2>
                    
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value">GH₵ <?php echo number_format($subtotal, 2); ?></span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value">GH₵ <?php echo number_format($shipping, 2); ?></span>
                    </div>

                    <div class="summary-row total">
                        <span>Total</span>
                        <span>GH₵ <?php echo number_format($total, 2); ?></span>
                    </div>

                    <a href="checkout.php<?php echo isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : ''; ?>" class="checkout-btn" style="display: block; text-align: center; text-decoration: none;">
                        Proceed to Checkout
                    </a>

                    <a href="products.php<?php echo isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : ''; ?>" class="continue-shopping">← Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer style="background-color: var(--secondary); color: white; text-align: center; padding: 25px 20px; margin-top: 60px;">
        <p style="margin: 0; font-size: 0.95rem;">Powered by <strong>PreOrda</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 0.85rem; opacity: 0.8;">&copy; <?php echo date('Y'); ?> PreOrda. All rights reserved.</p>
    </footer>
</body>
</html>
