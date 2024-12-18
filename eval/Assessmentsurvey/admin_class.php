<?php
session_start();
ini_set('display_errors', 1);

  
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;


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
		
		// Escape user input
		$identifier = $this->escape_template($identifier);
		$password = $this->escape_template($password);
		
		$type = array("", "users", "faculty_list", "student_list");
		$type2 = array("", "admin", "faculty", "student");
		
		// Get IP address
		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		// Check if the identifier is an email or school ID
		if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
			$condition = "email = '".$identifier."'";
		} else {
			$condition = "school_id = '".$identifier."'";
		}
		
		// Check for too many failed attempts
		$lockout_time = 15;
		$max_attempts = 3;
		
		$check_attempts = $this->db->query("SELECT COUNT(*) as attempt_count FROM login_attempts 
								   WHERE identifier = '$identifier' AND ip_address = '$ip_address' 
								   AND attempt_time > DATE_SUB(NOW(), INTERVAL $lockout_time MINUTE)");
		
		if($check_attempts && $row = $check_attempts->fetch_assoc()) {
			if($row['attempt_count'] >= $max_attempts) {
				return 4; // Account is temporarily locked
			}
		}
		
		$qry = $this->db->query("SELECT *, concat(firstname,' ',lastname) as name 
								FROM {$type[$login]} WHERE {$condition}");
		
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
			
			if($password_verified) {
				// Clear login attempts
				$this->db->query("DELETE FROM login_attempts 
								WHERE identifier = '$identifier' AND ip_address = '$ip_address'");
				
				// Update hash if using old MD5
				if($update_hash) {
					$new_hash = password_hash($password, PASSWORD_DEFAULT);
					$update = $this->db->query("UPDATE {$type[$login]} set password = '$new_hash' where id = ".$row['id']);
				}
				
				// Check if password needs to be changed (for students and faculty)
				if($login != 1 && $row['is_password_changed'] == 0) {
					$_SESSION['login_id'] = $row['id'];
					$_SESSION['temp_user_id'] = $row['id'];
					$_SESSION['temp_login_type'] = $login;
					$_SESSION['temp_name'] = $row['name'];
					$_SESSION['temp_email'] = $row['email'] ?? '';
					$_SESSION['temp_table'] = $type[$login];
					return 3; // Redirect to password change
				}
				
				// Normal login process
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $this->escape_template($value);
				}
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = $login;
				$_SESSION['login_view_folder'] = $type2[$login].'/';
				
				// Set academic data
				$academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1");
				if($academic->num_rows > 0){
					foreach($academic->fetch_array() as $k => $v){
						if(!is_numeric($k))
							$_SESSION['academic'][$k] = $v;
					}
				}
				
				return 1; // Success
			}
			
			// Record failed attempt
			$user_type = $type2[$login];
			$school_id = isset($row['school_id']) ? $row['school_id'] : null;
			$this->db->query("INSERT INTO login_attempts 
							(identifier, ip_address, attempt_time, user_type, school_id) 
							VALUES ('$identifier', '$ip_address', NOW(), '$user_type', " . 
							($school_id ? "'$school_id'" : "NULL") . ")");
			
			return 2; // Wrong password
		}
		
		// Record failed attempt for non-existent user
		$user_type = $type2[$login];
		$this->db->query("INSERT INTO login_attempts 
						(identifier, ip_address, attempt_time, user_type, school_id) 
						VALUES ('$identifier', '$ip_address', NOW(), '$user_type', NULL)");
		
		return 2; // User not found
	}

	function login3() {
		extract($_POST);
		
		// Get IP address
		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		// Check if the identifier is an email or school ID
		if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
			$condition = "email = '".$identifier."'";
		} else {
			$condition = "school_id = '".$identifier."'";
		}
		
		// Check for too many failed attempts
		$lockout_time = 15; // minutes
		$max_attempts = 3; // maximum attempts allowed
		
		$check_attempts = $this->db->query("SELECT COUNT(*) as attempt_count FROM login_attempts 
								   WHERE identifier = '$identifier' AND ip_address = '$ip_address' 
								   AND attempt_time > DATE_SUB(NOW(), INTERVAL $lockout_time MINUTE)");
		
		if($check_attempts && $row = $check_attempts->fetch_assoc()) {
			if($row['attempt_count'] >= $max_attempts) {
				return 4; // Account is temporarily locked
			}
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
				// Clear login attempts on successful login
				$this->db->query("DELETE FROM login_attempts 
								WHERE identifier = '$identifier' AND ip_address = '$ip_address'");
				
				// If MD5 was used, update to newer hashing method
				if ($update_hash) {
					$new_hash = password_hash($password, PASSWORD_DEFAULT);
					$update_query = "UPDATE users SET password = '{$new_hash}' WHERE id = {$row['id']}";
					$this->db->query($update_query);
				}
	
				// Store user data in session
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $this->escape_template($value);
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
			} else {
				// Record failed attempt
				$this->db->query("INSERT INTO login_attempts 
								(identifier, ip_address, attempt_time, user_type, school_id) 
								VALUES ('$identifier', '$ip_address', NOW(), 'superadmin', NULL)");
				return 2; // Wrong password
			}
		}
		
		// Record failed attempt for non-existent user
		$this->db->query("INSERT INTO login_attempts 
						(identifier, ip_address, attempt_time, user_type, school_id) 
						VALUES ('$identifier', '$ip_address', NOW(), 'superadmin', NULL)");
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
						$_SESSION['login_'.$key] = $this->escape_template($value);
				}
				
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = 4; 
				$_SESSION['login_view_folder'] = 'staff-cot/'; 
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

	function login4_cme() {
		extract($_POST);
		
		// Check if the identifier is an email or school ID
		if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
			$condition = "email = '".$identifier."'";
		} else {
			$condition = "school_id = '".$identifier."'";
		}
	
		$qry = $this->db->query("SELECT *, concat(firstname,' ',lastname) as name FROM cme_staff WHERE {$condition}");
		
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
					$update_query = "UPDATE cme_staff SET password = '{$new_hash}' WHERE id = {$row['id']}";
					$this->db->query($update_query);
				}
	
				// Store user data in session
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $this->escape_template($value);
				}
				
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = 5;
				$_SESSION['login_view_folder'] = 'staff-cme/';
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


	function login4_coe() {
		// Verify CSRF token first
		if (!isset($_POST['csrf_token']) || !$this->verify_csrf_token($_POST['csrf_token'])) {
			return 'invalid_token';
		}
		
		extract($_POST);
		
		// Check if the identifier is an email or school ID
		if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
			$condition = "email = '".$identifier."'";
		} else {
			$condition = "school_id = '".$identifier."'";
		}
	
		$qry = $this->db->query("SELECT *, concat(firstname,' ',lastname) as name FROM coe_staff WHERE {$condition}");
		
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
					$update_query = "UPDATE coe_staff SET password = '{$new_hash}' WHERE id = {$row['id']}";
					$this->db->query($update_query);
				}
	
				// Store user data in session
				foreach ($row as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $this->escape_template($value);
				}
				
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = 6;  // Changed to 6 for COE staff
				$_SESSION['login_view_folder'] = 'staff-coe/';  // Changed to staff-coe
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
						$_SESSION['login_'.$key] = $this->escape_template($value);
					}
				}
				$_SESSION['login_id'] = $row['id'];
				$_SESSION['login_type'] = 5;
				$_SESSION['login_name'] = $row['firstname'] . ' ' . $row['lastname'];
				$_SESSION['login_avatar'] = $row['avatar'];
				$_SESSION['login_view_folder'] = 'staff-cme/';
				return true;
			}
		}
		return false;
	}




   //18/10/2024
   function send_verification(){
    extract($_POST);
    
    if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_login_type'])) {
        return 0; // Invalid session state
    }
    
    $user_id = $_SESSION['temp_user_id'];
    $type = array("","users","faculty_list","student_list");
    $table = $type[$_SESSION['temp_login_type']];
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 5; // Invalid email format
    }
    
    // Check if email already exists for any user
    $check_email = $this->db->query("SELECT * FROM $table WHERE email = '$email' AND id != $user_id");
    if($check_email->num_rows > 0) {
        return 4; // Email already exists in database
    }
    
    // Generate and store verification code
    $verification_code = substr(md5(uniqid(mt_rand(), true)), 0, 6);
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $update = $this->db->query("UPDATE $table SET 
        email = '$email',
        verification_code = '$verification_code', 
        verification_code_expiry = '$expiry' 
        WHERE id = $user_id");
    
    if($update) {
        $_SESSION['temp_email'] = $email; // Store email in session
        
        // Send email with verification code
        if($this->send_verification_email($email, $verification_code)) {
            return 1; // Email sent successfully
        } else {
            return 3; // Error sending email
        }
    }
    return 2; // Error updating verification code
}

