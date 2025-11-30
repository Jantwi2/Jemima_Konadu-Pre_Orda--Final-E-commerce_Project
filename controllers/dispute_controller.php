<?php
require_once(__DIR__ . "/../classes/dispute_class.php");

// Create a new dispute
function create_dispute_ctr($order_id, $user_id, $vendor_id, $reason, $description)
{
    $dispute = new dispute_class();
    return $dispute->create_dispute($order_id, $user_id, $vendor_id, $reason, $description);
}

// Get all disputes
function get_all_disputes_ctr()
{
    $dispute = new dispute_class();
    return $dispute->get_all_disputes();
}

// Get user disputes
function get_user_disputes_ctr($user_id)
{
    $dispute = new dispute_class();
    return $dispute->get_user_disputes($user_id);
}

// Resolve dispute
function resolve_dispute_ctr($dispute_id, $status)
{
    $dispute = new dispute_class();
    return $dispute->resolve_dispute($dispute_id, $status);
}

// Get dispute details
function get_dispute_details_ctr($dispute_id)
{
    $dispute = new dispute_class();
    return $dispute->get_dispute_details($dispute_id);
}
?>
