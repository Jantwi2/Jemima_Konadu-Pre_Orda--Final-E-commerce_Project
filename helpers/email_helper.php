<?php
require_once(__DIR__ . '/../settings/email_config.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $altBody Plain text alternative
 * @return bool True on success, false on failure
 */
function send_email($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(REPLY_TO_EMAIL, FROM_NAME);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Generate OTP code
 * 
 * @return string OTP code
 */
function generate_otp() {
    $length = OTP_LENGTH;
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}

/**
 * Load email template
 * 
 * @param string $template Template filename
 * @param array $variables Variables to replace in template
 * @return string Rendered template
 */
function load_email_template($template, $variables = []) {
    $templatePath = EMAIL_TEMPLATES_DIR . $template;
    
    if (!file_exists($templatePath)) {
        error_log("Email template not found: $templatePath");
        return '';
    }
    
    $content = file_get_contents($templatePath);
    
    // Replace variables
    foreach ($variables as $key => $value) {
        $content = str_replace('{{' . $key . '}}', $value, $content);
    }
    
    return $content;
}

/**
 * Send OTP verification email
 * 
 * @param string $email Recipient email
 * @param string $name Recipient name
 * @param string $otp OTP code
 * @return bool Success status
 */
function send_otp_email($email, $name, $otp) {
    $subject = 'Verify Your Email - PreOrda';
    $body = load_email_template('otp_verification.html', [
        'name' => $name,
        'otp' => $otp,
        'expiry_minutes' => OTP_EXPIRY_MINUTES
    ]);
    
    return send_email($email, $subject, $body);
}

/**
 * Send pending approval email
 * 
 * @param string $email Recipient email
 * @param string $name Recipient name
 * @param string $businessName Business name
 * @return bool Success status
 */
function send_pending_approval_email($email, $name, $businessName) {
    $subject = 'Registration Successful - Pending Approval';
    $body = load_email_template('pending_approval.html', [
        'name' => $name,
        'business_name' => $businessName
    ]);
    
    return send_email($email, $subject, $body);
}

/**
 * Send account approved email
 * 
 * @param string $email Recipient email
 * @param string $name Recipient name
 * @param string $businessName Business name
 * @return bool Success status
 */
function send_account_approved_email($email, $name, $businessName) {
    $subject = 'Your Account Has Been Approved!';
    $loginUrl = 'http://localhost:8888/preorda/view/login.php'; // Update with your actual URL
    
    $body = load_email_template('account_approved.html', [
        'name' => $name,
        'business_name' => $businessName,
        'login_url' => $loginUrl
    ]);
    
    return send_email($email, $subject, $body);
}
/**
 * Send order confirmation email
 * 
 * @param string $email Customer email
 * @param string $customerName Customer name
 * @param int $orderId Order ID
 * @param string $paymentReference Payment reference
 * @param float $orderTotal Order total amount
 * @param string $shippingAddress Shipping address
 * @return bool Success status
 */
function send_order_confirmation_email($email, $customerName, $orderId, $paymentReference, $orderTotal, $shippingAddress) {
    $subject = 'Order Confirmation - PreOrda';
    $trackingUrl = 'http://localhost:8888/preorda/view/track.php?order_id=' . $orderId; // Update with your actual URL
    $supportEmail = REPLY_TO_EMAIL;
    
    $body = load_email_template('order_confirmation.html', [
        'customer_name' => $customerName,
        'order_id' => $orderId,
        'payment_reference' => $paymentReference,
        'order_total' => number_format($orderTotal, 2),
        'shipping_address' => $shippingAddress,
        'tracking_url' => $trackingUrl,
        'support_email' => $supportEmail
    ]);
    
    return send_email($email, $subject, $body);
}
/**
 * Send order status update email
 * 
 * @param string $email Customer email
 * @param string $customerName Customer name
 * @param int $orderId Order ID
 * @param string $newStatus New order status
 * @return bool Success status
 */
function send_order_status_email($email, $customerName, $orderId, $newStatus) {
    $subject = "Order #$orderId Status Update - PreOrda";
    $trackingUrl = 'http://localhost:8888/preorda/view/track.php?order_id=' . $orderId;
    $supportEmail = REPLY_TO_EMAIL;
    
    // Custom message based on status
    $statusMessage = "Your order is progressing.";
    switch(strtolower($newStatus)) {
        case 'confirmed':
            $statusMessage = "Your order has been confirmed by the vendor and is being processed.";
            break;
        case 'shipped':
            $statusMessage = "Great news! Your order has been shipped and is on its way to you.";
            break;
        case 'delivered':
            $statusMessage = "Your order has been delivered. We hope you enjoy your purchase!";
            break;
        case 'cancelled':
            $statusMessage = "Your order has been cancelled. If you have any questions, please contact support.";
            break;
    }
    
    $body = load_email_template('order_status_update.html', [
        'customer_name' => $customerName,
        'order_id' => $orderId,
        'new_status' => ucfirst($newStatus),
        'status_message' => $statusMessage,
        'tracking_url' => $trackingUrl,
        'support_email' => $supportEmail
    ]);
    
    return send_email($email, $subject, $body);
}
?>
