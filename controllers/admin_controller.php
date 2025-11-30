<?php
require_once(__DIR__ . "/../classes/admin_class.php");

// Get dashboard statistics
function get_dashboard_stats_ctr()
{
    $admin = new admin_class();
    return $admin->get_dashboard_stats();
}

// Get recent orders
function get_recent_orders_ctr($limit = 5)
{
    $admin = new admin_class();
    return $admin->get_recent_orders($limit);
}

// Get all orders
function get_all_orders_ctr($status = null, $search = null)
{
    $admin = new admin_class();
    return $admin->get_all_orders($status, $search);
}

// Get all users
function get_all_users_ctr($role = null, $search = null)
{
    $admin = new admin_class();
    return $admin->get_all_users($role, $search);
}

// Get pending vendor approvals
function get_pending_vendors_ctr()
{
    $admin = new admin_class();
    return $admin->get_pending_vendors();
}

// Approve vendor
function approve_vendor_ctr($vendor_id)
{
    $admin = new admin_class();
    return $admin->approve_vendor($vendor_id);
}

// Reject vendor
function reject_vendor_ctr($vendor_id)
{
    $admin = new admin_class();
    return $admin->reject_vendor($vendor_id);
}
?>
