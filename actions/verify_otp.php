<?php
session_start();
require_once(__DIR__ . '/../controllers/user_controller.php');
require_once(__DIR__ . '/../helpers/email_helper.php');

header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

try {
    $otp = $data['otp'] ?? '';
    
    if (empty($otp)) {
        echo json_encode(['status' => 'error', 'message' => 'OTP is required']);
        exit;
    }
    
    // Check if we have pending vendor data in session
    if (!isset($_SESSION['pending_vendor'])) {
        echo json_encode(['status' => 'error', 'message' => 'No pending verification found. Please register again.']);
        exit;
    }
    
    $pending = $_SESSION['pending_vendor'];
    
    // Verify OTP matches
    if ($otp !== $pending['otp']) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
        exit;
    }
    
    // Check if OTP has expired
    if (strtotime($pending['otp_expiry']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'Verification code has expired. Please register again.']);
        exit;
    }
    
    // OTP is valid - now create the vendor profile
    $vendor_result = add_vendor_ctr(
        $pending['user_id'],
        $pending['business_name'],
        $pending['registration_number'] ?? '', // Fixed typo and added default
        $pending['payout_account'] ?? ''
    );
    
    if ($vendor_result === true) {
        // Get user details for email
        require_once(__DIR__ . '/../classes/user_class.php');
        $user_obj = new user_class();
        $user = $user_obj->db_fetch_one("SELECT * FROM users WHERE user_id = '{$pending['user_id']}'");
        
        // Send pending approval email
        send_pending_approval_email(
            $user['email'],
            $user['full_name'],
            $pending['business_name']
        );
        
        // Clear pending data from session
        unset($_SESSION['pending_vendor']);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Email verified successfully! Your account is pending admin approval.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create vendor profile. Please contact support.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("OTP verification error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error occurred']);
}
?>
