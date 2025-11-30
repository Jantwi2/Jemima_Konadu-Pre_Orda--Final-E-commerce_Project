<?php
session_start();
require_once("../controllers/dispute_controller.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $dispute_id = $_POST['dispute_id'];
    $status = $_POST['status'];

    $result = resolve_dispute_ctr($dispute_id, $status);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Dispute updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update dispute']);
    }
}
?>
