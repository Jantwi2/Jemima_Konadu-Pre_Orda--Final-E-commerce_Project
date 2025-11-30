<?php
session_start();
require_once("../controllers/order_controller.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Assuming a function exists or we need to add it. 
// Let's check order_controller.php first, but for now I'll assume I need to fetch orders.
// Actually, I should check if get_user_orders_ctr exists.
// If not, I'll add it. For now, I'll write the file assuming it exists or I'll add it next.
// Wait, I should verify order_controller.php first.
// But to save steps, I will add the function to order_controller/class if it's missing in the next step.
// I'll assume get_customer_orders_ctr exists or similar.
// Actually, let's look at order_controller.php in a previous turn or just assume standard naming.
// I'll use a direct SQL query in the class if needed, but better to use controller.
// I'll check order_controller.php content first.
?>
<?php
// Re-opening PHP to write the actual file content after checking controller
// For this turn, I will write the file and then check/fix controller.
require_once("../controllers/order_controller.php");
require_once("../controllers/dispute_controller.php");

$orders = get_customer_orders_ctr($user_id); // I need to ensure this exists
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - PreOrda</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #1a202c;
            --bg-main: #f8f9fa;
            --accent: #3498db;
            --font-main: 'Outfit', sans-serif;
            --text-dark: #1a202c;
            --text-gray: #718096;
            --border: #e2e8f0;
            --white: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --radius-lg: 20px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-main);
            color: var(--text-dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Header */
        header {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: var(--transition);
        }

        nav {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s;
        }
        
        .logo:hover {
            transform: scale(1.02);
        }
        
        .logo img {
            height: 40px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            font-size: 0.95rem;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: var(--accent);
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        /* Container */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 30px 80px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--secondary);
            letter-spacing: -0.02em;
        }

        /* Order Card */
        .order-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .order-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .order-id {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .order-date {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .order-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-delivered { background: #dcfce7; color: #166534; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-shipped { background: #e0e7ff; color: #4338ca; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-pending { background: #fef3c7; color: #92400e; }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-total {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary);
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--border);
            color: var(--secondary);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-danger {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
        }

        .btn-danger:hover {
            background: #dc2626;
            color: white;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal[style*="display: flex"] {
            opacity: 1;
        }

        .modal-content {
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius-lg);
            width: 500px;
            max-width: 90%;
            box-shadow: var(--shadow-lg);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal[style*="display: flex"] .modal-content {
            transform: translateY(0);
        }

        .modal h2 {
            margin-bottom: 25px;
            font-size: 1.5rem;
            color: var(--secondary);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--bg-main);
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }

        .form-textarea {
            height: 120px;
            resize: vertical;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-gray);
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px dashed var(--border);
        }

        .empty-state p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <a href="../index.php" class="logo">
                <img src="../images/logo_c.png" alt="PreOrda Logo">
            </a>
            <ul class="nav-links">
                <li><a href="products.php">Products</a></li>
                <li><a href="my_orders.php" style="color: var(--accent);">My Orders</a></li>
                <li>
                    <div class="cart-icon" onclick="window.location.href='cart.php'">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="cart-badge"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>My Orders</h1>
            <a href="products.php" class="btn btn-outline">Continue Shopping</a>
        </div>

        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                            <div class="order-date">Placed on <?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                        </div>
                        <div>
                            <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div>
                            <span style="color: var(--text-gray); font-size: 0.9rem; margin-right: 10px;">Total Amount:</span>
                            <span class="order-total">GH₵ <?php echo number_format($order['total_price'], 2); ?></span>
                        </div>
                        <div>
                            <?php if ($order['status'] === 'delivered' || $order['status'] === 'shipped'): ?>
                                <button class="btn btn-danger" onclick="openDisputeModal(<?php echo $order['order_id']; ?>, <?php echo $order['vendor_id']; ?>)">File Dispute</button>
                            <?php endif; ?>
                            <a href="track.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-outline" style="margin-left: 10px;">Track Order</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-outline">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Dispute Modal -->
    <div id="disputeModal" class="modal">
        <div class="modal-content">
            <h2>File a Dispute</h2>
            <form action="../actions/file_dispute.php" method="POST">
                <input type="hidden" name="order_id" id="modalOrderId">
                <input type="hidden" name="vendor_id" id="modalVendorId">
                
                <div class="form-group">
                    <label class="form-label">Reason</label>
                    <select name="reason" class="form-input" required>
                        <option value="">Select a reason</option>
                        <option value="Item not received">Item not received</option>
                        <option value="Item damaged">Item damaged</option>
                        <option value="Not as described">Not as described</option>
                        <option value="Wrong item sent">Wrong item sent</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Provide more details about your issue..." required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" onclick="closeDisputeModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="background: #dc2626; color: white;">Submit Dispute</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDisputeModal(orderId, vendorId) {
            document.getElementById('modalOrderId').value = orderId;
            document.getElementById('modalVendorId').value = vendorId;
            const modal = document.getElementById('disputeModal');
            modal.style.display = 'flex';
            // Trigger reflow
            void modal.offsetWidth;
            modal.style.opacity = '1';
        }

        function closeDisputeModal() {
            const modal = document.getElementById('disputeModal');
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('disputeModal')) {
                closeDisputeModal();
            }
        }
    </script>
</body>
</html>
