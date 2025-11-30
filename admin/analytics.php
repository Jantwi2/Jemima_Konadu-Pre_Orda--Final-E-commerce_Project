<?php
session_start();
require_once("../controllers/admin_controller.php");
// Placeholder for analytics logic
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - PreOrda</title>
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

        .btn-outline {
            background-color: white;
            border: 1px solid var(--border);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: var(--light);
        }

        .content-card {
            background: white;
            padding: 32px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            text-align: center;
            color: var(--gray);
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
            <a href="disputes.php" class="nav-item">
                <span>⚖️</span> Disputes
            </a>
            <a href="analytics.php" class="nav-item active">
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
                <h1>Analytics</h1>
                <p>Platform performance and insights.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline">
                    <span>📅</span> <?php echo date('M d, Y'); ?>
                </button>
            </div>
        </header>

        <div class="content-card">
            <div style="font-size: 4rem; margin-bottom: 16px;">📈</div>
            <h2>Detailed Analytics Coming Soon</h2>
            <p style="margin-top: 8px;">We are working on bringing you comprehensive insights.</p>
        </div>
    </main>
</body>
</html>
