<?php
//connect to database class
require_once(__DIR__ . "/../settings/db_class.php");



//  public function add_brand($a,$b)
// 	{
// 		$ndb = new db_connection();	
// 		$name =  mysqli_real_escape_string($ndb->db_conn(), $a);
// 		$desc =  mysqli_real_escape_string($ndb->db_conn(), $b);
// 		$sql="INSERT INTO `brands`(`brand_name`, `brand_description`) VALUES ('$name','$desc')";
// 		return $this->db_query($sql);
// 	}
class vendor_class extends db_connection
{
    public function register_vendor($full_name, $email, $phone, $password, $address, $business_name, $business_type, $registration_number, $mobile_money_account)
    {
        $ndb = new db_connection();
        $full_name = mysqli_real_escape_string($ndb->db_conn(), $full_name);
        $email = mysqli_real_escape_string($ndb->db_conn(), $email);
        $phone = mysqli_real_escape_string($ndb->db_conn(), $phone);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $address = mysqli_real_escape_string($ndb->db_conn(), $address);
        $business_name = mysqli_real_escape_string($ndb->db_conn(), $business_name);
        $business_type = mysqli_real_escape_string($ndb->db_conn(), $business_type);
        $registration_number = mysqli_real_escape_string($ndb->db_conn(), $registration_number);
        $mobile_money_account = mysqli_real_escape_string($ndb->db_conn(), $mobile_money_account);

        // Check for duplicate email or phone
        $check_sql = "SELECT user_id FROM users WHERE email='$email' OR phone='$phone' LIMIT 1";
        $this->db_query($check_sql);
        if ($this->db_count() > 0) {
            return "duplicate";
        }

        // Insert into users
        $user_sql = "INSERT INTO users (full_name, email, phone, password_hash, address, user_type) 
                    VALUES ('$full_name', '$email', '$phone', '$password_hash', '$address', 'vendor')";
        if ($this->db_query($user_sql)) {
            $user_id = $this->insert_id();

            // Insert into vendors
            $vendor_sql = "INSERT INTO vendors (user_id, business_name, business_type, registration_number, mobile_money_account) 
                        VALUES ($user_id, '$business_name', '$business_type', '$registration_number', '$mobile_money_account')";
            if ($this->db_query($vendor_sql)) {
                return "success";
            } else {
                return "vendor_error";
            }
        } else {
            return "user_error";
        }
    }

    public function login_vendor($login, $password)
    {
        $ndb = new db_connection();
        $login = mysqli_real_escape_string($ndb->db_conn(), $login);
        $password = mysqli_real_escape_string($ndb->db_conn(), $password);

        $sql = "SELECT * FROM users WHERE (email='$login' OR phone='$login') AND user_type='vendor' LIMIT 1";
        $result = $this->db_query($sql); // Store the result of the query
        if ($this->db_count() == 1) {
            $user = $this->db_fetch_one($sql); // Pass the result to db_fetch_one
            if (password_verify($password, $user['password_hash'])) {
                return $user; // Return user data on successful login
            } else {
                return "invalid_password";
            }
        } else {
            return "user_not_found";
        }
    }


    // Get vendor details by vendor_id
    public function get_vendor_details($vendor_id)
    {
        $sql = "SELECT v.*, u.email, u.full_name 
                FROM vendors v 
                JOIN users u ON v.user_id = u.user_id 
                WHERE v.vendor_id = '$vendor_id'";
        return $this->db_fetch_one($sql);
    }
}

	



?>