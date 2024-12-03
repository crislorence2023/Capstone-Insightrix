<?php include 'db_connect.php' ?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
       :root {
    /* Colors */
    --primary-color: #0d6efd;
    --primary-hover: #43A047;
    --text-primary: #212529;
    --text-secondary: #2c3e50;
    --background-light: #f8f9fa;
    --border-color: #dee2e6;
    --shadow-color: rgba(0, 0, 0, 0.05);
    --shadow-hover: rgba(0, 0, 0, 0.1);
    --success-shadow: rgba(76, 175, 80, 0.2);
    
    /* Spacing */
    --spacing-xs: 0.375rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 0.75rem;
    --spacing-lg: 1rem;
    --spacing-xl: 1.25rem;
    --spacing-xxl: 1.5rem;
    
    /* Border radius */
    --border-radius-sm: 6px;
    --border-radius-md: 8px;
    --border-radius-lg: 10px;
    --border-radius-xl: 20px;
    
    /* Font sizes */
    --font-size-sm: 0.9em;
    --font-size-base: 0.95rem;
    --font-size-lg: 1.25rem;
    --font-size-xl: 1.5rem;
}

* {
    font-family: "Montserrat", sans-serif;
}

.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
    border-radius: var(--border-radius-xl) !important;
}

.department-container {
    background: #fff;
    border-radius: var(--border-radius-lg);
    box-shadow: 0 0 20px var(--shadow-color);
    margin-bottom: var(--spacing-xxl);
    padding: var(--spacing-xxl);
    transition: all 0.3s ease;
    width: 100%;
}

.department-container:hover {
    box-shadow: 0 0 25px var(--shadow-hover);
}

.department-header {
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: var(--spacing-xxl);
    padding-bottom: var(--spacing-lg);
}

.department-title {
    color: var(--text-primary);
    font-size: var(--font-size-xl);
    font-weight: 600;
    margin: 0;
}

.btn-action {
    border-radius: var(--border-radius-sm);
    margin: 0 2px;
    padding: var(--spacing-xs) var(--spacing-md);
    transition: all 0.2s;
}

.btn-action:hover {
    transform: translateY(-2px);
}

.new-class-btn {
    background: var(--primary-color);
    border: none;
    border-radius: var(--border-radius-sm);
    color: white;
    font-weight: 500;
    padding: var(--spacing-sm) var(--spacing-lg);
    transition: all 0.3s ease;
    margin-left: 32rem;
}

.new-class-btn:hover {
    background: var(--primary-hover);
    box-shadow: 0 4px 15px var(--success-shadow);
    transform: translateY(-2px);
}

.department-stats {
    background: var(--background-light);
    border-radius: var(--border-radius-md);
    margin-bottom: var(--spacing-lg);
    padding: var(--spacing-xl);
}

.stats-item {
    align-items: center;
    display: flex;
    justify-content: space-between;
    font-weight: 500;
}

.stats-item strong {
    font-weight: 600;
    color: var(--text-secondary);
}

.table {
    border-radius: var(--border-radius-md);
    overflow: hidden;
    width: 100% !important;
}

.table thead th {
    background-color: var(--background-light);
    border-bottom: 2px solid var(--border-color);
    color: var(--text-primary);
    font-weight: 600;
    white-space: nowrap;
}

.table td {
    font-size: var(--font-size-base);
    padding: var(--spacing-lg) var(--spacing-md);
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: var(--background-light);
}

.new-subject-btn .fa-plus {
    font-weight: 400;
    font-size: var(--font-size-sm);
    color: #555;
}

