<?php
session_start();

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header('Location: ../view/login.php');
    exit();
}

// Get vendor information from session
$vendor_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Vendor';
$business_name = isset($_SESSION['business_name']) ? $_SESSION['business_name'] : 'My Store';
$vendor_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Get initials for profile photo
$name_parts = explode(' ', $vendor_name);
$initials = '';
if (count($name_parts) >= 2) {
    $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
} else {
    $initials = strtoupper(substr($vendor_name, 0, 2));
}

require_once("../controllers/product_controller.php");
$vendor_id = $_SESSION['vendor_id'];

// Fetch data
$products = get_vendor_products_ctr($vendor_id);
$categories = get_all_categories_ctr();
$brands = get_all_brands_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listings - PreOrda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            color: #2d3748;
        }

        /* --- Global Layout Styles (Copied from vendor_setting.php) --- */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: #1a202c;
            color: white;
            padding: 20px;
            overflow-y: auto;
            z-index: 100;
        }

        .logo {
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            max-width: 180px;
            height: auto;
        }

        .nav-item {
            padding: 12px 16px;
            margin-bottom: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.2s;
            text-decoration: none;
            color: white;
        }

        .nav-item:hover {
            background: #2d3748;
        }

        .nav-item.active {
            background: #2b6cb0;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .vendor-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .vendor-name-text {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
        }

        .profile-photo-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #3182ce;
            cursor: pointer;
        }

        .profile-photo-initials {
            font-size: 16px;
            font-weight: 700;
            color: #3182ce;
        }

        .action-icons {
            display: flex;
            gap: 20px;
        }

        .action-icon {
            width: 24px;
            height: 24px;
            color: #4a5568;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .action-icon:hover {
            color: #3182ce;
        }
        
        .page-content-wrapper {
            padding: 30px;
        }
        
        .header {
            background: white;
            padding: 24px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header-subtitle {
            font-size: 14px;
            color: #718096;
        }
        /* --- End Global Layout Styles --- */

        /* --- Product Page Specific Styles --- */
        .page-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .search-container {
            flex-grow: 1;
            max-width: 400px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 14px 10px 40px; /* Space for icon */
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #a0aec0;
        }

        .btn-primary {
            background: #3182ce;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #2c5282;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 2px 4px rgba(0,0,0,0.06);
        }

        .product-image-area {
            height: 200px;
            background: #f7fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        .product-image-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }
        
        .status-active {
            background-color: #38a169; /* Green */
        }

        .status-draft {
            background-color: #e53e3e; /* Red */
        }
        
        .status-pending {
            background-color: #dd6b20; /* Orange */
        }

        .card-content {
            padding: 15px;
        }

        .product-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2d3748;
        }

        .product-price-sku {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .product-price {
            font-weight: 700;
            color: #3182ce;
        }

        .product-sku {
            color: #718096;
        }
        
        .product-stats {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #4a5568;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .edit-link {
            color: #3182ce;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .edit-link:hover {
            color: #2c5282;
            text-decoration: underline;
        }

        /* --- Responsive Styles --- */
        /* --- Modal Styles --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            display: flex;
            opacity: 1;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 90%;
            max-width: 600px;
            position: relative;
            transform: scale(0.95);
            transition: transform 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-header {
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-radius: 16px 16px 0 0;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
        }

        .close-modal {
            color: #a0aec0;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
            line-height: 1;
        }

        .close-modal:hover {
            color: #4a5568;
        }

        .modal-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
            font-size: 14px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #f8fafc;
            border-radius: 0 0 16px 16px;
        }

        .btn-secondary {
            background: white;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        .btn-danger {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background: #c53030;
            color: white;
            border-color: #c53030;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .logo {
                font-size: 18px;
            }

            .nav-item span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }

            .dashboard-header {
                padding: 15px 20px;
            }
            
            .vendor-name-text {
                display: none;
            }

            .page-content-wrapper {
                padding: 20px;
            }
            
            .page-actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .search-container {
                max-width: 100%;
                order: 2;
            }

            .btn-primary {
                order: 1;
                justify-content: center;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../images/logo_c.png" alt="PreOrda">
        </div>
        <a href="dashboard.php" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Overview</span>
        </a>
        <a href="products.php" class="nav-item active">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <span>Products</span>
        </a>
        <a href="orders.php" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span>Orders</span>
        </a>
        <a href="customers.php" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span>Customers</span>
        </a>
        <a href="brandcatmgt.php" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <span>Brands & Categories</span>
        </a>
        <a href="settings.php" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Settings</span>
        </a>
        
        <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid #2d3748;">
            <a href="../actions/logout.php" class="nav-item" style="color: #fc8181;">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="vendor-info">
                <div class="profile-photo-wrapper">
                    <span class="profile-photo-initials"><?php echo htmlspecialchars($initials); ?></span>
                </div>
                <span class="vendor-name-text"><?php echo htmlspecialchars($vendor_name); ?></span>
            </div>
            <div class="action-icons">
                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.16 6 8.356 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
        
        <div class="page-content-wrapper">
            <div class="header">
                <h1>Product Listings (Pre-Orders)</h1>
                <p class="header-subtitle">Manage all pre-order items available on your storefront</p>
            </div>

            <div class="page-actions">
                <div class="search-container">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" class="search-input" placeholder="Search by name, SKU, or category...">
                </div>
                
                <button class="btn-primary" onclick="openModal('addProductModal')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Add New Product
                </button>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'success'): ?>
                    <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Product added successfully!</div>
                <?php elseif ($_GET['msg'] == 'updated'): ?>
                    <div style="background: #bee3f8; color: #2c5282; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Product updated successfully!</div>
                <?php elseif ($_GET['msg'] == 'deleted'): ?>
                    <div style="background: #fed7d7; color: #9b2c2c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Product deleted successfully!</div>
                <?php elseif ($_GET['msg'] == 'error'): ?>
                    <div style="background: #fed7d7; color: #9b2c2c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">An error occurred. Please try again.</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="product-grid">
                <?php if (empty($products)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #718096;">
                        <svg style="width: 60px; height: 60px; margin-bottom: 15px; color: #cbd5e0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3>No products found</h3>
                        <p>Start by adding your first product to the catalog.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image-area">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='../images/Preorda.png'">
                                <?php 
                                $status_class = 'status-active';
                                if ($product['stock_status'] == 'out_of_stock') $status_class = 'status-draft';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo strtoupper(str_replace('_', ' ', $product['stock_status'])); ?></span>
                            </div>
                            <div class="card-content">
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="product-price-sku">
                                    <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <span class="product-sku">ID: #<?php echo $product['product_id']; ?></span>
                                </div>
                                <div class="product-stats">
                                    <span>Added: <?php echo date('M d', strtotime($product['created_at'])); ?></span>
                                    <div style="display: flex; gap: 10px;">
                                        <a href="#" class="edit-link" onclick='openEditModal(<?php echo json_encode($product); ?>)'>Edit</a>
                                        <a href="../actions/manage_product.php?delete=<?php echo $product['product_id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Product</h2>
                <span class="close-modal" onclick="closeModal('addProductModal')">&times;</span>
            </div>
            <form action="../actions/manage_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="add_product" value="1">
                    
                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-input" required placeholder="e.g. Luxury Leather Bag">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="price" class="form-input" step="0.01" required placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stock Status</label>
                            <select name="stock_status" class="form-select">
                                <option value="available">Available</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estimated Delivery Time (Days)</label>
                        <input type="number" name="estimated_delivery_time" class="form-input" required placeholder="e.g. 7">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Brand</label>
                            <select name="brand" class="form-select" required>
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea" required placeholder="Describe your product..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('addProductModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Product</h2>
                <span class="close-modal" onclick="closeModal('editProductModal')">&times;</span>
            </div>
            <form action="../actions/manage_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="edit_product" value="1">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <input type="hidden" name="existing_image" id="edit_existing_image">
                    
                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" id="edit_name" class="form-input" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="price" id="edit_price" class="form-input" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stock Status</label>
                            <select name="stock_status" id="edit_stock_status" class="form-select">
                                <option value="available">Available</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estimated Delivery Time (Days)</label>
                        <input type="number" name="estimated_delivery_time" id="edit_estimated_delivery_time" class="form-input" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" id="edit_category" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Brand</label>
                            <select name="brand" id="edit_brand" class="form-select" required>
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-textarea" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Change Image (Optional)</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                        <p style="font-size: 12px; color: #718096; margin-top: 5px;">Leave blank to keep current image</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editProductModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = "flex";
            // Trigger reflow
            modal.offsetHeight;
            modal.classList.add('show');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);
        }

        function openEditModal(product) {
            document.getElementById('edit_product_id').value = product.product_id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_category').value = product.category_id;
            document.getElementById('edit_brand').value = product.brand_id;
            document.getElementById('edit_stock_status').value = product.stock_status;
            document.getElementById('edit_estimated_delivery_time').value = product.estimated_delivery_time;
            document.getElementById('edit_existing_image').value = product.image_url;
            
            openModal('editProductModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal(event.target.id);
            }
        }
    </script>
        </div>
    </div>
</body>
</html>