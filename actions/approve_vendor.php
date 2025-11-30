<?php
session_start();
require_once("../controllers/admin_controller.php");

// Set JSON header
header('Content-Type: application/json');

// Check if admin is logged in (you can add proper admin authentication here)
// For now, we'll proceed with the action

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_POST['vendor_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$vendor_id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    require_once(__DIR__ . '/../controllers/admin_controller.php');
    require_once(__DIR__ . '/../helpers/email_helper.php');

    if ($action === 'approve') {
        $result = approve_vendor_ctr($vendor_id);
        
        if ($result) {
            // Get vendor and user details to send email
            require_once(__DIR__ . '/../classes/vendor_class.php');
            require_once(__DIR__ . '/../classes/user_class.php');
            
            $vendor_obj = new vendor_class();
            $user_obj = new user_class();
            
            $vendor = $vendor_obj->get_vendor_details($vendor_id);
            $user = $user_obj->db_fetch_one("SELECT * FROM users WHERE user_id = '{$vendor['user_id']}'");
            
            // Try to send approval email
            try {
                $email_sent = send_account_approved_email(
                    $user['email'],
                    $user['full_name'],
                    $vendor['business_name']
                );
                
                if ($email_sent) {
                    error_log("Approval email sent successfully to {$user['email']}");
                } else {
                    error_log("Failed to send approval email to {$user['email']}");
                }
            } catch (Exception $e) {
                error_log("Email sending error: " . $e->getMessage());
                // Continue anyway - vendor is approved even if email fails
            }
            
            echo json_encode(['success' => true, 'message' => 'Vendor approved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve vendor']);
        }
    } elseif ($action === 'reject') {
        $result = reject_vendor_ctr($vendor_id);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Vendor rejected successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reject vendor']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
