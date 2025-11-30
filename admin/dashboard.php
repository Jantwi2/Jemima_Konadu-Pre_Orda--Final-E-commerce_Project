<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../view/login.php');
    exit();
}

require_once("../controllers/admin_controller.php");

// Fetch dashboard stats
$stats = get_dashboard_stats_ctr();
$recent_orders = get_recent_orders_ctr(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PreOrda</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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

        .nav-icon {
            width: 20px;
            height: 20px;
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--dark);
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .trend {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 12px;
        }

        .trend.up { color: var(--success); }
        .trend.down { color: var(--accent); }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Recent Orders Table */
        .table-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        @media (max-width: 1024px) {
            .charts-section { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px; }
            .mobile-toggle { display: block; }
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
            <a href="dashboard.php" class="nav-item active">
                <span>📊</span> Dashboard
            </a>
            <a href="usermgt.php" class="nav-item">
                <span>👥</span> Users
            </a>
            <a href="orders.php" class="nav-item">
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
                <h1>Dashboard Overview</h1>
                <p>Welcome back, Admin! Here's what's happening today.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline">
                    <span>📅</span> <?php echo date('M d, Y'); ?>
                </button>
                <button class="btn btn-primary">
                    <span>⬇️</span> Export Report
                </button>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">GH₵ <?php echo number_format($stats['revenue'], 2); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background-color: #dcfce7; color: #166534;">
                        💰
                    </div>
                </div>
                <div class="trend up">
                    <span>↗</span> 12% vs last month
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?php echo number_format($stats['orders']); ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background-color: #dbeafe; color: #1e40af;">
                        📦
                    </div>
                </div>
                <div class="trend up">
                    <span>↗</span> 8% vs last month
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?php echo number_format($stats['users']); ?></div>
                        <div class="stat-label">Active Users</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background-color: #f3e8ff; color: #6b21a8;">
                        👥
                    </div>
                </div>
                <div class="trend up">
                    <span>↗</span> 24 new this week
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?php echo number_format($stats['disputes']); ?></div>
                        <div class="stat-label">Open Disputes</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background-color: #fee2e2; color: #991b1b;">
                        ⚖️
                    </div>
                </div>
                <div class="trend down">
                    <span>↘</span> 2 unresolved
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-card">
                <div class="card-header">
                    <h3 class="card-title">Revenue Overview</h3>
                    <select style="padding: 6px; border-radius: 6px; border: 1px solid var(--border);">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="card-header">
                    <h3 class="card-title">Order Status</h3>
                </div>
                <div class="chart-wrapper">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="card-title">Recent Orders</h3>
                <a href="orders.php" class="btn btn-outline" style="font-size: 0.9rem; padding: 8px 16px;">View All</a>
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
                        <?php if (!empty($recent_orders)): ?>
                            <?php foreach ($recent_orders as $order): ?>
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
                                    No recent orders found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue',
                    data: [1200, 1900, 3000, 5000, 2300, 3400, 4500],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Order Status Chart
        const orderCtx = document.getElementById('orderChart').getContext('2d');
        new Chart(orderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Processing', 'Cancelled'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: ['#27ae60', '#3498db', '#e74c3c'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>
