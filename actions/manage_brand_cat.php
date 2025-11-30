<?php
session_start();
require("../controllers/product_controller.php");

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header('Location: ../view/login.php');
    exit();
}

// --- Category Actions ---

if (isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $vendor_id = $_SESSION['user_id'];
    $result = add_category_ctr($name, $vendor_id);
    if ($result) {
        header("Location: ../vendor/brandcatmgt.php?msg=cat_added");
    } else {
        header("Location: ../vendor/brandcatmgt.php?msg=error");
    }

} elseif (isset($_POST['edit_category'])) {
    $id = $_POST['category_id'];
    $name = $_POST['name'];
    $result = edit_category_ctr($id, $name);
    if ($result) {
        header("Location: ../vendor/brandcatmgt.php?msg=cat_updated");
    } else {
        header("Location: ../vendor/brandcatmgt.php?msg=error");
    }

} elseif (isset($_GET['delete_cat'])) {
    $id = $_GET['delete_cat'];
    $result = delete_category_ctr($id);
    if ($result) {
        header("Location: ../vendor/brandcatmgt.php?msg=cat_deleted");
    } else {
        header("Location: ../vendor/brandcatmgt.php?msg=error");
    }

// --- Brand Actions ---

} elseif (isset($_POST['add_brand'])) {
    $name = $_POST['name'];
    $vendor_id = $_SESSION['user_id'];
    $result = add_brand_ctr($name, $vendor_id);
    if ($result) {
        header("Location: ../vendor/brandcatmgt.php?msg=brand_added");
    } else {
        header("Location: ../vendor/brandcatmgt.php?msg=error");
    }

} elseif (isset($_POST['edit_brand'])) {
    $id = $_POST['brand_id'];
    $name = $_POST['name'];
    $result = edit_brand_ctr($id, $name);
    if ($result) {
        header("Location: ../vendor/brandcatmgt.php?msg=brand_updated");
    } else {
        header("Location: ../vendor/brandcatmgt.php?msg=error");
    }

} elseif (isset($_GET['delete_brand'])) {
    $id = $_GET['delete_brand'];
    $result = delete_brand_ctr($id);
    if ($result) {
        header("Location: ../vendor/brandcatmgt.php?msg=brand_deleted");
    } else {
        header("Location: ../vendor/brandcatmgt.php?msg=error");
    }

} else {
    header("Location: ../vendor/brandcatmgt.php");
}
?>
