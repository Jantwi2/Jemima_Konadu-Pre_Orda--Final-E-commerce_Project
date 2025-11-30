<?php
session_start();
require_once("../controllers/dispute_controller.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../view/login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];
    $vendor_id = $_POST['vendor_id'];
    $reason = $_POST['reason'];
    $description = $_POST['description'];

    $result = create_dispute_ctr($order_id, $user_id, $vendor_id, $reason, $description);

    if ($result) {
        header("Location: ../view/my_orders.php?msg=dispute_filed");
    } else {
        header("Location: ../view/my_orders.php?msg=error");
    }
}
?>
