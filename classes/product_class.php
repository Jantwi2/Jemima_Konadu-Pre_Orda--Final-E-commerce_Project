<?php
require_once(__DIR__ . "/../settings/db_class.php");

class product_class extends db_connection
{
    // Get total number of products for a vendor
    public function get_vendor_product_count($vendor_id)
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE vendor_id = '$vendor_id'";
        $result = $this->db_fetch_one($sql);
        return $result['count'] ?? 0;
    }

    // Get all products for a vendor
    public function get_vendor_products($vendor_id)
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                LEFT JOIN brands b ON p.brand_id = b.brand_id 
                WHERE p.vendor_id = '$vendor_id' 
                ORDER BY p.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    // Get all products (public view)
    public function get_all_products()
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                LEFT JOIN brands b ON p.brand_id = b.brand_id 
                ORDER BY p.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    // Add a new product
    public function add_product($vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time)
    {
        $ndb = new db_connection();
        $name = mysqli_real_escape_string($ndb->db_conn(), $name);
        $description = mysqli_real_escape_string($ndb->db_conn(), $description);
        $image_url = mysqli_real_escape_string($ndb->db_conn(), $image_url);
        $estimated_delivery_time = mysqli_real_escape_string($ndb->db_conn(), $estimated_delivery_time);
        
        $sql = "INSERT INTO products (vendor_id, name, price, description, category_id, brand_id, image_url, stock_status, estimated_delivery_time, created_at) 
                VALUES ('$vendor_id', '$name', '$price', '$description', '$category_id', '$brand_id', '$image_url', '$stock_status', '$estimated_delivery_time', NOW())";
        
        return $this->db_query($sql);
    }

    // Edit an existing product
    public function edit_product($product_id, $vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time)
    {
        $ndb = new db_connection();
        $name = mysqli_real_escape_string($ndb->db_conn(), $name);
        $description = mysqli_real_escape_string($ndb->db_conn(), $description);
        $image_url = mysqli_real_escape_string($ndb->db_conn(), $image_url);
        $estimated_delivery_time = mysqli_real_escape_string($ndb->db_conn(), $estimated_delivery_time);
        
        $sql = "UPDATE products SET 
                name = '$name', 
                price = '$price', 
                description = '$description', 
                category_id = '$category_id', 
                brand_id = '$brand_id', 
                image_url = '$image_url', 
                stock_status = '$stock_status',
                estimated_delivery_time = '$estimated_delivery_time'
                WHERE product_id = '$product_id' AND vendor_id = '$vendor_id'";
        
        return $this->db_query($sql);
    }

    // Delete a product
    public function delete_product($product_id, $vendor_id)
    {
        $sql = "DELETE FROM products WHERE product_id = '$product_id' AND vendor_id = '$vendor_id'";
        return $this->db_query($sql);
    }

    // Get a single product
    public function get_product($product_id)
    {
        $sql = "SELECT * FROM products WHERE product_id = '$product_id'";
        return $this->db_fetch_one($sql);
    }

    // Get all categories
    public function get_all_categories()
    {
        $sql = "SELECT * FROM categories";
        return $this->db_fetch_all($sql);
    }

    // Get all brands
    public function get_all_brands()
    {
        $sql = "SELECT * FROM brands";
        return $this->db_fetch_all($sql);
    }

    public function get_vendor_brands($vendor_id)
    {
        $sql = "SELECT * FROM brands WHERE vendor_id = '$vendor_id'";
        return $this->db_fetch_all($sql);
    }

    public function get_vendor_categories($vendor_id)
    {
        $sql = "SELECT * FROM categories WHERE vendor_id = '$vendor_id'";
        return $this->db_fetch_all($sql);
    }

    // --- Category Management ---
    public function add_category($name, $vendor_id)
    {
        $ndb = new db_connection();
        $name = mysqli_real_escape_string($ndb->db_conn(), $name);
        $sql = "INSERT INTO categories (name, vendor_id) VALUES ('$name', '$vendor_id')";
        return $this->db_query($sql);
    }

    public function edit_category($id, $name)
    {
        $ndb = new db_connection();
        $name = mysqli_real_escape_string($ndb->db_conn(), $name);
        $sql = "UPDATE categories SET name = '$name' WHERE category_id = '$id'";
        return $this->db_query($sql);
    }

    public function delete_category($id)
    {
        $sql = "DELETE FROM categories WHERE category_id = '$id'";
        return $this->db_query($sql);
    }

    // --- Brand Management ---
    public function add_brand($name, $vendor_id)
    {
        $ndb = new db_connection();
        $name = mysqli_real_escape_string($ndb->db_conn(), $name);
        $sql = "INSERT INTO brands (name, vendor_id) VALUES ('$name', '$vendor_id')";
        return $this->db_query($sql);
    }

    public function edit_brand($id, $name)
    {
        $ndb = new db_connection();
        $name = mysqli_real_escape_string($ndb->db_conn(), $name);
        $sql = "UPDATE brands SET name = '$name' WHERE brand_id = '$id'";
        return $this->db_query($sql);
    }

    public function delete_brand($id)
    {
        $sql = "DELETE FROM brands WHERE brand_id = '$id'";
        return $this->db_query($sql);
    }
}
?>
