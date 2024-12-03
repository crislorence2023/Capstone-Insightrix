<?php
session_start();
ini_set('display_errors', 1);



Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}
	function login(){
		extract($_POST);
		$type = array("", "users", "faculty_list", "student_list");
		$type2 = array("", "admin", "faculty", "student");
		// Check if the identifier is an email or school ID
		if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
			$condition = "email = '".$identifier."'";
		} else {
			$condition = "school_id = '".$identifier."'";
		}
		$qry = $this->db->query("SELECT *, concat(firstname,' ',lastname) as name FROM {$type[$login]} WHERE {$condition}");
		if($qry->num_rows > 0){
			$row = $qry->fetch_array();
			$hashed_password = $row['password'];
			
			$password_verified = false;
			$update_hash = false;
	
			if (password_verify($password, $hashed_password)) {
				$password_verified = true;
			} elseif (md5($password) === $hashed_password) {
				$password_verified = true;
				$update_hash = true;
			}
	
			if ($password_verified) {
				// If MD5 was used, update to newer hashing method
				if ($update_hash) {
					$new_hash = password_hash($password, PASSWORD_DEFAULT);
					$update_query = "UPDATE {$type[$login]} SET password = '{$new_hash}' WHERE id = {$row['id']}";
					$this->db->query($update_query);
				}
	
				// Store user data in session
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = $login;
				$_SESSION['login_view_folder'] = $type2[$login].'/';
				$_SESSION['user_id'] = $row['id']; // Added for password change functionality
				
				// Set academic data
				$academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1");
				if($academic->num_rows > 0){
					foreach($academic->fetch_array() as $k => $v){
						if(!is_numeric($k))
							$_SESSION['academic'][$k] = $v;
					}
				}
				
				// Check if password needs to be changed (for students and faculty)
				if($login != 1 && $row['is_password_changed'] == 0) {
					return 3; // Redirect to password change
				}
				
				return 1; // Normal login success
			}
			return 2; // Wrong password
		}
		return 2; // User not found
	}





	function login3() {
		extract($_POST);
		
		// Check if the identifier is an email or school ID
		if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
			$condition = "email = '".$identifier."'";
		} else {
			$condition = "school_id = '".$identifier."'";
		}
	
		$qry = $this->db->query("SELECT *, concat(firstname,' ',lastname) as name FROM users WHERE {$condition}");
		
		if($qry->num_rows > 0) {
			$row = $qry->fetch_array();
			$hashed_password = $row['password'];
			
			$password_verified = false;
			$update_hash = false;
	
			if (password_verify($password, $hashed_password)) {
				$password_verified = true;
			} elseif (md5($password) === $hashed_password) {
				$password_verified = true;
				$update_hash = true;
			}
	
			if ($password_verified) {
				// If MD5 was used, update to newer hashing method
				if ($update_hash) {
					$new_hash = password_hash($password, PASSWORD_DEFAULT);
					$update_query = "UPDATE users SET password = '{$new_hash}' WHERE id = {$row['id']}";
					$this->db->query($update_query);
				}
	
				// Store user data in session
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = 1; // Hardcoded for superadmin
				$_SESSION['login_view_folder'] = 'superadmin/'; // Hardcoded admin folder
				$_SESSION['user_id'] = $row['id'];
	
				// Set academic data
				$academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1");
				if($academic->num_rows > 0) {
					foreach($academic->fetch_array() as $k => $v) {
						if(!is_numeric($k))
							$_SESSION['academic'][$k] = $v;
					}
				}
	
				return 1; // Login success
			}
			return 2; // Wrong password
		}
		return 2; // User not found
	}


	
	function login4() {
		extract($_POST);
		
		// Check if the identifier is an email or school ID
		if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
			$condition = "email = '".$identifier."'";
		} else {
			$condition = "school_id = '".$identifier."'";
		}
	
		$qry = $this->db->query("SELECT *, concat(firstname,' ',lastname) as name FROM staff WHERE {$condition}");
		
		if($qry->num_rows > 0) {
			$row = $qry->fetch_array();
			$hashed_password = $row['password'];
			
			$password_verified = false;
			$update_hash = false;
	
			if (password_verify($password, $hashed_password)) {
				$password_verified = true;
			} elseif (md5($password) === $hashed_password) {
				$password_verified = true;
				$update_hash = true;
			}
	
			if ($password_verified) {
				// If MD5 was used, update to newer hashing method
				if ($update_hash) {
					$new_hash = password_hash($password, PASSWORD_DEFAULT);
					$update_query = "UPDATE users SET password = '{$new_hash}' WHERE id = {$row['id']}";
					$this->db->query($update_query);
				}
	
				// Store user data in session
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = 4; // Hardcoded for superadmin
				$_SESSION['login_view_folder'] = 'staff/'; // Hardcoded admin folder
				$_SESSION['user_id'] = $row['id'];
	
				// Set academic data
				$academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1");
				if($academic->num_rows > 0) {
					foreach($academic->fetch_array() as $k => $v) {
						if(!is_numeric($k))
							$_SESSION['academic'][$k] = $v;
					}
				}
	
				return 1; // Login success
			}
			return 2; // Wrong password
		}
		return 2; // User not found
	}




		
	
	
	// Optional: Function to check remember me token
	function check_remember_token() {
		if(isset($_COOKIE['staff_remember_token'])) {
			$token = $_COOKIE['staff_remember_token'];
			$qry = $this->db->query("SELECT * FROM staff WHERE remember_token = '$token'");
			if($qry->num_rows > 0) {
				$row = $qry->fetch_array();
				// Log the user in automatically
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key)) {
						$_SESSION['login_'.$key] = $value;
					}
				}
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = 3;
				$_SESSION['login_name'] = $row['firstname'] . ' ' . $row['lastname'];
				$_SESSION['login_avatar'] = $row['avatar'];
				$_SESSION['login_view_folder'] = 'admin/';
				return true;
			}
		}
		return false;
	}




   //18/10/2024
   function send_verification(){
    extract($_POST);
    $user_id = $_SESSION['login_id'];
    $type = array("","users","faculty_list","student_list");
    $table = $type[$_SESSION['login_type']];
    
    // Check if email already exists for any user
    $check_email = $this->db->query("SELECT * FROM $table WHERE email = '$email'");
    if($check_email->num_rows > 0) {
        return 4; // Email already exists in database
    }
    
    // Generate and store verification code
    $verification_code = substr(md5(uniqid(mt_rand(), true)), 0, 6);
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $update = $this->db->query("UPDATE $table SET 
        verification_code = '$verification_code', 
        verification_code_expiry = '$expiry' 
        WHERE id = $user_id");
    
    if($update) {
        // Send email with verification code
        if($this->send_verification_email($email, $verification_code)) {
            return 1; // Email sent successfully
        } else {
            return 3; // Error sending email
        }
    } else {
        return 2; // Error updating verification code
    }
}

function verify_code(){
    extract($_POST);
    $user_id = $_SESSION['login_id'];
    $type = array("","users","faculty_list","student_list");
    $table = $type[$_SESSION['login_type']];
    
    // Only check verification code for the logged-in user
    $verify_query = $this->db->query("SELECT * FROM $table 
        WHERE id = $user_id 
        AND verification_code = '$verification_code' 
        AND verification_code_expiry > NOW()");
        
    if($verify_query->num_rows > 0) {
        return 1; // Verification successful
    }
    return 0; // Verification failed
}

function change_password(){
    extract($_POST);
    $user_id = $_SESSION['login_id'];
    $user_type = $_SESSION['login_type'];
    
    if(!$this->validatePassword($new_password)){
        return 2; // Password doesn't meet requirements
    }
    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $table = '';
    switch($user_type){
        case 2:
            $table = 'faculty_list';
            break;
        case 3:
            $table = 'student_list';
            break;
        default:
            return 0; // Invalid user type
    }
    
    // Check if new email is already used by another user
    $check_email = $this->db->query("SELECT * FROM $table WHERE email = '$email' AND id != $user_id");
    if($check_email->num_rows > 0) {
        return 3; // Email already exists
    }
    
    // Update password and email
    $current_datetime = date('Y-m-d H:i:s');
    $update = $this->db->query("UPDATE $table SET 
        password = '$hashed_password', 
        email = '$email',
        is_password_changed = 1, 
        verification_code = NULL, 
        verification_code_expiry = NULL,
        email_verified_at = '$current_datetime'
        WHERE id = $user_id");
    
    if($update){
        return 1; // Success
    }
    return 0; // Error
}

private function validatePassword($password) {
	return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
}

    function update_password($user_id, $password, $table) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query("UPDATE $table SET password = '$hashed_password' WHERE id = $user_id");
    }

    








   //October 27,2024
   








   function forgot_password() {
    extract($_POST);
    $type = array("","users","faculty_list","student_list");
    $table = $type[$login];
    
    // Sanitize email input
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    $qry = $this->db->query("SELECT * FROM $table WHERE email = '" . $this->db->real_escape_string($email) . "'");
    
    if($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $verification_code = substr(md5(uniqid(mt_rand(), true)), 0, 6);
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $update = $this->db->query("UPDATE $table SET 
            verification_code = '" . $this->db->real_escape_string($verification_code) . "', 
            verification_code_expiry = '" . $this->db->real_escape_string($expiry) . "' 
            WHERE id = " . (int)$row['id']);
        
        if($update) {
            // Using the existing PHPMailer setup
            require 'vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'Renrenpasilang@gmail.com';
                $mail->Password = 'jhmfczemtchqlnil';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Clear any previous recipients
                $mail->clearAddresses();
                
                // Recipients
                $mail->setFrom('Renrenpasilang@gmail.com', 'Insightrix Support');
                $mail->addAddress($email);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Verification Code';
                
                // Create a more professional email template
                $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #12686e;'>Password Reset Request</h2>
                    <p>You have requested to reset your password. Please use the following verification code:</p>
                    <div style='background-color: #f4f4f4; padding: 15px; text-align: center; margin: 20px 0;'>
                        <h1 style='color: #12686e; letter-spacing: 5px; margin: 0;'>{$verification_code}</h1>
                    </div>
                    <p>This code will expire in 1 hour.</p>
                    <p>If you didn't request this password reset, please ignore this email or contact support if you have concerns.</p>
                    <p style='color: #666; font-size: 12px; margin-top: 20px;'>
                        This is an automated message, please do not reply to this email.
                    </p>
                </div>";
                
                $mail->Body = $emailBody;
                $mail->AltBody = "Your verification code is: {$verification_code}\nThis code will expire in 1 hour.";
                
                if($mail->send()) {
                    return 1; // Success
                } else {
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                    return 3; // Email sending failed
                }
                
            } catch (Exception $e) {
                error_log("Mailer Exception: " . $e->getMessage());
                return 3; // Email sending failed
            }
        } else {
            return 3; // Database update failed
        }
    } else {
        return 2; // Email not found
    }
}
	
	

function verify_reset_code() {
    extract($_POST);
    $type = array("","users","faculty_list","student_list");
    $table = $type[$login];
    
    // Sanitize inputs
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $code = preg_replace("/[^a-zA-Z0-9]/", "", $code);
    
    // Query to check verification code
    $qry = $this->db->query("SELECT * FROM $table 
        WHERE email = '" . $this->db->real_escape_string($email) . "' 
        AND verification_code = '" . $this->db->real_escape_string($code) . "'
        AND verification_code_expiry > NOW()");
    
    if($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        
        // Generate a secure reset token
        $reset_token = bin2hex(random_bytes(32));
        $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Update the reset token
        $update = $this->db->query("UPDATE $table SET 
            reset_token = '" . $this->db->real_escape_string($reset_token) . "',
            reset_token_expiry = '" . $this->db->real_escape_string($reset_token_expiry) . "',
            verification_code = NULL,
            verification_code_expiry = NULL 
            WHERE id = " . (int)$row['id']);
            
        if($update) {
            return 1; // Success
        } else {
            return 3; // Database update failed
        }
    } else {
        return 2; // Invalid or expired code
    }
}
  


public function forgot_update_password() {
    extract($_POST);
    
    // Input validation
    if (!isset($email) || !isset($login) || !isset($password)) {
        return 2; // Missing required fields
    }
    
    // Sanitize inputs
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $login = filter_var($login, FILTER_SANITIZE_NUMBER_INT);
    
    // Determine correct table
    $type = array(
        "1" => "users",
        "2" => "faculty_list",
        "3" => "student_list"
    );
    
    if (!isset($type[$login])) {
        return 2; // Invalid login type
    }
    
    $table = $type[$login];
    
    try {
        // First check if this is the current password
        $check_stmt = $this->db->prepare("SELECT id, password FROM {$table} 
            WHERE email = ? 
            AND reset_token IS NOT NULL 
            AND reset_token_expiry > NOW()");
            
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            return 2; // No valid reset token found
        }
        
        $user = $result->fetch_assoc();
        
        // Check if new password matches current password
        if (password_verify($password, $user['password'])) {
            return 3; // Cannot use current password
        }
        
        // Hash new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $update_stmt = $this->db->prepare("UPDATE {$table} 
            SET password = ?,
                reset_token = NULL,
                reset_token_expiry = NULL
            WHERE id = ? AND email = ?");
            
        $update_stmt->bind_param("sis", $hashed_password, $user['id'], $email);
        $success = $update_stmt->execute();
        
        if ($success && $update_stmt->affected_rows > 0) {
            return 1; // Success
        }
        
        return 2; // Update failed
        
    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        return 2; // Database error
    }
}

	
	


	private function send_verification_email($email, $verification_code) {
		require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer
	
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		try {
			// Server settings
			$mail->isSMTP();
			$mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
			$mail->SMTPAuth   = true;
			$mail->Username   = 'Renrenpasilang@gmail.com'; // Replace with your email
			$mail->Password   = 'jhmfczemtchqlnil'; // Replace with your app password
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port       = 587;
	
			// Recipients
			$mail->setFrom('your-email@gmail.com', 'Your Name');  // Set sender
			$mail->addAddress($email);  // Add recipient
	
			// Content
			$mail->isHTML(true);
			$mail->Subject = 'Email Verification';
			$mail->Body    = "Your verification code is: <b>{$verification_code}</b>";
	
			$mail->send();
			return true;
		} catch (Exception $e) {
			error_log("Mailer Error: {$mail->ErrorInfo}");  // Error logging
			return false;
		}
	}
		










	
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	//superadmin
	function logout3(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:superadminlogin.php");
	}
     

	//staff
	function logout4(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:stafflogin.php");
	}

	
    // Add new method for change password logout
    function logout_change_password(){ 
        session_destroy(); 
        foreach ($_SESSION as $key => $value) { 
            unset($_SESSION[$key]); 
        } 
        // Return 1 instead of redirecting
        return 1;
    }





	function login2(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = '".$student_code."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['rs_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);

				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");

		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
					$_SESSION['login_id'] = $id;
				if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user(){
		try {
			extract($_POST);
			$data = "";
			$type = array("","users","faculty_list","student_list");
			
			// Validate login type exists in session
			if(!isset($_SESSION['login_type']) || !isset($type[$_SESSION['login_type']])) {
				return json_encode(['status' => 'error', 'message' => 'Invalid user type']);
			}
			
			$table = $type[$_SESSION['login_type']];
			
			// Check if password is being updated
			if(!empty($password)) {
				// Get user's current password from database
				$id = isset($id) ? intval($id) : 0;
				$check_pass = $this->db->query("SELECT password FROM `{$table}` WHERE id = {$id}");
				if($check_pass && $check_pass->num_rows > 0) {
					$current_pass = $check_pass->fetch_assoc()['password'];
					// Check if new password matches the old one
					if(md5($password) === $current_pass) {
						return 3; // Return code 3 for same password error
					}
				}
			}
			
			// Rest of your existing code...
			foreach($_POST as $k => $v){
				if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
					$v = $this->db->real_escape_string($v);
					if(empty($data)){
						$data .= " $k='$v' ";
					}else{
						$data .= ", $k='$v' ";
					}
				}
			}
			
			// Check for duplicate email
			$id = isset($id) ? intval($id) : 0;
			$email = $this->db->real_escape_string($email);
			$check = $this->db->query("SELECT * FROM `{$table}` WHERE email ='$email' ".(!empty($id) ? " AND id != {$id} " : ''));
			
			if(!$check) {
				return json_encode(['status' => 'error', 'message' => 'Database error: ' . $this->db->error]);
			}
			
			if($check->num_rows > 0){
				return 2; // Email already exists
			}
			
			// Handle image upload
			if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
				if($move){
					$data .= ", avatar = '$fname' ";
				}
			}
			
			// Handle password update
			if(!empty($password)) {
				$password = $this->db->real_escape_string($password);
				$data .= " ,password=md5('$password') ";
			}
			
			// Perform update or insert
			if(empty($id)){
				$query = "INSERT INTO `{$table}` SET $data";
			}else{
				$query = "UPDATE `{$table}` SET $data WHERE id = $id";
			}
			
			$save = $this->db->query($query);
			
			if($save){
				foreach ($_POST as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
				
				return 1;
			}else{
				return json_encode(['status' => 'error', 'message' => 'Database error: ' . $this->db->error]);
			}
			
		} catch (Exception $e) {
			return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}


	function delete_staff(){
        extract($_POST);
        
        if(!isset($id) || !is_numeric($id)) {
            return json_encode(['status' => 'error', 'error' => 'Invalid staff ID']);
        }
        
        // Use prepared statement to prevent SQL injection
        $stmt = $this->db->prepare("DELETE FROM staff WHERE id = ?");
        if(!$stmt) {
            return json_encode(['status' => 'error', 'error' => 'Database error']);
        }
        
        $stmt->bind_param("i", $id);
        $delete = $stmt->execute();
        
        if($delete){
            if($stmt->affected_rows > 0) {
                $stmt->close();
                return json_encode(['status' => 'success']);
            } else {
                $stmt->close();
                return json_encode(['status' => 'error', 'error' => 'Staff not found']);
            }
        }
        
        $stmt->close();
        return json_encode(['status' => 'error', 'error' => 'Failed to delete staff']);
    }

	function save_system_settings(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set $data where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}
	function save_subject(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM subject_list where code = '$code' and id != '{$id}' ")->num_rows;
		if($chk > 0){
			return 2;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO subject_list set $data");
		}else{
			$save = $this->db->query("UPDATE subject_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
	function delete_subject(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM subject_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_class(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		// Modified check to include department
		$chk = $this->db->query("SELECT * FROM class_list where curriculum = '$curriculum' and level = '$level' and section = '$section' and department = '$department' and id != '{$id}' ")->num_rows;
		if($chk > 0){
			return 2; // Class already exists in this department
		}
		if(isset($user_ids)){
			$data .= ", user_ids='".implode(',',$user_ids)."' ";
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO class_list set $data");
		}else{
			$save = $this->db->query("UPDATE class_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
	function delete_class(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM class_list where id = $id");
		if($delete){
			return 1;
		}
	}


	function delete_department(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM department_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_academic(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM academic_list where (".str_replace(",",'and',$data).") and id != '{$id}' ")->num_rows;
		if($chk > 0){
			return 2;
		}
		$hasDefault = $this->db->query("SELECT * FROM academic_list where is_default = 1")->num_rows;
		if($hasDefault == 0){
			$data .= " , is_default = 1 ";
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO academic_list set $data");
		}else{
			$save = $this->db->query("UPDATE academic_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_academic(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM academic_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function make_default(){
		extract($_POST);
		$update= $this->db->query("UPDATE academic_list set is_default = 0");
		$update1= $this->db->query("UPDATE academic_list set is_default = 1 where id = $id");
		$qry = $this->db->query("SELECT * FROM academic_list where id = $id")->fetch_array();
		if($update && $update1){
			foreach($qry as $k =>$v){
				if(!is_numeric($k))
					$_SESSION['academic'][$k] = $v;
			}

			return 1;
		}
	}
	function get_criteria(){
        extract($_POST);
        $qry = $this->db->query("SELECT * FROM criteria_list where id = ".$id);
        if($qry->num_rows > 0){
            echo json_encode($qry->fetch_assoc());
        } else {
            echo json_encode(['error' => 'Criteria not found']);
        }
    }
   
    function save_criteria(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id')) && !is_numeric($k)){
                if(empty($data)){
                    $data .= " $k='".addslashes($v)."' ";
                }else{
                    $data .= ", $k='".addslashes($v)."' ";
                }
            }
        }
        $check = $this->db->query("SELECT * FROM criteria_list where criteria ='$criteria' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
        if($check > 0){
            return 2;
        }
        if(empty($id)){
            $save = $this->db->query("INSERT INTO criteria_list set $data");
        }else{
            $save = $this->db->query("UPDATE criteria_list set $data where id = $id");
        }
        if($save){
            return 1;
        }
    }

   
	function delete_criteria(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM criteria_list where id = $id");
		if($delete){
			return 1;
		}
	}



	function save_criteria_order(){
        extract($_POST);
        $data = json_decode($order, true);
        foreach($data as $k => $v){
            $update = $this->db->query("UPDATE criteria_list SET order_by = {$v['order']} WHERE id = {$v['id']}");
        }
        if($update){
            return 1;
        }
    }
	
	function save_question(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		
		if(empty($id)){
			$lastOrder= $this->db->query("SELECT * FROM question_list where academic_id = $academic_id order by abs(order_by) desc limit 1");
			$lastOrder = $lastOrder->num_rows > 0 ? $lastOrder->fetch_array()['order_by'] + 1 : 0;
			$data .= ", order_by='$lastOrder' ";
			$save = $this->db->query("INSERT INTO question_list set $data");
		}else{
			$save = $this->db->query("UPDATE question_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_question(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM question_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_question_order(){
		$input = json_decode(file_get_contents('php://input'), true);
		
		if (!is_array($input) || empty($input)) {
			return json_encode(['status' => 'error', 'message' => 'No question IDs provided']);
		}
		
		$success = true;
		foreach ($input as $item) {
			$order = $this->db->real_escape_string($item['order']);
			$id = $this->db->real_escape_string($item['id']);
			$criteria_id = $this->db->real_escape_string($item['criteria_id']);
			
			$result = $this->db->query("UPDATE question_list SET order_by = $order, criteria_id = $criteria_id WHERE id = $id");
			
			if (!$result) {
				$success = false;
				break;
			}
		}
		
		if ($success) {
			return json_encode(['status' => 'success', 'message' => 'Question order updated successfully']);
		} else {
			return json_encode(['status' => 'error', 'message' => 'Failed to update question order']);
		}
	}


	function save_faculty(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)){
				if($k === 'email' && empty($v)) continue; // Skip empty email
				
				// Sanitize the value to prevent SQL injection
				$sanitized_value = $this->db->real_escape_string($v);
				
				if(empty($data)){
					$data .= " $k='$sanitized_value' ";
				} else {
					$data .= ", $k='$sanitized_value' ";
				}
			}
		}
	
		// Hash the password using password_hash if provided
		if(!empty($password)){
			$hashed_password = password_hash($password, PASSWORD_BCRYPT);
			$data .= ", password='$hashed_password' ";
		}
	
		// Only check for duplicate email if an email is provided
		if(!empty($email)){
			$check = $this->db->query("SELECT * FROM faculty_list WHERE email ='$email' " . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
			if($check > 0){
				return 2;
				exit;
			}
		}
	
		$check = $this->db->query("SELECT * FROM faculty_list WHERE school_id ='$school_id' " . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
		if($check > 0){
			return 3;
			exit;
		}
	
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";
		}
	
		if(empty($id)){
			$save = $this->db->query("INSERT INTO faculty_list SET $data");
		} else {
			$save = $this->db->query("UPDATE faculty_list SET $data WHERE id = $id");
		}
	
		if($save){
			return 1;
		}
	}
	
	function delete_faculty(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM faculty_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_student(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id', 'cpass', 'password', 'department')) && !is_numeric($k)){
				// Only add non-empty email to the data string
				if($k === 'email' && empty($v)) {
					continue; // Skip empty email
				}
				if(empty($data)){
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
	
		// Hash the password using password_hash if provided
		if(!empty($password)){
			$hashed_password = password_hash($password, PASSWORD_BCRYPT);
			$data .= ", password='$hashed_password' ";
		}
	
		// Only check for email duplicates if an email is provided
		if(!empty($email)) {
			$check = $this->db->query("SELECT * FROM student_list WHERE email ='$email' " . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
			if($check > 0){
				return 2;
				exit;
			}
		}
	
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";
		}
	
		if(empty($id)){
			$save = $this->db->query("INSERT INTO student_list SET $data");
		} else {
			$save = $this->db->query("UPDATE student_list SET $data WHERE id = $id");
		}
	
		if($save){
			return 1;
		}
	}
	
	function delete_student(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM student_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_task(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO task_list set $data");
		}else{
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_task(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_progress(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'progress')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!isset($is_complete))
			$data .= ", is_complete=0 ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO task_progress set $data");
		}else{
			$save = $this->db->query("UPDATE task_progress set $data where id = $id");
		}
		if($save){
		if(!isset($is_complete))
			$this->db->query("UPDATE task_list set status = 1 where id = $task_id ");
		else
			$this->db->query("UPDATE task_list set status = 2 where id = $task_id ");
			return 1;
		}
	}
	function delete_progress(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_progress where id = $id");
		if($delete){
			return 1;
		}
	}


 //06-10-2024

  // Function to delete evaluation from both tables
  function delete_evaluation(){
	extract($_POST);
	$evaluation_id = intval($evaluation_id); // Ensure it's an integer

	// Prepare the statements
	$stmt1 = $this->db->prepare("DELETE FROM evaluation_list WHERE evaluation_id = ?");
	$stmt2 = $this->db->prepare("DELETE FROM evaluation_answers WHERE evaluation_id = ?");

	// Bind the parameter and execute
	$stmt1->bind_param("i", $evaluation_id);
	$stmt2->bind_param("i", $evaluation_id);

	$stmt1->execute();
	$stmt2->execute();

	// Check if any rows were affected
	if($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0){
		return "Evaluation deleted successfully.";
	} else {
		return "No evaluation found with the given ID.";
	}
}

 //06-10-2024



 function delete_multiple_evaluations(){
    extract($_POST);
    
    if(!isset($evaluation_ids) || !is_array($evaluation_ids)) {
        return "No evaluations selected.";
    }

    // Convert all IDs to integers for security
    $evaluation_ids = array_map('intval', $evaluation_ids);
    
    // Create placeholders for the IN clause
    $placeholders = str_repeat('?,', count($evaluation_ids) - 1) . '?';
    
    // Prepare the statements
    $stmt1 = $this->db->prepare("DELETE FROM evaluation_list WHERE evaluation_id IN ($placeholders)");
    $stmt2 = $this->db->prepare("DELETE FROM evaluation_answers WHERE evaluation_id IN ($placeholders)");
    
    // Create type string for bind_param
    $types = str_repeat('i', count($evaluation_ids));
    
    // Bind parameters
    $stmt1->bind_param($types, ...$evaluation_ids);
    $stmt2->bind_param($types, ...$evaluation_ids);
    
    // Execute the statements
    $stmt1->execute();
    $stmt2->execute();
    
    // Check if any rows were affected
    if($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0){
        return "Selected evaluations deleted successfully.";
    } else {
        return "No evaluations found with the given IDs.";
    }
}







function save_restriction() {
    extract($_POST);
    
    try {
        $this->db->query("START TRANSACTION");
        
        // Ensure academic_id is valid
        if (!isset($academic_id) || empty($academic_id)) {
            throw new Exception("Invalid academic ID");
        }

        // First, get ALL existing restrictions regardless of pagination
        $all_existing = [];
        $existing_query = $this->db->query("SELECT id, faculty_id, class_id, subject_id 
                                          FROM restriction_list 
                                          WHERE academic_id = " . intval($academic_id));
                                          
        while ($row = $existing_query->fetch_assoc()) {
            $key = $row['faculty_id'] . '-' . $row['class_id'] . '-' . $row['subject_id'];
            $all_existing[$key] = $row['id'];
        }
        
        // Get submitted data from all department tables
        $submitted_data = [];
        
        // Process all submitted records (including those from hidden pages)
        if (isset($faculty_id) && is_array($faculty_id)) {
            foreach ($faculty_id as $k => $fid) {
                if (empty($fid) || empty($class_id[$k]) || empty($subject_id[$k])) {
                    continue;
                }
                
                $key = $fid . '-' . $class_id[$k] . '-' . $subject_id[$k];
                $submitted_data[$key] = [
                    'faculty_id' => intval($fid),
                    'class_id' => intval($class_id[$k]),
                    'subject_id' => intval($subject_id[$k]),
                    'rid' => isset($rid[$k]) ? intval($rid[$k]) : null
                ];
            }
        }
        
        // Process updates and inserts
        foreach ($submitted_data as $key => $data) {
            if (isset($all_existing[$key])) {
                // Update existing record
                $update_sql = "UPDATE restriction_list SET 
                              faculty_id = {$data['faculty_id']},
                              class_id = {$data['class_id']},
                              subject_id = {$data['subject_id']}
                              WHERE id = {$all_existing[$key]}
                              AND academic_id = " . intval($academic_id);
                              
                if (!$this->db->query($update_sql)) {
                    throw new Exception("Failed to update restriction: " . $this->db->error);
                }
            } else {
                // Insert new record
                $insert_sql = "INSERT INTO restriction_list 
                              (academic_id, faculty_id, class_id, subject_id)
                              VALUES 
                              (" . intval($academic_id) . ", 
                               {$data['faculty_id']}, 
                               {$data['class_id']}, 
                               {$data['subject_id']})";
                               
                if (!$this->db->query($insert_sql)) {
                    throw new Exception("Failed to insert restriction: " . $this->db->error);
                }
            }
        }
        
        // Only delete records that are no longer in the submitted data
        $to_delete = array_diff_key($all_existing, $submitted_data);
        
        if (!empty($to_delete)) {
            $delete_ids = implode(",", $to_delete);
            $delete_sql = "DELETE FROM restriction_list 
                          WHERE id IN ($delete_ids) 
                          AND academic_id = " . intval($academic_id);
                          
            if (!$this->db->query($delete_sql)) {
                throw new Exception("Failed to delete restrictions: " . $this->db->error);
            }
        }
        
        $this->db->query("COMMIT");
        return 1;
        
    } catch (Exception $e) {
        $this->db->query("ROLLBACK");
        error_log("Save restriction error: " . $e->getMessage());
        return 2;
    }
}





	function save_evaluation() {
		try {
			if (!isset($_SESSION['login_id']) || !isset($_SESSION['academic']['id'])) {
				throw new Exception('Session data missing');
			}
	
			extract($_POST);
	
			// Begin transaction
			$this->db->begin_transaction();
	
			// Prepare evaluation data
			$eval_data = array(
				'student_id' => $_SESSION['login_id'],
				'academic_id' => $_SESSION['academic']['id'],
				'subject_id' => $subject_id,
				'class_id' => $class_id,
				'restriction_id' => $restriction_id,
				'faculty_id' => $faculty_id
			);
	
			// Add comment if provided
			if (!empty($comment)) {
				$eval_data['comment'] = $this->db->real_escape_string(strip_tags($comment));
			}
	
			// Insert evaluation
			$columns = implode(", ", array_keys($eval_data));
			$values = implode("', '", array_values($eval_data));
			$sql = "INSERT INTO evaluation_list ($columns) VALUES ('$values')";
			
			if (!$this->db->query($sql)) {
				throw new Exception("Failed to save evaluation");
			}
	
			$evaluation_id = $this->db->insert_id;
	
			// Insert answers
			foreach ($qid as $q_id) {
				if (!isset($rate[$q_id])) continue;
				
				$rating = intval($rate[$q_id]);
				if ($rating < 1 || $rating > 5) continue;
				
				$answer_sql = "INSERT INTO evaluation_answers (evaluation_id, question_id, rate) 
							  VALUES ($evaluation_id, $q_id, $rating)";
				
				if (!$this->db->query($answer_sql)) {
					throw new Exception("Failed to save evaluation answers");
				}
			}
	
			// Check for low ratings with the current evaluation data
			$check_data = array(
				'academic_id' => $_SESSION['academic']['id'],
				'faculty_id' => $faculty_id,
				'subject_id' => $subject_id,
				'class_id' => $class_id
			);
			
			$this->check_low_ratings($check_data);
	
			// Commit transaction
			$this->db->commit();
	
			return json_encode(array(
				'status' => 'success',
				'message' => 'Evaluation saved successfully'
			));
	
		} catch (Exception $e) {
			// Rollback transaction on error
			$this->db->rollback();
			return json_encode(array(
				'status' => 'error',
				'message' => $e->getMessage()
			));
		}
	}
	
	
	// Add this function to calculate current average rating
	private function calculate_subject_rating($faculty_id, $subject_id, $class_id) {
		$query = "SELECT AVG(ea.rate) as average_rating
				  FROM evaluation_list e
				  JOIN evaluation_answers ea ON e.evaluation_id = ea.evaluation_id
				  WHERE e.faculty_id = ? 
				  AND e.subject_id = ?
				  AND e.class_id = ?
				  AND e.academic_id = ?";
				  
		$stmt = $this->db->prepare($query);
		$stmt->bind_param("iiii", $faculty_id, $subject_id, $class_id, $_SESSION['academic']['id']);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		
		return $row['average_rating'] ?? 0;
	}
	
	
	
	function get_class(){
		extract($_POST);
		$data = array();
		$query = "SELECT c.id, 
				  concat(c.curriculum,' ',c.level,' - ',c.section) as class,
				  s.id as sid,
				  concat(s.code,' - ',s.subject) as subj,
				  (SELECT AVG(rate) 
				   FROM evaluation_answers ea 
				   JOIN evaluation_list el ON ea.evaluation_id = el.evaluation_id 
				   WHERE el.faculty_id = {$fid} 
				   AND el.subject_id = s.id 
				   AND el.class_id = c.id 
				   AND el.academic_id = {$_SESSION['academic']['id']}) as average_rating
				  FROM restriction_list r 
				  INNER JOIN class_list c on c.id = r.class_id 
				  INNER JOIN subject_list s on s.id = r.subject_id 
				  WHERE r.faculty_id = {$fid} 
				  AND academic_id = {$_SESSION['academic']['id']}";
				  
		$get = $this->db->query($query);
		while($row = $get->fetch_assoc()){
			$data[] = $row;
		}
		return json_encode($data);
	}
	function get_report(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT * FROM evaluation_answers where evaluation_id in (SELECT evaluation_id FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id and class_id = $class_id ) ");
		$answered = $this->db->query("SELECT * FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id and class_id = $class_id");
			$rate = array();
		while($row = $get->fetch_assoc()){
			if(!isset($rate[$row['question_id']][$row['rate']]))
			$rate[$row['question_id']][$row['rate']] = 0;
			$rate[$row['question_id']][$row['rate']] += 1;

		}
		// $data[]= $row;
		$ta = $answered->num_rows;
		$r = array();
		foreach($rate as $qk => $qv){
			foreach($qv as $rk => $rv){
			$r[$qk][$rk] =($rate[$qk][$rk] / $ta) *100;
		}
	}
	$data['tse'] = $ta;
	$data['data'] = $r;
		
		return json_encode($data);

	}


	// In admin_class.php, modify the get_previous_semester_data() function:

		function get_previous_semester_data() {
			// Log the function call for debugging
			error_log("Function called");
			
			// Extract POST data (ensure $faculty_id is available)
			extract($_POST);
		
			// Initialize an empty array for returning data
			$data = array();
		
			// Debug log: Faculty ID check
			if (!isset($faculty_id)) {
				error_log("Error: Faculty ID not provided.");
				return json_encode(['error' => 'Faculty ID is missing.']);
			}
			
			// Fetch the current academic year and semester
			$current_academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();
			
			if (!$current_academic) {
				error_log("Error: Could not fetch current academic data.");
				return json_encode(['error' => 'Current academic data not found.']);
			}
		
			// Query to get previous semesters' data (excluding the current semester)
			$previous_semesters_query = "
				SELECT * FROM academic_list 
				WHERE id != " . intval($current_academic['id']) . " 
				ORDER BY year DESC, semester DESC";
			
			$previous_semesters = $this->db->query($previous_semesters_query);
		
			if (!$previous_semesters) {
				error_log("Error: Could not fetch previous semesters data.");
				return json_encode(['error' => 'Previous semester data not found.']);
			}
		
			// Debug log: Number of previous semesters found
			error_log("Found " . $previous_semesters->num_rows . " previous semesters.");
		
			// Loop through each previous semester
			while ($sem = $previous_semesters->fetch_assoc()) {
				$sem_data = array();
				$sem_data['academic'] = $sem;
		
				// Query to get subjects taught by the faculty in this semester
				$subjects_query = "
					SELECT DISTINCT s.id, s.code, s.subject 
					FROM restriction_list r 
					INNER JOIN subject_list s ON s.id = r.subject_id 
					WHERE r.faculty_id = " . intval($faculty_id) . " 
					AND r.academic_id = " . intval($sem['id']);
				
				// Debug log: Subjects query
				error_log("Subjects query: " . $subjects_query);
				
				$subjects = $this->db->query($subjects_query);
				
				if (!$subjects) {
					error_log("Error: Could not fetch subjects for semester " . $sem['id']);
					continue; // Skip this semester if no subjects found
				}
		
				// Debug log: Number of subjects found
				error_log("Found " . $subjects->num_rows . " subjects for semester " . $sem['id']);
		
				$sem_data['subjects'] = array();
		
				// Loop through each subject
				while ($subj = $subjects->fetch_assoc()) {
					$subj_data = $subj;
		
					// Query to get evaluation summary for this subject
					$eval_summary_query = "
    SELECT 
        COUNT(DISTINCT e.evaluation_id) AS total_evaluations,  /* Corrected from 'e.id' to 'e.evaluation_id' */
        AVG(ea.rate) AS average_rating
    FROM evaluation_list e 
    INNER JOIN evaluation_answers ea ON e.evaluation_id = ea.evaluation_id  /* Make sure to use the correct join field */
    WHERE e.faculty_id = " . intval($faculty_id) . " 
    AND e.subject_id = " . intval($subj['id']) . " 
    AND e.academic_id = " . intval($sem['id']);
					
					// Debug log: Evaluation summary query
					error_log("Evaluation summary query: " . $eval_summary_query);
		
					$eval_summary = $this->db->query($eval_summary_query);
		
					if ($eval_summary) {
						$subj_data['evaluation_summary'] = $eval_summary->fetch_assoc();
					} else {
						$subj_data['evaluation_summary'] = null;
						error_log("Error: Could not fetch evaluation summary for subject " . $subj['id']);
					}
		
					// Add subject data to semester data
					$sem_data['subjects'][] = $subj_data;
				}
		
				// Add semester data to overall data
				$data[] = $sem_data;
			}
		
			// Debug log: Final data being returned
			error_log("Returning data: " . json_encode($data));
		
			// Return the data as JSON
			return json_encode($data);
		}


		
	
	

		function get_all_subject_ratings(){
			extract($_POST);
			
			$query = "SELECT 
						s.code,
						s.subject as name,
						cl.level as class_name,
						AVG(r.rate) as average_rating,
						COUNT(DISTINCT e.id) as total_evaluations
					  FROM evaluation_list e
					  JOIN subject_list s ON e.subject_id = s.id
					  JOIN class_list cl ON e.class_id = cl.id
					  JOIN restriction_list r ON e.id = r.evaluation_id
					  WHERE e.faculty_id = ?
					  AND e.academic_id = ?
					  GROUP BY s.id, cl.id
					  HAVING average_rating < 2";
					  
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("ii", $faculty_id, $_SESSION['academic']['id']);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$ratings = array();
			while($row = $result->fetch_assoc()){
				$ratings[] = array(
					'code' => $row['code'],
					'name' => $row['name'],
					'class_name' => $row['class_name'],
					'average_rating' => number_format($row['average_rating'], 2),
					'total_evaluations' => $row['total_evaluations']
				);
			}
			
			return json_encode($ratings);
		}
		
		function create_notification($faculty_id, $message) {
			$query = "INSERT INTO notifications (faculty_id, message, is_read, created_at) 
					  VALUES (?, ?, 0, NOW())";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("is", $faculty_id, $message);
			return $stmt->execute();
		}
		
		function get_notifications() {
			extract($_POST);
			
			if(!isset($faculty_id)) return json_encode([]);
			
			$query = "SELECT n.*, 
					 DATE_FORMAT(n.created_at, '%M %d, %Y %h:%i %p') as formatted_date,
					 CASE 
						 WHEN n.created_at > NOW() - INTERVAL 24 HOUR THEN 1 
						 ELSE 0 
					 END as is_recent 
					 FROM notifications n 
					 WHERE n.faculty_id = ? 
					 ORDER BY n.is_read ASC, n.created_at DESC 
					 LIMIT 50";
					 
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $faculty_id);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$notifications = array();
			while($row = $result->fetch_assoc()) {
				$notifications[] = $row;
			}
			
			return json_encode($notifications);
		}
		
		function mark_notification_read() {
			extract($_POST);
			
			if(!isset($notification_id)) {
				return json_encode([
					'status' => 'error',
					'message' => 'Notification ID is required'
				]);
			}
			
			$query = "UPDATE notifications SET is_read = 1 
					 WHERE id = ? AND faculty_id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("ii", $notification_id, $_SESSION['login_id']);
			
			if($stmt->execute()) {
				return json_encode([
					'status' => 'success',
					'message' => 'Notification marked as read'
				]);
			} else {
				return json_encode([
					'status' => 'error',
					'message' => 'Failed to mark notification as read'
				]);
			}
		}
		
		function mark_all_notifications_read() {
			extract($_POST);
			
			if(!isset($faculty_id)) return false;
			
			$query = "UPDATE notifications SET is_read = 1 
					 WHERE faculty_id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $faculty_id);
			return $stmt->execute();
		}

		function delete_all_notifications() {
			extract($_POST);
			
			if(!isset($faculty_id)) return false;
			
			$query = "DELETE FROM notifications WHERE faculty_id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $faculty_id);
			return $stmt->execute();
		}
		
		

		// Modified check_low_ratings function
		function check_low_ratings($evaluation_data = null) {
			try {
				// Get current semester's academic ID if not provided
				if (!isset($evaluation_data['academic_id'])) {
					$academic_query = "SELECT id FROM academic_list WHERE status = 1 LIMIT 1";
					$academic_result = $this->db->query($academic_query);
					if (!$academic_result) {
						throw new Exception("Failed to get current academic period");
					}
					$academic_id = $academic_result->fetch_assoc()['id'];
				} else {
					$academic_id = $evaluation_data['academic_id'];
				}
		
				// Query to get faculty evaluations with low ratings
				$query = "SELECT 
							e.faculty_id,
							e.subject_id,
							e.class_id,
							s.code,
							s.subject as name,
							cl.level as class_name,
							ROUND(AVG(a.rate), 2) as average_rating,
							COUNT(DISTINCT e.evaluation_id) as total_evaluations
						FROM evaluation_list e
						JOIN subject_list s ON e.subject_id = s.id
						JOIN class_list cl ON e.class_id = cl.id
						JOIN evaluation_answers a ON e.evaluation_id = a.evaluation_id
						WHERE e.academic_id = ? ";
				
				// If specific evaluation data is provided, focus on that subject/faculty
				$params = array($academic_id);
				$types = "i";
				
				if ($evaluation_data) {
					$query .= " AND e.faculty_id = ? AND e.subject_id = ? AND e.class_id = ?";
					$params[] = $evaluation_data['faculty_id'];
					$params[] = $evaluation_data['subject_id'];
					$params[] = $evaluation_data['class_id'];
					$types .= "iii";
				}
				
				$query .= " GROUP BY e.faculty_id, e.subject_id, e.class_id
						   HAVING average_rating < 2";
		
				$stmt = $this->db->prepare($query);
				$stmt->bind_param($types, ...$params);
				$stmt->execute();
				$result = $stmt->get_result();
		
				while($row = $result->fetch_assoc()) {
					// Check for recent similar notification
					$check_query = "SELECT id FROM notifications 
								  WHERE faculty_id = ? 
								  AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
								  AND message LIKE ?";
					
					$check_stmt = $this->db->prepare($check_query);
					$message_pattern = "%{$row['code']} - {$row['name']} for {$row['class_name']}%";
					$check_stmt->bind_param("is", $row['faculty_id'], $message_pattern);
					$check_stmt->execute();
					$exists = $check_stmt->get_result()->num_rows > 0;
		
					if (!$exists) {
						// Create notification message
						$message = sprintf(
							"Your subject %s - %s for %s has received an average rating of %.2f from %d evaluations.",
							$row['code'],
							$row['name'],
							$row['class_name'],
							$row['average_rating'],
							$row['total_evaluations']
						);
		
						// Insert notification
						$insert_query = "INSERT INTO notifications (faculty_id, message, is_read, created_at) 
									   VALUES (?, ?, 0, NOW())";
						$insert_stmt = $this->db->prepare($insert_query);
						$insert_stmt->bind_param("is", $row['faculty_id'], $message);
						$insert_stmt->execute();
					}
				}
		
				return true;
		
			} catch (Exception $e) {
				error_log("Error in check_low_ratings: " . $e->getMessage());
				return false;
			}
		}
	
	function check_existing_notification($faculty_id, $subject_code, $rating) {
		$query = "SELECT id FROM notifications 
				  WHERE faculty_id = ? 
				  AND message LIKE ? 
				  AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
		$stmt = $this->db->prepare($query);
		$message_pattern = "%{$subject_code}%";
		$stmt->bind_param("is", $faculty_id, $message_pattern);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows > 0;
	}



	


	function check_and_create_low_rating_notifications() {
		// Get all evaluations with low ratings grouped by faculty and subject
		$query = "SELECT 
					e.faculty_id,
					e.subject_id,
					e.class_id,
					s.code,
					s.subject as name,
					cl.level as class_name,
					AVG(ea.rate) as average_rating,
					COUNT(DISTINCT e.evaluation_id) as total_evaluations
				  FROM evaluation_list e
				  JOIN subject_list s ON e.subject_id = s.id
				  JOIN class_list cl ON e.class_id = cl.id
				  JOIN evaluation_answers ea ON e.evaluation_id = ea.evaluation_id
				  WHERE e.academic_id = ?
				  GROUP BY e.faculty_id, e.subject_id, e.class_id
				  HAVING average_rating < 2";
				  
		$stmt = $this->db->prepare($query);
		$stmt->bind_param("i", $_SESSION['academic']['id']);
		$stmt->execute();
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc()) {
			// Check if notification already exists for this rating in last 24 hours
			$exists = $this->check_existing_notification(
				$row['faculty_id'], 
				$row['code'],
				$row['average_rating']
			);
			
			if (!$exists) {
				$message = "Your subject {$row['code']} - {$row['name']} for {$row['class_name']} " .
						  "has received an average rating of " . number_format($row['average_rating'], 2) . 
						  " from {$row['total_evaluations']} evaluations.";
				
				// Create notification for the faculty
				$this->create_notification($row['faculty_id'], $message);
			}
		}
	}
		function save_staff() {
			// Database connection assumed to be available as $this->db
			
			// Extract POST data
			$id = $_POST['id'] ?? '';
			$firstname = $_POST['firstname'] ?? '';
			$lastname = $_POST['lastname'] ?? '';
			$email = $_POST['email'] ?? '';
			$password = $_POST['password'] ?? '';
			
			// Basic validation for required fields
			if(empty($firstname) || empty($lastname) || empty($email)) {
				return 3; // Missing required fields
			}
			
			// Sanitize inputs
			$firstname = $this->db->real_escape_string($firstname);
			$lastname = $this->db->real_escape_string($lastname);
			$email = $this->db->real_escape_string($email);
			
			try {
				if(!empty($id)) {
					// EDIT MODE
					// Check if email exists but exclude current user
					$check = $this->db->query("SELECT id FROM staff WHERE email = '$email' AND id != '$id'");
					if($check->num_rows > 0) {
						return 2; // Email already exists
					}
					
					// Build update query
					$query = "UPDATE staff SET 
						firstname = '$firstname',
						lastname = '$lastname',
						email = '$email'";
					
					// Only update password if provided
					if(!empty($password)) {
						$hashed_password = password_hash($password, PASSWORD_BCRYPT);
						$query .= ", password = '$hashed_password'";
					}
					
					$query .= " WHERE id = '$id'";
					
				} else {
					// NEW STAFF MODE
					// Check if email exists
					$check = $this->db->query("SELECT id FROM staff WHERE email = '$email'");
					if($check->num_rows > 0) {
						return 2; // Email already exists
					}
					
					// Hash password (required for new staff)
					if(empty($password)) {
						return 3; // Password required for new staff
					}
					$hashed_password = password_hash($password, PASSWORD_BCRYPT);
					
					// Insert query
					$query = "INSERT INTO staff SET 
						firstname = '$firstname',
						lastname = '$lastname',
						email = '$email',
						password = '$hashed_password',
						date_created = NOW(),
						is_password_changed = 0";
				}
				
				// Execute query
				$save = $this->db->query($query);
				
				if($save) {
					return 1; // Success
				} else {
					return 0; // Database error
				}
				
			} catch (Exception $e) {
				error_log("Error saving staff: " . $e->getMessage());
				return 0; // Database error
			}
		}
	
// Inside your crud class

		
		
		function update_staff(){
			extract($_POST);
			$data = "";
			foreach($_POST as $k => $v){
				if(!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)){
					if(empty($data)){
						$data .= " $k='$v' ";
					} else {
						$data .= ", $k='$v' ";
					}
				}
			}
		
			if(!empty($password)){
				$hashed_password = password_hash($password, PASSWORD_BCRYPT);
				$data .= ", password='$hashed_password' ";
				$data .= ", is_password_changed = 1";
			}
		
			$check = $this->db->query("SELECT * FROM staff WHERE email ='$email' AND id != {$id}")->num_rows;
			if($check > 0){
				return 2;
				exit;
			}
		
			if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
				$data .= ", avatar = '$fname' ";
			}
		
			$save = $this->db->query("UPDATE staff SET $data WHERE id = $id");
		
			if($save){
				return 1;
			}
		}

		function check_academic_year() {
			$query = "SELECT * FROM academic_list WHERE is_default = 1 LIMIT 1";
			$result = $this->db->query($query);
			
			if($result) {
				$academic = $result->fetch_assoc();
				if($academic) {
					// Check if there's any change from current session
					if (!isset($_SESSION['academic']) || 
						$_SESSION['academic']['year'] != $academic['year'] || 
						$_SESSION['academic']['semester'] != $academic['semester'] || 
						$_SESSION['academic']['status'] != $academic['status']) {
						
						$_SESSION['academic'] = $academic;
						return json_encode($academic);
					}
				}
			}
			return json_encode(false);
		}
		

		function get_evaluation_list(){
			extract($_POST);
			
			$query = "SELECT DISTINCT r.id, r.faculty_id, r.class_id, r.subject_id,
					  f.firstname, f.lastname,
					  CONCAT(f.firstname, ' ', f.lastname) as faculty_name,
					  c.curriculum, c.level, c.section,
					  CONCAT(c.curriculum, ' ', c.level, ' - ', c.section) as class_name,
					  s.code as subject_code, s.subject as subject_name, s.department
					  FROM restriction_list r
					  LEFT JOIN faculty_list f ON r.faculty_id = f.id
					  LEFT JOIN class_list c ON r.class_id = c.id
					  LEFT JOIN subject_list s ON r.subject_id = s.id
					  WHERE r.academic_id = ?
					  ORDER BY faculty_name, class_name, subject_code";
					  
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $academic_id);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$data = array();
			while($row = $result->fetch_assoc()) {
				$data[] = $row;
			}
			
			return json_encode($data);
		}
		function send_evaluation_to_all_students() {
			extract($_POST);
		
			if (!isset($academic_id)) {
				return 2; // Invalid academic year
			}
		
			try {
				$this->db->query("START TRANSACTION");
		
				// Get active faculty assignments with student and class information
				$assignment_query = "
					SELECT DISTINCT
						fa.faculty_id,
						fa.class_id,
						fa.subject_id,
						fa.academic_year_id,
						fa.semester,
						c.department as class_department
					FROM faculty_assignments fa
					INNER JOIN class_list c ON fa.class_id = c.id
					INNER JOIN student_list s ON s.class_id = fa.class_id
					WHERE fa.is_active = 1 
					AND fa.academic_year_id = ?
					AND c.department != ''";
		
				$stmt = $this->db->prepare($assignment_query);
				$stmt->bind_param("i", $academic_id);
				$stmt->execute();
				$assignments = $stmt->get_result();
		
				// Check if we have any assignments to process
				if (!$assignments->num_rows) {
					$this->db->query("ROLLBACK");
					return 3; // No assignments to process
				}
		
				// Clear existing restrictions for this academic year
				$clear_stmt = $this->db->prepare("DELETE FROM restriction_list WHERE academic_id = ?");
				$clear_stmt->bind_param("i", $academic_id);
				$clear_stmt->execute();
		
				// Prepare the insert statement for restrictions
				$insert_stmt = $this->db->prepare("
					INSERT INTO restriction_list 
					(academic_id, faculty_id, class_id, subject_id) 
					VALUES (?, ?, ?, ?)
				");
		
				$restrictions_added = false;
		
				// Process each faculty assignment
				while ($assignment = $assignments->fetch_assoc()) {
					// Verify the subject department matches the class department
					$subject_query = "SELECT department FROM subject_list WHERE id = ? AND department = ?";
					$subject_stmt = $this->db->prepare($subject_query);
					$subject_stmt->bind_param("is", $assignment['subject_id'], $assignment['class_department']);
					$subject_stmt->execute();
					$subject_result = $subject_stmt->get_result();
		
					if ($subject_result->num_rows > 0) {
						// Check if this restriction already exists
						$check_query = "SELECT id FROM restriction_list 
									  WHERE academic_id = ? 
									  AND faculty_id = ? 
									  AND class_id = ? 
									  AND subject_id = ?";
						
						$check_stmt = $this->db->prepare($check_query);
						$check_stmt->bind_param("iiii",
							$academic_id,
							$assignment['faculty_id'],
							$assignment['class_id'],
							$assignment['subject_id']
						);
						$check_stmt->execute();
						$result = $check_stmt->get_result();
		
						// Only insert if this combination doesn't exist
						if ($result->num_rows === 0) {
							$insert_stmt->bind_param("iiii",
								$academic_id,
								$assignment['faculty_id'],
								$assignment['class_id'],
								$assignment['subject_id']
							);
		
							if (!$insert_stmt->execute()) {
								throw new Exception("Failed to insert restriction");
							}
							$restrictions_added = true;
						}
					}
				}
		
				if (!$restrictions_added) {
					$this->db->query("ROLLBACK");
					return 4; // No valid combinations found
				}
		
				$this->db->query("COMMIT");
				return 1; // Success
		
			} catch (Exception $e) {
				$this->db->query("ROLLBACK");
				error_log("Send to all students error: " . $e->getMessage());
				return 2; // Error
			}
		}

		
		function load_comments() {
			try {
				// Validate and sanitize input parameters
				$faculty_id = isset($_POST['faculty_id']) ? intval($_POST['faculty_id']) : 0;
				$subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : 0;
				$class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
		
				if (!$faculty_id || !$subject_id || !$class_id) {
					throw new Exception("Invalid input parameters");
				}
		
				// Get current academic year and semester from session
				$academic_id = isset($_SESSION['academic']['id']) ? $_SESSION['academic']['id'] : 0;
				
				if (!$academic_id) {
					throw new Exception("Invalid academic session");
				}
		
				// Prepare the SQL query - removed student name from selection
				$sql = "SELECT 
							e.evaluation_id,
							e.comment,
							e.date_taken
						FROM 
							evaluation_list e
						WHERE 
							e.faculty_id = ? 
							AND e.subject_id = ? 
							AND e.class_id = ?
							AND e.academic_id = ?
							AND e.comment IS NOT NULL 
							AND TRIM(e.comment) != ''
						ORDER BY 
							e.date_taken DESC";
		
				// Prepare and execute the statement
				$stmt = $this->db->prepare($sql);
				if (!$stmt) {
					error_log("Failed to prepare statement: " . $this->db->error);
					throw new Exception("Database error");
				}
		
				$stmt->bind_param("iiii", $faculty_id, $subject_id, $class_id, $academic_id);
				
				if (!$stmt->execute()) {
					error_log("Failed to execute query: " . $stmt->error);
					throw new Exception("Database error");
				}
		
				$result = $stmt->get_result();
				$comments = array();
		
				while ($row = $result->fetch_assoc()) {
					// Format the date
					$date = new DateTime($row['date_taken']);
					$row['date_taken'] = $date->format('M d, Y h:i A');
		
					// Sanitize the comment text
					$row['comment'] = htmlspecialchars($row['comment']);
		
					$comments[] = $row;
				}
		
				$stmt->close();
				return json_encode($comments);
		
			} catch (Exception $e) {
				error_log("Error in load_comments: " . $e->getMessage());
				return json_encode([
					'error' => true,
					'message' => $e->getMessage()
				]);
			}
		}

		function get_student_surveys() {
			try {
				// Check for required session variables
				if (!isset($_SESSION['login_id']) || !isset($_SESSION['academic']['id']) || !isset($_SESSION['login_class_id'])) {
					return json_encode([
						'error' => 'Missing session data',
						'debug' => [
							'login_id' => isset($_SESSION['login_id']),
							'academic_id' => isset($_SESSION['academic']['id']),
							'class_id' => isset($_SESSION['login_class_id'])
						]
					]);
				}
		
				$student_id = intval($_SESSION['login_id']);
				$academic_id = intval($_SESSION['academic']['id']);
				$class_id = intval($_SESSION['login_class_id']);
		
				// Query for both total and completed surveys
				$query = "SELECT 
					(SELECT COUNT(*) 
					 FROM restriction_list 
					 WHERE academic_id = ? AND class_id = ?) as total_surveys,
					
					COUNT(DISTINCT e.id) as completed_surveys,
					
					GROUP_CONCAT(
						DISTINCT CONCAT_WS('|',
							CONCAT(f.firstname, ' ', f.lastname),
							s.code,
							s.subject,
							e.date_taken
						) SEPARATOR '||'
					) as details
					FROM restriction_list r
					LEFT JOIN evaluation_list e ON r.id = e.restriction_id AND e.student_id = ?
					LEFT JOIN faculty_list f ON f.id = r.faculty_id
					LEFT JOIN subject_list s ON s.id = r.subject_id
					WHERE r.academic_id = ? AND r.class_id = ?";
				
				$stmt = $this->conn->prepare($query);
				if (!$stmt) {
					throw new Exception("Error preparing query: " . $this->conn->error);
				}
				
				$stmt->bind_param("iiiii", $academic_id, $class_id, $student_id, $academic_id, $class_id);
				$stmt->execute();
				$result = $stmt->get_result();
				
				if ($row = $result->fetch_assoc()) {
					$response = [
						'total_surveys' => intval($row['total_surveys']),
						'completed_surveys' => intval($row['completed_surveys']),
						'completed_list' => []
					];
					
					if (!empty($row['details'])) {
						$details = explode('||', $row['details']);
						foreach ($details as $detail) {
							$parts = explode('|', $detail);
							if (count($parts) === 4) {
								$response['completed_list'][] = [
									'faculty' => htmlspecialchars($parts[0]),
									'code' => htmlspecialchars($parts[1]),
									'subject' => htmlspecialchars($parts[2]),
									'date' => date('M d, Y h:i A', strtotime($parts[3]))
								];
							}
						}
					}
					
					return json_encode($response);
				}
				
				return json_encode([
					'total_surveys' => 0,
					'completed_surveys' => 0,
					'completed_list' => []
				]);
				
			} catch (Exception $e) {
				return json_encode([
					'error' => 'Database error: ' . $e->getMessage()
				]);
			}
		}


		public function get_department_list() {
			$query = "SELECT DISTINCT department FROM subject_list ORDER BY department ASC";
			$result = $this->db->query($query);
		
			$data = array();
			while($row = $result->fetch_assoc()) {
				$data[] = $row['department'];
			}
		
			return json_encode($data);
		}
		


		public function check_assignment() {
			if (!isset($_POST['faculty_id']) || !isset($_POST['class_id']) || !isset($_POST['subject_id'])) {
				return json_encode(['status' => 'error', 'message' => 'Missing required fields']);
			}
		
			try {
				// Sanitize and validate inputs
				$faculty_id = filter_var($_POST['faculty_id'], FILTER_VALIDATE_INT);
				$class_id = filter_var($_POST['class_id'], FILTER_VALIDATE_INT);
				$subject_id = filter_var($_POST['subject_id'], FILTER_VALIDATE_INT);
		
				if ($faculty_id === false || $class_id === false || $subject_id === false) {
					return json_encode(['status' => 'error', 'message' => 'Invalid input values']);
				}
		
				// Get current academic year
				$ay_query = "SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1";
				$ay_result = $this->db->query($ay_query);
				if ($ay_result->num_rows === 0) {
					return json_encode(['status' => 'error', 'message' => 'No active academic year found']);
				}
				$academic_year_id = $ay_result->fetch_assoc()['id'];
		
				// Check if faculty exists
				$faculty_check = $this->db->prepare("SELECT id FROM faculty_list WHERE id = ?");
				$faculty_check->bind_param("i", $faculty_id);
				$faculty_check->execute();
				if ($faculty_check->get_result()->num_rows === 0) {
					return json_encode(['status' => 'error', 'message' => 'Faculty not found']);
				}
		
				// Check if class exists
				$class_check = $this->db->prepare("SELECT id FROM class_list WHERE id = ?");
				$class_check->bind_param("i", $class_id);
				$class_check->execute();
				if ($class_check->get_result()->num_rows === 0) {
					return json_encode(['status' => 'error', 'message' => 'Class not found']);
				}
		
				// Check if subject exists
				$subject_check = $this->db->prepare("SELECT id FROM subject_list WHERE id = ?");
				$subject_check->bind_param("i", $subject_id);
				$subject_check->execute();
				if ($subject_check->get_result()->num_rows === 0) {
					return json_encode(['status' => 'error', 'message' => 'Subject not found']);
				}
		
				// Check if assignment already exists for current semester
				$stmt = $this->db->prepare("SELECT id FROM faculty_assignments 
										   WHERE faculty_id = ? AND class_id = ? AND subject_id = ? 
										   AND academic_year_id = ? AND is_active = 1");
				$stmt->bind_param("iiii", $faculty_id, $class_id, $subject_id, $academic_year_id);
				$stmt->execute();
				$result = $stmt->get_result();
		
				if ($result->num_rows > 0) {
					return json_encode(['status' => 'exists', 'message' => 'Assignment already exists']);
				}
		
				return json_encode(['status' => 'success', 'message' => 'Assignment available']);
		
			} catch (Exception $e) {
				error_log("Error in check_assignment: " . $e->getMessage());
				return json_encode(['status' => 'error', 'message' => 'Database error occurred']);
			}
		}
	
		public function save_assignment() {
			if (!isset($_POST['faculty_id']) || !isset($_POST['class_id']) || !isset($_POST['subject_id'])) {
				return json_encode(['status' => 'error', 'message' => 'Missing required fields']);
			}
		
			try {
				// Sanitize and validate inputs
				$faculty_id = filter_var($_POST['faculty_id'], FILTER_VALIDATE_INT);
				$class_id = filter_var($_POST['class_id'], FILTER_VALIDATE_INT);
				$subject_id = filter_var($_POST['subject_id'], FILTER_VALIDATE_INT);
		
				if ($faculty_id === false || $class_id === false || $subject_id === false) {
					return json_encode(['status' => 'error', 'message' => 'Invalid input values']);
				}
		
				// Get current academic year and semester from academic_list
				$academic_query = "SELECT id, semester FROM academic_list 
								  WHERE is_default = 1 AND status = 1 
								  LIMIT 1";
				$academic_result = $this->db->query($academic_query);
				
				if ($academic_result->num_rows === 0) {
					return json_encode(['status' => 'error', 
									  'message' => 'No active academic year and semester found']);
				}
				
				$academic_data = $academic_result->fetch_assoc();
				$academic_year_id = $academic_data['id'];
				$current_semester = $academic_data['semester'];
		
				// Check if assignment already exists
				$stmt = $this->db->prepare("SELECT id FROM faculty_assignments 
										   WHERE faculty_id = ? AND class_id = ? AND subject_id = ? 
										   AND academic_year_id = ? AND semester = ? AND is_active = 1");
				$stmt->bind_param("iiiii", 
								 $faculty_id, $class_id, $subject_id, 
								 $academic_year_id, $current_semester);
				$stmt->execute();
				$result = $stmt->get_result();
				
				if ($result->num_rows > 0) {
					return json_encode(['status' => 'exists', 
									  'message' => 'Assignment already exists for this semester']);
				}
		
				// Insert new assignment
				$user_id = $_SESSION['admin_id'] ?? 1; // Get the current admin user ID from session
				$insert_stmt = $this->db->prepare(
					"INSERT INTO faculty_assignments (
						faculty_id, class_id, subject_id, academic_year_id, 
						semester, is_active, assigned_by, assigned_at
					) VALUES (?, ?, ?, ?, ?, 1, ?, NOW())"
				);
				$insert_stmt->bind_param("iiiiii", 
					$faculty_id, $class_id, $subject_id, 
					$academic_year_id, $current_semester, $user_id
				);
				
				if ($insert_stmt->execute()) {
					return json_encode(['status' => 'success', 
									  'message' => 'Assignment saved successfully']);
				} else {
					return json_encode(['status' => 'error', 
									  'message' => 'Failed to save assignment']);
				}
		
			} catch (Exception $e) {
				error_log("Error in save_assignment: " . $e->getMessage());
				return json_encode(['status' => 'error', 
								  'message' => 'Database error occurred']);
			}
		}
	
		public function load_assignments() {
			$query = "SELECT r.id, 
							 CONCAT(f.firstname, ' ', f.lastname) as faculty_name,
							 CONCAT(c.level, ' - ', c.section, ' (', c.curriculum, ')') as class_name,
							 CONCAT(s.code, ' - ', s.subject) as subject_name
					  FROM restriction_list r
					  LEFT JOIN faculty_list f ON f.id = r.faculty_id
					  LEFT JOIN class_list c ON c.id = r.class_id
					  LEFT JOIN subject_list s ON s.id = r.subject_id
					  ORDER BY faculty_name ASC";
			
			$result = $this->db->query($query);
			$output = "";
			
			while($row = $result->fetch_assoc()) {
				$output .= "
					<tr>
						<td>{$row['faculty_name']}</td>
						<td>{$row['class_name']}</td>
						<td>{$row['subject_name']}</td>
						<td>
							<button class='btn btn-danger btn-sm delete-assignment' data-id='{$row['id']}'>
								Delete
							</button>
						</td>
					</tr>
				";
			}
			
			return $output;
		}
	
		public function delete_assignment() {
			extract($_POST);
			$id = intval($id);
			
			$stmt = $this->db->prepare("DELETE FROM faculty_assignments WHERE id = ?");
			$stmt->bind_param("i", $id);
			
			if($stmt->execute()) {
				return 1;
			}
			return 0;
		}


		

		public function load_assignments_grid() {
			try {
				// Get current academic year
				$ay_query = "SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1";
				$ay_result = $this->db->query($ay_query);
				if ($ay_result->num_rows === 0) {
					throw new Exception('No active academic year found');
				}
				$academic_year_id = $ay_result->fetch_assoc()['id'];
				
				// Base query to get class-subject combinations
				$query = "SELECT DISTINCT 
					c.id as class_id, 
					s.id as subject_id,
					c.department as class_department,
					s.department as subject_department,
					CONCAT(c.level, ' - ', c.section) as class_name,
					CONCAT(s.code, ' - ', s.subject) as subject_name,
					fa.id as assignment_id,
					f.id as faculty_id,
					CONCAT(f.firstname, ' ', f.lastname) as faculty_name
				FROM class_list c 
				INNER JOIN subject_list s ON c.department = s.department
				LEFT JOIN faculty_assignments fa ON fa.class_id = c.id 
					AND fa.subject_id = s.id 
					AND fa.academic_year_id = ? 
					AND fa.is_active = 1
				LEFT JOIN faculty_list f ON f.id = fa.faculty_id
				WHERE 1=1";
				
				$params = [$academic_year_id];
				$types = "i";
				
				// Add filters
				if (!empty($_POST['department'])) {
					$query .= " AND c.department = ?";
					$params[] = $_POST['department'];
					$types .= "s";
				}
				
				if (!empty($_POST['class_id'])) {
					$query .= " AND c.id = ?";
					$params[] = $_POST['class_id'];
					$types .= "i";
				}
				
				if (!empty($_POST['subject_id'])) {
					$query .= " AND s.id = ?";
					$params[] = $_POST['subject_id'];
					$types .= "i";
				}
				
				$query .= " ORDER BY c.department ASC, c.level ASC, s.code ASC";
				
				// Prepare and execute the query
				$stmt = $this->db->prepare($query);
				$stmt->bind_param($types, ...$params);
				$stmt->execute();
				$result = $stmt->get_result();
				
				// Generate output HTML
				$output = "";
				$current_department = "";
				
				while ($row = $result->fetch_assoc()) {
					// Add department header if it's a new department
					if ($current_department !== $row['class_department']) {
						if ($current_department !== "") {
							$output .= "</div></div>"; // Close previous department section
						}
						$current_department = $row['class_department'];
						$output .= "<div class='department-assignments mb-4'>";
						$output .= "<h4 class='department-assignments-header'>{$current_department}</h4>";
						$output .= "<div class='assignment-grid'>";
					}
					
					// Generate assignment card
					$output .= "<div class='assignment-card'>";
					$output .= "<div class='card-body p-3'>";
					$output .= "<h5 class='card-title'>{$row['class_name']}</h5>";
					$output .= "<h6 class='card-subtitle mb-2 text-muted'>{$row['subject_name']}</h6>";
					
					// Assignment dropzone
					$output .= "<div class='assignment-dropzone mt-3' 
									data-class='{$row['class_id']}' 
									data-subject='{$row['subject_id']}'>";
					
					// Show assigned faculty if exists
					if (!empty($row['faculty_id'])) {
						$output .= "<div class='assigned-faculty'>";
						$output .= "<i class='fas fa-user-check text-success me-2'></i>";
						$output .= "<span>{$row['faculty_name']}</span>";
						$output .= "<button class='btn btn-sm btn-danger float-end delete-assignment' 
									data-id='{$row['assignment_id']}'>";
						$output .= "<i class='fas fa-times'></i></button>";
						$output .= "</div>";
					} else {
						$output .= "<div class='text-center text-muted'>";
						$output .= "<i class='fas fa-plus-circle'></i> Drop faculty here";
						$output .= "</div>";
					}
					
					$output .= "</div>"; // Close dropzone
					$output .= "</div>"; // Close card-body
					$output .= "</div>"; // Close assignment-card
				}
				
				// Close last department section if exists
				if ($current_department !== "") {
					$output .= "</div></div>";
				}
				
				// Return output or empty state
				if (empty($output)) {
					$department = $_POST['department'] ?? 'No Department Selected';
					$output = "<div class='empty-department'>
								<i class='fas fa-info-circle mb-2'></i><br>
								No assignments found for {$department}
							  </div>";
				}
				
				echo $output;
				
			} catch (Exception $e) {
				error_log("Error in load_assignments_grid: " . $e->getMessage());
				echo "<div class='alert alert-danger'>
						<i class='fas fa-exclamation-triangle me-2'></i>
						Error loading assignments. Please try again.
					  </div>";
			}
		}

		public function get_departments() {
			try {
				$query = "SELECT DISTINCT department FROM faculty_list WHERE department IS NOT NULL ORDER BY department ASC";
				$result = $this->db->query($query);
				$departments = [];
				
				while($row = $result->fetch_assoc()) {
					$departments[] = $row['department'];
				}
				
				return json_encode(['status' => 'success', 'departments' => $departments]);
			} catch (Exception $e) {
				return json_encode(['status' => 'error', 'message' => 'Error fetching departments']);
			}
		}
		private function get_empty_department_html($department) {
            return "
                <div class='empty-department'>
                    <p>No assignments for this department</p>
                    <div class='assignment-dropzone' 
                        data-department='{$department}'
                        style='min-height: 100px; border: 2px dashed #dee2e6; border-radius: 4px; margin-top: 10px;'>
                        <div class='text-center py-3'>
                            <i class='fas fa-plus-circle mb-2'></i><br>
                            Drag faculty here to assign
                        </div>
                    </div>
                </div>";
        }









		//AssignSubjectClassess

		public function check_assignment_faculty() {
			extract($_POST);
			
			if(!isset($faculty_id) || !isset($class_id) || !isset($subject_id)) {
				return json_encode(['status' => 'error', 'message' => 'Missing required fields']);
			}
		
			// Get current academic year and semester
			$academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1");
			$academic_year = $academic_year_query->fetch_assoc();
			$academic_year_id = $academic_year['id'];
			
			// Assuming semester is stored in a session or configuration
			$semester = isset($_SESSION['semester']) ? $_SESSION['semester'] : 1;
		
			// Check if the class-subject combination already has an assigned faculty
			$stmt = $this->db->prepare("SELECT fa.*, CONCAT(f.firstname, ' ', f.lastname) as faculty_name 
									   FROM faculty_assignments fa 
									   LEFT JOIN faculty_list f ON fa.faculty_id = f.id 
									   WHERE fa.class_id = ? 
									   AND fa.subject_id = ? 
									   AND fa.academic_year_id = ? 
									   AND fa.semester = ? 
									   AND fa.is_active = 1");
			
			$stmt->bind_param("iiii", $class_id, $subject_id, $academic_year_id, $semester);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if($result->num_rows > 0) {
				$existing = $result->fetch_assoc();
				return json_encode([
					'status' => 'error', 
					'message' => "This class and subject is already assigned to {$existing['faculty_name']}"
				]);
			}
		
			// Check if faculty is already assigned to this class and subject
			$stmt = $this->db->prepare("SELECT id FROM faculty_assignments 
									   WHERE faculty_id = ? 
									   AND class_id = ? 
									   AND subject_id = ? 
									   AND academic_year_id = ? 
									   AND semester = ? 
									   AND is_active = 1");
			
			$stmt->bind_param("iiiii", $faculty_id, $class_id, $subject_id, $academic_year_id, $semester);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if($result->num_rows > 0) {
				return json_encode([
					'status' => 'error', 
					'message' => 'You have already assigned this faculty to this class and subject'
				]);
			}
			
			return json_encode(['status' => 'success']);
		}
		
		public function save_assignment_faculty() {
			extract($_POST);
			
			if(!isset($faculty_id) || !isset($class_id) || !isset($subject_id)) {
				return 0;
			}
		
			// First check for existing assignment and duplicates
			$check = json_decode($this->check_assignment_faculty(), true);
			if($check['status'] === 'error') {
				return 0;
			}
		
			// Get current academic year
			$academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1");
			$academic_year = $academic_year_query->fetch_assoc();
			$academic_year_id = $academic_year['id'];
			
			// Get current semester (assuming it's stored in session)
			$semester = isset($_SESSION['semester']) ? $_SESSION['semester'] : 1;
			
			// Get current user ID (assuming it's stored in session)
			$assigned_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
		
			try {
				// Prepare the insert statement
				$stmt = $this->db->prepare("INSERT INTO faculty_assignments 
										   (faculty_id, class_id, subject_id, academic_year_id, semester, assigned_by) 
										   VALUES (?, ?, ?, ?, ?, ?)");
				
				$stmt->bind_param("iiiiii", 
					$faculty_id, 
					$class_id, 
					$subject_id, 
					$academic_year_id, 
					$semester, 
					$assigned_by
				);
				
				$save = $stmt->execute();
				
				if($save) {
					return 1;
				}
			} catch(Exception $e) {
				error_log("Error saving faculty assignment: " . $e->getMessage());
				return 0;
			}
			
			return 0;
		}
	
		public function load_assignments_faculty() {
			// Get current academic year
			$academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1");
			$academic_year = $academic_year_query->fetch_assoc();
			$academic_year_id = $academic_year['id'];
			
			// Get current semester
			$semester = isset($_SESSION['semester']) ? $_SESSION['semester'] : 1;
		
			$query = "SELECT fa.*, 
					 CONCAT(f.firstname, ' ', f.lastname) as faculty_name,
					 CONCAT(c.level, ' - ', c.section) as class_name,
					 CONCAT(s.code, ' - ', s.subject) as subject_name
					 FROM faculty_assignments fa
					 LEFT JOIN faculty_list f ON fa.faculty_id = f.id
					 LEFT JOIN class_list c ON fa.class_id = c.id
					 LEFT JOIN subject_list s ON fa.subject_id = s.id
					 WHERE fa.academic_year_id = ? AND fa.semester = ? AND fa.is_active = 1
					 ORDER BY faculty_name ASC";
			
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("ii", $academic_year_id, $semester);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$output = "";
			while($row = $result->fetch_assoc()) {
				$output .= "
					<tr>
						<td>{$row['faculty_name']}</td>
						<td>{$row['class_name']}</td>
						<td>{$row['subject_name']}</td>
						<td>
							<button class='btn btn-danger btn-sm delete-assignment' data-id='{$row['id']}'>
								<i class='fas fa-trash'></i>
							</button>
						</td>
					</tr>
				";
			}
			
			return $output;
		}
	
		public function delete_assignment_faculty() {
			extract($_POST);
			
			if(!isset($id)) {
				return 0;
			}
			
			$stmt = $this->db->prepare("UPDATE faculty_assignments SET is_active = 0 WHERE id = ?");
			$stmt->bind_param("i", $id);
			
			$delete = $stmt->execute();
			
			if($delete) {
				return 1;
			}
			
			return 0;
		}

		public function get_assignment_faculty() {
			extract($_POST);
			
			if(!isset($id)) {
				return json_encode(['status' => 'error', 'message' => 'Missing assignment ID']);
			}
			
			$stmt = $this->db->prepare("SELECT * FROM faculty_assignments WHERE id = ? AND is_active = 1");
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				return json_encode(['status' => 'success', 'data' => $row]);
			}
			
			return json_encode(['status' => 'error', 'message' => 'Assignment not found']);
		}
		
		public function update_assignment_faculty() {
			extract($_POST);
			
			if(!isset($id) || !isset($faculty_id) || !isset($class_id) || !isset($subject_id)) {
				return json_encode(['status' => 'error', 'message' => 'Missing required fields']);
			}
		
			// Get current academic year
			$academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1");
			$academic_year = $academic_year_query->fetch_assoc();
			$academic_year_id = $academic_year['id'];
			
			// Get current semester
			$semester = isset($_SESSION['semester']) ? $_SESSION['semester'] : 1;
		
			// Check if the new assignment would create a duplicate
			$stmt = $this->db->prepare("SELECT id FROM faculty_assignments 
									   WHERE faculty_id = ? AND class_id = ? AND subject_id = ? 
									   AND academic_year_id = ? AND semester = ? AND is_active = 1 
									   AND id != ?");
			
			$stmt->bind_param("iiiiii", $faculty_id, $class_id, $subject_id, $academic_year_id, $semester, $id);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if($result->num_rows > 0) {
				return json_encode(['status' => 'error', 'message' => 'This assignment already exists']);
			}
		
			// Update the assignment
			$stmt = $this->db->prepare("UPDATE faculty_assignments 
									   SET faculty_id = ?, class_id = ?, subject_id = ? 
									   WHERE id = ? AND is_active = 1");
			
			$stmt->bind_param("iiii", $faculty_id, $class_id, $subject_id, $id);
			$save = $stmt->execute();
			
			if($save) {
				return json_encode(['status' => 'success']);
			}
			
			return json_encode(['status' => 'error', 'message' => 'Failed to update assignment']);
		}



		//November 12, 2024

		public function get_departments_faculty() {
			$query = "SELECT * FROM department_list ORDER BY name ASC";
			$result = $this->db->query($query);
			$departments = array();
			
			while($row = $result->fetch_assoc()) {
				$departments[] = $row;
			}
			
			return json_encode($departments);
		}
		
		public function get_faculty_by_department() {
			extract($_POST);
			
			if(!isset($department)) {
				return "";
			}
			
			$query = "SELECT id, CONCAT(firstname, ' ', lastname) as name 
					  FROM faculty_list 
					  WHERE department = ? 
					  ORDER BY lastname ASC";
			
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("s", $department);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$output = "";
			while($row = $result->fetch_assoc()) {
				$output .= "<option value='{$row['id']}'>{$row['name']}</option>";
			}
			
			return $output;
		}
		
		public function get_classes_by_department() {
			extract($_POST);
			
			if(!isset($department)) {
				return "";
			}
			
			$query = "SELECT id, CONCAT(level, ' - ', section, ' (', curriculum, ')') as class_name 
					  FROM class_list 
					  WHERE department = ? 
					  ORDER BY level ASC";
			
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("s", $department);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$output = "";
			while($row = $result->fetch_assoc()) {
				$output .= "<option value='{$row['id']}'>{$row['class_name']}</option>";
			}
			
			return $output;
		}
		
		public function get_subjects_by_department() {
			extract($_POST);
			
			if(!isset($department)) {
				return "";
			}
			
			$query = "SELECT id, CONCAT(code, ' - ', subject) as subject_name 
					  FROM subject_list 
					  WHERE department = ? 
					  ORDER BY subject ASC";
			
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("s", $department);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$output = "";
			while($row = $result->fetch_assoc()) {
				$output .= "<option value='{$row['id']}'>{$row['subject_name']}</option>";
			}
			
			return $output;
		}
		
		// Modified load_assignments_faculty function to handle department filtering
		public function load_assignments_faculties() {
			extract($_POST);
			
			// Get current academic year
			$academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1");
			$academic_year = $academic_year_query->fetch_assoc();
			$academic_year_id = $academic_year['id'];
			
			// Get current semester
			$semester = isset($_SESSION['semester']) ? $_SESSION['semester'] : 1;
		
			$query = "SELECT fa.*, 
					 CONCAT(f.firstname, ' ', f.lastname) as faculty_name,
					 CONCAT(c.level, ' - ', c.section, ' (', c.curriculum, ')') as class_name,
					 CONCAT(s.code, ' - ', s.subject) as subject_name,
					 f.department
					 FROM faculty_assignments fa
					 LEFT JOIN faculty_list f ON fa.faculty_id = f.id
					 LEFT JOIN class_list c ON fa.class_id = c.id
					 LEFT JOIN subject_list s ON fa.subject_id = s.id
					 WHERE fa.academic_year_id = ? AND fa.semester = ? AND fa.is_active = 1";
		
			if(isset($department) && $department !== 'all') {
				$query .= " AND f.department = ?";
			}
		
			$query .= " ORDER BY faculty_name ASC";
		
			$stmt = $this->db->prepare($query);
		
			if(isset($department) && $department !== 'all') {
				$stmt->bind_param("iis", $academic_year_id, $semester, $department);
			} else {
				$stmt->bind_param("ii", $academic_year_id, $semester);
			}
		
			$stmt->execute();
			$result = $stmt->get_result();
		
			$output = "";
			while($row = $result->fetch_assoc()) {
				$output .= "
					<tr>
						<td>{$row['faculty_name']}</td>
						<td>{$row['class_name']}</td>
						<td>{$row['subject_name']}</td>
						<td>{$row['department']}</td>
						<td>
							<button class='btn btn-danger btn-sm delete-assignment' data-id='{$row['id']}'>
								<i class='fas fa-trash'></i>
							</button>
						</td>
					</tr>
				";
			}
		
			return $output;
		}



		//November 15


	}
    
	

		
	
	