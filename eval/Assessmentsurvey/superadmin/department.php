<?php
require_once 'db_connect.php';

// Delete Operation
if(isset($_POST['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['delete_id']);
    $delete = $conn->query("DELETE FROM department_list WHERE id = '$id'");
    if($delete) {
        $msg = "<div class='alert alert-success'>Department deleted successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error deleting department!</div>";
    }
}

// Update Operation
if(isset($_POST['update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['edit_id']);
    $name = mysqli_real_escape_string($conn, $_POST['edit_name']);
    $description = mysqli_real_escape_string($conn, $_POST['edit_description']);
    
    $check = $conn->query("SELECT * FROM department_list WHERE name = '$name' AND id != '$id'");
    if($check->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>Department name already exists!</div>";
    } else {
        $update = $conn->query("UPDATE department_list SET name = '$name', description = '$description' WHERE id = '$id'");
        if($update) {
            $msg = "<div class='alert alert-success'>Department updated successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error updating department!</div>";
        }
    }
}

// Add Operation
if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $check = $conn->query("SELECT * FROM department_list WHERE name = '$name'");
    if($check->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>Department already exists!</div>";
    } else {
        $insert = $conn->query("INSERT INTO department_list (name, description) VALUES ('$name', '$description')");
        if($insert) {
            $msg = "<div class='alert alert-success'>Department added successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error adding department!</div>";
        }
    }
}

// Fetch departments
$departments = $conn->query("SELECT * FROM department_list ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary-rgb: 13, 110, 253;
            --bs-body-bg: #f8f9fa;
        }
        
        body {
           
            font-family: 'Montserrat', sans-serif;
        }
        
        .page-header {
           
            border-bottom: 1px solid rgba(var(--bs-primary-rgb), 0.1);
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .card {
            border: none;
            
        }
        
        
        
        .department-form {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.25rem;
            border-radius: 0.25rem;
            transition: transform 0.2s;
        }
        
        .btn-action:hover {
            transform: translateY(-1px);
        }
        
        .alert {
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .modal-content {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .datatable-card {
            padding: 1.5rem;
            background-color: white;
        }
        
        .dataTables_wrapper .dataTables_length select {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
        }
        .dashboard-container {
            padding: 2rem;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .header-card {
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            background: transparent;
        }

        .card-title {
            color:#1F2D3D;
            font-size: 20px;
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-floating > .form-control {
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .form-floating > .form-control:focus {
            border-color: #2193b0;
            box-shadow: 0 0 0 3px rgba(33, 147, 176, 0.1);
        }

        .btn-primary {
            background: #0D6EFD;
            border: none;
            border-radius: 10px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.25rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            color: #2c3e50;
            font-weight: 600;
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
            color: white;
            border: none;
        }

        .modal-header .btn-close {
            color: white;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .header-content {
                text-align: center;
            }
        }
        
        @media (max-width: 768px) {
            .table-responsive-md {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            #departmentTable {
                white-space: nowrap;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
   

        <div class="content-card">
            <!-- Add Department Form -->
            <div class="card-header">
                <h5 class="card-title">Add New Department</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="addDepartmentForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nameInput" name="name" required>
                                <label for="nameInput">Department Name</label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="descriptionInput" name="description">
                                <label for="descriptionInput">Description</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="submit" class="btn btn-primary h-100 w-100">
                                <i class="fas fa-plus me-2"></i>Add Department
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Departments Table -->
        <div class="content-card mt-4">
            <div class="card-header">
                <h5 class="card-title">Department List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive-md">
                    <table id="departmentTable" class="table table-hover mb-0 w-100">
                        <thead>
                            <tr>
                                <th class="px-4" style="min-width: 50px">#</th>
                                <th style="min-width: 150px">Department</th>
                                <th style="min-width: 200px">Description</th>
                                <th style="min-width: 120px">Date Added</th>
                                <th style="min-width: 100px" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1;
                            while($row = $departments->fetch_assoc()): 
                            ?>
                            <tr>
                                <td class="px-4"><?php echo $i++; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['date_created'])); ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-action" 
                                            onclick="editDepartment('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name']); ?>', '<?php echo htmlspecialchars($row['description']); ?>')"
                                            data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-action" 
                                            onclick="deleteDepartment('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name']); ?>')"
                                            data-bs-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="edit_name" id="edit_name" placeholder="Department Name" required>
                            <label>Department Name</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" name="edit_description" id="edit_description" placeholder="Description">
                            <label>Description</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="delete_id" id="delete_id">
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#departmentTable').DataTable({
                "responsive": false,
                "scrollX": true,
                "lengthChange": true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "order": [[1, 'asc']], // Sort by department name by default
                "columnDefs": [
                    { "orderable": false, "targets": 4 } // Disable sorting for actions column
                ]
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        // Edit Department
        function editDepartment(id, name, description) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        // Delete Department
        function deleteDepartment(id, name) {
            if(confirm('Are you sure you want to delete the department "' + name + '"?\nThis action cannot be undone.')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>