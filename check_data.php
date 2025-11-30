<?php
require("settings/db_class.php");
$db = new db_connection();
$conn = $db->db_conn();

echo "Categories:\n";
$result = $conn->query("SELECT * FROM categories");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo $row['category_id'] . ": " . $row['name'] . "\n";
    }
} else {
    echo "No categories found.\n";
}

echo "\nBrands:\n";
$result = $conn->query("SELECT * FROM brands");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo $row['brand_id'] . ": " . $row['name'] . "\n";
    }
} else {
    echo "No brands found.\n";
}
?>
