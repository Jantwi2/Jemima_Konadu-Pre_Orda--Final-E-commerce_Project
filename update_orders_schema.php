<?php
require_once("settings/db_class.php");

class SchemaUpdater extends db_connection {
    public function update_orders_table() {
        // Check if columns exist first to avoid errors
        $check_sql = "SHOW COLUMNS FROM orders LIKE 'shipping_address'";
        $result = $this->db_fetch_one($check_sql);
        
        if (!$result) {
            $sql = "ALTER TABLE orders 
                    ADD COLUMN shipping_address TEXT DEFAULT NULL AFTER status,
                    ADD COLUMN billing_address TEXT DEFAULT NULL AFTER shipping_address,
                    ADD COLUMN order_notes TEXT DEFAULT NULL AFTER billing_address";
            
            if ($this->db_query($sql)) {
                echo "Successfully added address columns to orders table.\n";
            } else {
                echo "Failed to add columns to orders table.\n";
            }
        } else {
            echo "Columns already exist in orders table.\n";
        }
    }
}

$updater = new SchemaUpdater();
$updater->update_orders_table();
?>
