<?php
session_start();
require("../controllers/user_controller.php");

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header('Location: ../view/login.php');
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

if (isset($_POST['update_settings'])) {
    $business_name = $_POST['business_name'];
    $tagline = $_POST['tagline'];
    $description = $_POST['description'];
    $primary_color = $_POST['primary_color'];
    $secondary_color = $_POST['secondary_color'];
    $background_color = $_POST['background_color'];
    $accent_color = $_POST['accent_color'];
    $font_family = $_POST['font_family'];
    
    // Image Upload Handling
    $logo_url = $_POST['existing_logo'];
    
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        // Ensure directory exists
        if (!file_exists("../uploads/logos/")) {
            mkdir("../uploads/logos/", 0777, true);
        }
        
        $target_dir = "../uploads/logos/";
        $file_extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
        $new_filename = "vendor_" . $vendor_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $logo_url = "../uploads/logos/" . $new_filename;
        } else {
            // Handle error
            header("Location: ../vendor/settings.php?msg=error");
            exit();
        }
    }

    $result = update_vendor_settings_ctr($vendor_id, $business_name, $tagline, $description, $logo_url, $primary_color, $secondary_color, $background_color, $accent_color, $font_family);

    if ($result) {
        // Update session business name if changed
        $_SESSION['business_name'] = $business_name;
        header("Location: ../vendor/settings.php?msg=updated");
    } else {
        header("Location: ../vendor/settings.php?msg=error");
    }
} else {
    header("Location: ../vendor/settings.php");
}
?>
