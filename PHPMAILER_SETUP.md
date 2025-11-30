# PHPMailer Installation Guide

## Step 1: Download PHPMailer

Visit https://github.com/PHPMailer/PHPMailer/releases and download the latest release (v6.9.1 or newer).

## Step 2: Extract to Project

Extract the downloaded ZIP file to:
```
/Applications/MAMP/htdocs/preorda/vendor/phpmailer/phpmailer/
```

The directory structure should look like:
```
/Applications/MAMP/htdocs/preorda/vendor/
└── phpmailer/
    └── phpmailer/
        └── src/
            ├── PHPMailer.php
            ├── SMTP.php
            ├── Exception.php
            └── ... (other files)
```

## Step 3: Update SMTP Configuration

Edit `/Applications/MAMP/htdocs/preorda/settings/email_config.php`:

```php
// For Gmail:
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password'); // Use Gmail App Password
define('SMTP_SECURE', 'tls');

// Update sender details:
define('FROM_EMAIL', 'noreply@yoursite.com');
define('FROM_NAME', 'PreOrda');
```

### Getting Gmail App Password:
1. Go to your Google Account settings
2. Security → 2-Step Verification → App passwords
3. Generate a new app password for "Mail"
4. Use that 16-character password in `SMTP_PASSWORD`

## Step 4: Test Email Functionality

Create a test file (`/Applications/MAMP/htdocs/preorda/test_email.php`):

```php
<?php
require_once('helpers/email_helper.php');

$result = send_email(
    'your-test-email@gmail.com',
    'Test Email',
    '<h1>PHPMailer Works!</h1><p>This is a test email.</p>'
);

echo $result ? 'Email sent successfully!' : 'Email failed to send';
?>
```

Visit `http://localhost:8888/preorda/test_email.php` to test.

## Current Workaround

Until PHPMailer is installed, the system will:
- Display the OTP code in an alert message
- Log the OTP in the PHP error log
- Still allow registration to proceed

**OTP is logged to:** `/Applications/MAMP/logs/php_error.log`
