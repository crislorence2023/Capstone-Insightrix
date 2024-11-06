<?php include 'db_connect.php' ?>
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_staff">
                    <i class="fa fa-plus"></i> Add New Staff
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="staff-container mb-4">
                <table class="table table-hover table-bordered staff-table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $staff_query = $conn->prepare("SELECT *, concat(firstname,' ',lastname) as name 
                            FROM staff ORDER BY concat(firstname,' ',lastname) ASC");
                        $staff_query->execute();
                        $result = $staff_query->get_result();
                        while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <th class="text-center"><?php echo $i++; ?></th>
                            <td><b><?php echo htmlspecialchars($row['name']); ?></b></td>
                            <td><b><?php echo htmlspecialchars($row['email']); ?></b></td>
                            <td><b><?php echo date("M d, Y", strtotime($row['date_created'])); ?></b></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item view_staff" href="javascript:void(0)" 
                                           data-id="<?php echo $row['id'] ?>">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="./index.php?page=edit_staff&id=<?php echo $row['id'] ?>">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <?php if($_SESSION['login_id'] != $row['id']): ?>
                                        <a class="dropdown-item delete_staff" href="javascript:void(0)"
                                           data-id="<?php echo $row['id'] ?>"
                                           data-name="<?php echo htmlspecialchars($row['name']) ?>">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uni_modal" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
</div>

<script>
$(document).ready(function(){
    // Initialize DataTable
    let staffTable = $('.staff-table').DataTable({
        "order": [[ 1, "asc" ]],
        "pageLength": 25,
        "columnDefs": [
            { "orderable": false, "targets": 4 }
        ],
        "responsive": true,
        "language": {
            "search": "Search staff:",
            "emptyTable": "No staff members found"
        }
    });
    

    // Delete Staff with confirmation
    $(document).on('click', '.delete_staff', function(){
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Staff Member',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                delete_staff(id);
            }
        });
    });
});

function delete_staff(id) {
    $.ajax({
        url: 'ajax.php?action=delete_staff',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        beforeSend: function() {
            start_load();
        },
        success: function(response) {
            if(response.status == 'success') {
                alert_toast("Staff member successfully deleted", 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alert_toast(response.message || "Failed to delete staff member", 'error');
            }
        },
        error: function() {
            alert_toast("Server error occurred", 'error');
        },
        complete: function() {
            end_load();
        }
    });
}
</script>