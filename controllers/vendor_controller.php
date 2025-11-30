<?php
//connect to the user account class
include("../classes/vendor_class.php");

//sanitize data

// function add_user_ctr($a,$b,$c,$d,$e,$f,$g){
// 	$adduser=new user_class();
// 	return $adduser->add_user($a,$b,$c,$d,$e,$f,$g);
// }

function add_vendor_ctr($full_name, $email, $phone, $password, $address, $business_name, $business_type, $registration_number, $mobile_money_account)
{
    $addvendor = new vendor_class();
    return $addvendor->register_vendor($full_name, $email, $phone, $password, $address, $business_name, $business_type, $registration_number, $mobile_money_account);
}

function login_vendor_ctr($login, $password)
{
    $loginvendor = new vendor_class();
    return $loginvendor->login_vendor($login, $password);
}

//--INSERT--//

//--SELECT--//

//--UPDATE--//

//--DELETE--//

?>