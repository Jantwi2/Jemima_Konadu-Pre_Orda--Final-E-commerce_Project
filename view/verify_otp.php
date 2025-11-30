<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - PreOrda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background: #ffffff;
            color: #1a1a1a;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-container {
            background: #ffffff;
            max-width: 480px;
            width: 100%;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #0a0a0a;
        }

        .subtitle {
            color: #666666;
            font-size: 14px;
            line-height: 1.5;
        }

        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
        }

        .otp-input {
            width: 56px;
            height: 64px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .otp-input:focus {
            outline: none;
            border-color: #0a0a0a;
            background-color: #f9f9f9;
        }

        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            font-size: 14px;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            font-size: 14px;
        }

        .success-message.show {
            display: block;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: #0a0a0a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn:hover {
            background: #2a2a2a;
        }

        btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .resend-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .resend-link a {
            color: #0a0a0a;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1.5px solid #0a0a0a;
        }

        .resend-link a:hover {
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="header">
            <div class="logo">PreOrda</div>
            <h1>Verify Your Email</h1>
            <p class="subtitle">We've sent a 6-digit verification code to your email address. Please enter it below to continue.</p>
        </div>

        <div id="errorMessage" class="error-message"></div>
        <div id="successMessage" class="success-message"></div>

        <form id="otpForm">
            <div class="otp-inputs">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autofocus>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
            </div>
            
            <button type="submit" class="btn">Verify Code</button>
        </form>

        <div class="resend-link">
            Didn't receive the code? <a href="register.php">Register again</a>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const form = document.getElementById('otpForm');
        const errorDiv = document.getElementById('errorMessage');
        const successDiv = document.getElementById('successMessage');

        // Auto-focus next input
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
            successDiv.classList.remove('show');
        }

        function showSuccess(message) {
            successDiv.textContent = message;
            successDiv.classList.add('show');
            errorDiv.classList.remove('show');
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const otp = Array.from(inputs).map(input => input.value).join('');
            
            if (otp.length !== 6) {
                showError('Please enter the complete 6-digit code');
                return;
            }

            const btn = form.querySelector('.btn');
            btn.classList.add('loading');
            btn.disabled = true;
            btn.textContent = 'Verifying...';

            try {
                const response = await fetch('../actions/verify_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ otp })
                });
                
                const result = await response.json();

                if (result.status === 'success') {
                    showSuccess(result.message);
                    setTimeout(() => {
                        window.location.href = 'pending_approval.php';
                    }, 2000);
                } else {
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    btn.textContent = 'Verify Code';
                    showError(result.message || 'Verification failed');
                }
            } catch (error) {
                btn.classList.remove('loading');
                btn.disabled = false;
                btn.textContent = 'Verify Code';
                showError('Connection error. Please try again.');
            }
        });
    </script>
</body>
</html>
