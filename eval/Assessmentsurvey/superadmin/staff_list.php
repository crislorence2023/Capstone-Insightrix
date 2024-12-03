<?php include 'db_connect.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Montserrat', sans-serif;
           
        }

       

        .card {
            margin-top: 1rem;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 1rem;
            margin-bottom: 1rem;
        }

        .card-header {
            border-top-right-radius: 1rem !important;
            border-top-left-radius: 1rem !important;
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.5rem;
        }

        .card-header h6 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #4a5568;
            padding: 1.25rem 1rem;
        }

        .table td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .btn-group-sm > .btn {
            padding: 0.375rem 0.75rem;
            font-weight: 500;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            transition: all 0.2s;
            margin: 0 0.25rem;
        }

        .btn-icon i {
            font-size: 0.875rem;
        }

        .btn-light:hover {
            background-color: #e2e6ea;
        }

        .btn-primary {
            font-weight: 500;
            padding: 0.5rem 1rem;
            letter-spacing: 0.3px;
        }

        /* DataTables Customization */
        .dataTables_wrapper {
            padding: 1.5rem;
        }

        .dataTables_length label {
            margin-bottom: 0;
            font-weight: 500;
        }

        .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            transition: border-color 0.15s ease-in-out;
            font-family: 'Montserrat', sans-serif;
        }

        .dataTables_filter input:focus {
            border-color: #4e73df;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .dataTables_info {
            color: #6c757d;
            padding-top: 1rem;
        }

        .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
            font-weight: 500;
        }

        .page-link {
            padding: 0.5rem 0.75rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 0.2em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }

        /* Modal Customization */
        .modal-content {
            border-radius: 0.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .m-0{
    margin: 0;
    color:#1F2D3D;
            font-size: 20px;
            font-weight: 600;
}

        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem;
               
            }

            .table td, .table th {
                padding: 1rem;
            }

            .avatar-circle {
                width: 36px;
                height: 36px;
            }

            .dataTables_wrapper {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="card">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Staff Management</h6>
                <a class="btn btn-primary btn-sm" href="./indexsuperadmin.php?page=new_staff">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Staff
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover staff-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%">#</th>
                            <th style="width: 25%">Name</th>
                            <th style="width: 25%">Email</th>
                            <th style="width: 15%">Department</th>
                            <th style="width: 15%">Date Created</th>
                            <th class="text-center" style="width: 15%">Actions</th>
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
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-medium"><?php echo htmlspecialchars($row['name']); ?></span>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" 
                                   class="text-decoration-none text-secondary">
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <?php echo $row['department'] ? htmlspecialchars($row['department']) : '<em>Not assigned</em>'; ?>
                                </span>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt mr-2 text-muted"></i>
                                <?php echo date("M d, Y", strtotime($row['date_created'])); ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-icon btn-light view_staff" 
                                            data-id="<?php echo $row['id'] ?>" 
                                            title="View Details">
                                        <i class="fas fa-eye text-primary"></i>
                                    </button>
                                    <a href="./index.php?page=edit_staff&id=<?php echo $row['id'] ?>" 
                                       class="btn btn-icon btn-light" 
                                       title="Edit Staff">
                                        <i class="fas fa-edit text-info"></i>
                                    </a>
                                    <?php if($_SESSION['login_id'] != $row['id']): ?>
                                    <button type="button" class="btn btn-icon btn-light delete_staff"
                                            data-id="<?php echo $row['id'] ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']) ?>"
                                            title="Delete Staff">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>
                                    <?php endif; ?>
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

<!-- Modal -->
<div class="modal fade" id="uni_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-body p-4">
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.js"></script>

<script>
$(document).ready(function(){
    // Initialize DataTable
    let staffTable = $('.staff-table').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "order": [[1, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": 5 },
            { "width": "5%", "targets": 0 },
            { "width": "25%", "targets": 1 },
            { "width": "25%", "targets": 2 },
            { "width": "15%", "targets": 3 },
            { "width": "15%", "targets": 4 },
            { "width": "15%", "targets": 5 }
        ],
        "responsive": true,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search staff members...",
            "lengthMenu": "_MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": '<i class="fas fa-angle-double-left"></i>',
                "last": '<i class="fas fa-angle-double-right"></i>',
                "next": '<i class="fas fa-angle-right"></i>',
                "previous": '<i class="fas fa-angle-left"></i>'
            }
        },
        "autoWidth": false,
        "dom": '<"row align-items-center"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
    });

    // View Staff Details
    $(document).on('click', '.view_staff', function(){
        let id = $(this).data('id');
        uni_modal('Staff Details', 'view_staff.php?id=' + id);
    });

    // Delete Staff
    $(document).on('click', '.delete_staff', function(){
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Staff Member?',
            html: `
                <p class="mb-2">You are about to delete:</p>
                <p class="font-weight-bold text-danger mb-3">${name}</p>
                <p class="text-muted small">This action cannot be undone.</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>Delete',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger mr-2',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                deleteStaff(id);
            }
        });
    });

    // Handle window resize
    $(window).on('resize', function() {
        staffTable.columns.adjust();
    });
});

// Delete Staff Function
function deleteStaff(id) {
    $.ajax({
        url: 'ajax.php?action=delete_staff',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'Deleting...',
                html: '<div class="loading-spinner"></div>',
                showConfirmButton: false,
                allowOutsideClick: false
            });
        },
        success: function(response) {
            if(response.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Staff member has been successfully deleted.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to delete staff member'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'An unexpected error occurred. Please try again later.'
            });
        }
    });
}

// Handle Modal
function uni_modal(title, url) {
    $('#uni_modal .modal-title').html(title);
    $('#uni_modal .modal-body').html(`<div class="text-center"><div class="loading-spinner"></div></div>`);
    $('#uni_modal').modal('show');
    $.ajax({
        url: url,
        success: function(response) {
            $('#uni_modal .modal-body').html(response);
        },
        error: function() {
            $('#uni_modal .modal-body').html(`
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <p>Failed to load content. Please try again.</p>
                </div>
            `);
        }
    });
}
</script>

</body>
</html>