<?php
session_start();
require("../controllers/product_controller.php");

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header('Location: ../view/login.php');
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];
    $brand_id = $_POST['brand'];
    $brand_id = $_POST['brand'];
    $stock_status = $_POST['stock_status'];
    $estimated_delivery_time = $_POST['estimated_delivery_time'];
    
    // Image Upload Handling
    $image_url = "default_product.png"; // Default
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "prod_" . time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "../uploads/products/" . $new_filename;
        }
    }

    $result = add_product_ctr($vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time);

    if ($result) {
        header("Location: ../vendor/products.php?msg=success");
    } else {
        header("Location: ../vendor/products.php?msg=error");
    }

} elseif (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];
    $brand_id = $_POST['brand'];
    $brand_id = $_POST['brand'];
    $stock_status = $_POST['stock_status'];
    $estimated_delivery_time = $_POST['estimated_delivery_time'];
    
    // Keep existing image if no new one uploaded
    $image_url = $_POST['existing_image'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Ensure directory exists
        if (!file_exists("../uploads/products/")) {
            mkdir("../uploads/products/", 0777, true);
        }

        $target_dir = "../uploads/products/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = "prod_" . time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "../uploads/products/" . $new_filename;
        } else {
            echo "Error uploading file.";
            exit();
        }
    }

    $result = edit_product_ctr($product_id, $vendor_id, $name, $price, $description, $category_id, $brand_id, $image_url, $stock_status, $estimated_delivery_time);

    if ($result) {
        header("Location: ../vendor/products.php?msg=updated");
    } else {
        header("Location: ../vendor/products.php?msg=error");
    }

} elseif (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $result = delete_product_ctr($product_id, $vendor_id);
    
    if ($result) {
        header("Location: ../vendor/products.php?msg=deleted");
    } else {
        header("Location: ../vendor/products.php?msg=error");
    }
} else {
    header("Location: ../vendor/products.php");
}
?>
