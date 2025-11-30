<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval - PreOrda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background: #f9f9f9;
            color: #1a1a1a;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            max-width: 600px;
            width: 100%;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            background: #fff3cd;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 24px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 16px;
            color: #0a0a0a;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #0a0a0a;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
            border-radius: 4px;
        }

        .info-box h3 {
            font-size: 16px;
            margin-bottom: 12px;
            color: #0a0a0a;
        }

        .info-box ul {
            margin: 0;
            padding-left: 20px;
            color: #666;
            font-size: 14px;
            line-height: 1.8;
        }

        .btn {
            display: inline-block;
            background: #0a0a0a;
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #2a2a2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⏳</div>
        <h1>Registration Successful!</h1>
        <p>Your email has been verified and your vendor account has been created.</p>
        <p>Your account is currently <strong>pending admin approval</strong>. Our team will review your application and notify you via email within 24-48 hours.</p>

        <div class="info-box">
            <h3>What happens next?</h3>
            <ul>
                <li>Our admin team will review your vendor profile</li>
                <li>You'll receive an email notification once approved</li>
                <li>After approval, you can log in and start setting up your store</li>
                <li>Add products and begin accepting pre-orders</li>
            </ul>
        </div>

        <p style="color: #999; font-size: 14px;">Thank you for choosing PreOrda. We're excited to have you on board!</p>
        
        <a href="../index.php" class="btn">Return to Homepage</a>
    </div>
</body>
</html>
