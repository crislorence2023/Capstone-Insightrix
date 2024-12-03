<!DOCTYPE html>
<html lang="en">
<?php session_start() ?>
<?php 
    if(!isset($_SESSION['login_id']))
        header('location:stafflogin_coe.php');
    include 'db_connect.php';
    ob_start();
    if(!isset($_SESSION['system'])){
        $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
        foreach($system as $k => $v){
            $_SESSION['system'][$k] = $v;
        }
    }
    ob_end_flush();
    include 'header.php' 
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include 'staff_coe_topbar.php' ?>
    <?php include 'staff-coe/coesd.php' ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="background-color: #FAFEFF;">
        <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body text-white">
            </div>
        </div>
        <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>
        
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php 
                    // Define whitelist based on all navigation links in the sidebar
                    $whitelist = [
                        // General Section
                        'home',
                        'evaluation-status',
                        'Room',
                        
                        // Academic Section
                        'subject_list',
                        'class_list',
                        
                        // Faculty Section
                        'new_faculty',
                        'faculty_list',
                        
                        // Student Section
                        'new_student',
                        'student_list',
                        
                        // Assignment Section
                        'AssignClassSubjects'
                    ];
                    
                    $blacklist = [
                        '../',          // Block directory traversal attempts
                        'config',       // Block access to configuration files
                        'log',          // Block access to log files
                        'tmp',          // Block access to temporary files
                        'cache',        // Block access to cache files
                        '.php',         // Block direct PHP file access attempts
                        '.ini',         // Block access to ini files
                        '.env',         // Block access to environment files
                        'backup',       // Block access to backup files
                        'admin',        // Block direct admin directory access
                        'includes',     // Block access to includes directory
                        'upload',       // Block direct access to upload directory
                        'system',       // Block access to system files
                        'database',     // Block database related files
                        'sql',          // Block SQL files
                        'install',      // Block installation files
                        'setup',        // Block setup files
                        'phpinfo',      // Block phpinfo access
                        'shell',        // Block shell access
                        'cmd',          // Block command execution
                        'exec',         // Block execution attempts
                        'closed',
                        'Completed Eval',
                        'done',
                        'evaluate',
                        'topbar',
                        'closed',
                        'done',
                        'result',
                        'StudentList',
                    ];

                    $page = isset($_GET['page']) ? $_GET['page'] : 'home';

                    // Check if the page is in the whitelist and not in the blacklist
                    if(in_array($page, $whitelist) && !in_array($page, $blacklist)) {
                        if(!file_exists('staff-coe/'.$page.".php")){
                            include '404.html';
                        } else {
                            include 'staff-coe/'.$page.'.php';
                        }
                    } else {
                        include '404.html'; // Show 404 if page is not allowed
                    }
                ?>
            </div>
        </section>

        <!-- Modals -->
        <div class="modal fade" id="confirm_modal" role='dialog'>
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmation</h5>
                    </div>
                    <div class="modal-body">
                        <div id="delete_content"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uni_modal" role='dialog' style="z-index: 99999;">
            <div class="modal-dialog modal-md" role="document" style="margin-top: 2rem;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uni_modal_right" role='dialog'>
            <div class="modal-dialog modal-full-height modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span class="fa fa-arrow-right"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="viewer_modal" role='dialog'>
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
                    <img src="" alt="">
                </div>
            </div>
        </div>
    </div>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
    </aside>
</div>

<?php include 'footer.php' ?>
</body>
</html>