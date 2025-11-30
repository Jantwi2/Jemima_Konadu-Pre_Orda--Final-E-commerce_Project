<?php
require_once(__DIR__ . "/../classes/order_class.php");

function get_vendor_revenue_ctr($vendor_id)
{
    $order = new order_class();
    return $order->get_vendor_revenue($vendor_id);
}

function get_vendor_active_orders_count_ctr($vendor_id)
{
    $order = new order_class();
    return $order->get_vendor_active_orders_count($vendor_id);
}

function get_vendor_customer_count_ctr($vendor_id)
{
    $order = new order_class();
    return $order->get_vendor_customer_count($vendor_id);
}

function get_vendor_recent_orders_ctr($vendor_id)
{
    $order = new order_class();
    return $order->get_vendor_recent_orders($vendor_id);
}

function get_vendor_orders_ctr($vendor_id)
{
    $order = new order_class();
    return $order->get_vendor_orders($vendor_id);
}

function update_order_status_ctr($order_id, $status)
{
    $order = new order_class();
    return $order->update_order_status($order_id, $status);
}

function get_customer_orders_ctr($user_id)
{
    $order = new order_class();
    return $order->get_customer_orders($user_id);
}
?>
