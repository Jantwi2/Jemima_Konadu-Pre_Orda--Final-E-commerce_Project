<?php
//connect to database class
require_once(__DIR__ . "/../settings/db_class.php");

/**
*General class to handle all functions 
*/
/**
 *
 */

//  public function add_brand($a,$b)
// 	{
// 		$ndb = new db_connection();	
// 		$name =  mysqli_real_escape_string($ndb->db_conn(), $a);
// 		$desc =  mysqli_real_escape_string($ndb->db_conn(), $b);
// 		$sql="INSERT INTO `brands`(`brand_name`, `brand_description`) VALUES ('$name','$desc')";
// 		return $this->db_query($sql);
// 	}
class category_class extends db_connection
{
	//--INSERT--//
     public function add_category($cat_name, $customer_id)
	{
		$ndb = new db_connection();	
		$cat_name =  mysqli_real_escape_string($ndb->db_conn(), $cat_name);
		$customer_id =  mysqli_real_escape_string($ndb->db_conn(), $customer_id);
		$sql="INSERT INTO `categories`(`cat_name`, `customer_id`) VALUES ('$cat_name', '$customer_id')";
		return $this->db_query($sql);
	}


    public function view_all_categories($user)
    {
        $ndb = new db_connection();	
        $user =  mysqli_real_escape_string($ndb->db_conn(), $user);
        $sql = "SELECT * FROM categories WHERE customer_id = $user";
        $result = $ndb->db_fetch_all($sql);
        return $result;
    }


    public function update_category($cat_id, $new_name)
    {
        $ndb = new db_connection();	
        $cat_id = mysqli_real_escape_string($ndb->db_conn(), $cat_id);
        $new_name = mysqli_real_escape_string($ndb->db_conn(), $new_name);

        $sql = "UPDATE `categories` SET `cat_name`='$new_name' WHERE `cat_id`='$cat_id'";
        return $this->db_query($sql);
    }


    public function delete_category($cat_id)
    {
        $ndb = new db_connection();	
        $cat_id = mysqli_real_escape_string($ndb->db_conn(), $cat_id);

        $sql = "DELETE FROM `categories` WHERE `cat_id`='$cat_id'";
        return $this->db_query($sql);
    }
	

}

?>