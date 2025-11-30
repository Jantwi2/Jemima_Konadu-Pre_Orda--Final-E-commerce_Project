<?php
header('Content-Type: application/json');

include("../controllers/category_controller.php");

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);
$cat_id = isset($data['id']) ? trim($data['id']) : '';

if (empty($cat_id)) {
    echo json_encode(['success' => false, 'message' => 'Category ID is required']);
    exit;
}

// Call the controller function
$result = delete_cat_ctr($cat_id);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
}
exit;

?>