function verify_code(){
    extract($_POST);
    
    if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_login_type'])) {
        return 0; // Invalid session state
    }
    
    $user_id = $_SESSION['temp_user_id'];
    $type = array("","users","faculty_list","student_list");
    $table = $type[$_SESSION['temp_login_type']];
    
    // Check verification code
    $verify_query = $this->db->query("SELECT * FROM $table 
        WHERE id = $user_id 
        AND verification_code = '$verification_code' 
        AND verification_code_expiry > NOW()");
        
    if($verify_query->num_rows > 0) {
        $_SESSION['email_verified'] = true;
        return 1; // Verification successful
    }
    return 0; // Verification failed
}
function change_password(){
    // Verify CSRF token first
    if (!isset($_POST['csrf_token']) || !$this->verify_csrf_token($_POST['csrf_token'])) {
        return 'invalid_token';
    }
    
    extract($_POST);
    $user_id = $_SESSION['temp_user_id'];
    $user_type = $_SESSION['temp_login_type'];
    
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
        // Clear temporary session data
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_login_type']);
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
    // Verify CSRF token first
    if (!isset($_POST['csrf_token']) || !$this->verify_csrf_token($_POST['csrf_token'])) {
        return 'invalid_token';
    }
    
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
		// Start session if not already started
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		
		// Clear all session variables
		$_SESSION = array();
		
		// Destroy the session cookie
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-3600, '/');
		}
		
		// Clear any remember me token if it exists
		if(isset($_COOKIE['superadmin_remember_token'])) {
			setcookie('superadmin_remember_token', '', time()-3600, '/');
		}
		
		// Destroy the session
		session_destroy();
		
		// Set cache control headers to prevent caching
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// Redirect to superadminlogin.php instead of login.php
		header("location:superadminlogin.php");
		exit();
	}
     

	//staff
	function logout4(){
		// Start session if not already started
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		
		// Clear all session variables
		$_SESSION = array();
		
		// Destroy the session cookie
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-3600, '/');
		}
		
		// Clear remember me token if it exists
		if(isset($_COOKIE['staff_remember_token'])) {
			setcookie('staff_remember_token', '', time()-3600, '/');
		}
		
		// Destroy the session
		session_destroy();
		
		// Set cache control headers
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		return json_encode(['status' => 'success']);
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




	function logout5(){
		// Start session if not already started
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		
		// Clear all session variables
		$_SESSION = array();
		
		// Destroy the session cookie
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-3600, '/');
		}
		
		
		
		// Destroy the session
		session_destroy();
		
		// Set cache control headers to prevent caching
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// Redirect to stafflogin_cme.php
		header("location:stafflogin_cme.php");
		exit();
	}


	function logout6(){
		// Start session if not already started
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		
		// Clear all session variables
		$_SESSION = array();
		
		// Destroy the session cookie
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-3600, '/');
		}
		
		
		
		// Destroy the session
		session_destroy();
		
		// Set cache control headers to prevent caching
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// Redirect to stafflogin_cme.php
		header("location:stafflogin_coe.php");
		exit();
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
	function save_user() {
		// Verify CSRF token first
		if (!isset($_POST['csrf_token']) || !$this->verify_csrf_token($_POST['csrf_token'])) {
			return 'invalid_token';
		}
		
		// Prepare base query
		if(empty($_POST['id'])) {
			$stmt = $this->db->prepare("INSERT INTO users (email, password, firstname, lastname, avatar) VALUES (?, ?, ?, ?, ?)");
		} else {
			$stmt = $this->db->prepare("UPDATE users SET email = ?, password = ?, firstname = ?, lastname = ?, avatar = ? WHERE id = ?");
		}
		
		// Sanitize inputs
		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		$firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
		$lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
		
		// Hash password if provided
		$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
		
		// Handle file upload
		$avatar = 'no-image-available.png';
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$avatar = $this->handle_file_upload($_FILES['img']);
		}
		
		// Bind parameters and execute
		if(empty($_POST['id'])) {
			$stmt->bind_param("sssss", $email, $password, $firstname, $lastname, $avatar);
		} else {
			$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
			$stmt->bind_param("sssssi", $email, $password, $firstname, $lastname, $avatar, $id);
		}
		
		return $stmt->execute() ? 1 : 0;
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
	function update_user() {
		extract($_POST);
		$data = "";
		$type = array("", "users", "faculty_list", "student_list");
	
		// Handle non-password fields first
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'table', 'password')) && !is_numeric($k)) {
				if ($k == 'email' && empty($v)) {
					continue; // Skip empty email field
				}
	
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
	
		// Check email duplication if email is provided
		if (isset($email) && !empty($email)) {
			$check = $this->db->query("SELECT * FROM {$type[$_SESSION['login_type']]} where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
			if ($check > 0) {
				return 2; // Email already exists
				exit;
			}
		}
	
		// Handle image upload
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
	
		// Only hash and update password if a new password is provided and it's not empty
		if (isset($password) && !empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$data .= ", password='$hashed_password' ";
		}
	
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO {$type[$_SESSION['login_type']]} set $data");
		} else {
			$save = $this->db->query("UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id");
		}
		if ($save) {
			foreach ($_POST as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
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
		
		// Modified check to include department and schedule_type
		$chk = $this->db->query("SELECT * FROM class_list 
			where curriculum = '$curriculum' 
			and level = '$level' 
			and section = '$section' 
			and department = '$department'
			and schedule_type = '$schedule_type'
			and id != '{$id}' ")->num_rows;
			
		if($chk > 0){
			return 2; // Class already exists in this department with the same schedule
		}
		
		if(isset($user_ids)){
			$data .= ", user_ids='".implode(',',$user_ids)."' ";
		}
		
		// Validate schedule_type
		if(!in_array($schedule_type, ['DAY', 'NIGHT'])) {
			$schedule_type = 'DAY'; // Set default if invalid value is provided
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


	function save_faculty() {
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)){
				if($k === 'email' && empty($v)) continue;
				
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
	
		// Handle file upload
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$upload_path = 'assets/uploads/';
			
			// Create directory if it doesn't exist
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0777, true);
			}
			
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], $upload_path . $fname);
			
			if($move){
				$data .= ", avatar = '$fname' ";
			} else {
				error_log("Failed to move uploaded file. Upload path: " . $upload_path . $fname);
				return 4; // New error code for file upload failure
			}
		}
	
		if(empty($id)){
			$save = $this->db->query("INSERT INTO faculty_list SET $data");
		} else {
			$save = $this->db->query("UPDATE faculty_list SET $data WHERE id = $id");
		}
	
		if($save){
			return 1;
		}
		return 0;
	}
	
	function delete_faculty(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM faculty_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_student(){
		extract($_POST);
		
		// Check for duplicate School ID
		$check_school_id = $this->db->query("SELECT * FROM student_list WHERE school_id = '$school_id'" . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
		if($check_school_id > 0){
			return 3; // School ID already exists
			exit;
		}
		
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
        // Start transaction
        $this->db->begin_transaction();

        // Get POST data
        extract($_POST);

        // Verify required fields
        if (!isset($restriction_id) || !isset($faculty_id) || !isset($class_id) || !isset($subject_id)) {
            throw new Exception("Missing required fields");
        }

        // Get current academic year
        $academic_qry = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1");
        if (!$academic_qry || $academic_qry->num_rows <= 0) {
            throw new Exception("No default academic year set");
        }
        $academic = $academic_qry->fetch_assoc();

        // Insert evaluation header
        $eval_query = "INSERT INTO evaluation_list (
            academic_id,
            class_id,
            student_id,
            subject_id,
            faculty_id,
            restriction_id,
            date_taken,
            comment
        ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        
        $stmt = $this->db->prepare($eval_query);
        $stmt->bind_param(
            "iiiiiss",
            $academic['id'],
            $class_id,
            $_SESSION['login_id'],
            $subject_id,
            $faculty_id,
            $restriction_id,
            $comment
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to save evaluation header");
        }
        
        $evaluation_id = $stmt->insert_id;

        // Save evaluation answers
        foreach ($rate as $question_id => $rating) {
            $answer_query = "INSERT INTO evaluation_answers (
                evaluation_id,
                question_id,
                rate
            ) VALUES (?, ?, ?)";
            
            $answer_stmt = $this->db->prepare($answer_query);
            $answer_stmt->bind_param("iii", $evaluation_id, $question_id, $rating);
            
            if (!$answer_stmt->execute()) {
                throw new Exception("Failed to save evaluation answer");
            }
        }

        // Create notification
        $notify_query = "INSERT INTO notifications (
            faculty_id,
            message,
            subject_id,
            class_id
        ) VALUES (?, ?, ?, ?)";

        // Get subject and class details for notification
        $details_query = "SELECT 
            s.code as subject_code,
            s.subject as subject_name,
            c.level,
            c.section
        FROM subject_list s 
        JOIN class_list c ON c.id = ?
        WHERE s.id = ?";
        
        $details_stmt = $this->db->prepare($details_query);
        $details_stmt->bind_param("ii", $class_id, $subject_id);
        $details_stmt->execute();
        $details = $details_stmt->get_result()->fetch_assoc();

        $message = sprintf(
            "New evaluation submitted for %s - %s (%s-%s)",
            $details['subject_code'],
            $details['subject_name'],
            $details['level'],
            $details['section']
        );

        $notify_stmt = $this->db->prepare($notify_query);
        $notify_stmt->bind_param("isii", $faculty_id, $message, $subject_id, $class_id);
        
        if (!$notify_stmt->execute()) {
            throw new Exception("Failed to create notification");
        }

        // Commit transaction
        $this->db->commit();
        return 1;

    } catch (Exception $e) {
        // Rollback transaction on error
        $this->db->rollback();
        error_log("Error saving evaluation: " . $e->getMessage());
        return json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
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
			
			$query = "SELECT 
				n.*,
				DATE_FORMAT(n.created_at, '%M %d, %Y %h:%i %p') as formatted_date,
				CASE 
					WHEN n.created_at > NOW() - INTERVAL 24 HOUR THEN 1 
					ELSE 0 
				END as is_recent,
				s.code as subject_code,
				s.subject as subject_name,
				cl.level as class_level,
				cl.section as class_section,
				cl.curriculum as class_curriculum,
				ROUND(n.rating_value, 2) as rating_average
			FROM notifications n 
			LEFT JOIN subject_list s ON n.subject_id = s.id
			LEFT JOIN class_list cl ON n.class_id = cl.id
			WHERE n.faculty_id = ? 
			ORDER BY n.created_at DESC, n.is_read ASC
			LIMIT 50";
			
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $faculty_id);
			$stmt->execute();
			$result = $stmt->get_result();
			
			$notifications = array();
			while($row = $result->fetch_assoc()) {
				// Format notification based on type
				if($row['notification_type'] == 'evaluation') {
					$class_info = sprintf(
						"%s %s-%s",
						$row['class_curriculum'], // e.g., BSIT
						$row['class_level'],      // e.g., 4
						$row['class_section']     // e.g., A
					);
					
					$rating_text = isset($row['rating_average']) ? 
						sprintf("(Rating: %.2f/5)", $row['rating_average']) : '';
					
					$row['message'] = sprintf(
						"New evaluation submitted for %s - %s (%s) %s",
						$row['subject_code'],
						$row['subject_name'],
						$class_info,
						$rating_text
					);
				}
				$notifications[] = $row;
			}
			
			return json_encode($notifications);
		}


		function mark_notification_read() {
			extract($_POST);
			
			if(!isset($notification_id)) {
				return json_encode(['status' => 'error', 'message' => 'Notification ID is required']);
			}
			
			$query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $notification_id);
			
			if($stmt->execute()) {
				return json_encode(['status' => 'success']);
			} else {
				return json_encode(['status' => 'error', 'message' => $this->db->error]);
			}
		}
		
		function mark_all_notifications_read() {
			extract($_POST);
			
			if(!isset($faculty_id)) {
				return json_encode(['status' => 'error', 'message' => 'Faculty ID is required']);
			}
			
			$query = "UPDATE notifications SET is_read = 1 WHERE faculty_id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $faculty_id);
			
			if($stmt->execute()) {
				return json_encode(['status' => 'success']);
			} else {
				return json_encode(['status' => 'error', 'message' => $this->db->error]);
			}
		}
		
		function delete_all_notifications() {
			extract($_POST);
			
			if(!isset($faculty_id)) {
				return json_encode(['status' => 'error', 'message' => 'Faculty ID is required']);
			}
			
			$query = "DELETE FROM notifications WHERE faculty_id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $faculty_id);
			
			if($stmt->execute()) {
				return json_encode(['status' => 'success']);
			} else {
				return json_encode(['status' => 'error', 'message' => $this->db->error]);
			}
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
		$department = $_POST['department'] ?? null; // Added department field
		
		// Basic validation for required fields
		if(empty($firstname) || empty($lastname) || empty($email)) {
			return 3; // Missing required fields
		}
		
		// Sanitize inputs
		$firstname = $this->db->real_escape_string($firstname);
		$lastname = $this->db->real_escape_string($lastname);
		$email = $this->db->real_escape_string($email);
		$department = $department ? $this->db->real_escape_string($department) : null;
		
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
					email = '$email',
					department = " . ($department ? "'$department'" : "NULL");
				
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
					department = " . ($department ? "'$department'" : "NULL") . ",
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
	
	function update_staff() {
		extract($_POST);
		$data = "";
		
		// Handle all fields except special cases
		foreach($_POST as $k => $v) {
			if(!in_array($k, array('id', 'cpass', 'password', 'department')) && !is_numeric($k)) {
				if(empty($data)) {
					$data .= " $k='" . $this->db->real_escape_string($v) . "' ";
				} else {
					$data .= ", $k='" . $this->db->real_escape_string($v) . "' ";
				}
			}
		}
		
		// Handle department separately since it can be NULL
		if(isset($_POST['department']) && $_POST['department'] !== '') {
			$department = $this->db->real_escape_string($_POST['department']);
			$data .= empty($data) ? " department='$department' " : ", department='$department' ";
		} else {
			$data .= empty($data) ? " department=NULL " : ", department=NULL ";
		}
		
		// Handle password update if provided
		if(!empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_BCRYPT);
			$data .= ", password='$hashed_password' ";
			$data .= ", is_password_changed = 1";
		}
		
		// Check for duplicate email
		$check = $this->db->query("SELECT * FROM staff WHERE email ='" . $this->db->real_escape_string($email) . "' AND id != {$id}")->num_rows;
		if($check > 0) {
			return 2;
			exit;
		}
		
		// Handle file upload if present
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";
		}
		
		// Update the staff record
		$save = $this->db->query("UPDATE staff SET $data WHERE id = $id");
		
		if($save) {
			return 1;
		}
		return 0;
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
				
				if ($result->num_rows >0) {
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
				$ay_query = "SELECT id FROM academic_list WHERE is_default =1 LIMIT 1";
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
			try {
				extract($_POST);
				
				if(!isset($faculty_id) || !isset($class_id) || !isset($subject_id)) {
					return json_encode([
						'status' => 'error',
						'message' => 'Missing required fields'
					]);
				}
			
				// Get current academic year and its semester
				$academic_year_query = $this->db->query("
					SELECT id, semester 
					FROM academic_list 
					WHERE is_default = 1 
					LIMIT 1
				");
				
				if(!$academic_year_query) {
					throw new Exception("Failed to get academic year");
				}
				
				$academic_year = $academic_year_query->fetch_assoc();
				if(!$academic_year) {
					throw new Exception("No default academic year set");
				}
				
				$academic_year_id = $academic_year['id'];
				$semester = $academic_year['semester']; // Get semester from default academic year
				$assigned_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
		
				// Rest of your existing code...
				
				$stmt = $this->db->prepare("
					INSERT INTO faculty_assignments (
						faculty_id, class_id, subject_id, 
						academic_year_id, semester, assigned_by
					) VALUES (?, ?, ?, ?, ?, ?)
				");
				
				$stmt->bind_param("iiiiii", 
					$faculty_id, 
					$class_id, 
					$subject_id, 
					$academic_year_id, 
					$semester, 
					$assigned_by
				);
				
				if($stmt->execute()) {
					return json_encode([
						'status' => 'success',
						'message' => 'Assignment saved successfully'
					]);
				} else {
					throw new Exception("Failed to save assignment");
				}
				
			} catch(Exception $e) {
				error_log("Error saving faculty assignment: " . $e->getMessage());
				return json_encode([
					'status' => 'error',
					'message' => 'Failed to save assignment: ' . $e->getMessage()
				]);
			}
		}
	
		public function load_assignments_faculty() {
			// Get current academic year
			$academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT1");
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
			$academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT1");
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
									   WHERE id = ? AND is_active =1");
			
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
			
			// Base query
			$sql = "SELECT 
				fa.id,
				f.firstname as faculty_name,
				f.lastname as faculty_lastname,
				c.curriculum as class_name,
				c.level,
				c.section,
				s.subject as subject_name,
				c.department
			FROM faculty_assignments fa
			JOIN faculty_list f ON fa.faculty_id = f.id
			JOIN class_list c ON fa.class_id = c.id
			JOIN subject_list s ON fa.subject_id = s.id
			WHERE fa.academic_year_id = ? AND fa.is_active = 1";
			
			// Add department filter if specified
			if (isset($department) && $department != 'all') {
				$sql .= " AND c.department = ?";
			}
			
			$stmt = $this->db->prepare($sql);
			
			if (isset($department) && $department != 'all') {
				$stmt->bind_param("is", $academic_year_id, $department);
			} else {
				$stmt->bind_param("i", $academic_year_id);
			}
			
			$stmt->execute();
			$result = $stmt->get_result();
			
			$output = "";
			while ($row = $result->fetch_assoc()) {
				$output .= "
					<tr>
						<td>{$row['faculty_name']} {$row['faculty_lastname']}</td>
						<td>{$row['class_name']} {$row['level']}-{$row['section']}</td>
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
			
			$stmt->close();
			return $output;
		}

		public function get_dashboard_stats() {
			try {
				// First check if there's a default academic year set
				$default_academic_query = $this->db->query("
					SELECT id, year, semester 
					FROM academic_list 
					WHERE is_default = 1 
					LIMIT 1
				");
				
				if ($default_academic_query->num_rows == 0) {
					return json_encode([
						'status' => 'warning',
						'message' => 'No default academic year set',
						'totalAssignments' => 0,
						'totalFaculty' => 0,
						'departmentStats' => [],
						'recentAssignments' => []
					]);
				}
		
				$default_academic = $default_academic_query->fetch_assoc();
				$academic_year_id = $default_academic['id'];
				$semester = $default_academic['semester'];
		
				// Get total active assignments for default academic year and semester
				$assignments_query = $this->db->prepare("
					SELECT COUNT(*) as total 
					FROM faculty_assignments 
					WHERE academic_year_id = ? 
					AND semester = ? 
					AND is_active = 1
				");
				$assignments_query->bind_param("ii", $academic_year_id, $semester);
				$assignments_query->execute();
				$total_assignments = $assignments_query->get_result()->fetch_assoc()['total'];
		
				// Get total faculty count
				$faculty_query = $this->db->query("
					SELECT COUNT(*) as total 
					FROM faculty_list
				");
				$total_faculty = $faculty_query->fetch_assoc()['total'];
		
				// Get assignments by department for default academic year and semester
				$dept_query = $this->db->prepare("
					SELECT 
						f.department,
						COUNT(DISTINCT fa.id) as total_assignments,
						COUNT(DISTINCT fa.faculty_id) as total_faculty
					FROM faculty_list f
					LEFT JOIN faculty_assignments fa ON f.id = fa.faculty_id
					AND fa.academic_year_id = ? 
					AND fa.semester = ?
					AND fa.is_active = 1
					GROUP BY f.department
					HAVING f.department != ''
					ORDER BY f.department ASC
				");
				$dept_query->bind_param("ii", $academic_year_id, $semester);
				$dept_query->execute();
				$dept_result = $dept_query->get_result();
		
				$department_stats = array();
				while($row = $dept_result->fetch_assoc()) {
					$department_stats[$row['department']] = [
						'assignments' => (int)$row['total_assignments'],
						'faculty' => (int)$row['total_faculty']
					];
				}
		
				// Get recent assignments
				$recent_query = $this->db->prepare("
					SELECT 
						fa.id,
						f.firstname,
						f.lastname,
						f.department,
						fa.assigned_at,
						al.year as academic_year
					FROM faculty_assignments fa
					JOIN faculty_list f ON fa.faculty_id = f.id
					JOIN academic_list al ON fa.academic_year_id = al.id
					WHERE fa.academic_year_id = ? 
					AND fa.semester = ?
					AND fa.is_active = 1
					ORDER BY fa.assigned_at DESC
					LIMIT 5
				");
				$recent_query->bind_param("ii", $academic_year_id, $semester);
				$recent_query->execute();
				$recent_result = $recent_query->get_result();
				
				$recent_assignments = array();
				while($row = $recent_result->fetch_assoc()) {
					$recent_assignments[] = [
						'id' => $row['id'],
						'faculty_name' => $row['firstname'] . ' ' . $row['lastname'],
						'department' => $row['department'],
						'assigned_at' => date('M d, Y', strtotime($row['assigned_at'])),
						'academic_year' => $row['academic_year']
					];
				}
		
				return json_encode([
					'status' => 'success',
					'academicYear' => $default_academic['year'],
					'semester' => $semester,
					'totalAssignments' => $total_assignments,
					'totalFaculty' => $total_faculty,
					'departmentStats' => $department_stats,
					'recentAssignments' => $recent_assignments
				]);
		
			} catch(Exception $e) {
				error_log("Error getting dashboard stats: " . $e->getMessage());
				return json_encode([
					'status' => 'error',
					'message' => 'Failed to fetch dashboard statistics'
				]);
			}
		}

//Cot Dashboard
public function get_dashboard_stats_cot() {
    try {
        // First check if there's a default academic year set
        $default_academic_query = $this->db->query("
            SELECT id, year, semester 
            FROM academic_list 
            WHERE is_default = 1 
            LIMIT 1
        ");
        
        if ($default_academic_query->num_rows == 0) {
            return json_encode([
                'status' => 'warning',
                'message' => 'No default academic year set',
                'totalAssignments' => 0,
                'totalFaculty' => 0,
                'departmentStats' => [],
                'recentAssignments' => []
            ]);
        }

        $default_academic = $default_academic_query->fetch_assoc();
        $academic_year_id = $default_academic['id'];
        $semester = $default_academic['semester'];

        // Get total active assignments for default academic year, semester, and COT
        $assignments_query = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM faculty_assignments fa
            JOIN faculty_list f ON fa.faculty_id = f.id
            WHERE fa.academic_year_id = ? 
            AND fa.semester = ? 
            AND fa.is_active = 1
            AND f.department LIKE 'COT%'
        ");
        $assignments_query->bind_param("ii", $academic_year_id, $semester);
        $assignments_query->execute();
        $total_assignments = $assignments_query->get_result()->fetch_assoc()['total'];

        // Get total faculty count for COT
        $faculty_query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM faculty_list
            WHERE department LIKE 'COT%'
        ");
        $total_faculty = $faculty_query->fetch_assoc()['total'];

        // Get assignments by department for default academic year, semester, and COT
        $dept_query = $this->db->prepare("
            SELECT 
                f.department,
                COUNT(DISTINCT fa.id) as total_assignments,
                COUNT(DISTINCT fa.faculty_id) as total_faculty
            FROM faculty_list f
            LEFT JOIN faculty_assignments fa ON f.id = fa.faculty_id
            AND fa.academic_year_id = ? 
            AND fa.semester = ?
            AND fa.is_active = 1
            WHERE f.department LIKE 'COT%'
            GROUP BY f.department
            ORDER BY f.department ASC
        ");
        $dept_query->bind_param("ii", $academic_year_id, $semester);
        $dept_query->execute();
        $dept_result = $dept_query->get_result();

        $department_stats = array();
        while($row = $dept_result->fetch_assoc()) {
            $department_stats[$row['department']] = [
                'assignments' => (int)$row['total_assignments'],
                'faculty' => (int)$row['total_faculty']
            ];
        }

        // Get recent assignments for COT
        $recent_query = $this->db->prepare("
            SELECT 
                fa.id,
                f.firstname,
                f.lastname,
                f.department,
                fa.assigned_at,
                al.year as academic_year
            FROM faculty_assignments fa
            JOIN faculty_list f ON fa.faculty_id = f.id
            JOIN academic_list al ON fa.academic_year_id = al.id
            WHERE fa.academic_year_id = ? 
            AND fa.semester = ?
            AND fa.is_active = 1
            AND f.department LIKE 'COT%'
            ORDER BY fa.assigned_at DESC
            LIMIT 5
        ");
        $recent_query->bind_param("ii", $academic_year_id, $semester);
        $recent_query->execute();
        $recent_result = $recent_query->get_result();
        
        $recent_assignments = array();
        while($row = $recent_result->fetch_assoc()) {
            $recent_assignments[] = [
                'id' => $row['id'],
                'faculty_name' => $row['firstname'] . ' ' . $row['lastname'],
                'department' => $row['department'],
                'assigned_at' => date('M d, Y', strtotime($row['assigned_at'])),
                'academic_year' => $row['academic_year']
            ];
        }

        return json_encode([
            'status' => 'success',
            'academicYear' => $default_academic['year'],
            'semester' => $semester,
            'totalAssignments' => $total_assignments,
            'totalFaculty' => $total_faculty,
            'departmentStats' => $department_stats,
            'recentAssignments' => $recent_assignments
        ]);

    } catch(Exception $e) {
        error_log("Error getting COT dashboard stats: " . $e->getMessage());
        return json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch COT dashboard statistics'
        ]);
    }
}

//COE Statistics

public function get_dashboard_stats_coe() {
    try {
        // First check if there's a default academic year set
        $default_academic_query = $this->db->query("
            SELECT id, year, semester 
            FROM academic_list 
            WHERE is_default = 1 
            LIMIT 1
        ");
        
        if ($default_academic_query->num_rows == 0) {
            return json_encode([
                'status' => 'warning',
                'message' => 'No default academic year set',
                'totalAssignments' => 0,
                'totalFaculty' => 0,
                'departmentStats' => [],
                'recentAssignments' => []
            ]);
        }

        $default_academic = $default_academic_query->fetch_assoc();
        $academic_year_id = $default_academic['id'];
        $semester = $default_academic['semester'];

        // Get total active assignments for default academic year, semester, and COT
        $assignments_query = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM faculty_assignments fa
            JOIN faculty_list f ON fa.faculty_id = f.id
            WHERE fa.academic_year_id = ? 
            AND fa.semester = ? 
            AND fa.is_active = 1
            AND f.department LIKE 'COE%'
        ");
        $assignments_query->bind_param("ii", $academic_year_id, $semester);
        $assignments_query->execute();
        $total_assignments = $assignments_query->get_result()->fetch_assoc()['total'];

        // Get total faculty count for COT
        $faculty_query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM faculty_list
            WHERE department LIKE 'COE%'
        ");
        $total_faculty = $faculty_query->fetch_assoc()['total'];

        // Get assignments by department for default academic year, semester, and COT
        $dept_query = $this->db->prepare("
            SELECT 
                f.department,
                COUNT(DISTINCT fa.id) as total_assignments,
                COUNT(DISTINCT fa.faculty_id) as total_faculty
            FROM faculty_list f
            LEFT JOIN faculty_assignments fa ON f.id = fa.faculty_id
            AND fa.academic_year_id = ? 
            AND fa.semester = ?
            AND fa.is_active = 1
            WHERE f.department LIKE 'COE%'
            GROUP BY f.department
            ORDER BY f.department ASC
        ");
        $dept_query->bind_param("ii", $academic_year_id, $semester);
        $dept_query->execute();
        $dept_result = $dept_query->get_result();

        $department_stats = array();
        while($row = $dept_result->fetch_assoc()) {
            $department_stats[$row['department']] = [
                'assignments' => (int)$row['total_assignments'],
                'faculty' => (int)$row['total_faculty']
            ];
        }

        // Get recent assignments for COT
        $recent_query = $this->db->prepare("
            SELECT 
                fa.id,
                f.firstname,
                f.lastname,
                f.department,
                fa.assigned_at,
                al.year as academic_year
            FROM faculty_assignments fa
            JOIN faculty_list f ON fa.faculty_id = f.id
            JOIN academic_list al ON fa.academic_year_id = al.id
            WHERE fa.academic_year_id = ? 
            AND fa.semester = ?
            AND fa.is_active = 1
            AND f.department LIKE 'COE%'
            ORDER BY fa.assigned_at DESC
            LIMIT 5
        ");
        $recent_query->bind_param("ii", $academic_year_id, $semester);
        $recent_query->execute();
        $recent_result = $recent_query->get_result();
        
        $recent_assignments = array();
        while($row = $recent_result->fetch_assoc()) {
            $recent_assignments[] = [
                'id' => $row['id'],
                'faculty_name' => $row['firstname'] . ' ' . $row['lastname'],
                'department' => $row['department'],
                'assigned_at' => date('M d, Y', strtotime($row['assigned_at'])),
                'academic_year' => $row['academic_year']
            ];
        }

        return json_encode([
            'status' => 'success',
            'academicYear' => $default_academic['year'],
            'semester' => $semester,
            'totalAssignments' => $total_assignments,
            'totalFaculty' => $total_faculty,
            'departmentStats' => $department_stats,
            'recentAssignments' => $recent_assignments
        ]);

    } catch(Exception $e) {
        error_log("Error getting COE dashboard stats: " . $e->getMessage());
        return json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch COE dashboard statistics'
        ]);
    }
}

//CME

public function get_dashboard_stats_cme() {
    try {
        // First check if there's a default academic year set
        $default_academic_query = $this->db->query("
            SELECT id, year, semester 
            FROM academic_list 
            WHERE is_default = 1 
            LIMIT 1
        ");
        
        if ($default_academic_query->num_rows == 0) {
            return json_encode([
                'status' => 'warning',
                'message' => 'No default academic year set',
                'totalAssignments' => 0,
                'totalFaculty' => 0,
                'departmentStats' => [],
                'recentAssignments' => []
            ]);
        }

        $default_academic = $default_academic_query->fetch_assoc();
        $academic_year_id = $default_academic['id'];
        $semester = $default_academic['semester'];

        // Get total active assignments for default academic year, semester, and COT
        $assignments_query = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM faculty_assignments fa
            JOIN faculty_list f ON fa.faculty_id = f.id
            WHERE fa.academic_year_id = ? 
            AND fa.semester = ? 
            AND fa.is_active = 1
            AND f.department LIKE 'CME%'
        ");
        $assignments_query->bind_param("ii", $academic_year_id, $semester);
        $assignments_query->execute();
        $total_assignments = $assignments_query->get_result()->fetch_assoc()['total'];

        // Get total faculty count for COT
        $faculty_query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM faculty_list
            WHERE department LIKE 'CME%'
        ");
        $total_faculty = $faculty_query->fetch_assoc()['total'];

        // Get assignments by department for default academic year, semester, and COT
        $dept_query = $this->db->prepare("
            SELECT 
                f.department,
                COUNT(DISTINCT fa.id) as total_assignments,
                COUNT(DISTINCT fa.faculty_id) as total_faculty
            FROM faculty_list f
            LEFT JOIN faculty_assignments fa ON f.id = fa.faculty_id
            AND fa.academic_year_id = ? 
            AND fa.semester = ?
            AND fa.is_active = 1
            WHERE f.department LIKE 'CME%'
            GROUP BY f.department
            ORDER BY f.department ASC
        ");
        $dept_query->bind_param("ii", $academic_year_id, $semester);
        $dept_query->execute();
        $dept_result = $dept_query->get_result();

        $department_stats = array();
        while($row = $dept_result->fetch_assoc()) {
            $department_stats[$row['department']] = [
                'assignments' => (int)$row['total_assignments'],
                'faculty' => (int)$row['total_faculty']
            ];
        }

        // Get recent assignments for COT
        $recent_query = $this->db->prepare("
            SELECT 
                fa.id,
                f.firstname,
                f.lastname,
                f.department,
                fa.assigned_at,
                al.year as academic_year
            FROM faculty_assignments fa
            JOIN faculty_list f ON fa.faculty_id = f.id
            JOIN academic_list al ON fa.academic_year_id = al.id
            WHERE fa.academic_year_id = ? 
            AND fa.semester = ?
            AND fa.is_active = 1
            AND f.department LIKE 'CME%'
            ORDER BY fa.assigned_at DESC
            LIMIT 5
        ");
        $recent_query->bind_param("ii", $academic_year_id, $semester);
        $recent_query->execute();
        $recent_result = $recent_query->get_result();
        
        $recent_assignments = array();
        while($row = $recent_result->fetch_assoc()) {
            $recent_assignments[] = [
                'id' => $row['id'],
                'faculty_name' => $row['firstname'] . ' ' . $row['lastname'],
                'department' => $row['department'],
                'assigned_at' => date('M d, Y', strtotime($row['assigned_at'])),
                'academic_year' => $row['academic_year']
            ];
        }

        return json_encode([
            'status' => 'success',
            'academicYear' => $default_academic['year'],
            'semester' => $semester,
            'totalAssignments' => $total_assignments,
            'totalFaculty' => $total_faculty,
            'departmentStats' => $department_stats,
            'recentAssignments' => $recent_assignments
        ]);

    } catch(Exception $e) {
        error_log("Error getting CME dashboard stats: " . $e->getMessage());
        return json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch CME dashboard statistics'
        ]);
    }
}


//CEAS statistic/dashboard








		//November 18 || Classroom.php

	


		public function add_student_to_class() {
			// Validate and sanitize inputs
			$school_id = filter_var($_POST['school_id'], FILTER_SANITIZE_STRING);
			$class_id = filter_var($_POST['class_id'], FILTER_SANITIZE_STRING);
			
			if (!$school_id || !$class_id) {
				return 0; // Invalid input
			}
			
			// Use prepared statements for all queries
			$stmt = $this->db->prepare("SELECT * FROM student_list WHERE school_id = ?");
			$stmt->bind_param("s", $school_id);
			$stmt->execute();
			$check = $stmt->get_result();
			
			if($check->num_rows == 0) {
				return 2; // Student not found
			}
			
			$student = $check->fetch_array();
			
			// Check if student is already in the class
			$stmt = $this->db->prepare("SELECT * FROM student_list WHERE id = ? AND class_id = ?");
			$stmt->bind_param("ss", $student['id'], $class_id);
			$stmt->execute();
			$check_class = $stmt->get_result();
			
			if($check_class->num_rows > 0) {
				return 3; // Student already in class
			}
			
			// Update student's class
			$stmt = $this->db->prepare("UPDATE student_list SET class_id = ? WHERE id = ?");
			$stmt->bind_param("ss", $class_id, $student['id']);
			$success = $stmt->execute();
			
			if($success) {
				// Create notification for the faculty
				$stmt = $this->db->prepare("SELECT faculty_id FROM faculty_assignments WHERE class_id = ? LIMIT 1");
				$stmt->bind_param("s", $class_id);
				$stmt->execute();
				$faculty = $stmt->get_result()->fetch_array();
				
				if($faculty) {
					$message = "New student {$student['firstname']} {$student['lastname']} has been added to your class.";
					$stmt = $this->db->prepare("INSERT INTO notifications (faculty_id, message) VALUES (?, ?)");
					$stmt->bind_param("ss", $faculty['faculty_id'], $message);
					$stmt->execute();
				}
				
				return 1; // Success
			}
			
			return 0; // Error
		}
	
		public function remove_student_from_class() {
			// Validate and sanitize input
			$student_id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
			
			if (!$student_id) {
				return 0; // Invalid input
			}
			
			// Use prepared statement to prevent SQL injection
			$stmt = $this->db->prepare("
				UPDATE student_list 
				SET class_id = NULL 
				WHERE id = ?
			");
			
			$stmt->bind_param("i", $student_id);
			$success = $stmt->execute();
			
			if ($success) {
				// Get student and class details for notification
				$stmt = $this->db->prepare("
					SELECT s.firstname, s.lastname, c.id as class_id 
					FROM student_list s 
					LEFT JOIN class_list c ON s.class_id = c.id 
					WHERE s.id = ?
				");
				$stmt->bind_param("i", $student_id);
				$stmt->execute();
				$result = $stmt->get_result();
				$student = $result->fetch_array();
				
				if ($student && $student['class_id']) {
					// Notify faculty about student removal
					$stmt = $this->db->prepare("
						SELECT DISTINCT faculty_id 
						FROM faculty_assignments 
						WHERE class_id = ?
					");
					$stmt->bind_param("i", $student['class_id']);
					$stmt->execute();
					$faculty_result = $stmt->get_result();
					
					while ($faculty = $faculty_result->fetch_array()) {
						$message = "Student {$student['firstname']} {$student['lastname']} has been removed from your class.";
						$stmt = $this->db->prepare("
							INSERT INTO notifications (faculty_id, message) 
							VALUES (?, ?)
						");
						$stmt->bind_param("is", $faculty['faculty_id'], $message);
						$stmt->execute();
					}
				}
				
				return 1; // Success
			}
			
			return 0; // Error
		}
		
	
		// Helper function to get current academic year
		private function get_current_academic_year() {
			$stmt = $this->db->prepare("
				SELECT id 
				FROM academic_list 
				WHERE is_default = 1 
				LIMIT1
			");
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row ? $row['id'] : null;
		}
		
		// Helper function to get current semester
		private function get_current_semester() {
			// You might want to store this in a settings table or configure it somewhere
			// For now, returning the current semester (assuming it's stored somewhere)
			// You should modify this according to your needs
			return 1; // or get it from your configuration/settings
		}








		
          

		//November 19,2024 home.php/faculty

		public function get_students($faculty_id, $academic_id) {
			$query = "
				SELECT DISTINCT 
					sl.id,
					sl.school_id,
					CONCAT(sl.firstname, ' ', sl.lastname) as student_name,
					cl.curriculum,
					cl.level,
					cl.section,
					s.code as subject_code,
					s.subject as subject_name
				FROM faculty_assignments fa
				INNER JOIN class_list cl ON fa.class_id = cl.id
				INNER JOIN student_list sl ON cl.id = sl.class_id
				INNER JOIN subject_list s ON fa.subject_id = s.id
				WHERE fa.faculty_id = ? 
				AND fa.academic_year_id = ?
				AND fa.is_active = 1
				ORDER BY sl.lastname ASC";
	
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("ii", $faculty_id, $academic_id);
			$stmt->execute();
			$result = $stmt->get_result();
	
			$students = array();
			while ($row = $result->fetch_assoc()) {
				$students[] = array(
					'id' => $row['id'],
					'school_id' => $row['school_id'],
					'student_name' => $row['student_name'],
					'class' => $row['curriculum'] . ' ' . $row['level'] . '-' . $row['section'],
					'subject' => $row['subject_code'] . ' - ' . $row['subject_name']
				);
			}
	
			return json_encode($students);
		}
		



		//add_subjects_to_class.php
 // Get available subjects for a class (subjects not yet assigned)
public function get_available_subjects() {
    extract($_POST);
    
    if(!isset($class_id)) {
        return json_encode([]);
    }

    $query = "SELECT s.* 
              FROM subject_list s 
              WHERE s.id NOT IN (
                  SELECT subject_id 
                  FROM class_subject_assignments 
                  WHERE class_id = ?
              )
              AND s.department = (
                  SELECT department 
                  FROM class_list 
                  WHERE id = ?
              )
              ORDER BY s.code ASC";
              
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("ii", $class_id, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects = array();
    while($row = $result->fetch_assoc()) {
        $subjects[] = array(
            'id' => $row['id'],
            'name' => $row['code'] . ' - ' . $row['subject']
        );
    }
    
    return json_encode($subjects);
}

// Get assigned subjects for a class
public function get_assigned_subjects() {
    extract($_POST);
    
    if(!isset($class_id)) {
        return json_encode([]);
    }

    $query = "SELECT s.*, csa.id as assignment_id 
              FROM class_subject_assignments csa 
              JOIN subject_list s ON csa.subject_id = s.id 
              WHERE csa.class_id = ? 
              ORDER BY s.code ASC";
              
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects = array();
    while($row = $result->fetch_assoc()) {
        $subjects[] = array(
            'id' => $row['id'],
            'assignment_id' => $row['assignment_id'],
            'name' => $row['code'] . ' - ' . $row['subject']
        );
    }
    
    return json_encode($subjects);
}

// Assign subject to class
public function get_available_subjects_class() {
	extract($_POST);
	
	if(!isset($class_id)) {
		return json_encode(['status' => 'error', 'message' => 'Class ID is required']);
	}

	try {
		$query = "SELECT s.* FROM subject_list s 
				 WHERE s.id NOT IN (
					 SELECT subject_id FROM class_subject_assignments 
					 WHERE class_id = ?
				 )
				 ORDER BY s.subject ASC";
		
		$stmt = $this->db->prepare($query);
		$stmt->bind_param("i", $class_id);
		$stmt->execute();
		$result = $stmt->get_result();
		
		$subjects = array();
		while($row = $result->fetch_assoc()) {
			$subjects[] = $row;
		}
		
		return json_encode($subjects);
	} catch(Exception $e) {
		error_log("Error fetching available subjects: " . $e->getMessage());
		return json_encode(['status' => 'error', 'message' => 'Failed to fetch subjects']);
	}
}

public function save_class_subject() {
    try {
        extract($_POST);
        
        // Debug logging
        error_log("Received POST data: " . print_r($_POST, true));

        // Validate required fields
        if(empty($class_id) || empty($subject_id)) {
            error_log("Missing required fields");
            return 0;
        }

        // Sanitize inputs
        $class_id = $this->db->real_escape_string($class_id);
        $subject_id = $this->db->real_escape_string($subject_id);
        $subject_code = $this->db->real_escape_string($subject_code);
        $subject_name = $this->db->real_escape_string($subject_name);

        // Check for duplicate entry
        $check = $this->db->query("SELECT id FROM class_subject 
                                  WHERE class_id = '$class_id' 
                                  AND subject_id = '$subject_id'");
        
        if($check->num_rows > 0) {
            error_log("Duplicate entry found");
            return 2; // Duplicate entry
        }

        // Insert new record
        $query = "INSERT INTO class_subject (class_id, subject_id, subject_code, subject_name) 
                 VALUES ('$class_id', '$subject_id', '$subject_code', '$subject_name')";
        
        error_log("Executing query: " . $query);
        
        if($this->db->query($query)) {
            error_log("Query successful");
            return 1; // Success
        } else {
            error_log("Query failed: " . $this->db->error);
            return 0; // Failed
        }

    } catch (Exception $e) {
        error_log("Error in save_class_subject: " . $e->getMessage());
        return 0;
    }
}

public function delete_class_subject() {
	extract($_POST);
	
	if(!isset($id)) {
		return 0;
	}

	$id = $this->db->real_escape_string($id);
	$query = "DELETE FROM class_subject WHERE id = '$id'";
	
	if($this->db->query($query)) {
		return 1;
	}
	return 0;
}

public function get_class_subject($id = null) {
	if($id) {
		$id = $this->db->real_escape_string($id);
		$query = "SELECT cs.*, cl.curriculum, cl.level, cl.section 
				 FROM class_subject cs 
				 INNER JOIN class_list cl ON cs.class_id = cl.id 
				 WHERE cs.id = '$id'";
		$result = $this->db->query($query);
		if($result->num_rows > 0) {
			return json_encode($result->fetch_assoc());
		}
	}
	return false;
}
public function get_class_assignments() {
    extract($_POST);
    
    // Build WHERE clause
    $where = [];
    $params = [];
    $types = "";
    
    // Add class filter if specified
    if(!empty($class_id)) {
        $where[] = "cl.id = ?";
        $params[] = $class_id;
        $types .= "i";
    }
    
    // Add department filter if specified
    if(!empty($department)) {
        $where[] = "cl.department = ?";
        $params[] = $department;
        $types .= "s";
    }
    
    // Combine WHERE conditions
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $query = "SELECT cl.id, 
                     cl.department,
                     CONCAT(cl.curriculum,' ',cl.level,' - ',cl.section) as class_name,
                     GROUP_CONCAT(
                         CONCAT(cs.id,'|',s.code,'|',s.subject)
                         ORDER BY s.code ASC
                         SEPARATOR '||'
                     ) as subjects
              FROM class_list cl
              LEFT JOIN class_subject cs ON cl.id = cs.class_id
              LEFT JOIN subject_list s ON cs.subject_id = s.id
              $whereClause
              GROUP BY cl.id
              ORDER BY cl.department ASC, cl.curriculum ASC, cl.level ASC, cl.section ASC";
    
    // Prepare and execute statement if there are parameters
    if (!empty($params)) {
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $this->db->query($query);
    }
    
    $output = "";
    $current_department = "";
    
    while($row = $result->fetch_assoc()) {
        // Add department header if it's a new department
        if ($current_department !== $row['department']) {
            if ($current_department !== "") {
                $output .= "</div>"; // Close previous department section
            }
            $current_department = $row['department'];
            $output .= "<div class='department-section mb-4'>";
            $output .= "<h4 class='department-header'>{$row['department']}</h4>";
        }
        
        // Add class card
        $output .= "<div class='class-card mb-3'>";
        $output .= "<h5 class='class-title'>{$row['class_name']}</h5>";
        $output .= "<div class='class-dropzone' data-class-id='{$row['id']}'>";
        
        if(!empty($row['subjects'])) {
            $subjects = explode("||", $row['subjects']);
            foreach($subjects as $subject) {
                list($id, $code, $name) = explode("|", $subject);
                $output .= "
                    <div class='assigned-subject'>
                        <span>$code - $name</span>
                        <i class='fas fa-times remove-subject' 
                           onclick='removeSubject($id)' 
                           title='Remove subject'></i>
                    </div>";
            }
        } else {
            $output .= "<div class='text-center text-muted py-3'>
                           <i class='fas fa-plus-circle mb-2'></i><br>
                           Drop subjects here
                       </div>";
        }
        
        $output .= "</div></div>"; // Close class-dropzone and class-card
    }
    
    // Close last department section if exists
    if ($current_department !== "") {
        $output .= "</div>";
    }
    
    // Show empty state if no results
    if (empty($output)) {
        $dept_message = !empty($department) ? " for $department" : "";
        $output = "<div class='alert alert-info'>
                    <i class='fas fa-info-circle me-2'></i>
                    No classes found{$dept_message}
                  </div>";
    }
    
    echo $output;
}



//Classroom.php


public function get_classroom_details() {
    extract($_POST);
    
    if(!isset($class_id)) {
        return json_encode(['status' => 'error', 'message' => 'No class ID specified']);
    }

    // Get class details
    $class_query = $this->db->prepare("
        SELECT * FROM class_list WHERE id = ?
    ");
    $class_query->bind_param("i", $class_id);
    $class_query->execute();
    $class = $class_query->get_result()->fetch_assoc();

    // Get subjects and instructors
    $subjects_query = $this->db->prepare("
        SELECT cs.*, s.code, s.subject, 
               CONCAT(f.firstname, ' ', f.lastname) as faculty_name
        FROM class_subject cs
        LEFT JOIN subject_list s ON cs.subject_id = s.id
        LEFT JOIN faculty_assignments fa ON cs.class_id = fa.class_id 
            AND cs.subject_id = fa.subject_id
        LEFT JOIN faculty_list f ON fa.faculty_id = f.id
        WHERE cs.class_id = ?
    ");
    $subjects_query->bind_param("i", $class_id);
    $subjects_query->execute();
    $subjects = $subjects_query->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get students
    $students_query = $this->db->prepare("
        SELECT * FROM student_list 
        WHERE class_id = ?
        ORDER BY lastname ASC, firstname ASC
    ");
    $students_query->bind_param("i", $class_id);
    $students_query->execute();
    $students = $students_query->get_result()->fetch_all(MYSQLI_ASSOC);

    return json_encode([
        'status' => 'success',
        'class' => $class,
        'subjects' => $subjects,
        'students' => $students
    ]);
}



//live Classroom

public function get_class_students() {
    extract($_POST);
    
    try {
        // Get only students assigned to this class
        $class_students = $this->db->query("SELECT id, firstname, lastname 
                                          FROM student_list 
                                          WHERE class_id = $class_id 
                                          ORDER BY lastname ASC, firstname ASC");
        
        $assigned_students = array();
        while($row = $class_students->fetch_assoc()) {
            $assigned_students[] = $row;
        }
        
        return json_encode(array(
            'status' => 'success',
            'assigned_students' => $assigned_students,
            'assigned_empty' => count($assigned_students) === 0
        ));
        
    } catch (Exception $e) {
        error_log("Error loading class students: " . $e->getMessage());
        return json_encode(array(
            'status' => 'error',
            'message' => $e->getMessage()
        ));
    }
}

public function save_classroom_assignment() {
    extract($_POST);
    
    try {
        // Validate required data
        if (empty($students)) {
            throw new Exception("No students assigned to the class.");
        }
        
        if (empty($assignments)) {
            throw new Exception("No subject-faculty assignments added.");
        }

        // Check if class already has assignments
        $check_query = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM faculty_assignments 
            WHERE class_id = ?
        ");
        $check_query->bind_param("i", $class_id);
        $check_query->execute();
        $existing_assignments = $check_query->get_result()->fetch_assoc()['count'];

        // Begin transaction
        $this->db->begin_transaction();

        if ($existing_assignments > 0) {
            // Update existing assignments
            $update_query = $this->db->prepare("
                UPDATE faculty_assignments 
                SET faculty_id = ?, subject_id = ?
                WHERE class_id = ? AND subject_id = ?
            ");

            foreach ($assignments as $assign) {
                $update_query->bind_param(
                    "iiii", 
                    $assign['faculty_id'], 
                    $assign['subject_id'],
                    $class_id,
                    $assign['subject_id']
                );
                $update_query->execute();

                // If no rows were updated, this is a new assignment
                if ($update_query->affected_rows == 0) {
                    $insert_query = $this->db->prepare("
                        INSERT INTO faculty_assignments (
                            faculty_id, class_id, subject_id, 
                            academic_year_id
                        ) VALUES (?, ?, ?, ?)
                    ");
                    $insert_query->bind_param(
                        "iiii", 
                        $assign['faculty_id'], 
                        $class_id, 
                        $assign['subject_id'], 
                        $academic_year_id
                    );
                    $insert_query->execute();
                }
            }
        } else {
            // Insert new assignments
            $assignment_query = $this->db->prepare("
                INSERT INTO faculty_assignments (
                    faculty_id, class_id, subject_id, 
                    academic_year_id
                ) VALUES (?, ?, ?, ?)
            ");

            foreach ($assignments as $assign) {
                $assignment_query->bind_param(
                    "iiii", 
                    $assign['faculty_id'], 
                    $class_id, 
                    $assign['subject_id'], 
                    $academic_year_id
                );
                $assignment_query->execute();
            }
        }

        // Update student class assignments
        $student_query = $this->db->prepare("
            UPDATE student_list 
            SET class_id = ? 
            WHERE id = ?
        ");

        foreach ($students as $student_id) {
            $student_query->bind_param("ii", $class_id, $student_id);
            $student_query->execute();
        }

        $this->db->commit();

        return json_encode([
            'status' => 'success',
            'message' => $existing_assignments > 0 ? 'update' : 'new',
            'existing_assignments' => $existing_assignments > 0
        ]);

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Error saving classroom assignment: " . $e->getMessage());
        return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

public function get_department_resources() {
    try {
        extract($_POST);
        
        if(!isset($department_id)) {
            throw new Exception("Department ID is required");
        }

        // Get department name
        $dept_query = "SELECT name FROM department_list WHERE id = ?";
        $stmt = $this->db->prepare($dept_query);
        $stmt->bind_param("i", $department_id);
        $stmt->execute();
        $dept_result = $stmt->get_result();
        
        if($dept_result->num_rows === 0) {
            throw new Exception("Department not found");
        }
        
        $department_name = $dept_result->fetch_assoc()['name'];

        // Get students from department's classes
        $student_query = "
            SELECT DISTINCT sl.id 
            FROM student_list sl
            INNER JOIN class_list cl ON sl.class_id = cl.id
            WHERE cl.department = ?
            ORDER BY sl.lastname ASC, sl.firstname ASC
        ";
        $stmt = $this->db->prepare($student_query);
        $stmt->bind_param("s", $department_name);
        $stmt->execute();
        $student_result = $stmt->get_result();
        
        $student_ids = array();
        while($row = $student_result->fetch_assoc()) {
            $student_ids[] = $row['id'];
        }

        // Get faculty from department
        $faculty_query = "
            SELECT id 
            FROM faculty_list 
            WHERE department = ?
            ORDER BY lastname ASC, firstname ASC
        ";
        $stmt = $this->db->prepare($faculty_query);
        $stmt->bind_param("s", $department_name);
        $stmt->execute();
        $faculty_result = $stmt->get_result();
        
        $faculty_ids = array();
        while($row = $faculty_result->fetch_assoc()) {
            $faculty_ids[] = $row['id'];
        }

        // Get subjects from department
        $subject_query = "
            SELECT id 
            FROM subject_list 
            WHERE department = ?
            ORDER BY subject ASC
        ";
        $stmt = $this->db->prepare($subject_query);
        $stmt->bind_param("s", $department_name);
        $stmt->execute();
        $subject_result = $stmt->get_result();
        
        $subject_ids = array();
        while($row = $subject_result->fetch_assoc()) {
            $subject_ids[] = $row['id'];
        }

        // Get classes from department
        $class_query = "
            SELECT id 
            FROM class_list 
            WHERE department = ?
            ORDER BY curriculum ASC, level ASC, section ASC
        ";
        $stmt = $this->db->prepare($class_query);
        $stmt->bind_param("s", $department_name);
        $stmt->execute();
        $class_result = $stmt->get_result();
        
        $class_ids = array();
        while($row = $class_result->fetch_assoc()) {
            $class_ids[] = $row['id'];
        }

        return json_encode([
            'status' => 'success',
            'students' => $student_ids,
            'faculty' => $faculty_ids,
            'subjects' => $subject_ids,
            'classes' => $class_ids,
            'department_name' => $department_name
        ]);

    } catch (Exception $e) {
        error_log("Error getting department resources: " . $e->getMessage());
        return json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}


//add new student modified november 23, 2024

public function get_classes_by_department_modified() {
    extract($_POST);
    
    if(!isset($department)) {
        return json_encode([
            'status' => 'error',
            'message' => 'No department specified'
        ]);
    }
    
    try {
        // Modified query to get all relevant class information
        $query = "SELECT DISTINCT cl.id, cl.curriculum, cl.level, cl.section 
                 FROM class_list cl 
                 WHERE cl.department = ?
                 ORDER BY cl.curriculum, cl.level, cl.section";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $classes = array();
        while($row = $result->fetch_assoc()) {
            $classes[] = [
                'id' => $row['id'],
                'curriculum' => $row['curriculum'],
                'level' => $row['level'],
                'section' => $row['section']
            ];
        }
        
        return json_encode([
            'status' => 'success',
            'classes' => $classes
        ]);
        
    } catch (Exception $e) {
        return json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

//modified_cme
public function get_classes_by_department_modified_cme() {
    extract($_POST);
    
    try {
        // Modified query to get only CME classes
        $query = "SELECT DISTINCT cl.id, cl.curriculum, cl.level, cl.section 
                 FROM class_list cl 
                 WHERE cl.department = 'CME'
                 AND cl.curriculum LIKE '%CME%'
                 ORDER BY cl.curriculum, cl.level, cl.section";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $classes = array();
        while($row = $result->fetch_assoc()) {
            $classes[] = [
                'id' => $row['id'],
                'curriculum' => $row['curriculum'],
                'level' => $row['level'],
                'section' => $row['section']
            ];
        }
        
        return json_encode([
            'status' => 'success',
            'classes' => $classes
        ]);
        
    } catch (Exception $e) {
        return json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}






//evaluation status

function get_evaluation_status() {
    try {
        $academic_id = isset($_POST['academic_id']) ? $_POST['academic_id'] : null;
        $semester = isset($_POST['semester']) ? $_POST['semester'] : null;
        $department = isset($_POST['department']) ? $_POST['department'] : null;

        $query = "
            SELECT 
                cl.curriculum,
                cl.level,
                cl.section,
                sl.id as student_id,
                sl.school_id,
                sl.firstname,
                sl.lastname,
                f.firstname as faculty_fname,
                f.lastname as faculty_lname,
                s.code as subject_code,
                s.subject as subject_name,
                fa.semester,
                CASE 
                    WHEN el.evaluation_id IS NOT NULL THEN 'Submitted'
                    ELSE 'Pending'
                END as status,
                el.date_taken
            FROM student_list sl
            INNER JOIN class_list cl ON sl.class_id = cl.id
            INNER JOIN faculty_assignments fa ON cl.id = fa.class_id
            INNER JOIN faculty_list f ON fa.faculty_id = f.id
            INNER JOIN subject_list s ON fa.subject_id = s.id
            LEFT JOIN evaluation_list el ON (
                sl.id = el.student_id AND
                fa.faculty_id = el.faculty_id AND
                fa.subject_id = el.subject_id AND
                fa.academic_year_id = el.academic_id
            )
            WHERE fa.is_active = 1";

        $params = [];
        $types = "";

        // Add filters
        if ($academic_id) {
            $query .= " AND fa.academic_year_id = ?";
            $params[] = $academic_id;
            $types .= "i";
        }

        if ($semester) {
            $query .= " AND fa.semester = ?";
            $params[] = $semester;
            $types .= "i";
        }

        if ($department) {
            $query .= " AND cl.curriculum = ?";
            $params[] = $department;
            $types .= "s";
        }

        $query .= " ORDER BY cl.curriculum, cl.level, cl.section, sl.lastname, sl.firstname";

        // Prepare and execute
        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            // Format date to text month
            $formatted_date = $row['date_taken'] ? 
                date('F j, Y \a\t g:i a', strtotime($row['date_taken'])) : 
                null;

            $data[] = array(
                'class_name' => "{$row['curriculum']} {$row['level']}-{$row['section']}",
                'student_id' => $row['school_id'],
                'student_name' => "{$row['lastname']}, {$row['firstname']}",
                'faculty_name' => "{$row['faculty_lname']}, {$row['faculty_fname']}",
                'subject' => "({$row['subject_code']}) {$row['subject_name']}",
                'semester' => $row['semester'],
                'status' => $row['status'],
                'date_taken' => $formatted_date
            );
        }

        return json_encode(['status' => 'success', 'data' => $data]);

    } catch (Exception $e) {
        error_log("Error in get_evaluation_status: " . $e->getMessage());
        return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

public function get_sections_by_department() {
    extract($_POST);
    
    if(!isset($department)) {
        return json_encode([
            'status' => 'error',
            'message' => 'No department specified'
        ]);
    }
    
    try {
        // Debug
        error_log("Department: " . $department);
        
        $query = "SELECT DISTINCT 
                 cl.section,
                 CONCAT(cl.curriculum, ' ', cl.level, '-', cl.section) as class_name
                 FROM class_list cl 
                 WHERE cl.department = ?
                 ORDER BY cl.curriculum, cl.level, cl.section";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sections = array();
        while($row = $result->fetch_assoc()) {
            $sections[] = array(
                'section' => $row['section'],
                'class_name' => $row['class_name']
            );
        }
        
        // Debug
        error_log("Found sections: " . print_r($sections, true));
        
        return json_encode([
            'status' => 'success',
            'sections' => $sections
        ]);
        
    } catch (Exception $e) {
        error_log("Error in get_sections_by_department: " . $e->getMessage());
        return json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

//faculty - students

// Add this function to admin_class.php
public function get_student_details_faculty() {
	extract($_POST);
	
	if(!isset($student_id)) {
		return json_encode(['status' => 'error', 'message' => 'Student ID is required']);
	}
	 // Modified query to search by school_id instead of id
	$query = "
		SELECT 
			sl.*,
			cl.curriculum,
			cl.level,
			cl.section
		FROM student_list sl
		LEFT JOIN class_list cl ON sl.class_id = cl.id
		WHERE sl.school_id = ?";
	 try {
		$stmt = $this->db->prepare($query);
		$stmt->bind_param("s", $student_id); // Changed to string parameter
		$stmt->execute();
		$result = $stmt->get_result();
		 if($result->num_rows > 0) {
			$student = $result->fetch_assoc();
			$student['class'] = $student['curriculum'] . ' ' . $student['level'] . '-' . $student['section'];
			
			// Ensure avatar path is correct
			$student['avatar_path'] = !empty($student['avatar']) && $student['avatar'] !== 'no-image-available.png'
				? '../uploads/' . $student['avatar']
				: '../assets/img/no-image-available.png';
			 return json_encode([
				'status' => 'success',
				'data' => $student
			]);
		}
		 return json_encode([
			'status' => 'error',
			'message' => 'Student not found'
		]);
	 } catch(Exception $e) {
		error_log("Error fetching student details: " . $e->getMessage());
		return json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch student details'
		]);
	}



	//Different Staff
	
	
function login4_ceas() {
    extract($_POST);
    
    // Check if college is provided
    if (!isset($college) || empty($college)) {
        return 4; // Return code for missing college selection
    }
    
    // Validate that only CEAS is allowed
    if ($college !== 'CEAS') {
        return 6; // Invalid department selection
    }
    
    // Check if the identifier is an email
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $qry = $this->db->query("SELECT * FROM ceas_staff WHERE email = '".$identifier."'");
    } else {
        return 2; // Invalid identifier (must be email for staff)
    }
    
    if($qry->num_rows > 0) {
        $row = $qry->fetch_array();
        
        if (password_verify($password, $row['password'])) {
            // Store user data in session
            foreach ($row as $key => $value) {
                if($key != 'password' && !is_numeric($key))
                    $_SESSION['login_'.$key] = $this->escape_template($value);
            }
            $_SESSION['login_id'] = $row['id'];
            $_SESSION['login_type'] = 4; // Staff type
            $_SESSION['login_department'] = 'CEAS';
            
            return 'CEAS'; // Success
        }
        return 2; // Wrong password
    }
    return 2; // User not found
}




// Add logout functions for each department




 
}















//Completed Evaluations in student dashboard

 
public function get_completed_evaluations() {
	if(!isset($_SESSION['login_id'])) {
		return json_encode([
			'status' => 'error',
			'message' => 'User not logged in'
		]);
	}
	
	$student_id = $_SESSION['login_id'];
	$faculty_id = isset($_GET['faculty_id']) ? $_GET['faculty_id'] : null;
	$academic_id = isset($_GET['academic_id']) ? $_GET['academic_id'] : null;
	$semester = isset($_GET['semester']) ? $_GET['semester'] : null;
	
	$query = "
		SELECT 
			e.evaluation_id,
			e.date_taken,
			f.firstname as faculty_firstname,
			f.lastname as faculty_lastname,
			s.code as subject_code,
			s.subject as subject_name,
			c.curriculum,
			c.level,
			c.section,
			a.year,
			a.semester,
			e.comment
		FROM evaluation_list e
		LEFT JOIN faculty_list f ON e.faculty_id = f.id
		LEFT JOIN subject_list s ON e.subject_id = s.id
		LEFT JOIN class_list c ON e.class_id = c.id
		LEFT JOIN academic_list a ON e.academic_id = a.id
		WHERE e.student_id = ?";
	
	$params = [$student_id];
	$types = "i";
	
	if ($faculty_id && $faculty_id !== '') {
		$query .= " AND e.faculty_id = ?";
		$params[] = $faculty_id;
		$types .= "i";
	}
	
	if ($academic_id && $academic_id !== '') {
		$query .= " AND e.academic_id = ?";
		$params[] = $academic_id;
		$types .= "i";
	}
	
	if ($semester && $semester !== '') {
		$query .= " AND a.semester = ?";
		$params[] = $semester;
		$types .= "i";
	}
	
	$query .= " ORDER BY e.date_taken DESC";
	
	try {
		$stmt = $this->db->prepare($query);
		$stmt->bind_param($types, ...$params);
		$stmt->execute();
		$result = $stmt->get_result();
		
		$evaluations = array();
		while($row = $result->fetch_assoc()) {
			$row['date_taken'] = date('F j, Y g:i A', strtotime($row['date_taken']));
			$row['class'] = $row['curriculum'] . ' ' . $row['level'] . '-' . $row['section'];
			$row['faculty_name'] = $row['faculty_lastname'] . ', ' . $row['faculty_firstname'];
			$row['academic_period'] = $row['year'] . ' - ' . $this->get_semester_text($row['semester']);
			
			$evaluations[] = $row;
		}
		
		return json_encode([
			'status' => 'success',
			'data' => $evaluations
		]);
		
	} catch(Exception $e) {
		error_log("Error fetching completed evaluations: " . $e->getMessage());
		return json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch completed evaluations'
		]);
	}
} 
  private function get_semester_text($semester) {
	switch($semester) {
		case 1:
			return '1st Semester';
		case 2:
			return '2nd Semester';
		case 3:
			return 'Summer';
		default:
			return 'Unknown Semester';
	}
}


public function get_evaluation_details() {
	if(!isset($_POST['evaluation_id'])) {
		return json_encode([
			'status' => 'error',
			'message' => 'Evaluation ID not provided'
		]);
	}
	
	$evaluation_id = $_POST['evaluation_id'];
	
	// Updated query to include academic year information
	$query = "
		SELECT 
			c.criteria,
			q.question,
			ea.rate as answer,
			e.comment,
			CONCAT(a.year, ' - ', 
				CASE a.semester 
					WHEN 1 THEN '1st Semester'
					WHEN 2 THEN '2nd Semester'
					WHEN 3 THEN 'Summer'
					ELSE 'Unknown'
				END
			) as academic_period
		FROM evaluation_answers ea
		JOIN evaluation_list e ON ea.evaluation_id = e.evaluation_id
		JOIN question_list q ON ea.question_id = q.id
		JOIN criteria_list c ON q.criteria_id = c.id
		JOIN academic_list a ON e.academic_id = a.id
		WHERE ea.evaluation_id = ?
		ORDER BY c.order_by ASC, q.order_by ASC";
		
	try {
		$stmt = $this->db->prepare($query);
		$stmt->bind_param("i", $evaluation_id);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if ($result->num_rows == 0) {
			return json_encode([
				'status' => 'error',
				'message' => 'No evaluation details found'
			]);
		}
		
		$questions = array();
		$comment = '';
		$academic_period = '';
		
		while($row = $result->fetch_assoc()) {
			$questions[] = [
				'criteria' => $row['criteria'],
				'question' => $row['question'],
				'answer' => $row['answer']
			];
			$comment = $row['comment'];
			$academic_period = $row['academic_period'];
		}
		
		return json_encode([
			'status' => 'success',
			'data' => [
				'questions' => $questions,
				'comment' => $comment,
				'academic_period' => $academic_period
			]
		]);
		
	} catch(Exception $e) {
		error_log("Error fetching evaluation details: " . $e->getMessage());
		return json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch evaluation details: ' . $e->getMessage()
		]);
	}
} 



//Staffs add_new_staff

function save_staff_coe() {
    extract($_POST);
    
    // Validate required fields
    if(empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        return 3; // Missing fields
    }
    
    // Check if email exists
    $stmt = $this->db->prepare("SELECT id FROM coe_staff WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check = $stmt->get_result();
    if($check->num_rows > 0) {
        return 2; // Email exists
    }
    
    // Handle image upload
    $avatar = 'no-image-available.png'; // Default image
    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
        $fname = strtotime(date('y-m-d H:i')).'_'.basename($_FILES['img']['name']);
        $target_dir = 'assets/uploads/';
        $target_file = $target_dir . $fname;
        
        // Check if file is an image
        $check = getimagesize($_FILES['img']['tmp_name']);
        if($check !== false) {
            if(move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
                $avatar = $fname;
            }
        }
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new staff
    $stmt = $this->db->prepare("INSERT INTO coe_staff (firstname, lastname, email, password, department, avatar) VALUES (?, ?, ?, ?, 'COE', ?)");
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $avatar);
    $save = $stmt->execute();
    
    return $save ? 1 : 0;
}

function save_staff_ceas() {
    extract($_POST);
    
    // Validate required fields
    if(empty($firstname) || empty($lastname) || empty($email)) {
        return 3; // Missing fields
    }
    
    // Check if email exists
    $check = $this->db->query("SELECT id FROM ceas_staff WHERE email = '$email'");
    if($check->num_rows > 0) {
        return 2; // Email exists
    }
    
    // Handle image upload
    $avatar = 'no-image-available.png'; // Default image
    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
        $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
        $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
        if($move) {
            $avatar = $fname;
        }
    }
    
    // New staff
    $password = password_hash($password, PASSWORD_DEFAULT);
    $save = $this->db->query("INSERT INTO ceas_staff (firstname, lastname, email, password, department, avatar) 
        VALUES ('$firstname', '$lastname', '$email', '$password', 'CEAS', '$avatar')");
    
    return $save ? 1 : 0;
}
function save_staff_cme() {
    extract($_POST);
    
    // Validate required fields
    if(empty($firstname) || empty($lastname) || empty($email)) {
        return 3; // Missing fields
    }
    
    // Check if email exists
    $check = $this->db->query("SELECT id FROM cme_staff WHERE email = '$email'");
    if($check->num_rows > 0) {
        return 2; // Email exists
    }
    
    // Handle image upload
    $avatar = 'no-image-available.png'; // Default image
    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
        $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
        $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
        if($move) {
            $avatar = $fname;
        }
    }
    
    // New staff
    $password = password_hash($password, PASSWORD_DEFAULT);
    $save = $this->db->query("INSERT INTO cme_staff (firstname, lastname, email, password, department, avatar) 
        VALUES ('$firstname', '$lastname', '$email', '$password', 'CME', '$avatar')");
    
    return $save ? 1 : 0;
}



function delete_staff_cme() {
    extract($_POST);
    $delete = $this->db->query("DELETE FROM cme_staff WHERE id = " . $id);
    return $delete ? 1 : 0;
}

function delete_staff_coe() {
    extract($_POST);
    $delete = $this->db->query("DELETE FROM coe_staff WHERE id = " . $id);
    return $delete ? 1 : 0;
}

function delete_staff_ceas() {
    extract($_POST);
    $delete = $this->db->query("DELETE FROM ceas_staff WHERE id = " . $id);
    return $delete ? 1 : 0;
}


//edit staffs coe,cme and ceas


function update_staff_ceas() {
    extract($_POST);
    
    // Validate required fields
    if(empty($firstname) || empty($lastname)) {
        return 3; // Missing fields
    }
    
    $data = " firstname = '$firstname' ";
    $data .= ", lastname = '$lastname' ";
    
    // Only update email if provided
    if(!empty($email)) {
        // Check if email exists for other users
        $check = $this->db->query("SELECT id FROM ceas_staff WHERE email = '$email' AND id != '$id'");
        if($check->num_rows > 0) {
            return 2; // Email exists
        }
        $data .= ", email = '$email' ";
    }
    
    // Only update password if provided
    if(!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data .= ", password = '$password' ";
    }
    
    $save = $this->db->query("UPDATE ceas_staff SET $data WHERE id = $id");
    
    return $save ? 1 : 0;
}

function update_staff_cme() {
    extract($_POST);
    
    if(empty($firstname) || empty($lastname)) {
        return 3;
    }
    
    $data = " firstname = '$firstname' ";
    $data .= ", lastname = '$lastname' ";
    
    if(!empty($email)) {
        $check = $this->db->query("SELECT id FROM cme_staff WHERE email = '$email' AND id != '$id'");
        if($check->num_rows > 0) {
            return 2;
        }
        $data .= ", email = '$email' ";
    }
    
    if(!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data .= ", password = '$password' ";
    }
    
    $save = $this->db->query("UPDATE cme_staff SET $data WHERE id = $id");
    
    return $save ? 1 : 0;
}

function update_staff_coe() {
    extract($_POST);
    
    if(empty($firstname) || empty($lastname)) {
        return 3;
    }
    
    $data = " firstname = '$firstname' ";
    $data .= ", lastname = '$lastname' ";
    
    if(!empty($email)) {
        $check = $this->db->query("SELECT id FROM coe_staff WHERE email = '$email' AND id != '$id'");
        if($check->num_rows > 0) {
            return 2;
        }
        $data .= ", email = '$email' ";
    }
    
    if(!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data .= ", password = '$password' ";
    }
    
    $save = $this->db->query("UPDATE coe_staff SET $data WHERE id = $id");
    
    return $save ? 1 : 0;
}



//Assign Faculty to Classes

public function get_assignments() {
    try {
        // First get the default academic year and semester
        $academic_query = $this->db->query("
            SELECT id, semester 
            FROM academic_list 
            WHERE is_default = 1 
            LIMIT 1
        ");
        
        if(!$academic_query || $academic_query->num_rows == 0) {
            throw new Exception("No default academic year found");
        }
        
        $academic = $academic_query->fetch_assoc();
        
        // Now get the assignments for this academic year and semester
        $query = $this->db->query("
            SELECT 
                fa.id,
                fa.faculty_id,
                fa.class_id,
                fa.subject_id,
                fa.academic_year_id,
                fa.semester,
                f.firstname as faculty_firstname,
                f.lastname as faculty_lastname,
                c.curriculum,
                c.level,
                c.section,
                s.code as subject_code,
                s.subject as subject_name,
                a.year as academic_year
            FROM faculty_assignments fa
            JOIN faculty_list f ON fa.faculty_id = f.id
            JOIN class_list c ON fa.class_id = c.id
            JOIN subject_list s ON fa.subject_id = s.id
            JOIN academic_list a ON fa.academic_year_id = a.id
            WHERE fa.academic_year_id = {$academic['id']}
            AND fa.semester = {$academic['semester']}
            ORDER BY c.curriculum ASC, c.level ASC, c.section ASC
        ");
        
        $assignments = array();
        while($row = $query->fetch_assoc()) {
            $assignments[] = $row;
        }
        
        return json_encode([
            'status' => 'success',
            'data' => $assignments
        ]);
        
    } catch(Exception $e) {
        return json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}


//Staff-cme-assignclasssubjects and evlaution status

public function load_assignments_faculties_cme() {
    extract($_POST);
    
    // Get current academic year
    $academic_year_query = $this->db->query("SELECT id FROM academic_list WHERE is_default = 1 LIMIT 1");
    $academic_year = $academic_year_query->fetch_assoc();
    $academic_year_id = $academic_year['id'];
    
    // Base query with CME department filter
    $sql = "SELECT 
        fa.id,
        f.firstname as faculty_name,
        f.lastname as faculty_lastname,
        c.curriculum as class_name,
        c.level,
        c.section,
        s.subject as subject_name,
        c.department
    FROM faculty_assignments fa
    JOIN faculty_list f ON fa.faculty_id = f.id
    JOIN class_list c ON fa.class_id = c.id
    JOIN subject_list s ON fa.subject_id = s.id
    WHERE fa.academic_year_id = ? AND fa.is_active = 1 AND c.department = 'CME'";
    
    // Prepare and execute the query
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $academic_year_id);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $output = "";
    while ($row = $result->fetch_assoc()) {
        $output .= "
            <tr>
                <td>{$row['faculty_name']} {$row['faculty_lastname']}</td>
                <td>{$row['class_name']} {$row['level']}-{$row['section']}</td>
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
    
    $stmt->close();
    return $output;
}


function get_evaluation_status_cme() {
    try {
        $academic_id = isset($_POST['academic_id']) ? $_POST['academic_id'] : null;
        $semester = isset($_POST['semester']) ? $_POST['semester'] : null;

        $query = "
            SELECT 
                cl.curriculum,
                cl.level,
                cl.section,
                sl.id as student_id,
                sl.school_id,
                sl.firstname,
                sl.lastname,
                f.firstname as faculty_fname,
                f.lastname as faculty_lname,
                s.code as subject_code,
                s.subject as subject_name,
                fa.semester,
                CASE 
                    WHEN el.evaluation_id IS NOT NULL THEN 'Submitted'
                    ELSE 'Pending'
                END as status,
                el.date_taken
            FROM student_list sl
            INNER JOIN class_list cl ON sl.class_id = cl.id
            INNER JOIN faculty_assignments fa ON cl.id = fa.class_id
            INNER JOIN faculty_list f ON fa.faculty_id = f.id
            INNER JOIN subject_list s ON fa.subject_id = s.id
            LEFT JOIN evaluation_list el ON (
                sl.id = el.student_id AND
                fa.faculty_id = el.faculty_id AND
                fa.subject_id = el.subject_id AND
                fa.academic_year_id = el.academic_id
            )
            WHERE fa.is_active = 1
            AND cl.department = 'CME'"; // Add fixed filter for CME department

        $params = [];
        $types = "";

        // Add academic year filter
        if ($academic_id) {
            $query .= " AND fa.academic_year_id = ?";
            $params[] = $academic_id;
            $types .= "i";
        }

        // Add semester filter
        if ($semester) {
            $query .= " AND fa.semester = ?";
            $params[] = $semester;
            $types .= "i";
        }

        $query .= " ORDER BY cl.curriculum, cl.level, cl.section, sl.lastname, sl.firstname";

        // Prepare and execute
        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            // Format date to text month
            $formatted_date = $row['date_taken'] ? 
                date('F j, Y \a\t g:i a', strtotime($row['date_taken'])) : 
                null;

            $data[] = array(
                'class_name' => "{$row['curriculum']} {$row['level']}-{$row['section']}",
                'student_id' => $row['school_id'],
                'student_name' => "{$row['lastname']}, {$row['firstname']}",
                'faculty_name' => "{$row['faculty_lname']}, {$row['faculty_fname']}",
                'subject' => "({$row['subject_code']}) {$row['subject_name']}",
                'semester' => $row['semester'],
                'status' => $row['status'],
                'date_taken' => $formatted_date
            );
        }

        return json_encode(['status' => 'success', 'data' => $data]);

    } catch (Exception $e) {
        error_log("Error in get_evaluation_status_cme: " . $e->getMessage());
        return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}


function get_evaluation_status_coe() {
    try {
        $academic_id = isset($_POST['academic_id']) ? $_POST['academic_id'] : null;
        $semester = isset($_POST['semester']) ? $_POST['semester'] : null;

        $query = "
            SELECT 
                cl.curriculum,
                cl.level,
                cl.section,
                sl.id as student_id,
                sl.school_id,
                sl.firstname,
                sl.lastname,
                f.firstname as faculty_fname,
                f.lastname as faculty_lname,
                s.code as subject_code,
                s.subject as subject_name,
                fa.semester,
                CASE 
                    WHEN el.evaluation_id IS NOT NULL THEN 'Submitted'
                    ELSE 'Pending'
                END as status,
                el.date_taken
            FROM student_list sl
            INNER JOIN class_list cl ON sl.class_id = cl.id
            INNER JOIN faculty_assignments fa ON cl.id = fa.class_id
            INNER JOIN faculty_list f ON fa.faculty_id = f.id
            INNER JOIN subject_list s ON fa.subject_id = s.id
            LEFT JOIN evaluation_list el ON (
                sl.id = el.student_id AND
                fa.faculty_id = el.faculty_id AND
                fa.subject_id = el.subject_id AND
                fa.academic_year_id = el.academic_id
            )
            WHERE fa.is_active = 1
            AND cl.department = 'COE'"; // Filter for COE department

        $params = [];
        $types = "";

        // Add academic year filter
        if ($academic_id) {
            $query .= " AND fa.academic_year_id = ?";
            $params[] = $academic_id;
            $types .= "i";
        }

        // Add semester filter
        if ($semester) {
            $query .= " AND fa.semester = ?";
            $params[] = $semester;
            $types .= "i";
        }

        $query .= " ORDER BY cl.curriculum, cl.level, cl.section, sl.lastname, sl.firstname";

        // Prepare and execute
        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            // Format date to text month
            $formatted_date = $row['date_taken'] ? 
                date('F j, Y \a\t g:i a', strtotime($row['date_taken'])) : 
                null;

            $data[] = array(
                'class_name' => "{$row['curriculum']} {$row['level']}-{$row['section']}",
                'student_id' => $row['school_id'],
                'student_name' => "{$row['lastname']}, {$row['firstname']}",
                'faculty_name' => "{$row['faculty_lname']}, {$row['faculty_fname']}",
                'subject' => "({$row['subject_code']}) {$row['subject_name']}",
                'semester' => $row['semester'],
                'status' => $row['status'],
                'date_taken' => $formatted_date
            );
        }

        return json_encode(['status' => 'success', 'data' => $data]);

    } catch (Exception $e) {
        error_log("Error in get_evaluation_status_coe: " . $e->getMessage());
        return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function export_semester_ratings() {
    try {
        // Get current academic year
        $academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();
        if (!$academic) {
            throw new Exception("No default academic year set");
        }

        // First, get all criteria and questions that have been used in evaluations this semester
        $criteria_query = "SELECT DISTINCT c.id, c.criteria, c.order_by 
                          FROM criteria_list c
                          INNER JOIN question_list q ON q.criteria_id = c.id 
                          INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                          INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                          WHERE e.academic_id = ? AND c.criteria != ''
                          ORDER BY c.order_by";
        
        $stmt = $this->db->prepare($criteria_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $criteria = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get questions organized by criteria, but only those used this semester
        $all_questions = [];
        foreach ($criteria as $c) {
            $question_query = "SELECT DISTINCT
                q.id as question_id,
                q.question,
                q.order_by
                FROM question_list q
                INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                WHERE q.criteria_id = ? AND e.academic_id = ?
                ORDER BY q.order_by";
            
            $stmt = $this->db->prepare($question_query);
            $stmt->bind_param("ii", $c['id'], $academic['id']);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $all_questions = array_merge($all_questions, $questions);
        }

        // Get all evaluation results with department and schedule type
        $eval_query = "SELECT e.*, s.school_id, s.firstname, s.lastname, 
                             f.firstname as faculty_fname, f.lastname as faculty_lname,
                             sub.code as subject_code, sub.subject as subject_name,
                             c.curriculum, c.level, c.section, c.department, c.schedule_type,
                             a.year as academic_year, a.semester
                      FROM evaluation_list e
                      INNER JOIN student_list s ON e.student_id = s.id
                      INNER JOIN faculty_list f ON e.faculty_id = f.id
                      INNER JOIN subject_list sub ON e.subject_id = sub.id
                      INNER JOIN class_list c ON e.class_id = c.id
                      INNER JOIN academic_list a ON e.academic_id = a.id
                      WHERE e.academic_id = ?
                      ORDER BY c.department, c.curriculum, c.level, c.section, s.lastname, s.firstname";
        
        $stmt = $this->db->prepare($eval_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Set headers for Excel download
        header('Content-Type: text/csv; charset=utf-8');
        $filename = sprintf('evaluation_report_%s_sem%d.csv', 
            $academic['year'], 
            $academic['semester']
        );
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        
        // Add BOM for Excel to recognize UTF-8
        echo "\xEF\xBB\xBF";

        // Output headers
        $headers = [
            'Academic Year',
            'Semester',
            'Department',
            'Student ID',
            'Student Name',
            'Faculty',
            'Subject',
            'Class',
            'Date Evaluated'
        ];

        // Add question headers
        foreach ($all_questions as $q) {
            $headers[] = $q['question'];
        }
        $headers[] = 'Comments';

        // Create output file handle
        $output = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($output, $headers);

        // Get all answers and organize them by evaluation_id
        if (!empty($evaluations)) {
            $answer_query = "SELECT evaluation_id, question_id, rate 
                            FROM evaluation_answers 
                            WHERE evaluation_id IN (" . implode(',', array_column($evaluations, 'evaluation_id')) . ")";
            $answers = $this->db->query($answer_query)->fetch_all(MYSQLI_ASSOC);
            
            $answer_map = [];
            foreach ($answers as $ans) {
                $answer_map[$ans['evaluation_id']][$ans['question_id']] = $ans['rate'];
            }

            // Output data rows
            foreach ($evaluations as $eval) {
                // Convert semester number to text
                $semester_text = match((int)$eval['semester']) {
                    1 => '1st Semester',
                    2 => '2nd Semester',
                    3 => 'Summer',
                    default => 'Unknown'
                };

                // Format class name with section and schedule type
                $class_name = $eval['curriculum'] . ' ' . $eval['level'] . $eval['section'];
                if (!empty($eval['schedule_type'])) {
                    $class_name .= '-' . $eval['schedule_type'];
                }

                $row = [
                    $eval['academic_year'],
                    $semester_text,
                    $eval['department'],
                    $eval['school_id'],
                    "{$eval['lastname']}, {$eval['firstname']}",
                    "{$eval['faculty_lname']}, {$eval['faculty_fname']}",
                    "({$eval['subject_code']}) {$eval['subject_name']}",
                    $class_name,
                    date('Y-m-d H:i:s', strtotime($eval['date_taken']))
                ];

                // Add answers in order of questions
                foreach ($all_questions as $q) {
                    $row[] = $answer_map[$eval['evaluation_id']][$q['question_id']] ?? '';
                }

                // Add comment at the end
                $row[] = $eval['comment'] ?? '';

                // Write the row
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;

    } catch (Exception $e) {
        error_log("Error exporting ratings: " . $e->getMessage());
        echo "Error exporting data: " . $e->getMessage();
    }
}


function export_semester_ratings_cot() {
    try {
        // Get current academic year
        $academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();
        if (!$academic) {
            throw new Exception("No default academic year set");
        }

        // First, get all criteria and questions that have been used in evaluations this semester for COT
        $criteria_query = "SELECT DISTINCT c.id, c.criteria, c.order_by 
                          FROM criteria_list c
                          INNER JOIN question_list q ON q.criteria_id = c.id 
                          INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                          INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                          INNER JOIN class_list cl ON e.class_id = cl.id
                          WHERE e.academic_id = ? AND c.criteria != ''
                          AND cl.department = 'COT'
                          ORDER BY c.order_by";
        
        $stmt = $this->db->prepare($criteria_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $criteria = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get questions organized by criteria, but only those used this semester in COT
        $all_questions = [];
        foreach ($criteria as $c) {
            $question_query = "SELECT DISTINCT
                q.id as question_id,
                q.question,
                q.order_by
                FROM question_list q
                INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                INNER JOIN class_list cl ON e.class_id = cl.id
                WHERE q.criteria_id = ? AND e.academic_id = ?
                AND cl.department = 'COT'
                ORDER BY q.order_by";
            
            $stmt = $this->db->prepare($question_query);
            $stmt->bind_param("ii", $c['id'], $academic['id']);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $all_questions = array_merge($all_questions, $questions);
        }

        // Get all evaluation results with department and schedule type - filtered for COT
        $eval_query = "SELECT e.*, s.school_id, s.firstname, s.lastname, 
                             f.firstname as faculty_fname, f.lastname as faculty_lname,
                             sub.code as subject_code, sub.subject as subject_name,
                             c.curriculum, c.level, c.section, c.department, c.schedule_type,
                             a.year as academic_year, a.semester
                      FROM evaluation_list e
                      INNER JOIN student_list s ON e.student_id = s.id
                      INNER JOIN faculty_list f ON e.faculty_id = f.id
                      INNER JOIN subject_list sub ON e.subject_id = sub.id
                      INNER JOIN class_list c ON e.class_id = c.id
                      INNER JOIN academic_list a ON e.academic_id = a.id
                      WHERE e.academic_id = ? AND c.department = 'COT'
                      ORDER BY c.curriculum, c.level, c.section, s.lastname, s.firstname";
        
        $stmt = $this->db->prepare($eval_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Set headers for Excel download
        header('Content-Type: text/csv; charset=utf-8');
        $filename = sprintf('COT_evaluation_report_%s_sem%d.csv', 
            $academic['year'], 
            $academic['semester']
        );
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        
        // Rest of the code remains the same...
        echo "\xEF\xBB\xBF";

        $headers = [
            'Academic Year',
            'Semester',
            'Department',
            'Student ID',
            'Student Name',
            'Faculty',
            'Subject',
            'Class',
            'Date Evaluated'
        ];

        foreach ($all_questions as $q) {
            $headers[] = $q['question'];
        }
        $headers[] = 'Comments';

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        if (!empty($evaluations)) {
            $answer_query = "SELECT evaluation_id, question_id, rate 
                            FROM evaluation_answers 
                            WHERE evaluation_id IN (" . implode(',', array_column($evaluations, 'evaluation_id')) . ")";
            $answers = $this->db->query($answer_query)->fetch_all(MYSQLI_ASSOC);
            
            $answer_map = [];
            foreach ($answers as $ans) {
                $answer_map[$ans['evaluation_id']][$ans['question_id']] = $ans['rate'];
            }

            foreach ($evaluations as $eval) {
                $semester_text = match((int)$eval['semester']) {
                    1 => '1st Semester',
                    2 => '2nd Semester',
                    3 => 'Summer',
                    default => 'Unknown'
                };

                $class_name = $eval['curriculum'] . ' ' . $eval['level'] . $eval['section'];
                if (!empty($eval['schedule_type'])) {
                    $class_name .= '-' . $eval['schedule_type'];
                }

                $row = [
                    $eval['academic_year'],
                    $semester_text,
                    $eval['department'],
                    $eval['school_id'],
                    "{$eval['lastname']}, {$eval['firstname']}",
                    "{$eval['faculty_lname']}, {$eval['faculty_fname']}",
                    "({$eval['subject_code']}) {$eval['subject_name']}",
                    $class_name,
                    date('Y-m-d H:i:s', strtotime($eval['date_taken']))
                ];

                foreach ($all_questions as $q) {
                    $row[] = $answer_map[$eval['evaluation_id']][$q['question_id']] ?? '';
                }

                $row[] = $eval['comment'] ?? '';
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;

    } catch (Exception $e) {
        error_log("Error exporting ratings: " . $e->getMessage());
        echo "Error exporting data: " . $e->getMessage();
    }
}

function export_semester_ratings_coe() {
    try {
        // Get current academic year
        $academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();
        if (!$academic) {
            throw new Exception("No default academic year set");
        }

        // First, get all criteria and questions that have been used in evaluations this semester for COE
        $criteria_query = "SELECT DISTINCT c.id, c.criteria, c.order_by 
                          FROM criteria_list c
                          INNER JOIN question_list q ON q.criteria_id = c.id 
                          INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                          INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                          INNER JOIN class_list cl ON e.class_id = cl.id
                          WHERE e.academic_id = ? AND c.criteria != ''
                          AND cl.department = 'COE'
                          ORDER BY c.order_by";
        
        $stmt = $this->db->prepare($criteria_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $criteria = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get questions organized by criteria, but only those used this semester in COE
        $all_questions = [];
        foreach ($criteria as $c) {
            $question_query = "SELECT DISTINCT
                q.id as question_id,
                q.question,
                q.order_by
                FROM question_list q
                INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                INNER JOIN class_list cl ON e.class_id = cl.id
                WHERE q.criteria_id = ? AND e.academic_id = ?
                AND cl.department = 'COE'
                ORDER BY q.order_by";
            
            $stmt = $this->db->prepare($question_query);
            $stmt->bind_param("ii", $c['id'], $academic['id']);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $all_questions = array_merge($all_questions, $questions);
        }

        // Get all evaluation results with department and schedule type - filtered for COE
        $eval_query = "SELECT e.*, s.school_id, s.firstname, s.lastname, 
                             f.firstname as faculty_fname, f.lastname as faculty_lname,
                             sub.code as subject_code, sub.subject as subject_name,
                             c.curriculum, c.level, c.section, c.department, c.schedule_type,
                             a.year as academic_year, a.semester
                      FROM evaluation_list e
                      INNER JOIN student_list s ON e.student_id = s.id
                      INNER JOIN faculty_list f ON e.faculty_id = f.id
                      INNER JOIN subject_list sub ON e.subject_id = sub.id
                      INNER JOIN class_list c ON e.class_id = c.id
                      INNER JOIN academic_list a ON e.academic_id = a.id
                      WHERE e.academic_id = ? AND c.department = 'COE'
                      ORDER BY c.curriculum, c.level, c.section, s.lastname, s.firstname";
        
        $stmt = $this->db->prepare($eval_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Set headers for Excel download
        header('Content-Type: text/csv; charset=utf-8');
        $filename = sprintf('COE_evaluation_report_%s_sem%d.csv', 
            $academic['year'], 
            $academic['semester']
        );
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        
        // Rest of the code remains the same...
        echo "\xEF\xBB\xBF";

        $headers = [
            'Academic Year',
            'Semester',
            'Department',
            'Student ID',
            'Student Name',
            'Faculty',
            'Subject',
            'Class',
            'Date Evaluated'
        ];

        foreach ($all_questions as $q) {
            $headers[] = $q['question'];
        }
        $headers[] = 'Comments';

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        if (!empty($evaluations)) {
            $answer_query = "SELECT evaluation_id, question_id, rate 
                            FROM evaluation_answers 
                            WHERE evaluation_id IN (" . implode(',', array_column($evaluations, 'evaluation_id')) . ")";
            $answers = $this->db->query($answer_query)->fetch_all(MYSQLI_ASSOC);
            
            $answer_map = [];
            foreach ($answers as $ans) {
                $answer_map[$ans['evaluation_id']][$ans['question_id']] = $ans['rate'];
            }

            foreach ($evaluations as $eval) {
                $semester_text = match((int)$eval['semester']) {
                    1 => '1st Semester',
                    2 => '2nd Semester',
                    3 => 'Summer',
                    default => 'Unknown'
                };

                $class_name = $eval['curriculum'] . ' ' . $eval['level'] . $eval['section'];
                if (!empty($eval['schedule_type'])) {
                    $class_name .= '-' . $eval['schedule_type'];
                }

                $row = [
                    $eval['academic_year'],
                    $semester_text,
                    $eval['department'],
                    $eval['school_id'],
                    "{$eval['lastname']}, {$eval['firstname']}",
                    "{$eval['faculty_lname']}, {$eval['faculty_fname']}",
                    "({$eval['subject_code']}) {$eval['subject_name']}",
                    $class_name,
                    date('Y-m-d H:i:s', strtotime($eval['date_taken']))
                ];

                foreach ($all_questions as $q) {
                    $row[] = $answer_map[$eval['evaluation_id']][$q['question_id']] ?? '';
                }

                $row[] = $eval['comment'] ?? '';
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;

    } catch (Exception $e) {
        error_log("Error exporting ratings: " . $e->getMessage());
        echo "Error exporting data: " . $e->getMessage();
    }
}

function export_semester_ratings_cme() {
    try {
        // Get current academic year
        $academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();
        if (!$academic) {
            throw new Exception("No default academic year set");
        }

        // First, get all criteria and questions that have been used in evaluations this semester for CME
        $criteria_query = "SELECT DISTINCT c.id, c.criteria, c.order_by 
                          FROM criteria_list c
                          INNER JOIN question_list q ON q.criteria_id = c.id 
                          INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                          INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                          INNER JOIN class_list cl ON e.class_id = cl.id
                          WHERE e.academic_id = ? AND c.criteria != ''
                          AND cl.department = 'CME'
                          ORDER BY c.order_by";
        
        $stmt = $this->db->prepare($criteria_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $criteria = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get questions organized by criteria, but only those used this semester in CME
        $all_questions = [];
        foreach ($criteria as $c) {
            $question_query = "SELECT DISTINCT
                q.id as question_id,
                q.question,
                q.order_by
                FROM question_list q
                INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                INNER JOIN class_list cl ON e.class_id = cl.id
                WHERE q.criteria_id = ? AND e.academic_id = ?
                AND cl.department = 'CME'
                ORDER BY q.order_by";
            
            $stmt = $this->db->prepare($question_query);
            $stmt->bind_param("ii", $c['id'], $academic['id']);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $all_questions = array_merge($all_questions, $questions);
        }

        // Get all evaluation results with department and schedule type - filtered for CME
        $eval_query = "SELECT e.*, s.school_id, s.firstname, s.lastname, 
                             f.firstname as faculty_fname, f.lastname as faculty_lname,
                             sub.code as subject_code, sub.subject as subject_name,
                             c.curriculum, c.level, c.section, c.department, c.schedule_type,
                             a.year as academic_year, a.semester
                      FROM evaluation_list e
                      INNER JOIN student_list s ON e.student_id = s.id
                      INNER JOIN faculty_list f ON e.faculty_id = f.id
                      INNER JOIN subject_list sub ON e.subject_id = sub.id
                      INNER JOIN class_list c ON e.class_id = c.id
                      INNER JOIN academic_list a ON e.academic_id = a.id
                      WHERE e.academic_id = ? AND c.department = 'CME'
                      ORDER BY c.curriculum, c.level, c.section, s.lastname, s.firstname";
        
        $stmt = $this->db->prepare($eval_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Set headers for Excel download
        header('Content-Type: text/csv; charset=utf-8');
        $filename = sprintf('CME_evaluation_report_%s_sem%d.csv', 
            $academic['year'], 
            $academic['semester']
        );
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        
        // Add BOM for Excel to recognize UTF-8
        echo "\xEF\xBB\xBF";

        $headers = [
            'Academic Year',
            'Semester',
            'Department',
            'Student ID',
            'Student Name',
            'Faculty',
            'Subject',
            'Class',
            'Date Evaluated'
        ];

        foreach ($all_questions as $q) {
            $headers[] = $q['question'];
        }
        $headers[] = 'Comments';

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        if (!empty($evaluations)) {
            $answer_query = "SELECT evaluation_id, question_id, rate 
                            FROM evaluation_answers 
                            WHERE evaluation_id IN (" . implode(',', array_column($evaluations, 'evaluation_id')) . ")";
            $answers = $this->db->query($answer_query)->fetch_all(MYSQLI_ASSOC);
            
            $answer_map = [];
            foreach ($answers as $ans) {
                $answer_map[$ans['evaluation_id']][$ans['question_id']] = $ans['rate'];
            }

            foreach ($evaluations as $eval) {
                $semester_text = match((int)$eval['semester']) {
                    1 => '1st Semester',
                    2 => '2nd Semester',
                    3 => 'Summer',
                    default => 'Unknown'
                };

                $class_name = $eval['curriculum'] . ' ' . $eval['level'] . $eval['section'];
                if (!empty($eval['schedule_type'])) {
                    $class_name .= '-' . $eval['schedule_type'];
                }

                $row = [
                    $eval['academic_year'],
                    $semester_text,
                    $eval['department'],
                    $eval['school_id'],
                    "{$eval['lastname']}, {$eval['firstname']}",
                    "{$eval['faculty_lname']}, {$eval['faculty_fname']}",
                    "({$eval['subject_code']}) {$eval['subject_name']}",
                    $class_name,
                    date('Y-m-d H:i:s', strtotime($eval['date_taken']))
                ];

                foreach ($all_questions as $q) {
                    $row[] = $answer_map[$eval['evaluation_id']][$q['question_id']] ?? '';
                }

                $row[] = $eval['comment'] ?? '';
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;

    } catch (Exception $e) {
        error_log("Error exporting ratings: " . $e->getMessage());
        echo "Error exporting data: " . $e->getMessage();
    }
}



function export_semester_ratings_ceas() {
    try {
        // Get current academic year
        $academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();
        if (!$academic) {
            throw new Exception("No default academic year set");
        }

        // First, get all criteria and questions that have been used in evaluations this semester for CEAS
        $criteria_query = "SELECT DISTINCT c.id, c.criteria, c.order_by 
                          FROM criteria_list c
                          INNER JOIN question_list q ON q.criteria_id = c.id 
                          INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                          INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                          INNER JOIN class_list cl ON e.class_id = cl.id
                          WHERE e.academic_id = ? AND c.criteria != ''
                          AND cl.department = 'CEAS'
                          ORDER BY c.order_by";
        
        $stmt = $this->db->prepare($criteria_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $criteria = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get questions organized by criteria, but only those used this semester in CEAS
        $all_questions = [];
        foreach ($criteria as $c) {
            $question_query = "SELECT DISTINCT
                q.id as question_id,
                q.question,
                q.order_by
                FROM question_list q
                INNER JOIN evaluation_answers ea ON ea.question_id = q.id
                INNER JOIN evaluation_list e ON e.evaluation_id = ea.evaluation_id
                INNER JOIN class_list cl ON e.class_id = cl.id
                WHERE q.criteria_id = ? AND e.academic_id = ?
                AND cl.department = 'CEAS'
                ORDER BY q.order_by";
            
            $stmt = $this->db->prepare($question_query);
            $stmt->bind_param("ii", $c['id'], $academic['id']);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $all_questions = array_merge($all_questions, $questions);
        }

        // Get all evaluation results with department and schedule type - filtered for CEAS
        $eval_query = "SELECT e.*, s.school_id, s.firstname, s.lastname, 
                             f.firstname as faculty_fname, f.lastname as faculty_lname,
                             sub.code as subject_code, sub.subject as subject_name,
                             c.curriculum, c.level, c.section, c.department, c.schedule_type,
                             a.year as academic_year, a.semester
                      FROM evaluation_list e
                      INNER JOIN student_list s ON e.student_id = s.id
                      INNER JOIN faculty_list f ON e.faculty_id = f.id
                      INNER JOIN subject_list sub ON e.subject_id = sub.id
                      INNER JOIN class_list c ON e.class_id = c.id
                      INNER JOIN academic_list a ON e.academic_id = a.id
                      WHERE e.academic_id = ? AND c.department = 'CEAS'
                      ORDER BY c.curriculum, c.level, c.section, s.lastname, s.firstname";
        
        $stmt = $this->db->prepare($eval_query);
        $stmt->bind_param("i", $academic['id']);
        $stmt->execute();
        $evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Set headers for Excel download
        header('Content-Type: text/csv; charset=utf-8');
        $filename = sprintf('CEAS_evaluation_report_%s_sem%d.csv', 
            $academic['year'], 
            $academic['semester']
        );
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        
        // Rest of the code remains the same...
        echo "\xEF\xBB\xBF";

        $headers = [
            'Academic Year',
            'Semester',
            'Department',
            'Student ID',
            'Student Name',
            'Faculty',
            'Subject',
            'Class',
            'Date Evaluated'
        ];

        foreach ($all_questions as $q) {
            $headers[] = $q['question'];
        }
        $headers[] = 'Comments';

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        if (!empty($evaluations)) {
            $answer_query = "SELECT evaluation_id, question_id, rate 
                            FROM evaluation_answers 
                            WHERE evaluation_id IN (" . implode(',', array_column($evaluations, 'evaluation_id')) . ")";
            $answers = $this->db->query($answer_query)->fetch_all(MYSQLI_ASSOC);
            
            $answer_map = [];
            foreach ($answers as $ans) {
                $answer_map[$ans['evaluation_id']][$ans['question_id']] = $ans['rate'];
            }

            foreach ($evaluations as $eval) {
                $semester_text = match((int)$eval['semester']) {
                    1 => '1st Semester',
                    2 => '2nd Semester',
                    3 => 'Summer',
                    default => 'Unknown'
                };

                $class_name = $eval['curriculum'] . ' ' . $eval['level'] . $eval['section'];
                if (!empty($eval['schedule_type'])) {
                    $class_name .= '-' . $eval['schedule_type'];
                }

                $row = [
                    $eval['academic_year'],
                    $semester_text,
                    $eval['department'],
                    $eval['school_id'],
                    "{$eval['lastname']}, {$eval['firstname']}",
                    "{$eval['faculty_lname']}, {$eval['faculty_fname']}",
                    "({$eval['subject_code']}) {$eval['subject_name']}",
                    $class_name,
                    date('Y-m-d H:i:s', strtotime($eval['date_taken']))
                ];

                foreach ($all_questions as $q) {
                    $row[] = $answer_map[$eval['evaluation_id']][$q['question_id']] ?? '';
                }

                $row[] = $eval['comment'] ?? '';
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;

    } catch (Exception $e) {
        error_log("Error exporting ratings: " . $e->getMessage());
        echo "Error exporting data: " . $e->getMessage();
    }
}








//december 8,2024

function export_evaluation_summary() {
    try {
        // Validate input
        if (!isset($_POST['faculty_id']) || !isset($_POST['academic_id'])) {
            throw new Exception('Missing required parameters');
        }

		require __DIR__ . '/../vendor/autoload.php';
      
        $faculty_id = intval($_POST['faculty_id']);
        $academic_id = intval($_POST['academic_id']);

        // Get faculty details
        $faculty_query = "SELECT CONCAT(lastname, ', ', firstname) as name 
                         FROM faculty_list WHERE id = ?";
        $stmt = $this->db->prepare($faculty_query);
        $stmt->bind_param('i', $faculty_id);
        $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();

        // Get academic year details
        $academic_query = "SELECT year, semester FROM academic_list WHERE id = ?";
        $stmt = $this->db->prepare($academic_query);
        $stmt->bind_param('i', $academic_id);
        $stmt->execute();
        $academic = $stmt->get_result()->fetch_assoc();

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Faculty Evaluation System')
            ->setLastModifiedBy('System Administrator')
            ->setTitle('Evaluation Summary')
            ->setSubject('Faculty Evaluation Summary')
            ->setDescription('Evaluation Summary Report');

        // Add header
        $sheet->setCellValue('A1', 'FACULTY EVALUATION SUMMARY');
        $sheet->setCellValue('A2', 'Faculty: ' . $faculty['name']);
        $sheet->setCellValue('A3', 'Academic Year: ' . $academic['year']);
        $sheet->setCellValue('A4', 'Semester: ' . $academic['semester']);

        // Style header
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);

        // Get evaluation data
        $eval_query = "SELECT 
            e.*, 
            q.question,
            c.criteria,
            ROUND(AVG(a.rate), 2) as average_rating
            FROM evaluation_list e 
            JOIN evaluation_answers a ON e.evaluation_id = a.evaluation_id
            JOIN question_list q ON a.question_id = q.id
            JOIN criteria_list c ON q.criteria_id = c.id
            WHERE e.faculty_id = ? AND e.academic_id = ?
            GROUP BY q.id
            ORDER BY c.order_by, q.order_by";
        
        $stmt = $this->db->prepare($eval_query);
        $stmt->bind_param('ii', $faculty_id, $academic_id);
        $stmt->execute();
        $evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Add data headers
        $sheet->setCellValue('A6', 'Criteria');
        $sheet->setCellValue('B6', 'Question');
        $sheet->setCellValue('C6', 'Average Rating');

        // Style headers
        $headerStyle = $sheet->getStyle('A6:C6');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setRGB('CCCCCC');

        // Add data
        $row = 7;
        foreach ($evaluations as $eval) {
            $sheet->setCellValue('A' . $row, $eval['criteria']);
            $sheet->setCellValue('B' . $row, $eval['question']);
            $sheet->setCellValue('C' . $row, $eval['average_rating']);
            
            // Style data rows
            $sheet->getStyle('A'.$row.':C'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Auto-size columns
        foreach(range('A','C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create PDF Writer
        $writer = new Mpdf($spreadsheet);
        
        // Clean output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Set headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="evaluation_summary_'.date('Y-m-d').'.pdf"');
        header('Cache-Control: max-age=0');

        // Save to output
        $writer->save('php://output');
        exit();

    } catch (Exception $e) {
        error_log("PDF Generation Error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
}


//December 12, 2024 || security

private function handle_file_upload($file) {
    // Whitelist allowed file types
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        throw new Exception('Invalid file type');
    }
    
    // Generate secure filename
    $new_filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $upload_path = 'assets/uploads/' . $new_filename;
    
    // Verify it's a real image
    if (!getimagesize($file['tmp_name'])) {
        throw new Exception('Invalid image file');
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $new_filename;
    }
    
    throw new Exception('File upload failed');
}

private function sanitize_input($data, $type = 'string') {
    switch($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
        default:
            return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }
}

private function validate_token() {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_POST['csrf_token']) || 
        $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }
}


private function generate_csrf_token() {
	if (!isset($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

private function verify_csrf_token($token) {
	if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
		return false;
	}
	return true;
}


private function secure_session_start() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

private function escape_template($string) {
    // Remove potential Twig/template syntax
    $string = preg_replace('/{[{%].*?[%}]}/', '', $string);
    // Escape special characters
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}



}









		





	

    
	

		
	
	