<?php include 'db_connect.php' ?>

<!DOCTYPE html>
<html>
<head>
    <title>Subject Management</title>
    <!-- Include required CDN libraries -->
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: "Montserrat", sans-serif;
        }

        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
            border-radius: 20px !important;
            
        }

        .department-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .department-container:hover {
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
        }
        
        .department-header {
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }
        
        .department-title {
            color: #212529;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .btn-action {
            border-radius: 6px;
            margin: 0 2px;
            padding: 0.375rem 0.75rem;
            transition: all 0.2s;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
        
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
        
        .department-stats {
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1.25rem;
        }
        
        .stats-item {
            align-items: center;
            display: flex;
            justify-content: space-between;
            font-weight: 500;
        }
        
        .stats-item strong {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .empty-state {
            align-items: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            text-align: center;
        }
        
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        /* DataTables Customization */
        .dataTables_wrapper {
            padding: 1rem 0;
        }

        .dataTables_length select {
            font-family: "Montserrat", sans-serif !important;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
        }

        .dataTables_filter input {
            font-family: "Montserrat", sans-serif !important;
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .page-link {
            font-family: "Montserrat", sans-serif !important;
        }

/* Mobile Responsive Styles */
@media screen and (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }

    /* Card and Container Adjustments */
    .department-container {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .department-header {
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .department-title {
        font-size: 1.25rem;
    }

    /* Stats Layout */
    .department-stats {
        margin-bottom: 0.5rem;
    }

    .stats-item {
        flex-direction: column;
        text-align: center;
        gap: 0.25rem;
    }

    /* Button Adjustments */
    .new-subject-btn {
        margin-left: 0;
        width: 50%;
        margin-top: 0.5rem;
		font-size: 15px;
    }

    /* Table Horizontal Scroll */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
        /* Add smooth scrolling effect */
        scroll-behavior: smooth;
    }

    /* Add scroll indicators */
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

    /* Ensure table takes full width */
    .table {
        width: 100% !important;
        margin-bottom: 0;
    }

    /* Minimum column widths for better readability */
    .table th,
    .table td {
        white-space: nowrap;
        min-width: 100px;
    }
    
    .table td:first-child {
        min-width: 50px; /* For the # column */
    }
    
    .table td:nth-child(4) {
        min-width: 200px; /* For description column */
    }

    /* DataTables Adjustments */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
        width: 100%;
    }

    .dataTables_wrapper .dataTables_filter input {
        width: 100%;
        margin-left: 0;
        margin-top: 0.5rem;
    }

    .dataTables_wrapper .dataTables_paginate {
        text-align: center;
        margin-top: 1rem;
        overflow-x: auto;
        white-space: nowrap;
    }

    /* Empty State Adjustments */
    .empty-state {
        padding: 2rem 1rem;
    }
}

/* Small Mobile Devices */
@media screen and (max-width: 480px) {
    .department-title {
        font-size: 1.1rem;
    }

    .btn-action {
        padding: 0.25rem 0.5rem;
    }

    .empty-state i {
        font-size: 2em;
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
                        <h5 class="mb-0 fw-bold">Subject Management</h5>
                        <button class="new-subject-btn new_subject">
                            <i class="fas fa-plus me-2"></i>Add New Subject
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <?php
                        // Fetch only CME department subjects
                        $subjects = array();
                        $total_subjects = 0;
                        
                        $subject_query = $conn->query("SELECT * FROM subject_list WHERE department='CME' ORDER BY subject ASC");
                        while($subject_row = $subject_query->fetch_assoc()) {
                            $subjects[] = $subject_row;
                            $total_subjects++;
                        }
                        ?>
                        
                        <!-- Statistics Panel -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="department-stats">
                                    <div class="stats-item">
                                        <span>Department:</span>
                                        <strong>CME</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="department-stats">
                                    <div class="stats-item">
                                        <span>Total Subjects:</span>
                                        <strong><?php echo $total_subjects; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Subject List Table -->
                        <div class="department-container">
                            <div class="department-header">
                                <h6 class="department-title">CME Department Subjects</h6>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover subject-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="15%">Code</th>
                                            <th width="20%">Subject</th>
                                            <th width="45%">Description</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if(count($subjects) > 0):
                                            $i = 1;
                                            foreach($subjects as $row): 
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i++ ?></td>
                                            <td><?php echo $row['code'] ?></td>
                                            <td><?php echo $row['subject'] ?></td>
                                            <td><?php echo $row['description'] ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-action btn-primary btn-sm manage_subject" 
                                                            data-id='<?php echo $row['id'] ?>'
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-action btn-danger btn-sm delete_subject" 
                                                            data-id="<?php echo $row['id'] ?>"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php 
                                            endforeach;
                                        else:
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No subjects found</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Required Scripts -->
   
    
    <script>
        $(document).ready(function(){
            // Initialize DataTables with custom configuration
            $('.subject-table').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    search: "",
                    searchPlaceholder: "Search subjects..."
                }
            });

            // Event handlers
            $('.new_subject').click(function(){
                uni_modal("New Subject","staff-cme/manage_subject.php", "mid-large");
            });

            $('.manage_subject').click(function(){
                uni_modal("Manage Subject","staff-cme/manage_subject.php?id="+$(this).attr('data-id'), "mid-large");
            });

            $('.delete_subject').click(function(){
                _conf("Are you sure you want to delete this subject?", "delete_subject", [$(this).attr('data-id')]);
            });
        });

        function delete_subject($id){
            start_load();
            $.ajax({
                url: 'ajax.php?action=delete_subject',
                method: 'POST',
                data: {id: $id},
                success: function(resp){
                    if(resp == 1){
                        alert_toast("Subject successfully deleted", 'success');
                        setTimeout(function(){
                            location.reload()
                        }, 1500)
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    alert_toast("An error occurred", 'error');
                    end_load();
                }
            });
        }
    </script>
</body>
</html>