<?php
require_once("settings/db_class.php");

$db = new db_connection();
$db->db_connect();

// Check orders table columns
$sql = "DESCRIBE orders";
$result = $db->db_fetch_all($sql);

echo "<h2>Orders Table Structure:</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>
