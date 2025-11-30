<?php
//connect to the user account class
include("../classes/category_class.php");

//sanitize data

function add_cat_ctr($cat_name, $customer_id){
	$addcat=new category_class();
	return $addcat->add_category($cat_name, $customer_id);
}

function get_all_cat_ctr($user){
    $cat=new category_class();
    return $cat->view_all_categories($user);
}
    

function update_cat_ctr($cat_id, $new_name){
    $updatecat=new category_class();
    return $updatecat->update_category($cat_id, $new_name);
}

function delete_cat_ctr($cat_id){
    $deletecat=new category_class();
    return $deletecat->delete_category($cat_id);
}

//--INSERT--//

//--SELECT--//

//--UPDATE--//

//--DELETE--//

?>