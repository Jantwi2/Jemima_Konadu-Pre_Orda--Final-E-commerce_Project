<?php
session_start();
require_once("../controllers/admin_controller.php");

// Fetch stats
$stats = get_dashboard_stats_ctr();
$pending_vendors = get_pending_vendors_ctr();

// Fetch all users with filters
$role = isset($_GET['role']) ? $_GET['role'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$all_users = get_all_users_ctr($role, $search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - PreOrda</title>
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

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #219150;
        }

        .btn-danger {
            background-color: var(--accent);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-outline {
            background-color: white;
            border: 1px solid var(--border);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: var(--light);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
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

        /* Tables */
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

        .status-active { background-color: #dcfce7; color: #166534; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-blocked { background-color: #fee2e2; color: #991b1b; }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

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
            .stats-grid { grid-template-columns: 1fr; }
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
            <a href="usermgt.php" class="nav-item active">
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
                <h1>User Management</h1>
                <p>Manage users, vendors, and approvals.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline">
                    <span>📅</span> <?php echo date('M d, Y'); ?>
                </button>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?php echo number_format($stats['users']); ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background-color: #f3e8ff; color: #6b21a8;">
                        👥
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approvals Section -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="card-title">Pending Vendor Approvals</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Vendor Name</th>
                            <th>Store Name</th>
                            <th>Email</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pending_vendors)): ?>
                            <?php foreach ($pending_vendors as $vendor): ?>
                            <tr id="vendor-<?php echo $vendor['vendor_id']; ?>">
                                <td><?php echo htmlspecialchars($vendor['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($vendor['business_name']); ?></td>
                                <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                                <td><?php echo isset($vendor['created_at']) ? date('M j, Y', strtotime($vendor['created_at'])) : 'N/A'; ?></td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-success btn-sm" onclick="approveVendor(<?php echo $vendor['vendor_id']; ?>, '<?php echo addslashes($vendor['business_name']); ?>')">Approve</button>
                                        <button class="btn btn-danger btn-sm" onclick="rejectVendor(<?php echo $vendor['vendor_id']; ?>)">Reject</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 32px; color: var(--gray);">No pending approvals.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Users Section -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="card-title">All Users</h3>
                <form method="GET" class="filters">
                    <input type="text" name="search" placeholder="Search users..." class="filter-input" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <select name="role" class="filter-input" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="vendor" <?php echo $role == 'vendor' ? 'selected' : ''; ?>>Vendor</option>
                        <option value="buyer" <?php echo $role == 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                        <option value="admin" <?php echo $role == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">Search</button>
                    <?php if ($role || $search): ?>
                        <a href="usermgt.php" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.9rem; text-decoration: none;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_users)): ?>
                            <?php foreach ($all_users as $user): ?>
                            <tr>
                                <td style="font-weight: 600;">#USR-<?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo ucfirst($user['user_type']); ?></td>
                                <td><span class="status-badge status-active">Active</span></td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button style="background: none; border: none; cursor: pointer; color: var(--gray);">
                                        ⋮
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 32px; color: var(--gray);">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function approveVendor(id, name) {
            if (confirm(`Are you sure you want to approve ${name}?`)) {
                fetch('../actions/approve_vendor.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `vendor_id=${id}&action=approve`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`vendor-${id}`);
                        row.style.backgroundColor = '#dcfce7';
                        setTimeout(() => {
                            row.remove();
                            alert(data.message);
                            const tbody = document.querySelector('.table-responsive table tbody');
                            if (tbody.querySelectorAll('tr').length === 0) {
                                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 32px; color: var(--gray);">No pending approvals.</td></tr>';
                            }
                        }, 500);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the vendor.');
                });
            }
        }

        function rejectVendor(id) {
            if (confirm('Are you sure you want to reject this application?')) {
                fetch('../actions/approve_vendor.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `vendor_id=${id}&action=reject`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`vendor-${id}`);
                        row.style.backgroundColor = '#fee2e2';
                        setTimeout(() => {
                            row.remove();
                            alert(data.message);
                            const tbody = document.querySelector('.table-responsive table tbody');
                            if (tbody.querySelectorAll('tr').length === 0) {
                                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 32px; color: var(--gray);">No pending approvals.</td></tr>';
                            }
                        }, 500);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the vendor.');
                });
            }
        }


    </script>
</body>
</html>
