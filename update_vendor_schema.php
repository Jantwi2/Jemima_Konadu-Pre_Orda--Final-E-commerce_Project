<?php
require("settings/db_class.php");
$db = new db_connection();
$conn = $db->db_conn();

$queries = [
    "ALTER TABLE vendors ADD COLUMN vendor_slug VARCHAR(255) UNIQUE",
    "ALTER TABLE vendors ADD COLUMN tagline VARCHAR(255)",
    "ALTER TABLE vendors ADD COLUMN description TEXT",
    "ALTER TABLE vendors ADD COLUMN logo_url VARCHAR(255)",
    "ALTER TABLE vendors ADD COLUMN primary_color VARCHAR(7) DEFAULT '#3182ce'",
    "ALTER TABLE vendors ADD COLUMN secondary_color VARCHAR(7) DEFAULT '#2d3748'",
    "ALTER TABLE vendors ADD COLUMN background_color VARCHAR(7) DEFAULT '#ffffff'",
    "ALTER TABLE vendors ADD COLUMN accent_color VARCHAR(7) DEFAULT '#f7fafc'",
    "ALTER TABLE vendors ADD COLUMN font_family VARCHAR(50) DEFAULT 'Inter'"
];

foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Successfully executed: $sql\n";
    } else {
        echo "Error executing $sql: " . $conn->error . "\n";
    }
}

// Generate slugs for existing vendors
$result = $conn->query("SELECT vendor_id, business_name FROM vendors WHERE vendor_slug IS NULL");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['business_name'])));
        // Ensure uniqueness (simple check)
        $check = $conn->query("SELECT vendor_id FROM vendors WHERE vendor_slug = '$slug'");
        if ($check->num_rows > 0) {
            $slug .= '-' . $row['vendor_id'];
        }
        $conn->query("UPDATE vendors SET vendor_slug = '$slug' WHERE vendor_id = " . $row['vendor_id']);
        echo "Generated slug '$slug' for vendor ID " . $row['vendor_id'] . "\n";
    }
}
?>
