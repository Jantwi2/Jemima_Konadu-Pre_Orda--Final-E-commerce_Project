<?php
//connect to database class
require_once(__DIR__ . "/../settings/db_class.php");

class user_class extends db_connection
{
    /**
     * Checks if a user with the given email already exists in the 'users' table.
     */
    public function email_exists($email_address) {
        $ndb = new db_connection(); 
        $email = mysqli_real_escape_string($ndb->db_conn(), $email_address);

        $sql = "SELECT user_id FROM users WHERE email = '$email' LIMIT 1";
        $this->db_query($sql);

        return ($this->db_count() > 0); 
    }

    /**
     * Registers a new user into the 'users' table.
     * Returns the new user_id on success, "duplicate" if email exists, or false on failure.
     */
    public function add_user($full_name, $email, $phone, $password, $country, $city, $user_type)
    {
        $ndb = new db_connection(); 

        // Sanitize input variables and map to new column names
        $name     = mysqli_real_escape_string($ndb->db_conn(), $full_name);
        $email_safe    = mysqli_real_escape_string($ndb->db_conn(), $email);
        $contact  = mysqli_real_escape_string($ndb->db_conn(), $phone);
        $raw_password = mysqli_real_escape_string($ndb->db_conn(), $password);
        $address  = mysqli_real_escape_string($ndb->db_conn(), $city . ", " . $country); // Combine into address
        $role     = mysqli_real_escape_string($ndb->db_conn(), $user_type);

       
        if ($this->email_exists($email)) {
            return "duplicate"; 
        }

        // Use password_hash as the column name
        $hashedPassword = password_hash($raw_password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO `users`
                (`full_name`, `email`, `phone`, `password_hash`, `address`, `user_type`, `created_at`, `is_active`) 
                VALUES ('$name','$email_safe','$contact','$hashedPassword','$address','$role', NOW(), 1)";

        $result = $this->db_query($sql);
        
        if ($result) {
            // Return the newly created user's ID
            return $this->insert_id();
        }
        
        return false;
    }

    // Get vendor details
    public function get_vendor_details($vendor_id)
    {
        $sql = "SELECT * FROM vendors WHERE vendor_id = '$vendor_id'";
        return $this->db_fetch_one($sql);
    }

    // Update vendor settings
    public function update_vendor_settings($vendor_id, $business_name, $tagline, $description, $logo_url, $primary_color, $secondary_color, $background_color, $accent_color, $font_family)
    {
        $ndb = new db_connection();
        $business_name = mysqli_real_escape_string($ndb->db_conn(), $business_name);
        $tagline = mysqli_real_escape_string($ndb->db_conn(), $tagline);
        $description = mysqli_real_escape_string($ndb->db_conn(), $description);
        $logo_url = mysqli_real_escape_string($ndb->db_conn(), $logo_url);
        $primary_color = mysqli_real_escape_string($ndb->db_conn(), $primary_color);
        $secondary_color = mysqli_real_escape_string($ndb->db_conn(), $secondary_color);
        $background_color = mysqli_real_escape_string($ndb->db_conn(), $background_color);
        $accent_color = mysqli_real_escape_string($ndb->db_conn(), $accent_color);
        $font_family = mysqli_real_escape_string($ndb->db_conn(), $font_family);

        $sql = "UPDATE vendors SET 
                business_name = '$business_name',
                tagline = '$tagline',
                description = '$description',
                logo_url = '$logo_url',
                primary_color = '$primary_color',
                secondary_color = '$secondary_color',
                background_color = '$background_color',
                accent_color = '$accent_color',
                font_family = '$font_family'
                WHERE vendor_id = '$vendor_id'";
        
        return $this->db_query($sql);
    }
    /**
     * Logs in a user based on email or phone and password.
     */
    public function login_user($login, $password)
    {
        $ndb   = new db_connection();
        $login_safe = mysqli_real_escape_string($ndb->db_conn(), $login);
        $raw_password = mysqli_real_escape_string($ndb->db_conn(), $password);

        // Fetch user by email OR phone from the 'users' table
        $sql = "SELECT * FROM `users` WHERE `email` = '$login_safe' OR `phone` = '$login_safe' LIMIT 1";
        $result = $this->db_fetch_one($sql);

        if ($result) {
            // Verify password against the 'password_hash' column
            if (password_verify($raw_password, $result['password_hash'])) {
                // Set session variables upon successful login, using new column names
                $_SESSION['user_id']   = $result['user_id'];
                $_SESSION['full_name'] = $result['full_name'];
                $_SESSION['email']= $result['email'];
                $_SESSION['user_type'] = $result['user_type'];
                
                // Return the user ID for subsequent checks (like vendor profile)
                return $result['user_id']; 
            } else {
                return "invalid_password";
            }
        } else {
            return "not_found"; // Email/phone not in DB
        }
    }

    /**
     * Adds a new vendor profile linked by user_id to the 'vendors' table.
     */
    public function add_vendor($user_id, $business_name, $registration_number, $mobile_money_account)
    {
        // Use $this connection instead of creating a new instance
        $conn = $this->db_conn();
        
        // Sanitize inputs for the new vendor schema
        $uid = mysqli_real_escape_string($conn, $user_id);
        $bname = mysqli_real_escape_string($conn, $business_name);
        $reg_num = mysqli_real_escape_string($conn, $registration_number);
        $mma = mysqli_real_escape_string($conn, $mobile_money_account);

        // Check if user_id already exists in vendors (ensures a user has only one vendor profile)
        $check_sql = "SELECT vendor_id FROM vendors WHERE user_id = '$uid'";
        $this->db_query($check_sql);
        if ($this->db_count() > 0) {
            return "duplicate";
        }

        // Generate Slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $business_name)));
        // Check uniqueness
        $check_slug = "SELECT vendor_id FROM vendors WHERE vendor_slug = '$slug'";
        $this->db_query($check_slug);
        if ($this->db_count() > 0) {
            $slug .= '-' . uniqid();
        }

        // Insert into 'vendors' table
        $sql = "INSERT INTO `vendors` 
                (`user_id`, `business_name`, `vendor_slug`, `registration_number`, `mobile_money_account`, `verified`) 
                VALUES ('$uid', '$bname', '$slug', '$reg_num', '$mma', 0)";
        
        $result = $this->db_query($sql);
        if (!$result) {
            // Use the class property $this->db which holds the connection used by db_query
            error_log("Vendor Insert Failed: " . mysqli_error($this->db));
            return mysqli_error($this->db); // Return error message for debugging
        }
        return true;
    }

    /**
     * Checks if a user has a vendor profile and updates session data if found.
     */
    public function login_vendor($user_id)
    {
        $ndb = new db_connection();
        $uid = mysqli_real_escape_string($ndb->db_conn(), $user_id);

        // Fetch vendor profile by the authenticated user's ID
        $sql = "SELECT * FROM `vendors` WHERE `user_id` = '$uid' LIMIT 1";
        $result = $this->db_fetch_one($sql);

        if ($result) {
            // Set vendor-specific session data.
            // Assumes core user session variables are already set by login_user().
            $_SESSION['vendor_id'] = $result['vendor_id'];
            $_SESSION['business_name'] = $result['business_name'];
            $_SESSION['user_type'] = 'vendor'; // Update the role for this session
            return "success";
        } else {
            return "not_found";
        }
    }

    // Get vendor details by slug
    public function get_vendor_by_slug($slug)
    {
        $ndb = new db_connection();
        $slug = mysqli_real_escape_string($ndb->db_conn(), $slug);
        $sql = "SELECT * FROM vendors WHERE vendor_slug = '$slug'";
        return $this->db_fetch_one($sql);
    }
}
?>
