<?php
//connect to the user account class
include(__DIR__ . "/../classes/user_class.php");

//sanitize data

// function add_user_ctr($a,$b,$c,$d,$e,$f,$g){
// 	$adduser=new user_class();
// 	return $adduser->add_user($a,$b,$c,$d,$e,$f,$g);
// }

function add_user_ctr($customer_name,$customer_email,$customer_contact,$customer_password,$customer_country,$customer_city, $customer_role){
	$adduser=new user_class();
	return $adduser->add_user($customer_name,$customer_email,$customer_contact,$customer_password,$customer_country,$customer_city, $customer_role);
}

function login_user_ctr($customer_email, $customer_password) {
	$loginUser = new user_class();
	return $loginUser->login_user($customer_email, $customer_password);
}

function add_vendor_ctr($user_id, $business_name, $registration_number, $mobile_money_account) {
    $addVendor = new user_class();
    return $addVendor->add_vendor($user_id, $business_name, $registration_number, $mobile_money_account);
}

function login_vendor_ctr($user_id)
{
    $user = new user_class();
    return $user->login_vendor($user_id);
}

function get_vendor_details_ctr($vendor_id)
{
    $user = new user_class();
    return $user->get_vendor_details($vendor_id);
}

function update_vendor_settings_ctr($vendor_id, $business_name, $tagline, $description, $logo_url, $primary_color, $secondary_color, $background_color, $accent_color, $font_family)
{
    $user = new user_class();
    return $user->update_vendor_settings($vendor_id, $business_name, $tagline, $description, $logo_url, $primary_color, $secondary_color, $background_color, $accent_color, $font_family);
}

function get_vendor_by_slug_ctr($slug)
{
    $user = new user_class();
    return $user->get_vendor_by_slug($slug);
}
?>
