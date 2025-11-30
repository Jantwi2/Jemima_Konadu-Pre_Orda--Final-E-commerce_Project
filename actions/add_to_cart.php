<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product ID and quantity
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($product_id > 0 && $quantity > 0) {
    // Add to cart or update quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    // Return success with cart count
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'message' => 'Added to cart successfully!'
        ]);
        exit();
    } else {
        // Redirect to cart page
        $store_param = isset($_POST['store']) ? '?store=' . htmlspecialchars($_POST['store']) : '';
        header('Location: ../view/cart.php' . $store_param);
        exit();
    }
} else {
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product or quantity'
        ]);
        exit();
    } else {
        $store_param = isset($_POST['store']) ? '?store=' . htmlspecialchars($_POST['store']) : '';
        header('Location: ../view/products.php' . $store_param);
        exit();
    }
}
?>
