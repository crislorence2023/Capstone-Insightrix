<?php include'db_connect.php' ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-bold">User Management</h5>
                    <button class="new-subject-btn" onclick="window.location.href='./index.php?page=new_user'">
                        <i class="fas fa-plus me-2"></i>Add New User
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="list">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="35%">Name</th>
                                    <th width="35%">Email</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by concat(firstname,' ',lastname) asc");
                                while($row= $qry->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td><?php echo ucwords($row['name']) ?></td>
                                    <td><?php echo $row['email'] ?></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-action btn-info btn-sm view_user" 
                                                    data-id='<?php echo $row['id'] ?>'
                                                    title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-action btn-primary btn-sm" 
                                                    onclick="window.location.href='./index.php?page=edit_user&id=<?php echo $row['id'] ?>'"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-action btn-danger btn-sm delete_user" 
                                                    data-id="<?php echo $row['id'] ?>"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
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
    </div>
</div>

<script>
    $(document).ready(function(){
        // Initialize DataTables with custom configuration
        $('#list').DataTable({
            pageLength: 10,
            responsive: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search users..."
            }
        });

        $('.view_user').click(function(){
            uni_modal("<i class='fa fa-id-card'></i> User Details","view_user.php?id="+$(this).attr('data-id'))
        })

        $('.delete_user').click(function(){
            _conf("Are you sure to delete this user?","delete_user",[$(this).attr('data-id')])
        })
    })
    function delete_user($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_user',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)

                }
            }
        })
    }
</script>

<style>
    /* Button Styling */
    .new-subject-btn {
        background: #0d6efd;
        border: none;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        margin-left: auto;
    }
    
    .new-subject-btn:hover {
        background: #43A047;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        transform: translateY(-2px);
    }

    /* Action Button Styling */
    .btn-action {
        border-radius: 6px;
        margin: 0 2px;
        padding: 0.375rem 0.75rem;
        transition: all 0.2s;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }

    .d-flex.gap-1 {
        gap: 0.5rem !important;
    }

    /* Mobile Responsive Styles */
    @media screen and (max-width: 768px) {
        .new-subject-btn {
            margin-left: 0;
            width: 50%;
            margin-top: 0.5rem;
            font-size: 15px;
        }
    }

    /* Add these styles to match the subject list table */
    .table {
        border-radius: 8px;
        overflow: hidden;
        width: 100% !important;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #212529;
        font-weight: 600;
        white-space: nowrap;
    }

    .table td {
        font-size: 0.95rem;
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Mobile Responsive Styles */
    @media screen and (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
            scroll-behavior: smooth;
        }

        .table-responsive::before,
        .table-responsive::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 30px;
            z-index: 2;
            pointer-events: none;
        }

        .table-responsive::before {
            left: 0;
            background: linear-gradient(to right, rgba(255,255,255,0.9), rgba(255,255,255,0));
        }

        .table-responsive::after {
            right: 0;
            background: linear-gradient(to left, rgba(255,255,255,0.9), rgba(255,255,255,0));
        }

        .table {
            width: 100% !important;
            margin-bottom: 0;
        }

        .table th,
        .table td {
            white-space: nowrap;
            min-width: 100px;
        }
    }
</style>