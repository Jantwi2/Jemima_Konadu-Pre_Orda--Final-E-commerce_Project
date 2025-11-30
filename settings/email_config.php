<?php
/**
 * Email Configuration
 * Update these settings with your SMTP credentials
 */

// SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com'); // Change to your SMTP host
define('SMTP_PORT', 465); // Use 465 for SSL (more reliable with Gmail)
define('SMTP_USERNAME', 'jemimaantwi47@gmail.com'); // Your email address
define('SMTP_PASSWORD', 'wlvc kgbf dgne zlep'); // Your email password or app password
define('SMTP_SECURE', 'ssl'); // Use 'ssl' for port 465

// Email Settings
define('FROM_EMAIL', 'jemimaantwi47@gmail.com'); // From email address
define('FROM_NAME', 'PreOrda'); // From name
define('REPLY_TO_EMAIL', 'jemimaantwi47@gmail.com'); // Reply-to email

// OTP Settings
define('OTP_LENGTH', 6); // Length of OTP code
define('OTP_EXPIRY_MINUTES', 15); // OTP expiry time in minutes

// Email Templates Directory
define('EMAIL_TEMPLATES_DIR', __DIR__ . '/../templates/emails/');
?>
