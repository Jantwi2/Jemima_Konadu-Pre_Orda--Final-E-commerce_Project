<?php
session_start();
require_once("../controllers/user_controller.php"); // adjust path if needed
header("Content-Type: application/json");

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "error" => "Invalid request method."
    ]);
    exit;
}

$email    = isset($_POST['email']) ? trim($_POST['email']) : "";
$password = isset($_POST['password']) ? trim($_POST['password']) : "";

// Basic input validation
if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "error" => "Email and password are required."
    ]);
    exit;
}

$result = login_customer_ctr($email, $password);

if ($result === "success") {
    echo json_encode([
        "success" => true,
        "redirect" => "../index.php" // adjust to your dashboard path
    ]);
} elseif ($result === "invalid_password") {
    echo json_encode([
        "success" => false,
        "error" => "Incorrect password."
    ]);
} elseif ($result === "not_found") {
    echo json_encode([
        "success" => false,
        "error" => "No account found with that email."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Login failed. Please try again."
    ]);
}
?>
