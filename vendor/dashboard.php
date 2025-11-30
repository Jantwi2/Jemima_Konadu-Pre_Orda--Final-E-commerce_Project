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

// Include controllers for dashboard stats
require_once("../controllers/product_controller.php");
require_once("../controllers/order_controller.php");

$vendor_id = $_SESSION['vendor_id'];

// Fetch Dashboard Stats
$total_revenue = get_vendor_revenue_ctr($vendor_id);
$active_orders = get_vendor_active_orders_count_ctr($vendor_id);
$total_products = get_vendor_product_count_ctr($vendor_id);
$total_customers = get_vendor_customer_count_ctr($vendor_id);

// Fetch Recent Orders
$recent_orders = get_vendor_recent_orders_ctr($vendor_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - PreOrda</title>
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
            padding: 30px;
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 24px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
        }

        .store-url {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            background: #ebf8ff;
            border-radius: 8px;
            font-size: 14px;
            color: #2c5282;
        }

        .copy-btn {
            background: #3182ce;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .copy-btn:hover {
            background: #2c5282;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-change {
            font-size: 13px;
            color: #48bb78;
            margin-top: 8px;
        }

        .content-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
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

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            font-size: 13px;
            color: #4a5568;
            text-transform: uppercase;
        }

        .table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tr:hover {
            background: #f7fafc;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-warning {
            background: #fef5e7;
            color: #744210;
        }

        .badge-info {
            background: #bee3f8;
            color: #2c5282;
        }

        .badge-danger {
            background: #fed7d7;
            color: #9b2c2c;
        }

        .product-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            background: transparent;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-icon:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
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
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            min-height: 100px;
            resize: vertical;
        }

        .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        /* Dashboard Header Styles */
        .dashboard-header {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 30px;
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
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../images/logo_c.png" alt="PreOrda">
        </div>
        <a href="dashboard.php" class="nav-item active">
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
                <div>
                    <div class="vendor-name-text"><?php echo htmlspecialchars($business_name); ?></div>
                    <div style="font-size: 12px; color: #718096;">Dashboard Overview</div>
                </div>
            </div>
            
            <div class="action-icons">
                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.16 6 8.356 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <a href="settings.php" style="text-decoration: none;">
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div id="overviewView">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
                    <div class="stat-change">Lifetime earnings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Active Pre-Orders</div>
                    <div class="stat-value"><?php echo $active_orders; ?></div>
                    <div class="stat-change">Pending & Confirmed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Products</div>
                    <div class="stat-value"><?php echo $total_products; ?></div>
                    <div class="stat-change">In your catalog</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-value"><?php echo $total_customers; ?></div>
                    <div class="stat-change">Unique buyers</div>
                </div>
            </div>

            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Recent Orders</h2>
                    <button class="btn-primary" onclick="viewAllOrders()">View All</button>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Arrival Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px; color: #718096;">No orders found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#ORD-<?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($order['product_name']); ?>
                                        <?php if ($order['item_count'] > 1): ?>
                                            <span style="font-size: 11px; color: #718096;">(+<?php echo $order['item_count'] - 1; ?> more)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                    <td>
                                        <?php 
                                        $status_class = 'badge-info';
                                        if ($order['status'] === 'confirmed' || $order['status'] === 'delivered') $status_class = 'badge-success';
                                        if ($order['status'] === 'pending') $status_class = 'badge-warning';
                                        if ($order['status'] === 'cancelled') $status_class = 'badge-danger'; // You might need to define badge-danger CSS
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
                                    </td>
                                    <td><?php echo $order['estimated_delivery_date'] ? date('M d, Y', strtotime($order['estimated_delivery_date'])) : 'TBD'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal" id="productModal">
        <div class="modal-content">
            <h2 class="modal-header">Add New Product</h2>
            <div class="form-group">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-input" placeholder="Enter product name">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-textarea" placeholder="Product description..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Price</label>
                <input type="number" class="form-input" placeholder="0.00" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Expected Arrival Date</label>
                <input type="date" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Payment Option</label>
                <select class="form-select">
                    <option>Full Payment</option>
                    <option>Deposit Required</option>
                    <option>Payment on Arrival</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deposit Amount (%)</label>
                <input type="number" class="form-input" placeholder="30" min="0" max="100">
            </div>
            <div class="form-group">
                <label class="form-label">Product Image URL</label>
                <input type="text" class="form-input" placeholder="https://...">
            </div>
            <div class="modal-actions">
                <button class="btn-secondary" onclick="closeProductModal()">Cancel</button>
                <button class="btn-primary" onclick="saveProduct()">Add Product</button>
            </div>
        </div>
    </div>

    <script>
        function copyStoreUrl() {
            const url = 'PreOrda.com/amma-boutique';
            navigator.clipboard.writeText(url).then(() => {
                const btn = event.target;
                btn.textContent = 'Copied!';
                setTimeout(() => {
                    btn.textContent = 'Copy Link';
                }, 2000);
            });
        }

        function viewAllOrders() {
            window.location.href = 'orders.php';
        }

        function openProductModal() {
            document.getElementById('productModal').classList.add('active');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        function saveProduct() {
            alert('Product saved successfully!');
            closeProductModal();
        }

        document.getElementById('productModal').addEventListener('click', (e) => {
            if (e.target.id === 'productModal') {
                closeProductModal();
            }
        });
    </script>
</body>
</html>