/* Mobile Responsive Styles */
@media screen and (max-width: 768px) {
    .container-fluid {
        padding: var(--spacing-sm);
    }

    .department-container {
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .department-header {
        padding-bottom: var(--spacing-sm);
        margin-bottom: var(--spacing-lg);
    }

    .department-title {
        font-size: var(--font-size-lg);
    }

    .new-class-btn {
        margin-left: 0;
        width: 50%;
        margin-top: var(--spacing-sm);
        font-size: 15px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: var(--spacing-lg);
        scroll-behavior: smooth;
    }

    .table th,
    .table td {
        white-space: nowrap;
        min-width: 100px;
    }
    
    .table td:first-child {
        min-width: 50px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: var(--spacing-lg);
        width: 100%;
    }

    .dataTables_wrapper .dataTables_filter input {
        width: 100%;
        margin-left: 0;
        margin-top: var(--spacing-sm);
    }
}
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Class Management</h5>
                        <button class="new-class-btn new_class">
                            <i class="fas fa-plus me-2"></i>Add New Class
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <?php
                        // Fetch all departments and classes
                        $departments = array();
                        $classes = array();
                        $total_classes = 0;
                        
                        $class_query = $conn->query("SELECT *, concat(curriculum,' ',level,section,' - ',schedule_type) as `class` FROM class_list WHERE department = 'COT' ORDER BY curriculum ASC, level ASC, section ASC");
                        while($class_row = $class_query->fetch_assoc()) {
                            $dept = $class_row['department'];
                            if (!in_array($dept, $departments)) {
                                $departments[] = $dept;
                            }
                            $classes[$dept][] = $class_row;
                            $total_classes++;
                        }
                        ?>
                        
                        <!-- Modified Statistics Panel -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="department-stats">
                                    <div class="stats-item">
                                        <span>Total Classes:</span>
                                        <strong><?php echo $total_classes; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // Generate tables for each department
                        foreach($departments as $dept) {
                            if (isset($classes[$dept])) {
                                ?>
                                <div class="department-container">
                                    <div class="department-header">
                                        <h6 class="department-title">
                                            <?php echo $dept != '' ? $dept . ' Department' : 'Unassigned Department'; ?>
                                        </h6>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover class-table">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="20%">Department</th>
                                                    <th width="25%">Class</th>
                                                    <th width="20%">Schedule Type</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i = 1;
                                                foreach($classes[$dept] as $row): 
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $i++ ?></td>
                                                    <td><?php echo $row['department'] ?></td>
                                                    <td><?php echo $row['curriculum'] . ' ' . $row['level'] . $row['section'] ?></td>
                                                    <td><?php echo $row['schedule_type'] ?></td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <button class="btn btn-action btn-primary btn-sm manage_class" 
                                                                    data-id='<?php echo $row['id'] ?>'
                                                                    title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-action btn-danger btn-sm delete_class" 
                                                                    data-id="<?php echo $row['id'] ?>"
                                                                    title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php
                            }
                        }

                        if (empty($departments)) {
                            ?>
                            <div class="empty-state">
                                <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                                <h5>No Classes Found</h5>
                                <p class="text-muted">Start by adding a new class using the button above.</p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function(){
            // Initialize DataTables with custom configuration
            $('.class-table').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    search: "",
                    searchPlaceholder: "Search classes..."
                }
            });

            // Event handlers
            $('.new_class').click(function(){
                uni_modal("New Class","<?php echo $_SESSION['login_view_folder'] ?>manage_class.php");
            });

            $('.manage_class').click(function(){
                uni_modal("Manage Class","<?php echo $_SESSION['login_view_folder'] ?>manage_class.php?id="+$(this).attr('data-id'));
            });

            $('.delete_class').click(function(){
                if(confirm("Are you sure you want to delete this class? This action cannot be undone.")) {
                    delete_class($(this).attr('data-id'));
                }
            });
        });

        function delete_class($id){
            $.ajax({
                url: 'ajax.php?action=delete_class',
                method: 'POST',
                data: {id: $id},
                success: function(resp){
                    if(resp == 1){
                        const toast = document.createElement('div');
                        toast.className = 'position-fixed top-0 end-0 p-3';
                        toast.style.zIndex = '1050';
                        toast.innerHTML = `
                            <div class="toast align-items-center text-white bg-success border-0" role="alert">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        Class successfully deleted
                                    </div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(toast);
                        
                        const toastEl = new bootstrap.Toast(toast.querySelector('.toast'));
                        toastEl.show();

                        setTimeout(function(){
                            location.reload();
                        }, 1500);
                    }
                }
            });
        }
    </script>
</body>
</html>