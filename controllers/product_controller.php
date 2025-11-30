<?php
require_once(__DIR__ . "/../classes/product_class.php");

function get_vendor_product_count_ctr($vendor_id)
{
    $product = new product_class();
    return $product->get_vendor_product_count($vendor_id);
}

function get_vendor_products_ctr($vendor_id)
{
    $product = new product_class();
    return $product->get_vendor_products($vendor_id);
}

function get_all_products_ctr()
{
    $product = new product_class();
    return $product->get_all_products();
}

function add_product_ctr($vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time)
{
    $product = new product_class();
    return $product->add_product($vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time);
}

function edit_product_ctr($product_id, $vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time)
{
    $product = new product_class();
    return $product->edit_product($product_id, $vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time);
}

function delete_product_ctr($product_id, $vendor_id)
{
    $product = new product_class();
    return $product->delete_product($product_id, $vendor_id);
}

function get_all_categories_ctr()
{
    $product = new product_class();
    return $product->get_all_categories();
}

function get_all_brands_ctr()
{
    $product = new product_class();
    return $product->get_all_brands();
}

function get_vendor_brands_ctr($vendor_id)
{
    $product = new product_class();
    return $product->get_vendor_brands($vendor_id);
}

function get_vendor_categories_ctr($vendor_id)
{
    $product = new product_class();
    return $product->get_vendor_categories($vendor_id);
}

// --- Category Controllers ---
function add_category_ctr($name, $vendor_id)
{
    $product = new product_class();
    return $product->add_category($name, $vendor_id);
}

function edit_category_ctr($id, $name)
{
    $product = new product_class();
    return $product->edit_category($id, $name);
}

function delete_category_ctr($id)
{
    $product = new product_class();
    return $product->delete_category($id);
}

// --- Brand Controllers ---
function add_brand_ctr($name, $vendor_id)
{
    $product = new product_class();
    return $product->add_brand($name, $vendor_id);
}

function edit_brand_ctr($id, $name)
{
    $product = new product_class();
    return $product->edit_brand($id, $name);
}

function delete_brand_ctr($id)
{
    $product = new product_class();
    return $product->delete_brand($id);
}
?>
