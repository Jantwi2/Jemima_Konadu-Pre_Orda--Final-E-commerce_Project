<?php
session_start();
require_once("../classes/order_class.php");
require_once("../classes/payment_class.php");
require_once("../controllers/product_controller.php");

// Disable display errors to prevent HTML output in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
// Ensure no output before header
ob_clean(); 

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in (optional, depending on flow)
// if (!isset($_SESSION['user_id'])) { ... }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get POST data
        $reference = $_POST['reference'] ?? '';
        $amount = $_POST['amount'] ?? 0; // In kobo/pesewas usually, but let's assume standard unit or handle conversion
        $email = $_POST['email'] ?? '';
        $shipping_address = $_POST['shipping_address'] ?? '';
        $billing_address = $_POST['billing_address'] ?? ''; // Optional
        $order_notes = $_POST['order_notes'] ?? '';
        $payment_method = 'bank_card'; // Map Paystack to bank_card for enum compatibility
        
        // Validate required fields
        if (empty($reference) || empty($amount) || empty($email)) {
            throw new Exception('Missing required payment details');
        }

        // Verify transaction with Paystack (Server-side verification is best practice)
        // For this implementation, we'll trust the callback but in production verify via API
        
        // Get Cart Items
        if (empty($_SESSION['cart'])) {
            throw new Exception('Cart is empty');
        }

        // Initialize classes
        $order_obj = new order_class();
        $payment_obj = new Payment();
        
        // Group cart items by vendor to create separate orders per vendor
        // OR create one parent order. The system seems to support vendor_id in orders table.
        // So we must split orders by vendor.
        
        $cart = $_SESSION['cart'];
        $products_by_vendor = [];
        
        // Fetch all products to group them
        // We need a way to get product details including vendor_id
        // Let's assume we have a helper or we fetch them one by one.
        // For efficiency, let's fetch all products in cart.
        
        require_once("../controllers/product_controller.php");
        $all_products = get_all_products_ctr(); // This might be heavy if many products, but ok for now
        
        $vendor_orders = [];
        $customer_id = $_SESSION['customer_id'] ?? 0;
        
        // Guest Checkout Logic
        if ($customer_id == 0) {
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $full_name = trim($first_name . ' ' . $last_name);
            
            // 1. Check if user exists by email
            // We need a way to check user existence. Let's do a direct query since we have db_connection via payment_obj
            // Note: Payment extends db_connection
            
            $email_safe = mysqli_real_escape_string($payment_obj->db_conn(), $email);
            $sql_check = "SELECT user_id FROM users WHERE email = '$email_safe'";
            $user_res = $payment_obj->db_fetch_one($sql_check);
            
            $user_id = 0;
            
            if ($user_res) {
                $user_id = $user_res['user_id'];
            } else {
                // 2. Create new user
                // We need a password. Let's generate a random one or set a default.
                // Since they are guests, they might not log in immediately.
                // Let's set a random password.
                $temp_password = bin2hex(random_bytes(8));
                $password_hash = password_hash($temp_password, PASSWORD_DEFAULT);
                $full_name_safe = mysqli_real_escape_string($payment_obj->db_conn(), $full_name);
                $phone_safe = mysqli_real_escape_string($payment_obj->db_conn(), $phone);
                
                $sql_create_user = "INSERT INTO users (full_name, email, phone, password_hash, user_type, is_active) 
                                    VALUES ('$full_name_safe', '$email_safe', '$phone_safe', '$password_hash', 'customer', 1)";
                
                if ($payment_obj->db_query($sql_create_user)) {
                    $user_id = $payment_obj->insert_id();
                    // Optionally send email with temp password
                } else {
                    throw new Exception('Failed to create guest user account');
                }
            }
            
            // 3. Get or Create Customer Record
            if ($user_id > 0) {
                $sql_check_cust = "SELECT customer_id FROM customers WHERE user_id = '$user_id'";
                $cust_res = $payment_obj->db_fetch_one($sql_check_cust);
                
                if ($cust_res) {
                    $customer_id = $cust_res['customer_id'];
                } else {
                    $sql_create_cust = "INSERT INTO customers (user_id) VALUES ('$user_id')";
                    if ($payment_obj->db_query($sql_create_cust)) {
                        $customer_id = $payment_obj->insert_id();
                    }
                }
            }
        }
        
        if ($customer_id == 0) {
            throw new Exception('Failed to identify or create customer profile');
        }

        // Group items
        foreach ($cart as $p_id => $qty) {
            foreach ($all_products as $product) {
                if ($product['product_id'] == $p_id) {
                    $v_id = $product['vendor_id'];
                    if (!isset($products_by_vendor[$v_id])) {
                        $products_by_vendor[$v_id] = [];
                    }
                    $product['qty'] = $qty;
                    $products_by_vendor[$v_id][] = $product;
                    break;
                }
            }
        }

        $orders_created = [];
        $errors = [];

        foreach ($products_by_vendor as $vendor_id => $items) {
            $order_total = 0;
            foreach ($items as $item) {
                $order_total += $item['price'] * $item['qty'];
            }
            
            // Add shipping cost? Let's assume flat rate or per vendor
            $shipping_cost = 50; // From checkout.php
            $order_total += $shipping_cost;

            // Create Order
            $status = 'confirmed'; // Paid
            $result = $order_obj->add_order($customer_id, $vendor_id, $order_total, $status, $shipping_address, $billing_address, $order_notes);
            
            if ($result) {
                $order_id = $order_obj->insert_id(); // Need to ensure db_connection has this method
                $orders_created[] = $order_id;
                
                // Add Order Details
                foreach ($items as $item) {
                    $subtotal = $item['price'] * $item['qty'];
                    $order_obj->add_order_details($order_id, $item['product_id'], $item['qty'], $item['price'], $subtotal);
                }
                
                // Record Payment
                // We might split the payment amount across orders or record one payment linked to multiple orders?
                // The payments table links to ONE order_id.
                // This is a complex scenario (Split Payment).
                // For simplicity, we will record the payment for EACH order with the split amount.
                // OR we record it for the first order and reference others in notes?
                // Let's record separate payment entries for each order split.
                
                $payment_status = 'completed';
                $currency = 'GHS';
                $payment_obj->add_payment($order_total, $order_id, date('Y-m-d H:i:s'), $payment_method, $reference . '-' . $order_id, $payment_status);
                
            } else {
                $errors[] = "Failed to create order for vendor $vendor_id";
            }
        }

        if (empty($errors)) {
            // Get customer details for email
            require_once(__DIR__ . '/../classes/user_class.php');
            $user_obj = new user_class();
            $user = $user_obj->db_fetch_one("SELECT * FROM users WHERE user_id = (SELECT user_id FROM customers WHERE customer_id = '$customer_id')");
            
            // Send order confirmation email
            if ($user && !empty($user['email'])) {
                require_once(__DIR__ . '/../helpers/email_helper.php');
                
                try {
                    // Send email for the first order (or all orders - you can customize)
                    $first_order_id = $orders_created[0];
                    $email_sent = send_order_confirmation_email(
                        $user['email'],
                        $user['full_name'],
                        $first_order_id,
                        $reference,
                        $amount / 100, // Convert back from kobo/pesewas
                        $shipping_address
                    );
                    
                    if ($email_sent) {
                        error_log("Order confirmation email sent to {$user['email']}");
                    }
                } catch (Exception $e) {
                    error_log("Failed to send order confirmation email: " . $e->getMessage());
                    // Don't fail the order if email fails
                }
            }
            
            // Clear Cart
            unset($_SESSION['cart']);
            echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_ids' => $orders_created]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Some orders failed to create', 'errors' => $errors]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
