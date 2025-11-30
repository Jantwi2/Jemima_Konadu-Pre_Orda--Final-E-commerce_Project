<?php
require("settings/db_class.php");
$db = new db_connection();
$conn = $db->db_conn();

echo "Checking for vendors with missing slugs...\n";

// Generate slugs for existing vendors where slug is NULL or empty
$result = $conn->query("SELECT vendor_id, business_name FROM vendors WHERE vendor_slug IS NULL OR vendor_slug = ''");

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['business_name'])));
        
        // Ensure uniqueness
        $check = $conn->query("SELECT vendor_id FROM vendors WHERE vendor_slug = '$slug' AND vendor_id != " . $row['vendor_id']);
        if ($check->num_rows > 0) {
            $slug .= '-' . $row['vendor_id'];
        }
        
        $update = $conn->query("UPDATE vendors SET vendor_slug = '$slug' WHERE vendor_id = " . $row['vendor_id']);
        
        if ($update) {
            echo "Generated slug '$slug' for vendor ID " . $row['vendor_id'] . "\n";
        } else {
            echo "Failed to update vendor ID " . $row['vendor_id'] . ": " . $conn->error . "\n";
        }
    }
} else {
    echo "No vendors found with missing slugs.\n";
}

echo "Done.\n";
?>
