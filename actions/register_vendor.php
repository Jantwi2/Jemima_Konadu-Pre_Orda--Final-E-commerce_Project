<?php
error_log("Starting register_vendor.php");
session_start();
// This controller file must contain the definitions for add_user_ctr and add_vendor_ctr
require(__DIR__ . "/../controllers/user_controller.php");
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1); // Log errors instead

header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
error_log("Vendor Registration Input: " . $input);
$data = json_decode($input, true);

try {
    if ($data) {
        $full_name = $data['fullName'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $password = $data['password'] ?? '';
        $store_name = $data['storeName'] ?? '';
        
        // Ensure that registrationNumber and payoutAccount are included in the JavaScript payload 
        // if they are required in the DB. If they are missing, empty strings (default below) will be sent.
        $registration_number = $data['registrationNumber'] ?? ''; 
        $payout_account = $data['payoutAccount'] ?? '';

        // Step 1: Create user account
        // The add_user method now returns: "duplicate" (string), the new user_id (int), or false (bool)
        $user_creation_result = add_user_ctr(
            $full_name,
            $email,
            $phone,
            $password,
            '', // country - empty for now (will be combined into address)
            '', // city - empty for now (will be combined into address)
            'vendor' // user_type
        );

        error_log("User Creation Result: " . print_r($user_creation_result, true));

        if ($user_creation_result === "duplicate") {
            echo json_encode(['status' => 'duplicate', 'message' => 'Email already exists']);
            exit;
        }

        if (!$user_creation_result) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create user account']);
            exit;
        }

        // Step 2: Use the newly created user's ID
        $user_id = $user_creation_result;

        // Step 3: Generate OTP and send verification email
        require_once(__DIR__ . '/../helpers/email_helper.php');
        
        $otp = generate_otp();
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
        
        // Store OTP temporarily in session (we'll create vendor after verification)
        $_SESSION['pending_vendor'] = [
            'user_id' => $user_id,
            'business_name' => $store_name,
            'registration_number' => $registration_number,
            'payout_account' => $payout_account,
            'otp' => $otp,
            'otp_expiry' => $otp_expiry
        ];
        
        // Try to send OTP email
        try {
            $email_sent = send_otp_email($email, $full_name, $otp);
        } catch (Error $e) {
            // PHPMailer not installed - log error and provide fallback
            error_log("PHPMailer not installed: " . $e->getMessage());
            error_log("OTP for $email: $otp (valid for " . OTP_EXPIRY_MINUTES . " minutes)");
            $email_sent = false;
        }
        
        if ($email_sent) {
            error_log("OTP sent successfully to $email for user_id: $user_id");
            echo json_encode([
                'status' => 'otp_sent', 
                'message' => 'Verification code sent to your email',
                'user_id' => $user_id
            ]);
        } else {
            // Email failed - provide OTP in response for development
            error_log("Failed to send OTP email to $email. OTP: $otp");
            echo json_encode([
                'status' => 'otp_sent',
                'message' => 'Email service not configured. Your OTP is: ' . $otp,
                'otp' => $otp, // Only for development - remove in production!
                'user_id' => $user_id,
                'note' => 'Please install PHPMailer to enable email functionality'
            ]);
        }
    } else {
        echo json_encode(['status' => 'invalid', 'message' => 'Invalid data received']);
    }
} catch (Exception $e) {
    // Log error for server-side inspection
    error_log("Vendor registration error: " . $e->getMessage()); 
    
    // Check for duplicate entry errors
    $error_message = $e->getMessage();
    if (strpos($error_message, 'Duplicate entry') !== false) {
        if (strpos($error_message, 'email') !== false) {
            echo json_encode(['status' => 'duplicate', 'message' => 'This email address is already registered']);
        } elseif (strpos($error_message, 'phone') !== false) {
            echo json_encode(['status' => 'duplicate', 'message' => 'This phone number is already registered']);
        } else {
            echo json_encode(['status' => 'duplicate', 'message' => 'An account with these details already exists']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Server error occurred. Please try again.']);
    }
}