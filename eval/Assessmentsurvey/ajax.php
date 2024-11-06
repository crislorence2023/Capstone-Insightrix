

<?php
ob_start();
date_default_timezone_set("Asia/Manila");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();


if($action == 'login'){ 
    $login = $crud->login(); 
    if($login == 1) {
        echo 1; // Successful login
    } elseif($login == 2) {
        echo 2; // Login failed
    } elseif($login == 3) {
        echo 3; // Password change required
    }
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}


//superadmin
if($action == 'login3'){
	$login = $crud->login3();
	if($login)
		echo $login;
}


//staff
if($action == 'login4'){
    $login = $crud->login4();
    if($login)
        echo $login;
}

if($action == 'logout4'){
    $logout = $crud->logout4();
    if($logout)
        echo $logout;
}



if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}


//superadmin
if($action == 'logout3'){
	$logout = $crud->logout3();
	if($logout)
		echo $logout;
}




if($action == 'logout_change_password'){ 
    $logout = $crud->logout_change_password(); 
    if($logout) 
        echo $logout; 
}

if($action == 'change_password'){
    $change_password = $crud->change_password();
    if($change_password)
        echo $change_password;
} 




if($action == 'verify_email'){
    $save = $crud->verfiy_email();
    if($save)
        echo $save;
}


if($action == 'forgot_password'){
    $save = $crud->forgot_password();
    if($save)
        echo $save;
}

if($action == 'verify_reset_code'){
    $save = $crud->verify_reset_code();
    if($save)
        echo $save;
}

if($action == 'reset_password'){
    $save = $crud->reset_password();
    if($save)
        echo $save;
}


//06//10/2024
if($action == 'delete_evaluation'){
    $delete = $crud->delete_evaluation();
    if($delete)
        echo $delete;
}
elseif($action == 'delete_multiple_evaluations'){
    $delete = $crud->delete_multiple_evaluations();
    if($delete)
        echo $delete;
}

if($action == 'send_verification'){
    $result = $crud->send_verification();
    echo $result;
}

if($action == 'verify_code'){
    $result = $crud->verify_code();
    echo $result;
}
if($action == 'forgot_update_password'){
    $update = $crud->forgot_update_password();
    if($update)
        echo $update;
}




if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'update_user'){
	$save = $crud->update_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'save_subject'){
	$save = $crud->save_subject();
	if($save)
		echo $save;
}
if($action == 'delete_subject'){
	$save = $crud->delete_subject();
	if($save)
		echo $save;
}
if($action == 'save_class'){
	$save = $crud->save_class();
	if($save)
		echo $save;
}
if($action == 'delete_class'){
	$save = $crud->delete_class();
	if($save)
		echo $save;
}
if($action == 'save_academic'){
	$save = $crud->save_academic();
	if($save)
		echo $save;
}
if($action == 'delete_academic'){
	$save = $crud->delete_academic();
	if($save)
		echo $save;
}
if($action == 'make_default'){
	$save = $crud->make_default();
	if($save)
		echo $save;
}



if($action == 'get_criteria'){
    $crud->get_criteria();
}





if($action == 'save_criteria'){
	$save = $crud->save_criteria();
	if($save)
		echo $save;
}
if($action == 'delete_criteria'){
	$save = $crud->delete_criteria();
	if($save)
		echo $save;
}
if($action == 'save_question'){
	$save = $crud->save_question();
	if($save)
		echo $save;
}
if($action == 'delete_question'){
	$save = $crud->delete_question();
	if($save)
		echo $save;
}

if($action == 'save_criteria_question'){
	$save = $crud->save_criteria_question();
	if($save)
		echo $save;
}
if($action == 'save_criteria_order'){
	$save = $crud->save_criteria_order();
	if($save)
		echo $save;
}

if($action == 'save_question_order'){
    $save = $crud->save_question_order();
    if($save)
		echo $save;
}
if($action == 'save_faculty'){
	$save = $crud->save_faculty();
	if($save)
		echo $save;
}
if($action == 'delete_faculty'){
	$save = $crud->delete_faculty();
	if($save)
		echo $save;
}
if($action == 'save_student'){
	$save = $crud->save_student();
	if($save)
		echo $save;
}
if($action == 'delete_student'){
	$save = $crud->delete_student();
	if($save)
		echo $save;
}
if($action == 'save_restriction'){
	$save = $crud->save_restriction();
	if($save)
		echo $save;
}
if($action == 'save_evaluation'){
	$save = $crud->save_evaluation();
	if($save)
		echo $save;
}

if($action == 'get_class'){
	$get = $crud->get_class();
	if($get)
		echo $get;
}
if($action == 'get_report'){
	$get = $crud->get_report();
	if($get)
		echo $get;
}



if($action == 'save_comment'){
    $save = $crud->save_comment();
 if($save)
       echo $save;
}

if($action == 'load_comments'){
    $comments = $crud->load_comments();
    echo $comments;
}

if($action == 'get_previous_semester_data'){
    $get = $crud->get_previous_semester_data();
    if($get)
        echo $get;
}
if($action == 'get_all_subject_ratings'){
    $save = $crud->get_all_subject_ratings();
    if($save)
        echo $save;
}
if($action == 'get_notifications'){
    $save = $crud->get_notifications();
    if($save)
        echo $save;
}

if($action == 'mark_notification_read'){
    $save = $crud->mark_notification_read();
    if($save)
        echo $save;
}

if($action == 'mark_all_notifications_read'){
    $save = $crud->mark_all_notifications_read();
    if($save)
        echo $save;
}


//staff

if($action == 'save_staff'){
	$save = $crud->save_staff();
	if($save)
		echo $save;
}
if($action == 'update_staff'){
	$save = $crud->update_staff();
	if($save)
		echo $save;
}
if($action == 'delete_staff'){
	$save = $crud->delete_staff();
	if($save)
		echo $save;
}

if($action == "send_evaluation_to_all"){
    $result = $crud->send_evaluation_to_all_students();
    if($result == 4) {
        echo 3; // No matching departments found - reuse existing error code
    } else {
        echo $result;
    }
}

if($action == 'get_evaluation_list'){
    $list = $crud->get_evaluation_list();
    if($list)
        echo $list;
}

if($action == "delete_restriction"){
    echo $crud->delete_restriction();
}

if($action == "check_academic_year") {
    $academic = $crud->check_academic_year();
    if($academic)
        echo $academic;
}


if($action == 'get_student_surveys'){
    $result = $crud->get_student_surveys();
    echo $result;
}


if($action == 'delete_department'){
	$save = $crud->delete_department();
	if($save)
		echo $save;
}




if($action == 'get_performance_overview'){
    $performance = $crud->get_performance_overview();
    if($performance)
        echo $performance;
}
ob_end_flush();
?>
