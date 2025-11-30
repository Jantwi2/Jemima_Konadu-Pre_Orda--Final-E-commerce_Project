<?php
session_start();
require_once("../controllers/order_controller.php");
require_once("../helpers/email_helper.php");

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $order_id = $data['order_id'] ?? null;
    $status = $data['status'] ?? null;
    $customer_email = $data['customer_email'] ?? null;
    $customer_name = $data['customer_name'] ?? 'Customer';

    if (!$order_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Invalid request data']);
        exit;
    }

    // Update order status
    $result = update_order_status_ctr($order_id, $status);
    
    if ($result) {
        // Send email notification
        if ($customer_email) {
            try {
                $email_sent = send_order_status_email(
                    $customer_email,
                    $customer_name,
                    $order_id,
                    $status
                );
                
                if ($email_sent) {
                    error_log("Order status email sent to $customer_email");
                } else {
                    error_log("Failed to send order status email to $customer_email");
                }
            } catch (Exception $e) {
                error_log("Email sending error: " . $e->getMessage());
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
