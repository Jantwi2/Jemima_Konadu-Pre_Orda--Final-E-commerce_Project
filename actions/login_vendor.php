<?php
session_start();
require("../controllers/user_controller.php");
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1); // Log errors instead
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $login = $data['login'] ?? '';
    $password = $data['password'] ?? '';

    // Step 1: Authenticate the user (uses email or phone)
    $user_result = login_user_ctr($login, $password);

    if ($user_result === 'invalid_password') {
        echo json_encode(['status' => 'invalid_password']);
        exit;
    } elseif ($user_result === 'not_found') {
        echo json_encode(['status' => 'user_not_found']);
        exit;
    }

    // $user_result now contains the user_id (int)
    $user_id = $user_result;
    $user_type = $_SESSION['user_type'] ?? '';

    // Check user type
    if ($user_type === 'admin') {
        // Admin login successful
        echo json_encode(['status' => 'success', 'role' => 'admin']);
        exit;
    } elseif ($user_type === 'vendor') {
        // Proceed to check vendor profile
    } else {
        // If they are a basic user trying to log into the vendor/admin portal
        // Log them out and deny access.
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'error', 'message' => 'Access denied. Vendor or Admin account required.']);
        exit;
    }

    // Step 2: Check if this user has a vendor profile (in the 'vendors' table)
    $vendor_result = login_vendor_ctr($user_id);

    if ($vendor_result === 'success') {
        // Both user account and vendor profile are confirmed.
        echo json_encode(['status' => 'success', 'role' => 'vendor']);
    } elseif ($vendor_result === 'not_found') {
        echo json_encode(['status' => 'error', 'message' => 'Vendor profile registration incomplete. Contact support.']);
    } else {
        // Catch-all for unexpected errors from login_vendor_ctr
        error_log("Unexpected vendor login result for user_id $user_id: " . print_r($vendor_result, true));
        echo json_encode(['status' => 'error', 'message' => 'Login failed due to an unexpected error.']);
    }
} catch (Exception $e) {
    error_log("Vendor login exception: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error occurred']);
}