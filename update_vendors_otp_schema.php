<?php
require_once("settings/db_class.php");

class SchemaUpdater extends db_connection {
    public function add_otp_tracking_to_vendors() {
        // Add OTP columns to vendors table
        $sql = "ALTER TABLE vendors 
                ADD COLUMN otp VARCHAR(10) DEFAULT NULL AFTER verified,
                ADD COLUMN otp_expiry DATETIME DEFAULT NULL AFTER otp,
                ADD COLUMN email_verified TINYINT(1) DEFAULT 0 AFTER otp_expiry";
        
        if ($this->db_query($sql)) {
            echo "Successfully added OTP tracking columns to vendors table.\n";
            return true;
        } else {
            echo "Failed to add OTP columns. They may already exist.\n";
            return false;
        }
    }
}

// Run the update
$updater = new SchemaUpdater();
$updater->add_otp_tracking_to_vendors();
?>
