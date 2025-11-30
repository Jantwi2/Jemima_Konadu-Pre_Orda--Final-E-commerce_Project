<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../view/login.php');
    exit();
}

require_once("../controllers/admin_controller.php");

// Fetch all orders with filters
$status = isset($_GET['status']) ? $_GET['status'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$orders = get_all_orders_ctr($status, $search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - PreOrda</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --success: #27ae60;
            --warning: #f1c40f;
            --light: #f8f9fa;
            --dark: #1a202c;
            --gray: #a0aec0;
            --border: #e2e8f0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0f2f5;
            color: var(--dark);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
        }

        .nav-menu {
            padding: 24px 16px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #64748b;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-item:hover, .nav-item.active {
            background-color: #f1f5f9;
            color: var(--secondary);
        }

        .nav-item.active {
            background-color: #e0f2fe;
        }

        .user-profile {
            padding: 20px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary);
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-info h4 {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .user-info p {
            font-size: 0.8rem;
            color: var(--gray);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 32px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
        }

        .page-title p {
            color: var(--gray);
            margin-top: 4px;
        }

        .header-actions {
            display: flex;
            gap: 16px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1a252f;
        }

        .btn-outline {
            background-color: white;
            border: 1px solid var(--border);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: var(--light);
        }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 32px;
        }

        .table-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 16px 24px;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            background-color: #f8fafc;
        }

        td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            color: var(--dark);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: currentColor;
        }

        .status-confirmed { background-color: #dcfce7; color: #166534; }
        .status-processing { background-color: #dbeafe; color: #1e40af; }
        .status-shipped { background-color: #e0e7ff; color: #3730a3; }
        .status-delivered { background-color: #f3e8ff; color: #6b21a8; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }

        .filters {
            display: flex;
            gap: 12px;
        }

        .filter-input {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon">P</div>
            <span class="logo-text">PreOrda</span>
        </div>

        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-item">
                <span>📊</span> Dashboard
            </a>
            <a href="usermgt.php" class="nav-item">
                <span>👥</span> Users
            </a>
            <a href="orders.php" class="nav-item active">
                <span>📦</span> Orders
            </a>
            <a href="shippers.php" class="nav-item">
                <span>🚚</span> Shippers
            </a>
            <a href="disputes.php" class="nav-item">
                <span>⚖️</span> Disputes
            </a>
            <a href="analytics.php" class="nav-item">
                <span>📈</span> Analytics
            </a>
            <a href="settings.php" class="nav-item">
                <span>⚙️</span> Settings
            </a>
        </nav>

        <div class="user-profile">
            <div class="avatar">AD</div>
            <div class="user-info">
                <h4>Admin User</h4>
                <p>Super Admin</p>
            </div>
            <a href="../actions/logout.php" style="margin-left: auto; color: var(--accent); text-decoration: none;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div class="page-title">
                <h1>Order Management</h1>
                <p>View and manage all platform orders.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline">
                    <span>📅</span> <?php echo date('M d, Y'); ?>
                </button>
                <button class="btn btn-primary">
                    <span>⬇️</span> Export Orders
                </button>
            </div>
        </header>

        <div class="table-card">
            <div class="table-header">
                <h3 class="card-title">All Orders</h3>
                <form method="GET" class="filters">
                    <input type="text" name="search" placeholder="Search orders..." class="filter-input" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <select name="status" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="processing" <?php echo $status == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $status == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $status == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">Search</button>
                    <?php if ($status || $search): ?>
                        <a href="orders.php" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.9rem; text-decoration: none;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td style="font-weight: 600;">#<?php echo $order['order_id']; ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 24px; height: 24px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                            <?php echo strtoupper(substr($order['full_name'], 0, 1)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($order['full_name']); ?>
                                    </div>
                                </td>
                                <td>GH₵ <?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <button style="background: none; border: none; cursor: pointer; color: var(--gray);">
                                        ⋮
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 32px; color: var(--gray);">
                                    No orders found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
