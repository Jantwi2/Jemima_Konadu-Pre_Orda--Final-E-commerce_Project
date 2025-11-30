<?php
require("settings/db_class.php");
$db = new db_connection();
$conn = $db->db_conn();

$queries = [
    "ALTER TABLE brands ADD COLUMN vendor_id INT DEFAULT NULL",
    "ALTER TABLE categories ADD COLUMN vendor_id INT DEFAULT NULL"
];

foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Successfully executed: $sql\n";
    } else {
        echo "Error executing $sql: " . $conn->error . "\n";
    }
}
?>
