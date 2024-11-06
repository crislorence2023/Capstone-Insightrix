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



    function update_password($user_id, $password, $table) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query("UPDATE $table SET password = '$hashed_password' WHERE id = $user_id");
    }

    

    function forgot_password() {
        extract($_POST);
        $type = array("","users","faculty_list","student_list");
        $table = $type[$login];
        
        $qry = $this->db->query("SELECT * FROM $table WHERE email = '$email'");
        if($qry->num_rows > 0) {
            $row = $qry->fetch_assoc();
            $verification_code = substr(md5(uniqid(mt_rand(), true)), 0, 6);
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $update = $this->db->query("UPDATE $table SET verification_code = '$verification_code', verification_code_expiry = '$expiry' WHERE id = ".$row['id']);
            
            if($update) {
                // Send email with verification code
                // You'll need to implement an email sending function here
                return 1; // Email sent successfully
            } else {
                return 3; // Error updating verification code
            }
        } else {
            return 2; // Email not found
        }
    }
	
	private function validatePassword($password) {
		return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
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
		extract($_POST);
		$data = "";
		$type = array("","users","faculty_list","student_list");
	foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
				
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM {$type[$_SESSION['login_type']]} where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(!empty($password))
			$data .= " ,password=md5('$password') ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO {$type[$_SESSION['login_type']]} set $data");
		}else{
			echo "UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id";
			$save = $this->db->query("UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
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
	
		$check = $this->db->query("SELECT * FROM faculty_list WHERE email ='$email' " . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
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
	
		$check = $this->db->query("SELECT * FROM student_list WHERE email ='$email' " . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
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











	function save_restriction(){
		extract($_POST);
		$filtered = implode(",",array_filter($rid));
		if(!empty($filtered))
			$this->db->query("DELETE FROM restriction_list where id not in ($filtered) and academic_id = $academic_id");
		else
			$this->db->query("DELETE FROM restriction_list where  academic_id = $academic_id");
		foreach($rid as $k => $v){
			$data = " academic_id = $academic_id ";
			$data .= ", faculty_id = {$faculty_id[$k]} ";
			$data .= ", class_id = {$class_id[$k]} ";
			$data .= ", subject_id = {$subject_id[$k]} ";
			if(empty($v)){
				$save[] = $this->db->query("INSERT INTO restriction_list set $data ");
			}else{
				$save[] = $this->db->query("UPDATE restriction_list set $data where id = $v ");
			}
		}
			return 1;
	}
	function save_evaluation() {
		extract($_POST);
		$data = " student_id = {$_SESSION['login_id']} ";
		$data .= ", academic_id = $academic_id ";
		$data .= ", subject_id = $subject_id ";
		$data .= ", class_id = $class_id ";
		$data .= ", restriction_id = $restriction_id ";
		$data .= ", faculty_id = $faculty_id ";
		$data .= ", academic_id = {$_SESSION['academic']['id']} ";
		
		// Add comment to the evaluation_list data if available
		if (!empty($comment)) {
			$data .= ", comment = '{$this->db->real_escape_string($comment)}' ";
		}
	
		// Insert into evaluation_list
		$save = $this->db->query("INSERT INTO evaluation_list SET $data");
	
		if ($save) {
			$eid = $this->db->insert_id;
	
			// Insert questions and their ratings into evaluation_answers
			foreach ($qid as $k => $v) {
				$data = " evaluation_id = $eid ";
				$data .= ", question_id = $v ";
				$data .= ", rate = {$rate[$v]} ";
				$ins[] = $this->db->query("INSERT INTO evaluation_answers SET $data");
			}
	
			if (isset($ins)) {
				return 1;
			}
		}
	}
	
	function get_class(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT c.id,concat(c.curriculum,' ',c.level,' - ',c.section) as class,s.id as sid,concat(s.code,' - ',s.subject) as subj FROM restriction_list r inner join class_list c on c.id = r.class_id inner join subject_list s on s.id = r.subject_id where r.faculty_id = {$fid} and academic_id = {$_SESSION['academic']['id']} ");
		while($row= $get->fetch_assoc()){
			$data[]=$row;
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
		
		
		
	}		
		
	
	