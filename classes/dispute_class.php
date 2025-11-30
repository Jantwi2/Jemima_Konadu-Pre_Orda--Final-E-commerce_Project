<?php
require_once(__DIR__ . "/../settings/db_class.php");

class dispute_class extends db_connection
{
    // Create a new dispute
    public function create_dispute($order_id, $user_id, $vendor_id, $reason, $description)
    {
        $order_id = mysqli_real_escape_string($this->db_conn(), $order_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        $vendor_id = mysqli_real_escape_string($this->db_conn(), $vendor_id);
        $reason = mysqli_real_escape_string($this->db_conn(), $reason);
        $description = mysqli_real_escape_string($this->db_conn(), $description);

        $sql = "INSERT INTO disputes (order_id, user_id, vendor_id, reason, description, status, created_at) 
                VALUES ('$order_id', '$user_id', '$vendor_id', '$reason', '$description', 'open', NOW())";
        
        return $this->db_query($sql);
    }

    // Get all disputes (for admin)
    public function get_all_disputes()
    {
        $sql = "SELECT d.*, o.order_date, u.full_name as buyer_name, v.business_name as vendor_name 
                FROM disputes d
                JOIN orders o ON d.order_id = o.order_id
                JOIN users u ON d.user_id = u.user_id
                JOIN vendors v ON d.vendor_id = v.vendor_id
                ORDER BY d.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    // Get disputes for a specific user
    public function get_user_disputes($user_id)
    {
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        $sql = "SELECT d.*, o.order_date, v.business_name as vendor_name 
                FROM disputes d
                JOIN orders o ON d.order_id = o.order_id
                JOIN vendors v ON d.vendor_id = v.vendor_id
                WHERE d.user_id = '$user_id'
                ORDER BY d.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    // Resolve a dispute
    public function resolve_dispute($dispute_id, $status)
    {
        $dispute_id = mysqli_real_escape_string($this->db_conn(), $dispute_id);
        $status = mysqli_real_escape_string($this->db_conn(), $status); // 'resolved' or 'rejected'

        $sql = "UPDATE disputes SET status = '$status' WHERE dispute_id = '$dispute_id'";
        return $this->db_query($sql);
    }

    // Get dispute details
    public function get_dispute_details($dispute_id)
    {
        $dispute_id = mysqli_real_escape_string($this->db_conn(), $dispute_id);
        $sql = "SELECT d.*, o.total_price, u.full_name as buyer_name, u.email as buyer_email, v.business_name as vendor_name
                FROM disputes d
                JOIN orders o ON d.order_id = o.order_id
                JOIN users u ON d.user_id = u.user_id
                JOIN vendors v ON d.vendor_id = v.vendor_id
                WHERE d.dispute_id = '$dispute_id'";
        return $this->db_fetch_one($sql);
    }
}
?>
