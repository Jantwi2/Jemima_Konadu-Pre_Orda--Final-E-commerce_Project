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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Listings - PreOrda</title>
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

        /* --- Global Layout Styles --- */
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

        /* --- Customer Page Specific Styles --- */
        .customer-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .customer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
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

        .btn-secondary {
            background: white;
            color: #4a5568;
            border: 1px solid #e2e8f0;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-secondary:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }


        /* Customer Table */
        .customer-table {
            width: 100%;
            border-collapse: separate; 
            border-spacing: 0;
            font-size: 14px;
        }
        
        .customer-table th, .customer-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .customer-table th {
            background: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .customer-table tr:hover {
            background: #ebf8ff;
        }

        .customer-table tr:last-child td {
            border-bottom: none;
        }
        
        .customer-name {
            font-weight: 600;
            color: #2d3748;
        }

        .customer-email {
            color: #718096;
            font-size: 13px;
            display: block;
        }

        .stat-value {
            font-weight: 700;
            color: #3182ce;
        }
        
        .view-profile-btn {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }
        
        .view-profile-btn:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }
        
        /* --- Responsive Styles --- */
        @media (max-width: 1024px) {
            .customer-table th:nth-child(4), 
            .customer-table td:nth-child(4) {
                display: none; /* Hide Last Order Date on mid-size screens */
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
                display: none;
            }

            .page-content-wrapper {
                padding: 20px;
            }
            
            .customer-actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .search-container {
                max-width: 100%;
            }
            
            .customer-container {
                padding: 10px;
                overflow-x: auto;
            }

            .customer-table {
                min-width: 700px; /* Ensure table remains readable */
            }

            .customer-table th:nth-child(3), 
            .customer-table td:nth-child(3) {
                display: none; /* Hide total spent on smaller phones */
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
        <a href="customers.php" class="nav-item active"> <!-- ACTIVE ITEM CHANGED TO CUSTOMERS -->
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
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="vendor-info">
                <div class="profile-photo-wrapper">
                    <span class="profile-photo-initials">A B</span>
                </div>
                <span class="vendor-name-text">Amma Boutique Vendor</span>
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
            <!-- Page Header -->
            <div class="header">
                <h1>Customer Management</h1>
                <p class="header-subtitle">View and manage customers who have placed pre-orders with your store</p>
            </div>

            <!-- Customer Actions: Search and Export -->
            <div class="customer-actions">
                <div class="search-container">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" class="search-input" placeholder="Search by name or email address...">
                </div>
                
                <button class="btn-secondary" onclick="alert('Exporting customer data...')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Customers
                </button>
            </div>

            <!-- Customer Table Container -->
            <div class="customer-container">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Pre-Orders</th>
                            <th>Total Spent</th>
                            <th class="hidden-on-mobile">Last Order Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span class="customer-name">Jane Doe</span>
                                <span class="customer-email">jane.doe@example.com</span>
                            </td>
                            <td><span class="stat-value">3</span></td>
                            <td><span class="stat-value">$1,600.00</span></td>
                            <td class="hidden-on-mobile">2025-11-20</td>
                            <td><button class="view-profile-btn" onclick="alert('Viewing Jane Doe\'s profile')">View Profile</button></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="customer-name">Mark Smith</span>
                                <span class="customer-email">mark.smith@web.de</span>
                            </td>
                            <td><span class="stat-value">1</span></td>
                            <td><span class="stat-value">$199.00</span></td>
                            <td class="hidden-on-mobile">2025-11-21</td>
                            <td><button class="view-profile-btn" onclick="alert('Viewing Mark Smith\'s profile')">View Profile</button></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="customer-name">Sarah Lee</span>
                                <span class="customer-email">sarah.lee@gmail.com</span>
                            </td>
                            <td><span class="stat-value">5</span></td>
                            <td><span class="stat-value">$5,020.50</span></td>
                            <td class="hidden-on-mobile">2025-10-15</td>
                            <td><button class="view-profile-btn" onclick="alert('Viewing Sarah Lee\'s profile')">View Profile</button></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="customer-name">David Chen</span>
                                <span class="customer-email">david.c@co.uk</span>
                            </td>
                            <td><span class="stat-value">2</span></td>
                            <td><span class="stat-value">$400.00</span></td>
                            <td class="hidden-on-mobile">2025-11-24</td>
                            <td><button class="view-profile-btn" onclick="alert('Viewing David Chen\'s profile')">View Profile</button></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="customer-name">Elena Rodriguez</span>
                                <span class="customer-email">e.rodriguez@mail.com</span>
                            </td>
                            <td><span class="stat-value">1</span></td>
                            <td><span class="stat-value">$450.00</span></td>
                            <td class="hidden-on-mobile">2025-11-18</td>
                            <td><button class="view-profile-btn" onclick="alert('Viewing Elena Rodriguez\'s profile')">View Profile</button></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="customer-name">John Williams</span>
                                <span class="customer-email">john.w@live.com</span>
                            </td>
                            <td><span class="stat-value">8</span></td>
                            <td><span class="stat-value">$8,900.00</span></td>
                            <td class="hidden-on-mobile">2025-11-10</td>
                            <td><button class="view-profile-btn" onclick="alert('Viewing John Williams\'s profile')">View Profile</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</body>
</html>