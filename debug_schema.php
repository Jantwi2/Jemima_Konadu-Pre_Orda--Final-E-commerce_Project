<?php
require("settings/db_class.php");

$db = new db_connection();
$conn = $db->db_conn();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Tables in database:\n";
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $table = $row[0];
    echo "\nTable: $table\n";
    $cols = $conn->query("DESCRIBE $table");
    while ($col = $cols->fetch_assoc()) {
        echo "  " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
?>
