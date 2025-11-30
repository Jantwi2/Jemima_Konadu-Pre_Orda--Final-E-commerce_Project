<?php
require_once("../settings/db_class.php");

class Payment extends db_connection {

    // Add a new payment
    public function add_payment($amount, $order_id, $payment_date, $payment_method, $transaction_id, $status) {
        $sql = "INSERT INTO payments (amount, order_id, payment_date, payment_method, transaction_id, status) 
                VALUES ('$amount', '$order_id', '$payment_date', '$payment_method', '$transaction_id', '$status')";
        return $this->db_query($sql);
    }

    // Get payment details by transaction ID
    public function get_payment_by_transaction_id($transaction_id) {
        $sql = "SELECT * FROM payments WHERE transaction_id = '$transaction_id'";
        return $this->db_fetch_one($sql);
    }
    
    // Get payments by order ID
    public function get_payments_by_order_id($order_id) {
        $sql = "SELECT * FROM payments WHERE order_id = '$order_id'";
        return $this->db_fetch_all($sql);
    }
}
?>
