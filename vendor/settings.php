<?php
session_start();

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header('Location: ../view/login.php');
    exit();
}

require_once("../controllers/user_controller.php");
require_once("../helpers/encryption.php");

$vendor_id = $_SESSION['vendor_id'];
$vendor_details = get_vendor_details_ctr($vendor_id);

// Default values if not set
$business_name = $vendor_details['business_name'] ?? 'My Store';
$tagline = $vendor_details['tagline'] ?? '';
$description = $vendor_details['description'] ?? '';
$logo_url = $vendor_details['logo_url'] ?? '';
$primary_color = $vendor_details['primary_color'] ?? '#3182ce';
$secondary_color = $vendor_details['secondary_color'] ?? '#2d3748';
$background_color = $vendor_details['background_color'] ?? '#ffffff';
$accent_color = $vendor_details['accent_color'] ?? '#f7fafc';
$font_family = $vendor_details['font_family'] ?? 'Inter';
$vendor_slug = $vendor_details['vendor_slug'] ?? '';

// Encrypt the vendor slug for the shareable link
$encrypted_slug = encrypt_slug($vendor_slug);
$shareable_link = "http://" . $_SERVER['HTTP_HOST'] . "/~jemima.antwi/view/products.php?store=" . $encrypted_slug;

// Get initials for profile photo
$name_parts = explode(' ', $_SESSION['full_name']);
$initials = '';
if (count($name_parts) >= 2) {
    $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
} else {
    $initials = strtoupper(substr($_SESSION['full_name'], 0, 2));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Settings - PreOrda</title>
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

        /* --- Sidebar Styles (Unchanged) --- */
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
        
        /* --- NEW: Top Dashboard Header Bar Styles --- */
        .dashboard-header {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky; /* Makes the header stick to the top */
            top: 0;
            z-index: 50; /* Below sidebar, above content */
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
        
        /* --- Content Area Padding Adjustments --- */
        .page-content-wrapper {
            padding: 30px;
        }
        
        .header {
            /* Removed padding-top/bottom because the dashboard-header handles it */
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

        /* --- Settings Container and Form Styles (Unchanged) --- */
        .settings-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }

        .settings-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #2d3748;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3182ce;
        }

        .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            min-height: 100px;
            resize: vertical;
        }

        .form-hint {
            font-size: 13px;
            color: #718096;
            margin-top: 6px;
        }

        .color-picker-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .color-input-wrapper {
            position: relative;
        }

        .color-preview {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
        }

        .color-input {
            padding-right: 56px;
        }

        .logo-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: #f7fafc;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logo-upload-area:hover {
            border-color: #3182ce;
            background: #ebf8ff;
        }

        .logo-upload-area.has-image {
            border-style: solid;
            padding: 20px;
        }

        .logo-preview {
            max-width: 200px;
            max-height: 120px;
            margin: 0 auto;
            display: block;
        }

        .upload-icon {
            font-size: 48px;
            color: #cbd5e0;
            margin-bottom: 12px;
        }

        .upload-text {
            color: #4a5568;
            font-size: 14px;
        }

        .upload-hint {
            color: #718096;
            font-size: 13px;
            margin-top: 8px;
        }

        .btn-primary {
            background: #3182ce;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #2c5282;
        }

        .btn-secondary {
            background: white;
            color: #4a5568;
            border: 1px solid #e2e8f0;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .preview-panel {
            position: sticky;
            top: 30px;
        }

        .preview-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .preview-label {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .storefront-preview {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }

        .preview-header {
            padding: 20px;
            transition: background 0.3s;
        }

        .preview-logo {
            width: 120px;
            height: 60px;
            background: #f7fafc;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .preview-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .preview-logo-placeholder {
            color: #cbd5e0;
            font-size: 12px;
            text-align: center;
        }

        .preview-store-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            transition: color 0.3s;
        }

        .preview-tagline {
            font-size: 14px;
            color: #718096;
        }

        .preview-product {
            padding: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .preview-product-image {
            width: 100%;
            height: 140px;
            background: #f7fafc;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .preview-product-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .preview-price {
            font-size: 18px;
            font-weight: 700;
            transition: color 0.3s;
        }

        .preview-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.3s;
        }

        .font-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .font-option {
            padding: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .font-option:hover {
            border-color: #cbd5e0;
        }

        .font-option.selected {
            border-color: #3182ce;
            background: #ebf8ff;
        }

        .font-preview-text {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .font-name {
            font-size: 12px;
            color: #718096;
        }

        .share-link-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .share-link-text {
            flex: 1;
            font-family: monospace;
            color: #2d3748;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .copy-btn {
            background: white;
            border: 1px solid #cbd5e0;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background: #edf2f7;
            color: #2d3748;
        }

        /* --- Responsive Styles (Updated for new header/layout) --- */
        @media (max-width: 1200px) {
            .settings-container {
                grid-template-columns: 1fr;
            }

            .preview-panel {
                position: static;
            }
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
                display: none; /* Hide vendor name on mobile */
            }

            .page-content-wrapper {
                padding: 20px;
            }
            
            .color-picker-group {
                grid-template-columns: 1fr;
            }

            .font-selector {
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
        <a href="brandcatmgt.php" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <span>Brands & Categories</span>
        </a>
        <a href="settings.php" class="nav-item active">
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
                <span class="vendor-name-text"><?php echo htmlspecialchars($business_name); ?></span>
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
                <h1>Store Customization</h1>
                <p class="header-subtitle">Personalize your storefront with your brand colors, logo, and style</p>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'updated'): ?>
                    <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Settings updated successfully!</div>
                <?php elseif ($_GET['msg'] == 'error'): ?>
                    <div style="background: #fed7d7; color: #9b2c2c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">An error occurred. Please try again.</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="settings-container">
                <form action="../actions/update_settings.php" method="POST" enctype="multipart/form-data" class="settings-form">
                    <input type="hidden" name="update_settings" value="1">
                    
                    <div class="form-section">
                        <h2 class="section-title">Share Your Store</h2>
                        <div class="form-group">
                            <label class="form-label">Your Store Link</label>
                            <div class="share-link-box">
                                <span class="share-link-text" id="shareLink"><?php echo htmlspecialchars($shareable_link); ?></span>
                                <button type="button" class="copy-btn" onclick="copyLink()">Copy</button>
                            </div>
                            <p class="form-hint">Share this link with your customers to view your products.</p>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="section-title">Store Information</h2>
                        
                        <div class="form-group">
                            <label class="form-label">Store Name</label>
                            <input type="text" name="business_name" class="form-input" id="storeName" value="<?php echo htmlspecialchars($business_name); ?>" oninput="updatePreview()">
                            <p class="form-hint">This will be displayed as your store's main heading</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Store Tagline</label>
                            <input type="text" name="tagline" class="form-input" id="storeTagline" value="<?php echo htmlspecialchars($tagline); ?>" oninput="updatePreview()">
                            <p class="form-hint">A brief description that appears below your store name</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Store Description</label>
                            <textarea name="description" class="form-textarea" id="storeDescription"><?php echo htmlspecialchars($description); ?></textarea>
                            <p class="form-hint">Detailed description for your store's about section</p>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="section-title">Store Logo</h2>
                        
                        <div class="form-group">
                            <input type="hidden" name="existing_logo" value="<?php echo htmlspecialchars($logo_url); ?>">
                            <div class="logo-upload-area <?php echo !empty($logo_url) ? 'has-image' : ''; ?>" id="logoUploadArea" onclick="document.getElementById('logoInput').click()">
                                <div id="logoPlaceholder" style="<?php echo !empty($logo_url) ? 'display: none;' : ''; ?>">
                                    <div class="upload-icon">📤</div>
                                    <div class="upload-text">Click to upload your logo</div>
                                    <div class="upload-hint">PNG, JPG or SVG (max 2MB)</div>
                                </div>
                                <img id="logoPreviewImg" class="logo-preview" src="<?php echo htmlspecialchars($logo_url); ?>" style="<?php echo empty($logo_url) ? 'display: none;' : ''; ?>">
                            </div>
                            <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;" onchange="handleLogoUpload(event)">
                            <p class="form-hint">Recommended size: 400x200px for best results</p>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="section-title">Brand Colors</h2>
                        
                        <div class="color-picker-group">
                            <div class="form-group">
                                <label class="form-label">Primary Color</label>
                                <div class="color-input-wrapper">
                                    <input type="text" name="primary_color" class="form-input color-input" id="primaryColor" value="<?php echo htmlspecialchars($primary_color); ?>" oninput="updatePreview()">
                                    <input type="color" class="color-preview" id="primaryColorPicker" value="<?php echo htmlspecialchars($primary_color); ?>" oninput="syncColorInput('primary')">
                                </div>
                                <p class="form-hint">Main brand color for buttons and accents</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Secondary Color</label>
                                <div class="color-input-wrapper">
                                    <input type="text" name="secondary_color" class="form-input color-input" id="secondaryColor" value="<?php echo htmlspecialchars($secondary_color); ?>" oninput="updatePreview()">
                                    <input type="color" class="color-preview" id="secondaryColorPicker" value="<?php echo htmlspecialchars($secondary_color); ?>" oninput="syncColorInput('secondary')">
                                </div>
                                <p class="form-hint">Used for headings and text</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Background Color</label>
                                <div class="color-input-wrapper">
                                    <input type="text" name="background_color" class="form-input color-input" id="backgroundColor" value="<?php echo htmlspecialchars($background_color); ?>" oninput="updatePreview()">
                                    <input type="color" class="color-preview" id="backgroundColorPicker" value="<?php echo htmlspecialchars($background_color); ?>" oninput="syncColorInput('background')">
                                </div>
                                <p class="form-hint">Main background color</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Accent Color</label>
                                <div class="color-input-wrapper">
                                    <input type="text" name="accent_color" class="form-input color-input" id="accentColor" value="<?php echo htmlspecialchars($accent_color); ?>" oninput="updatePreview()">
                                    <input type="color" class="color-preview" id="accentColorPicker" value="<?php echo htmlspecialchars($accent_color); ?>" oninput="syncColorInput('accent')">
                                </div>
                                <p class="form-hint">Used for hover effects and highlights</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="section-title">Typography</h2>
                        
                        <div class="form-group">
                            <label class="form-label">Font Family</label>
                            <input type="hidden" name="font_family" id="fontFamilyInput" value="<?php echo htmlspecialchars($font_family); ?>">
                            <div class="font-selector">
                                <div class="font-option <?php echo $font_family == 'Inter' ? 'selected' : ''; ?>" data-font="'Inter', sans-serif" onclick="selectFont(this)">
                                    <div class="font-preview-text" style="font-family: 'Inter', sans-serif;">Aa</div>
                                    <div class="font-name">Inter</div>
                                </div>
                                <div class="font-option <?php echo $font_family == 'Playfair Display' ? 'selected' : ''; ?>" data-font="'Playfair Display', serif" onclick="selectFont(this)">
                                    <div class="font-preview-text" style="font-family: 'Playfair Display', serif;">Aa</div>
                                    <div class="font-name">Playfair</div>
                                </div>
                                <div class="font-option <?php echo $font_family == 'Roboto' ? 'selected' : ''; ?>" data-font="'Roboto', sans-serif" onclick="selectFont(this)">
                                    <div class="font-preview-text" style="font-family: 'Roboto', sans-serif;">Aa</div>
                                    <div class="font-name">Roboto</div>
                                </div>
                                <div class="font-option <?php echo $font_family == 'Courier Prime' ? 'selected' : ''; ?>" data-font="'Courier Prime', monospace" onclick="selectFont(this)">
                                    <div class="font-preview-text" style="font-family: 'Courier Prime', monospace;">Aa</div>
                                    <div class="font-name">Courier</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn-primary">Save Changes</button>
                        <button type="button" class="btn-secondary" onclick="window.location.reload()">Discard Changes</button>
                    </div>
                </form>

                <div class="preview-panel">
                    <div class="preview-label">Live Preview</div>
                    <div class="preview-card">
                        <div class="storefront-preview" id="storePreview">
                            <div class="preview-header" id="previewHeader">
                                <div class="preview-logo" id="previewLogoContainer">
                                    <?php if (!empty($logo_url)): ?>
                                        <img src="<?php echo htmlspecialchars($logo_url); ?>" id="previewLogoImg">
                                    <?php else: ?>
                                        <div class="preview-logo-placeholder" id="previewLogoPlaceholder">Your Logo</div>
                                        <img src="" id="previewLogoImg" style="display: none;">
                                    <?php endif; ?>
                                </div>
                                <div class="preview-store-name" id="previewStoreName"><?php echo htmlspecialchars($business_name); ?></div>
                                <div class="preview-tagline" id="previewTagline"><?php echo htmlspecialchars($tagline); ?></div>
                            </div>
                            <div class="preview-product" id="previewProduct">
                                <div class="preview-product-image"></div>
                                <div class="preview-product-name">Luxury Handbag</div>
                                <div class="preview-price" id="previewPrice">$299.00</div>
                                <button class="preview-btn" id="previewBtn">Pre-Order Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function syncColorInput(type) {
            const picker = document.getElementById(type + 'ColorPicker');
            const input = document.getElementById(type + 'Color');
            input.value = picker.value;
            updatePreview();
        }

        function handleLogoUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update form preview
                    const previewImg = document.getElementById('logoPreviewImg');
                    const placeholder = document.getElementById('logoPlaceholder');
                    const uploadArea = document.getElementById('logoUploadArea');
                    
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    placeholder.style.display = 'none';
                    uploadArea.classList.add('has-image');

                    // Update live preview
                    const livePreviewImg = document.getElementById('previewLogoImg');
                    const livePlaceholder = document.getElementById('previewLogoPlaceholder');
                    
                    livePreviewImg.src = e.target.result;
                    livePreviewImg.style.display = 'block';
                    if(livePlaceholder) livePlaceholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        function selectFont(element) {
            // Remove selected class from all options
            document.querySelectorAll('.font-option').forEach(opt => opt.classList.remove('selected'));
            // Add selected class to clicked option
            element.classList.add('selected');
            
            // Update hidden input
            const fontName = element.querySelector('.font-name').innerText;
            // Map display name to value if needed, or just use name
            let fontValue = 'Inter';
            if(fontName === 'Playfair') fontValue = 'Playfair Display';
            else if(fontName === 'Roboto') fontValue = 'Roboto';
            else if(fontName === 'Courier') fontValue = 'Courier Prime';
            
            document.getElementById('fontFamilyInput').value = fontValue;
            
            updatePreview();
        }

        function updatePreview() {
            // Get values
            const storeName = document.getElementById('storeName').value;
            const tagline = document.getElementById('storeTagline').value;
            const primaryColor = document.getElementById('primaryColor').value;
            const secondaryColor = document.getElementById('secondaryColor').value;
            const backgroundColor = document.getElementById('backgroundColor').value;
            const accentColor = document.getElementById('accentColor').value;
            
            // Get selected font
            const selectedFontOption = document.querySelector('.font-option.selected');
            const fontStack = selectedFontOption ? selectedFontOption.getAttribute('data-font') : "'Inter', sans-serif";

            // Update Preview Elements
            document.getElementById('previewStoreName').textContent = storeName || 'Store Name';
            document.getElementById('previewTagline').textContent = tagline || 'Your store tagline goes here';
            
            // Update Colors
            const previewHeader = document.getElementById('previewHeader');
            const previewProduct = document.getElementById('previewProduct');
            const previewBtn = document.getElementById('previewBtn');
            const previewStoreName = document.getElementById('previewStoreName');
            const previewPrice = document.getElementById('previewPrice');

            // Apply Background
            previewHeader.style.backgroundColor = backgroundColor;
            previewProduct.style.backgroundColor = backgroundColor;

            // Apply Secondary Color (Text)
            previewStoreName.style.color = secondaryColor;
            
            // Apply Primary Color (Buttons/Highlights)
            previewBtn.style.backgroundColor = primaryColor;
            previewBtn.style.color = '#ffffff'; // Assuming white text on primary buttons
            previewPrice.style.color = primaryColor;

            // Apply Font
            document.getElementById('storePreview').style.fontFamily = fontStack;
        }

        function copyLink() {
            const linkText = document.getElementById('shareLink').innerText;
            navigator.clipboard.writeText(linkText).then(() => {
                const btn = document.querySelector('.copy-btn');
                const originalText = btn.innerText;
                btn.innerText = 'Copied!';
                setTimeout(() => {
                    btn.innerText = originalText;
                }, 2000);
            });
        }

        // Initialize preview on load
        window.addEventListener('DOMContentLoaded', updatePreview);
    </script>
</body>
</html>