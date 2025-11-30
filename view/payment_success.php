<?php
session_start();
require_once("../controllers/product_controller.php");
require_once("../helpers/encryption.php");

// Basic success page
$reference = $_GET['ref'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - PreOrda</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .success-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .icon {
            font-size: 64px;
            color: #48bb78;
            margin-bottom: 20px;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 10px;
        }
        p {
            color: #718096;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background-color: #2c3e50;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background-color: #1a202c;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon">✓</div>
        <h1>Payment Successful!</h1>
        <p>Thank you for your order. Your payment reference is <strong><?php echo htmlspecialchars($reference); ?></strong>.</p>
        <p>We have sent a confirmation email with your order details and tracking information.</p>
        <p style="color: #999; font-size: 14px;">Redirecting to order tracking in <span id="countdown">3</span> seconds...</p>
        <a href="track.php?ref=<?php echo urlencode($reference); ?>" class="btn">Track Your Order</a>
    </div>
    
    <script>
        // Auto-redirect to track page after 3 seconds
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = 'track.php?ref=<?php echo urlencode($reference); ?>';
            }
        }, 1000);
    </script>
</body>
</html>
