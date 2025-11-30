<?php
require_once(__DIR__ . "/../settings/db_class.php");

class admin_class extends db_connection
{
    // Get dashboard statistics
    public function get_dashboard_stats()
    {
        $stats = [];
        
        // Total Revenue (sum of all completed orders)
        $sql_revenue = "SELECT SUM(total_price) as total_revenue FROM orders WHERE status = 'delivered'";
        $result_revenue = $this->db_fetch_one($sql_revenue);
        $stats['revenue'] = $result_revenue['total_revenue'] ?? 0;

        // Total Orders
        $sql_orders = "SELECT COUNT(*) as total_orders FROM orders";
        $result_orders = $this->db_fetch_one($sql_orders);
        $stats['orders'] = $result_orders['total_orders'] ?? 0;

        // Active Users
        $sql_users = "SELECT COUNT(*) as active_users FROM users WHERE is_active = 1";
        $result_users = $this->db_fetch_one($sql_users);
        $stats['users'] = $result_users['active_users'] ?? 0;

        // Open Disputes (mock for now if table doesn't exist, or check if it does)
        // Assuming a disputes table or checking orders with dispute status
        $stats['disputes'] = 0; // Placeholder until disputes table is confirmed

        return $stats;
    }

    // Get recent orders
    public function get_recent_orders($limit = 5)
    {
        // Using order_date instead of created_at, and full_name instead of fname/lname
        // Also joining customers then users as per order_class logic
        $sql = "SELECT o.*, u.full_name 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.customer_id
                JOIN users u ON c.user_id = u.user_id 
                ORDER BY o.order_date DESC 
                LIMIT $limit";
        return $this->db_fetch_all($sql);
    }

    // Get all orders with optional filters
    public function get_all_orders($status = null, $search = null)
    {
        $sql = "SELECT o.*, u.full_name 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.customer_id
                JOIN users u ON c.user_id = u.user_id 
                WHERE 1=1";

        if ($status) {
            $status = mysqli_real_escape_string($this->db_conn(), $status);
            $sql .= " AND o.status = '$status'";
        }

        if ($search) {
            $search = mysqli_real_escape_string($this->db_conn(), $search);
            $sql .= " AND (u.full_name LIKE '%$search%' OR o.order_id LIKE '%$search%')";
        }

        $sql .= " ORDER BY o.order_date DESC";
        return $this->db_fetch_all($sql);
    }

    // Get all users with optional role filter
    public function get_all_users($role = null, $search = null)
    {
        $sql = "SELECT * FROM users WHERE 1=1";
        
        if ($role) {
            $role = mysqli_real_escape_string($this->db_conn(), $role);
            $sql .= " AND user_type = '$role'";
        }

        if ($search) {
            $search = mysqli_real_escape_string($this->db_conn(), $search);
            $sql .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%')";
        }

        $sql .= " ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }

    // Get pending vendor approvals
    public function get_pending_vendors()
    {
        // verified = 0 means pending
        // Note: business_type doesn't exist in vendors table, only business_name
        $sql = "SELECT v.vendor_id, v.business_name, v.registration_number, u.full_name, u.email, u.created_at
                FROM vendors v 
                JOIN users u ON v.user_id = u.user_id 
                WHERE v.verified = 0";
        return $this->db_fetch_all($sql);
    }

    // Approve vendor
    public function approve_vendor($vendor_id)
    {
        $vendor_id = mysqli_real_escape_string($this->db_conn(), $vendor_id);
        
        // Update vendor verified status to 1
        $sql_vendor = "UPDATE vendors SET verified = 1 WHERE vendor_id = '$vendor_id'";
        $result = $this->db_query($sql_vendor);

        if ($result) {
            return true;
        }
        return false;
    }

    // Reject vendor
    public function reject_vendor($vendor_id)
    {
        $vendor_id = mysqli_real_escape_string($this->db_conn(), $vendor_id);
        // Assuming 2 is rejected, or we could delete. Let's set to 2 for now.
        $sql = "UPDATE vendors SET verified = 2 WHERE vendor_id = '$vendor_id'";
        return $this->db_query($sql);
    }
}
?>
