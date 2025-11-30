<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Not Found - PreOrda</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #2d3748;
            --bg-main: #f8f9fa;
            --accent: #3498db;
            --font-main: 'Outfit', sans-serif;
            --text-dark: #1a202c;
            --text-gray: #718096;
            --white: #ffffff;
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }

        .container {
            background: var(--white);
            padding: 60px 40px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .icon-container {
            width: 80px;
            height: 80px;
            background: #ebf8ff;
            color: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .icon-container svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 16px;
            line-height: 1.2;
        }

        p {
            color: var(--text-gray);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 30px 0;
            width: 100%;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0.8;
        }

        .logo img {
            height: 30px;
        }

        .logo span {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1>Store Not Found</h1>
        
        <p>
            We couldn't find the store you're looking for. <br>
            Please ensure you have the correct link provided by the vendor to access their shop.
        </p>

        <div class="divider"></div>

        <div class="logo">
            <img src="../images/logo_c.png" alt="PreOrda Logo">
            <span>PreOrda</span>
        </div>
    </div>
</body>
</html>
