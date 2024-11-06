<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
    $qry = $conn->prepare("SELECT *, CONCAT(firstname, ' ', lastname) as name FROM staff WHERE id = ?");
    $qry->bind_param("i", $_GET['id']);
    $qry->execute();
    $result = $qry->get_result();
    $row = $result->fetch_array();
    foreach($row as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="card card-widget widget-user shadow">
        <div class="widget-user-header bg-dark">
            <h3 class="widget-user-username"><?php echo ucwords($name) ?></h3>
            <h5 class="widget-user-desc"><?php echo htmlspecialchars($email) ?></h5>
        </div>
        <div class="widget-user-image">
            <?php if($avatar == 'no-image-available.png' || (!empty($avatar) && !is_file('assets/uploads/'.$avatar))): ?>
            <span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px;height:90px">
                <h4><?php echo strtoupper(substr($firstname, 0,1).substr($lastname, 0,1)) ?></h4>
            </span>
            <?php else: ?>
            <img class="img-circle elevation-2" src="assets/uploads/<?php echo $avatar ?>" alt="User Avatar" style="width: 90px;height:90px;object-fit: cover">
            <?php endif ?>
        </div>
        <div class="card-footer">
            <div class="container-fluid">
                <dl>
                    <dt>Staff ID</dt>
                    <dd><?php echo $id ?></dd>
                    <dt>Name</dt>
                    <dd><?php echo ucwords($firstname.' '.$lastname) ?></dd>
                    <dt>Email</dt>
                    <dd><?php echo htmlspecialchars($email) ?></dd>
                    <dt>Date Created</dt>
                    <dd><?php echo date("F d, Y h:i A", strtotime($date_created)) ?></dd>
                    <dt>Account Status</dt>
                    <dd>
                        <?php if($is_password_changed == 1): ?>
                            <span class="badge badge-success">Password Changed</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Default Password</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer display p-0 m-0">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

<style>
    .widget-user-header {
        padding: 20px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    
    .widget-user-username {
        margin-bottom: 5px;
        font-size: 25px;
        font-weight: 600;
        color: #fff;
    }
    
    .widget-user-desc {
        margin-top: 0;
        color: #fff;
    }
    
    .widget-user-image {
        position: absolute;
        top: 65px;
        left: 50%;
        margin-left: -45px;
    }
    
    .card-footer {
        padding-top: 40px;
    }
    
    dl {
        margin-bottom: 0;
    }
    
    dt {
        font-weight: 600;
    }
    
    dd {
        margin-bottom: 15px;
    }
</style>