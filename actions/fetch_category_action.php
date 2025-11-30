<?php
//connect to the category controller
include("../controllers/category_controller.php");

//fetch all categories
$categories = get_all_cat_ctr($_SESSION['customer_id']);
if(!$categories){
    $errors[] = "No categories found";
}   else{
    $categories = $categories;
}

//sanitize data
// if(isset($_POST['fetch_categories'])){
// 	//call the controller function      
// 	$result = get_all_cat_ctr();
// 	if($result){
// 		$categories = $result;
// 	}else{
// 		$errors[] = "Failed to fetch categories";   
// 	}
// }
?>