<?php
session_start();
require_once("../controllers/admin_controller.php");
require_once("../controllers/dispute_controller.php");

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../view/login.php');
    exit();
}

$disputes = get_all_disputes_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disputes - PreOrda</title>
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

        .status-open { background-color: #fee2e2; color: #991b1b; }
        .status-resolved { background-color: #dcfce7; color: #166534; }
        .status-rejected { background-color: #f1f5f9; color: #64748b; }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
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
            <a href="orders.php" class="nav-item">
                <span>📦</span> Orders
            </a>
            <a href="shippers.php" class="nav-item">
                <span>🚚</span> Shippers
            </a>
            <a href="disputes.php" class="nav-item active">
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
                <h1>Dispute Resolution Center</h1>
                <p>Manage and resolve buyer disputes.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline">
                    <span>📅</span> <?php echo date('M d, Y'); ?>
                </button>
            </div>
        </header>

        <div class="table-card">
            <div class="table-header">
                <h3 class="card-title">All Disputes</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Dispute ID</th>
                            <th>Order ID</th>
                            <th>Buyer</th>
                            <th>Vendor</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($disputes)): ?>
                            <?php foreach ($disputes as $dispute): ?>
                            <tr id="dispute-<?php echo $dispute['dispute_id']; ?>">
                                <td style="font-weight: 600;">#DSP-<?php echo $dispute['dispute_id']; ?></td>
                                <td>#<?php echo $dispute['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($dispute['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($dispute['vendor_name']); ?></td>
                                <td><?php echo htmlspecialchars($dispute['reason']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($dispute['status']); ?>">
                                        <?php echo ucfirst($dispute['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($dispute['created_at'])); ?></td>
                                <td>
                                    <?php if ($dispute['status'] == 'open'): ?>
                                        <button class="btn btn-primary btn-sm" onclick="resolveDispute(<?php echo $dispute['dispute_id']; ?>, 'resolved')">Resolve</button>
                                        <button class="btn btn-outline btn-sm" onclick="resolveDispute(<?php echo $dispute['dispute_id']; ?>, 'rejected')">Dismiss</button>
                                    <?php else: ?>
                                        <span style="color: var(--gray); font-size: 0.85rem;">Closed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 32px; color: var(--gray);">
                                    No disputes found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function resolveDispute(id, status) {
            const action = status === 'resolved' ? 'resolve' : 'dismiss';
            if (confirm(`Are you sure you want to ${action} this dispute?`)) {
                fetch('../actions/resolve_dispute.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `dispute_id=${id}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Dispute updated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            }
        }
    </script>
</body>
</html>
