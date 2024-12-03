<!DOCTYPE html>
<html lang="en">
<?php session_start() ?>
<?php 
    if(!isset($_SESSION['login_id']))
        header('location:stafflogin.php');
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
    <?php include 'stafftopbar.php' ?>
    <?php include 'staff-ceas/sidebar.php' ?>  <!-- Hardcoded to staff sidebar -->

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
                    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
                    if(!file_exists('staff/'.$page.".php")){
                        include '404.html';
                    } else {
                        include 'staff/'.$page.'.php';
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

        <div class="modal fade" id="uni_modal" role='dialog'>
            <div class="modal-dialog modal-md" role="document">
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

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2024 <a href="">Pasilang</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b><?php echo $_SESSION['system']['name'] ?></b>
        </div>
    </footer>
</div>

<?php include 'footer.php' ?>
</body>
</html>