<?php include 'db_connect.php' ?>
<div class="col-lg-12">
    <div class="card shadow-sm rounded-lg">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Academic Year Management</h5>
                <button class="btn btn-primary new_academic">
                    <i class="fa fa-plus mr-2"></i>Add New Academic Year
                </button>
            </div>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <table class="table table-hover" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="25%">
                        <col width="25%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th class="text-center">System Default</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM academic_list order by abs(year) desc, abs(semester) desc");
                        while($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i++ ?></td>
                            <td class="font-weight-bold"><?php echo $row['year'] ?></td>
                            <td class="font-weight-bold"><?php echo $row['semester'] ?></td>
                            <td class="text-center">
                                <?php if($row['is_default'] == 0): ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm make_default" data-id="<?php echo $row['id'] ?>">
                                        Set Default
                                    </button>
                                <?php else: ?>
                                    <span class="badge badge-primary px-3 py-2">Default</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $status_class = '';
                                $status_text = '';
                                switch($row['status']) {
                                    case 0:
                                        $status_class = 'badge-secondary';
                                        $status_text = 'Not Started';
                                        break;
                                    case 1:
                                        $status_class = 'badge-success';
                                        $status_text = 'In Progress';
                                        break;
                                    case 2:
                                        $status_class = 'badge-primary';
                                        $status_text = 'Closed';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $status_class ?> px-3 py-2"><?php echo $status_text ?></span>
                            </td>
                            <td class="text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-primary btn-sm manage_academic" data-id='<?php echo $row['id'] ?>' title="Edit">
            <i class="fas fa-edit text-white"></i>
        </button>
        <button type="button" class="btn btn-danger btn-sm delete_academic" data-id="<?php echo $row['id'] ?>' title="Delete">
            <i class="fas fa-trash text-white"></i>
        </button>
    </div>
</td>
                        </tr>    
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.badge {
    font-weight: 500;
    font-size: 0.85rem;
}
.btn-group .btn {
   
    padding: 0.25rem 0.5rem;
    margin: 0 8px;
    border-radius: 0.25rem !important;
}
.table td, .table th {
    vertical-align: middle;
    white-space: nowrap;
}
.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}
/* Ensure minimum width for the table */
#list {
    min-width: 800px;
}
/* Style the scrollbar */
div::-webkit-scrollbar {
    height: 8px;
}
div::-webkit-scrollbar-track {
    background: #f1f1f1;
}
div::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
div::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
$(document).ready(function(){
    // Initialize DataTable with minimal configuration
    $('#list').DataTable({
        "scrollX": false,  // Disable DataTables scrollX since we're handling it manually
        "responsive": false, // Disable responsive features
        "autoWidth": false,
        "dom": '<"top"f>rt<"bottom"lip><"clear">',
        "language": {
            "search": "<i class='fas fa-search'></i>",
            "searchPlaceholder": "Search records..."
        },
        "ordering": true,
        "info": true
    });

    // Event Handlers
    $('.new_academic').click(function(){
        uni_modal("Add New Academic Year", "<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php");
    });

    $('.manage_academic').click(function(){
        uni_modal("Edit Academic Year", "<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php?id=" + $(this).attr('data-id'));
    });

    $('.delete_academic').click(function(){
        _conf("Are you sure to delete this academic?", "delete_academic", [$(this).attr('data-id')]);
    });

    $('.make_default').click(function(){
        _conf("Are you sure to make this academic year as the system default?", "make_default", [$(this).attr('data-id')]);
    });
});

function delete_academic($id){
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_academic',
        method: 'POST',
        data: {id: $id},
        success: function(resp){
            if(resp==1){
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function(){
                    location.reload();
                }, 1500);
            }
        }
    });
}

function make_default($id){
    start_load();
    $.ajax({
        url: 'ajax.php?action=make_default',
        method: 'POST',
        data: {id: $id},
        success: function(resp){
            if(resp==1){
                alert_toast("Default Academic Year Updated", 'success');
                setTimeout(function(){
                    location.reload();
                }, 1500);
            }
        }
    });
}
</script>