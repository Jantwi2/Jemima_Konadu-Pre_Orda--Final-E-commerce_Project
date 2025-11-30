<?php
require_once(__DIR__ . "/../settings/db_class.php");

class order_class extends db_connection
{
    // Get total revenue for a vendor (sum of confirmed/delivered orders)
    public function get_vendor_revenue($vendor_id)
    {
        // Assuming 'confirmed', 'shipped', 'delivered' count as revenue
        $sql = "SELECT SUM(total_price) as revenue FROM orders 
                WHERE vendor_id = '$vendor_id' 
                AND status IN ('confirmed', 'shipped', 'delivered')";
        $result = $this->db_fetch_one($sql);
        return $result['revenue'] ?? 0;
    }

    // Get active pre-orders count (pending or confirmed)
    public function get_vendor_active_orders_count($vendor_id)
    {
        $sql = "SELECT COUNT(*) as count FROM orders 
                WHERE vendor_id = '$vendor_id' 
                AND status IN ('pending', 'confirmed', 'shipped')";
        $result = $this->db_fetch_one($sql);
        return $result['count'] ?? 0;
    }

    // Get total unique customers for a vendor
    public function get_vendor_customer_count($vendor_id)
    {
        $sql = "SELECT COUNT(DISTINCT customer_id) as count FROM orders WHERE vendor_id = '$vendor_id'";
        $result = $this->db_fetch_one($sql);
        return $result['count'] ?? 0;
    }

    // Get recent orders with customer and product details
    public function get_vendor_recent_orders($vendor_id, $limit = 5)
    {
        // This query joins orders, customers, users (for name), and order_details + products (for product name)
        // Note: An order might have multiple products, but for the dashboard list we might just show one or "Multiple"
        // For simplicity, let's just get the order and customer info first, and maybe the first product name
        
        $sql = "SELECT 
                    o.order_id, 
                    o.total_price, 
                    o.status, 
                    o.estimated_delivery_date,
                    u.full_name as customer_name,
                    (SELECT p.name FROM order_details od JOIN products p ON od.product_id = p.product_id WHERE od.order_id = o.order_id LIMIT 1) as product_name,
                    (SELECT COUNT(*) FROM order_details od WHERE od.order_id = o.order_id) as item_count
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                JOIN users u ON c.user_id = u.user_id
                WHERE o.vendor_id = '$vendor_id'
                ORDER BY o.order_date DESC
                LIMIT $limit";
        
        return $this->db_fetch_all($sql);
    }
    // Create a new order
    public function add_order($customer_id, $vendor_id, $total_price, $status, $shipping_address, $billing_address, $order_notes)
    {
        $sql = "INSERT INTO orders (customer_id, vendor_id, total_price, status, shipping_address, billing_address, order_notes, order_date) 
                VALUES ('$customer_id', '$vendor_id', '$total_price', '$status', '$shipping_address', '$billing_address', '$order_notes', NOW())";
        return $this->db_query($sql);
    }

    // Add order details
    public function add_order_details($order_id, $product_id, $quantity, $unit_price, $subtotal)
    {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal) 
                VALUES ('$order_id', '$product_id', '$quantity', '$unit_price', '$subtotal')";
        return $this->db_query($sql);
    }
    // Update order status
    public function update_order_status($order_id, $status)
    {
        $sql = "UPDATE orders SET status = '$status' WHERE order_id = '$order_id'";
        return $this->db_query($sql);
    }

    // Get all orders for a vendor
    public function get_vendor_orders($vendor_id)
    {
        $sql = "SELECT 
                    o.order_id, 
                    o.total_price, 
                    o.status, 
                    o.order_date,
                    u.full_name as customer_name,
                    u.email as customer_email
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                JOIN users u ON c.user_id = u.user_id
                WHERE o.vendor_id = '$vendor_id'
                ORDER BY o.order_date DESC";
        return $this->db_fetch_all($sql);
    }
    // Get all orders for a customer
    public function get_customer_orders($user_id)
    {
        $sql = "SELECT 
                    o.order_id, 
                    o.total_price, 
                    o.status, 
                    o.order_date,
                    o.vendor_id,
                    v.business_name as vendor_name
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                JOIN vendors v ON o.vendor_id = v.vendor_id
                WHERE c.user_id = '$user_id'
                ORDER BY o.order_date DESC";
        return $this->db_fetch_all($sql);
    }
}
?>
