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
$vendor_id = $_SESSION['user_id'];
$categories = get_vendor_categories_ctr($vendor_id);
$brands = get_vendor_brands_ctr($vendor_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand & Category Management - PreOrda</title>
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

        /* --- Page Specific Styles --- */
        .management-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .management-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        .btn-add {
            background: #3182ce;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add:hover {
            background: #2c5282;
        }

        .list-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .list-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item:hover {
            background: #f7fafc;
        }

        .item-name {
            font-weight: 500;
            color: #4a5568;
        }

        .item-actions {
            display: flex;
            gap: 10px;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            color: #a0aec0;
            transition: color 0.2s;
            padding: 4px;
        }

        .btn-icon:hover {
            color: #3182ce;
        }

        .btn-icon.delete:hover {
            color: #e53e3e;
        }

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
            max-width: 500px;
            position: relative;
            transform: scale(0.95);
            transition: transform 0.3s ease;
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

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
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
        }

        .btn-primary:hover {
            background: #2c5282;
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

            .management-grid {
                grid-template-columns: 1fr;
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
        <a href="products.php" class="nav-item">
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
        <a href="brandcatmgt.php" class="nav-item active">
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
                <h1>Brand & Category Management</h1>
                <p class="header-subtitle">Manage the categories and brands available for your products</p>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <?php if (strpos($_GET['msg'], 'added') !== false): ?>
                    <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Item added successfully!</div>
                <?php elseif (strpos($_GET['msg'], 'updated') !== false): ?>
                    <div style="background: #bee3f8; color: #2c5282; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Item updated successfully!</div>
                <?php elseif (strpos($_GET['msg'], 'deleted') !== false): ?>
                    <div style="background: #fed7d7; color: #9b2c2c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Item deleted successfully!</div>
                <?php elseif ($_GET['msg'] == 'error'): ?>
                    <div style="background: #fed7d7; color: #9b2c2c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">An error occurred. Please try again.</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="management-grid">
                <!-- Categories Section -->
                <div class="management-card">
                    <div class="card-header">
                        <div class="card-title">Categories</div>
                        <button class="btn-add" onclick="openModal('addCategoryModal')">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New
                        </button>
                    </div>
                    <div class="list-container">
                        <?php if (empty($categories)): ?>
                            <div style="padding: 20px; text-align: center; color: #718096;">No categories found.</div>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <div class="list-item">
                                    <span class="item-name"><?php echo htmlspecialchars($cat['name']); ?></span>
                                    <div class="item-actions">
                                        <button class="btn-icon" onclick='openEditCategoryModal(<?php echo json_encode($cat); ?>)'>
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <a href="../actions/manage_brand_cat.php?delete_cat=<?php echo $cat['category_id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure? This might affect products using this category.');">
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Brands Section -->
                <div class="management-card">
                    <div class="card-header">
                        <div class="card-title">Brands</div>
                        <button class="btn-add" onclick="openModal('addBrandModal')">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New
                        </button>
                    </div>
                    <div class="list-container">
                        <?php if (empty($brands)): ?>
                            <div style="padding: 20px; text-align: center; color: #718096;">No brands found.</div>
                        <?php else: ?>
                            <?php foreach ($brands as $brand): ?>
                                <div class="list-item">
                                    <span class="item-name"><?php echo htmlspecialchars($brand['name']); ?></span>
                                    <div class="item-actions">
                                        <button class="btn-icon" onclick='openEditBrandModal(<?php echo json_encode($brand); ?>)'>
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <a href="../actions/manage_brand_cat.php?delete_brand=<?php echo $brand['brand_id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure? This might affect products using this brand.');">
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Category</h2>
                <span class="close-modal" onclick="closeModal('addCategoryModal')">&times;</span>
            </div>
            <form action="../actions/manage_brand_cat.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="add_category" value="1">
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-input" required placeholder="e.g. Electronics">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('addCategoryModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Category</h2>
                <span class="close-modal" onclick="closeModal('editCategoryModal')">&times;</span>
            </div>
            <form action="../actions/manage_brand_cat.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="edit_category" value="1">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="edit_category_name" class="form-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editCategoryModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div id="addBrandModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Brand</h2>
                <span class="close-modal" onclick="closeModal('addBrandModal')">&times;</span>
            </div>
            <form action="../actions/manage_brand_cat.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="add_brand" value="1">
                    <div class="form-group">
                        <label class="form-label">Brand Name</label>
                        <input type="text" name="name" class="form-input" required placeholder="e.g. Nike">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('addBrandModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Add Brand</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div id="editBrandModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Brand</h2>
                <span class="close-modal" onclick="closeModal('editBrandModal')">&times;</span>
            </div>
            <form action="../actions/manage_brand_cat.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="edit_brand" value="1">
                    <input type="hidden" name="brand_id" id="edit_brand_id">
                    <div class="form-group">
                        <label class="form-label">Brand Name</label>
                        <input type="text" name="name" id="edit_brand_name" class="form-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editBrandModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Update Brand</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = "flex";
            modal.offsetHeight; // Trigger reflow
            modal.classList.add('show');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);
        }

        function openEditCategoryModal(category) {
            document.getElementById('edit_category_id').value = category.category_id;
            document.getElementById('edit_category_name').value = category.name;
            openModal('editCategoryModal');
        }

        function openEditBrandModal(brand) {
            document.getElementById('edit_brand_id').value = brand.brand_id;
            document.getElementById('edit_brand_name').value = brand.name;
            openModal('editBrandModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal(event.target.id);
            }
        }
    </script>
</body>
</html>
