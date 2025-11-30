<?php
require("settings/db_class.php");
$db = new db_connection();
$conn = $db->db_conn();

// Insert Categories
$categories = ['Fashion', 'Electronics', 'Home & Living', 'Beauty', 'Art'];
foreach ($categories as $cat) {
    $conn->query("INSERT INTO categories (name) VALUES ('$cat')");
}

// Insert Brands
$brands = ['Generic', 'Premium', 'Handmade', 'Local Artisan'];
foreach ($brands as $brand) {
    $conn->query("INSERT INTO brands (name) VALUES ('$brand')");
}

echo "Seeded categories and brands.";
?>
