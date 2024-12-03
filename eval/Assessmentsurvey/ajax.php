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

if($action == "set_role"){
    $_SESSION['user_role'] = $_POST['role'];
    exit;
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

//staff_cme
if($action == 'login4_cme'){
    $login = $crud->login4_cme();
    if($login)
        echo $login;
}

if($action == 'logout4_cme'){
    $logout = $crud->logout4_cme();
    if($logout)
        echo $logout;
}

if($action == 'logout4_ceas'){
    $logout = $crud->logout4_ceas();
    if($logout)
        echo $logout;
}

//staff_coe
if($action == 'login4_coe'){
    $login = $crud->login4_coe();
    if($login)
        echo $login;
}



if($action == 'logout4_coe'){
    $logout = $crud->logout4_coe();
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

if($action == "mark_notification_read"){
    echo $crud->mark_notification_read();
}
if($action == "mark_all_notifications_read"){
    echo $crud->mark_all_notifications_read();
}
if($action == "delete_all_notifications"){
    echo $crud->delete_all_notifications();
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



//10/11/2024
if($action == 'save_assignment') {
    $result = $crud->save_assignment();
    echo $result;
}

if($action == 'load_assignments') {
    $get = $crud->load_assignments();
    echo $get;
}

if($action == 'delete_assignment') {
    $result = $crud->delete_assignment();
    echo $result;
}

if($action == 'check_assignment') {
    $result = $crud->check_assignment();
    echo $result;
}

if($action == 'load_assignments_grid') {
    $result = $crud->load_assignments_grid();
    echo $result;
}

//November 11,2024

if($action == 'load_assignments_faculty') {
    $get = $crud->load_assignments_faculty();  // Changed from load_assignments()
    echo $get;
}

//cme

if($action == 'load_assignments_faculties_cme') {
    $get = $crud->load_assignments_faculties_cme();  // Changed from load_assignments()
    echo $get;
}

if($action == 'delete_assignment_faculty') {
    $result = $crud->delete_assignment_faculty();  // Changed from delete_assignment()
    echo $result;
}

if($action == 'save_assignment_faculty') {
    $result = $crud->save_assignment_faculty();  // Changed from save_assignment()
    echo $result;
}

if($action == 'check_assignment_faculty') {
    $result = $crud->check_assignment_faculty();  // Changed from check_assignment()
    echo $result;
}
if($action == 'update_assignment_faculty') {
    $result = $crud->update_assignment_faculty();
    echo $result;
}
if($action == 'get_assignment_faculty') {
    $result = $crud->get_assignment_faculty();
    echo $result;
}

//November 12,2024
if($action == 'get_departments_faculty') {
    $result = $crud->get_departments_faculty();
    echo $result;
}

if($action == 'get_faculty_by_department') {
    $result = $crud->get_faculty_by_department();
    echo $result;
}

if($action == 'get_classes_by_department') {
    $result = $crud->get_classes_by_department();
    echo $result;
}
if($action == 'get_subjects_by_department') {
    $result = $crud->get_subjects_by_department();
    echo $result;
}
if($action == 'load_assignments_faculties') {
    $result = $crud->load_assignments_faculties();
    echo $result;
}

if($action == 'get_dashboard_stats') {
    $result = $crud->get_dashboard_stats();
    echo $result;
}

if($action == 'get_dashboard_stats_cot') {
    $result = $crud->get_dashboard_stats_cot();
    echo $result;
}

//November 18,2024 || Classroom

if($action == 'add_student_to_class') {
    $result = $crud->add_student_to_class();
    echo $result;
}

if($action == 'get_class_details') {
    $class_id = $_POST['class_id'];
    $result = $crud->get_class_details($class_id);
    echo $result;
}





if($action == "check_academic_year_subjects") {
    $academic = $crud->check_academic_year_subjects();
    if($academic)
        echo $academic;
}

if($action == 'delete_from_class'){
    $result = $crud->delete_from_class();
    echo $result;
    exit;
}

//Add_subjects_to_class.php

// Add this to your existing ajax.php file
if($action == 'save_class_subject'){
    $result = $crud->save_class_subject();
    echo $result;
}

if($action == 'delete_class_subject'){
    $result = $crud->delete_class_subject();
    echo $result;
}

if($action == 'get_class_assignments'){
    $crud->get_class_assignments();
}








//November 19,2024 for faculty dashboard, home

if ($action == 'get_students') {
    $faculty_id = $_POST['faculty_id'];
    $academic_id = $_SESSION['academic']['id'];
    echo $crud->get_students($faculty_id, $academic_id);
}




if($action == 'get_performance_overview'){
    $performance = $crud->get_performance_overview();
    if($performance)
        echo $performance;
}

if ($action == 'get_department_list') {
    $departments = $crud->get_department_list();
    if ($departments) {
        echo $departments;
    }
}

//Classroom.php


if($action == 'get_classroom_details') {
    $classroom_details = $crud->get_classroom_details();
    if($classroom_details)
        echo $classroom_details;
}

//live Classroom

if($action == 'save_classroom_assignment') {
    $save = $crud->save_classroom_assignment();
    if($save)
        echo $save;
}

if($action == 'get_class_students') {
    $students = $crud->get_class_students();
    if($students)
        echo $students;
}

if($action == 'get_department_resources') {
    $resources = $crud->get_department_resources();
    if($resources)
        echo $resources;
}

//add new students modified

if($action == 'get_classes_by_department_modified') {
    $classes = $crud->get_classes_by_department_modified();
    if($classes)
        echo $classes;
}

//add new student modified_cme
if($action == 'get_classes_by_department_modified_cme') {
    $classes = $crud->get_classes_by_department_modified_cme();
    if($classes)
        echo $classes;
}



//status-evaluation.php

if($action == 'get_evaluation_status') {
    $result = $crud->get_evaluation_status();
    echo $result;
}

if($action == 'get_evaluation_status_cme') {
    $result = $crud->get_evaluation_status_cme();
    echo $result;
}

if($action == 'get_evaluation_status_coe') {
    $result = $crud->get_evaluation_status_coe();
    echo $result;
}
if($action == 'get_evaluation_status_ceas') {
    $result = $crud->get_evaluation_status_ceas();
    echo $result;
}

if ($_GET['action'] == 'get_sections_by_department') {
    $result = $crud->get_sections_by_department();
    if($result)
        echo $result;
}

//faculty-section. student

if($action == "get_student_details_faculty"){
    echo $crud->get_student_details_faculty();
}

//completed evaluations
if($action == 'get_completed_evaluations'){
    $result = $crud->get_completed_evaluations();
    echo $result;
}

if($action == 'get_evaluation_details'){
    $result = $crud->get_evaluation_details();
    echo $result;
} 



//December 1 modify staff 3 departments


if($action == 'update_staff_ceas'){
	$save = $crud->update_staff_ceas();
	if($save)
		echo $save;
}
if($action == 'delete_staff_ceas'){
	$save = $crud->delete_staff_ceas();
	if($save)
		echo $save;
}


if($action == 'update_staff_cme'){
	$save = $crud->update_staff_cme();
	if($save)
		echo $save;
}
if($action == 'delete_staff_cme'){
	$save = $crud->delete_staff_cme();
	if($save)
		echo $save;
}

if($action == 'update_staff_coe'){
	$save = $crud->update_staff_coe();
	if($save)
		echo $save;
}
if($action == 'delete_staff_coe'){
	$save = $crud->delete_staff_coe();
	if($save)
		echo $save;
}


//save staff 3 departments

if($action == 'save_staff_cme'){
    $save = $crud->save_staff_cme();
    if($save)
        echo $save;
}
if($action == 'save_staff_coe'){
    $save = $crud->save_staff_coe();
    if($save)
        echo $save;
}
if($action == 'save_staff_ceas'){
    $save = $crud->save_staff_ceas();
    if($save)
        echo $save;
}


//get assignments








ob_end_flush();
?>
