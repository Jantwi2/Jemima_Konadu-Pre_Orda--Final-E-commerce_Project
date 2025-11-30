<?php
session_start();
require_once("../controllers/product_controller.php");
require_once("../controllers/user_controller.php");
require_once("../helpers/encryption.php");

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$product_id) {
    header('Location: products.php');
    exit();
}

// Fetch product details
$products = get_all_products_ctr(); // Get all products and filter
$current_product = null;
foreach ($products as $p) {
    if ($p['product_id'] == $product_id) {
        $current_product = $p;
        break;
    }
}

if (!$current_product) {
    header('Location: products.php');
    exit();
}

// Check if vendor storefront
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

// Get cart count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <title><?php echo htmlspecialchars($current_product['name']); ?> - PreOrda</title>
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

        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 30px;
            font-size: 0.95rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb a {
            color: var(--text-gray);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }
        
        .breadcrumb span {
            color: var(--text-dark);
            font-weight: 500;
        }

        /* Product Details Grid */
        .product-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }

        /* Product Image */
        .product-image-section {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .main-image {
            width: 100%;
            height: 500px;
            background: #f1f5f9;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid var(--border);
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .main-image:hover img {
            transform: scale(1.05);
        }

        .no-image {
            font-size: 5rem;
            color: #cbd5e0;
        }

        /* Product Info */
        .product-category-badge {
            display: inline-block;
            background: rgba(44, 62, 80, 0.05);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .product-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 20px;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .product-price {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-description {
            font-size: 1.05rem;
            color: var(--text-gray);
            line-height: 1.8;
            margin-bottom: 40px;
        }

        .product-meta {
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 30px 0;
            margin-bottom: 40px;
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .meta-item:last-child {
            margin-bottom: 0;
        }

        .meta-label {
            color: var(--text-gray);
            font-weight: 500;
        }

        .meta-value {
            color: var(--text-dark);
            font-weight: 600;
        }

        /* Quantity Selector */
        .quantity-section {
            margin-bottom: 30px;
        }

        .quantity-label {
            display: block;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--secondary);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quantity-selector {
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
            width: 44px;
            height: 44px;
            border: none;
            background: var(--white);
            border-radius: var(--radius-sm);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-dark);
            box-shadow: var(--shadow-sm);
        }

        .qty-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
        }

        .qty-input {
            width: 60px;
            height: 44px;
            text-align: center;
            border: none;
            background: transparent;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        .qty-input:focus {
            outline: none;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }

        .btn-primary {
            flex: 1;
            padding: 18px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(44, 62, 80, 0.25);
        }

        .btn-secondary {
            padding: 18px;
            background: var(--white);
            color: var(--primary);
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            background: rgba(44, 62, 80, 0.05);
            transform: translateY(-3px);
        }

        /* Features List */
        .features-section {
            background: rgba(52, 152, 219, 0.05);
            padding: 30px;
            border-radius: var(--radius-lg);
            border: 1px dashed var(--accent);
        }

        .features-section h3 {
            color: var(--secondary);
            margin-bottom: 20px;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 1rem;
            color: var(--text-dark);
        }
        
        .feature-item:last-child {
            margin-bottom: 0;
        }

        .feature-icon {
            color: var(--primary);
            font-weight: 700;
            background: var(--white);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            box-shadow: var(--shadow-sm);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .product-details {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .product-image-section {
                position: static;
            }
            
            .product-title {
                font-size: 2rem;
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
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="products.php<?php echo isset($_GET['store']) ? '?store=' . htmlspecialchars($_GET['store']) : ''; ?>">Products</a> / 
            <span><?php echo htmlspecialchars($current_product['name']); ?></span>
        </div>

        <!-- Product Details -->
        <div class="product-details">
            <!-- Product Image -->
            <div class="product-image-section">
                <div class="main-image">
                    <?php if (!empty($current_product['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($current_product['image_url']); ?>" alt="<?php echo htmlspecialchars($current_product['name']); ?>">
                    <?php else: ?>
                        <div class="no-image">📦</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <span class="product-category-badge"><?php echo htmlspecialchars($current_product['category_name'] ?? 'General'); ?></span>
                
                <h1 class="product-title"><?php echo htmlspecialchars($current_product['name']); ?></h1>
                
                <div class="product-price">GH₵ <?php echo number_format($current_product['price'], 2); ?></div>
                
                <p class="product-description">
                    <?php echo htmlspecialchars($current_product['description'] ?? 'No description available.'); ?>
                </p>

                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">Brand</span>
                        <span class="meta-value"><?php echo htmlspecialchars($current_product['brand_name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Estimated Delivery</span>
                        <span class="meta-value"><?php echo htmlspecialchars($current_product['estimated_delivery_time'] ?? '3-5'); ?> days</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Availability</span>
                        <span class="meta-value">Pre-Order</span>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="quantity-section">
                    <label class="quantity-label">Quantity</label>
                    <div class="quantity-selector">
                        <button class="qty-btn" onclick="decrementQty()">-</button>
                        <input type="number" class="qty-input" id="quantity" value="1" min="1" max="99">
                        <button class="qty-btn" onclick="incrementQty()">+</button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-primary" onclick="addToCart()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; vertical-align: middle; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Add to Cart
                    </button>
                    <button class="btn-secondary" onclick="toggleWishlist()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; vertical-align: middle;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Features -->
                <div class="features-section">
                    <h3>Why Pre-Order?</h3>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Guaranteed availability</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Secure payment</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Direct from verified vendors</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Free cancellation within 24 hours</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: var(--secondary); color: white; text-align: center; padding: 25px 20px; margin-top: 60px;">
        <p style="margin: 0; font-size: 0.95rem;">Powered by <strong>PreOrda</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 0.85rem; opacity: 0.8;">&copy; <?php echo date('Y'); ?> PreOrda. All rights reserved.</p>
    </footer>

    <script>
        function incrementQty() {
            const input = document.getElementById('quantity');
            const currentVal = parseInt(input.value);
            if (currentVal < 99) {
                input.value = currentVal + 1;
            }
        }

        function decrementQty() {
            const input = document.getElementById('quantity');
            const currentVal = parseInt(input.value);
            if (currentVal > 1) {
                input.value = currentVal - 1;
            }
        }

        function addToCart() {
            const quantity = document.getElementById('quantity').value;
            
            // Create a form and submit to add_to_cart action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../actions/add_to_cart.php';
            
            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = 'product_id';
            productInput.value = <?php echo $product_id; ?>;
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = quantity;

            // Add store parameter if present
            const urlParams = new URLSearchParams(window.location.search);
            const storeParam = urlParams.get('store');
            if (storeParam) {
                const storeInput = document.createElement('input');
                storeInput.type = 'hidden';
                storeInput.name = 'store';
                storeInput.value = storeParam;
                form.appendChild(storeInput);
            }
            
            form.appendChild(productInput);
            form.appendChild(quantityInput);
            document.body.appendChild(form);
            form.submit();
        }

        function toggleWishlist() {
            // TODO: Implement wishlist functionality
            alert('Added to wishlist!');
        }
    </script>
</body>
</html>
