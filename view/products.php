<?php
session_start();
require_once("../controllers/product_controller.php");
require_once("../controllers/user_controller.php");
require_once("../helpers/encryption.php");

// Check if this is a vendor-specific storefront
$is_vendor_storefront = false;
$vendor_data = null;
$vendor_id = null;

if (isset($_GET['store'])) {
    $encrypted_slug = $_GET['store'];
    $vendor_slug = decrypt_slug($encrypted_slug);
    
    if ($vendor_slug) {
        $vendor_data = get_vendor_by_slug_ctr($vendor_slug);
        if ($vendor_data) {
            $is_vendor_storefront = true;
            $vendor_id = $vendor_data['vendor_id'];
        }
    }
}

// Enforce store parameter
if (!$is_vendor_storefront) {
    include 'store_not_found.php';
    exit();
}

// Fetch data based on storefront type
if ($is_vendor_storefront && $vendor_id) {
    $products = get_vendor_products_ctr($vendor_id);
} else {
    // Should not reach here due to check above, but for safety
    include 'store_not_found.php';
    exit();
}

$categories = get_all_categories_ctr();
$brands = get_all_brands_ctr();

// Get cart count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <?php if ($is_vendor_storefront && $vendor_data): ?>
    <title><?php echo htmlspecialchars($vendor_data['business_name']); ?> - PreOrda</title>
    <?php else: ?>
    <title>Discover Products - PreOrda</title>
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
            --accent-hover: #2980b9;
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
            font-size: 1.2rem;
            color: var(--text-dark);
            transition: var(--transition);
            padding: 8px;
            border-radius: 50%;
        }
        
        .cart-icon:hover {
            color: var(--accent);
            background-color: rgba(0,0,0,0.03);
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

        /* Hero Section */
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

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px 80px;
        }

        /* Page Layout */
        .page-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 50px;
        }

        /* Sidebar */
        .sidebar {
            background-color: var(--white);
            padding: 30px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            height: fit-content;
            position: sticky;
            top: 100px;
            transition: var(--transition);
        }
        
        .sidebar:hover {
            box-shadow: var(--shadow-md);
            border-color: transparent;
        }

        .filter-section {
            margin-bottom: 35px;
        }
        
        .filter-section:last-child {
            margin-bottom: 0;
        }

        .filter-section h3 {
            font-size: 0.9rem;
            margin-bottom: 15px;
            color: var(--secondary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filter-option {
            margin-bottom: 12px;
        }

        .filter-option label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.95rem;
            color: var(--text-gray);
            transition: var(--transition);
        }
        
        .filter-option label:hover {
            color: var(--primary);
            transform: translateX(2px);
        }

        .filter-option input[type="checkbox"],
        .filter-option input[type="radio"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
            border-radius: 4px;
        }

        .price-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .price-range input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.9rem;
            transition: var(--transition);
            background: var(--bg-main);
        }
        
        .price-range input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }

        .clear-filters {
            width: 100%;
            padding: 12px;
            background-color: var(--bg-main);
            color: var(--text-gray);
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 20px;
            transition: var(--transition);
        }

        .clear-filters:hover {
            background-color: #e2e8f0;
            color: var(--text-dark);
        }

        /* Main Content */
        .main-content {
            min-height: 600px;
        }

        /* Search and Sort Bar */
        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: var(--white);
            padding: 20px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .search-wrapper {
            flex: 1;
            max-width: 450px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 14px 20px 14px 50px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-family: inherit;
            transition: var(--transition);
            background: var(--bg-main);
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
            pointer-events: none;
        }

        .sort-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .sort-select {
            padding: 12px 20px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-family: inherit;
            cursor: pointer;
            background-color: var(--white);
            color: var(--text-dark);
            transition: var(--transition);
        }
        
        .sort-select:hover {
            border-color: var(--text-gray);
        }
        
        .sort-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .results-count {
            font-size: 0.95rem;
            color: var(--text-gray);
        }
        
        .results-count strong {
            color: var(--text-dark);
            font-weight: 700;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background-color: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .product-image-wrapper {
            width: 100%;
            height: 260px;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-card:hover .product-image {
            transform: scale(1.08);
        }
        
        .no-image-placeholder {
            font-size: 3rem;
            color: #cbd5e0;
        }

        .wishlist-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            z-index: 2;
            opacity: 0;
            transform: translateY(-10px);
        }

        .product-card:hover .wishlist-btn {
            opacity: 1;
            transform: translateY(0);
        }
        
        .wishlist-btn:hover {
            transform: scale(1.1);
            background-color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .wishlist-btn.active {
            color: #e53e3e;
            opacity: 1;
            transform: translateY(0);
        }

        .product-info {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .product-category {
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(44, 62, 80, 0.05);
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        .product-brand {
            font-size: 0.85rem;
            color: var(--text-gray);
            font-weight: 500;
        }

        .product-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 10px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            margin-top: auto;
            margin-bottom: 20px;
        }

        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        
        .delivery-info {
            font-size: 0.8rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .preorder-btn {
            padding: 12px 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: var(--transition);
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .preorder-btn:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 100px 20px;
            color: var(--text-gray);
            grid-column: 1 / -1;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px dashed var(--border);
        }
        
        .no-results-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .no-results h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--text-dark);
            font-weight: 700;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .page-layout {
                grid-template-columns: 240px 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 900px) {
            .page-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                margin-bottom: 30px;
            }
            
            .controls-bar {
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            .search-wrapper, .sort-wrapper {
                max-width: none;
            }
            
            .sort-wrapper {
                justify-content: space-between;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 0 20px 60px;
            }
            
            .hero h1 {
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
                <li><a href="products.php" style="color: var(--accent);">Products</a></li>
                <li><a href="my_orders.php">My Orders</a></li>
                <li>
                    <div class="cart-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="cart-badge" id="cartCount"><?php echo $cart_count; ?></span>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="hero">
        <?php if ($is_vendor_storefront && $vendor_data): ?>
            <h1><?php echo htmlspecialchars($vendor_data['business_name']); ?></h1>
            <?php if (!empty($vendor_data['tagline'])): ?>
                <p><?php echo htmlspecialchars($vendor_data['tagline']); ?></p>
            <?php else: ?>
                <p>Explore our exclusive collection of products.</p>
            <?php endif; ?>
        <?php else: ?>
            <h1>Discover Unique Finds</h1>
            <p>Pre-order exclusive items directly from verified vendors.</p>
        <?php endif; ?>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Page Layout -->
        <div class="page-layout">
            <!-- Sidebar Filters -->
            <aside class="sidebar">
                <div class="filter-section">
                    <h3>Categories</h3>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <div class="filter-option">
                                <label>
                                    <input type="checkbox" class="category-filter" value="<?php echo htmlspecialchars($cat['name']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 0.9rem; color: var(--text-gray);">No categories available</p>
                    <?php endif; ?>
                </div>

                <div class="filter-section">
                    <h3>Brands</h3>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): ?>
                            <div class="filter-option">
                                <label>
                                    <input type="checkbox" class="brand-filter" value="<?php echo htmlspecialchars($brand['name']); ?>">
                                    <?php echo htmlspecialchars($brand['name']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 0.9rem; color: var(--text-gray);">No brands available</p>
                    <?php endif; ?>
                </div>

                <div class="filter-section">
                    <h3>Price Range (GH₵)</h3>
                    <div class="price-range">
                        <input type="number" id="minPrice" placeholder="Min" min="0">
                        <span>-</span>
                        <input type="number" id="maxPrice" placeholder="Max" min="0">
                    </div>
                </div>

                <button class="clear-filters" id="clearFilters">Clear All Filters</button>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Controls Bar -->
                <div class="controls-bar">
                    <div class="search-wrapper">
                        <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" class="search-input" id="searchInput" placeholder="Search products, brands...">
                    </div>
                    <div class="sort-wrapper">
                        <span class="results-count">Showing <strong id="resultCount">0</strong> products</span>
                        <select class="sort-select" id="sortSelect">
                            <option value="newest">Newest First</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="name-asc">Name: A to Z</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="product-grid" id="productGrid">
                    <!-- Products will be dynamically inserted here -->
                </div>

                <!-- No Results -->
                <div class="no-results" id="noResults" style="display: none;">
                    <div class="no-results-icon">🔍</div>
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or search terms</p>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const products = <?php echo json_encode($products); ?>;
        const currentStore = "<?php echo isset($_GET['store']) ? htmlspecialchars($_GET['store']) : ''; ?>";
        
        let filteredProducts = [...products];
        let wishlist = []; // In a real app, fetch this from local storage or DB

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            renderProducts(filteredProducts);
            updateResultCount();
            setupEventListeners();
        });

        // Setup Event Listeners
        function setupEventListeners() {
            // Category Filters
            document.querySelectorAll('.category-filter').forEach(cb => {
                cb.addEventListener('change', applyFilters);
            });

            // Brand Filters
            document.querySelectorAll('.brand-filter').forEach(cb => {
                cb.addEventListener('change', applyFilters);
            });

            // Price Inputs
            document.getElementById('minPrice').addEventListener('input', applyFilters);
            document.getElementById('maxPrice').addEventListener('input', applyFilters);

            // Search Input
            document.getElementById('searchInput').addEventListener('input', applyFilters);

            // Sort Select
            document.getElementById('sortSelect').addEventListener('change', applyFilters);

            // Clear Filters
            document.getElementById('clearFilters').addEventListener('click', () => {
                document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                document.getElementById('minPrice').value = '';
                document.getElementById('maxPrice').value = '';
                document.getElementById('searchInput').value = '';
                document.getElementById('sortSelect').value = 'newest';
                applyFilters();
            });
        }

        // Apply Filters
        function applyFilters() {
            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked'))
                .map(cb => cb.value.toLowerCase());
            
            const selectedBrands = Array.from(document.querySelectorAll('.brand-filter:checked'))
                .map(cb => cb.value.toLowerCase());
            
            const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
            
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const sortValue = document.getElementById('sortSelect').value;

            filteredProducts = products.filter(product => {
                const categoryName = (product.category_name || '').toLowerCase();
                const brandName = (product.brand_name || '').toLowerCase();
                const productName = (product.name || '').toLowerCase();
                const productDesc = (product.description || '').toLowerCase();
                const price = parseFloat(product.price);

                const matchesCategory = selectedCategories.length === 0 || selectedCategories.includes(categoryName);
                const matchesBrand = selectedBrands.length === 0 || selectedBrands.includes(brandName);
                const matchesPrice = price >= minPrice && price <= maxPrice;
                const matchesSearch = productName.includes(searchTerm) || 
                                     productDesc.includes(searchTerm) ||
                                     brandName.includes(searchTerm);
                
                return matchesCategory && matchesBrand && matchesPrice && matchesSearch;
            });

            // Apply Sorting
            sortProducts(sortValue);
            
            renderProducts(filteredProducts);
            updateResultCount();
        }

        // Sort Products
        function sortProducts(sortValue) {
            filteredProducts.sort((a, b) => {
                if (sortValue === 'price-low') {
                    return parseFloat(a.price) - parseFloat(b.price);
                } else if (sortValue === 'price-high') {
                    return parseFloat(b.price) - parseFloat(a.price);
                } else if (sortValue === 'name-asc') {
                    return a.name.localeCompare(b.name);
                } else if (sortValue === 'newest') {
                    return new Date(b.created_at) - new Date(a.created_at);
                }
                return 0;
            });
        }

        // Render Products
        function renderProducts(productsToRender) {
            const grid = document.getElementById('productGrid');
            const noResults = document.getElementById('noResults');

            if (productsToRender.length === 0) {
                grid.innerHTML = '';
                noResults.style.display = 'block';
                return;
            }

            noResults.style.display = 'none';
            
            grid.innerHTML = productsToRender.map(product => {
                // Handle image path
                let imagePath = product.image_url;
                if (imagePath && !imagePath.startsWith('../')) {
                    // Adjust path if needed, assuming images are in ../images/products/
                    // But if image_url is stored as relative path from root or absolute, we might need adjustment
                    // For now, trust the DB value or provide a fallback
                }
                
                // Fallback image if empty
                const imgHtml = imagePath 
                    ? `<img src="${imagePath}" alt="${product.name}" class="product-image">`
                    : `<div class="no-image-placeholder">📦</div>`;

                let productUrl = `productdetails.php?id=${product.product_id}`;
                if (currentStore) {
                    productUrl += `&store=${currentStore}`;
                }

                return `
                    <div class="product-card" onclick="window.location.href='${productUrl}'">
                        <div class="product-image-wrapper">
                            ${imgHtml}
                            <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist(${product.product_id}, this)">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="product-info">
                            <div class="product-meta">
                                <span class="product-category">${product.category_name || 'General'}</span>
                                <span class="product-brand">${product.brand_name || ''}</span>
                            </div>
                            <h3 class="product-name">${product.name}</h3>
                            <div class="delivery-info">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>${product.estimated_delivery_time || '3-5'} days delivery</span>
                            </div>
                            <div class="product-price">GH₵ ${parseFloat(product.price).toLocaleString()}</div>
                            <div class="product-footer">
                                <button class="preorder-btn" onclick="event.stopPropagation(); addToCart(${product.product_id})">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; vertical-align: middle; margin-right: 6px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Pre-Order Now
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Update result count
        function updateResultCount() {
            document.getElementById('resultCount').textContent = filteredProducts.length;
        }

        // Toggle Wishlist (Mock function)
        function toggleWishlist(id, btn) {
            btn.classList.toggle('active');
            if (btn.classList.contains('active')) {
                btn.innerHTML = `<svg width="20" height="20" fill="#e53e3e" stroke="#e53e3e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>`;
            } else {
                btn.innerHTML = `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>`;
            }
        }

        // Add to Cart
        function addToCart(productId) {
            // Create a form and submit to add_to_cart action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../actions/add_to_cart.php';
            
            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = 'product_id';
            productInput.value = productId;
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = 1;

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
    </script>

    <!-- Footer -->
    <footer style="background-color: var(--secondary); color: white; text-align: center; padding: 25px 20px; margin-top: 60px;">
        <p style="margin: 0; font-size: 0.95rem;">Powered by <strong>PreOrda</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 0.85rem; opacity: 0.8;">&copy; <?php echo date('Y'); ?> PreOrda. All rights reserved.</p>
    </footer>
</body>
</html>