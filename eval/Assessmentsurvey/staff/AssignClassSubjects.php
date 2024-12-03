<?php
include './db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Faculty to Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
     body {
    font-family: 'Montserrat', sans-serif;
    background-color: #f5f6fa;
    height: 100vh;
    overflow-y: auto;
    position: relative;
}

.main-container {
    padding: 2rem;
    height: calc(100vh - 4rem);
    z-index: 1;
    position: relative;
}

/* Left Column - Assignment Form */
.col-lg-4 .card {
    position: sticky;
    top: 2rem;
    height: calc(100vh - 4rem);
    overflow-y: auto;
}

/* Right Column - Assignments Table */
.col-lg-8 .card {
    height: auto;
    overflow: visible;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    z-index: 2;
    position: relative;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #eee;
    padding: 1.5rem;
    border-radius: 15px 15px 0 0 !important;
}

.card-body {
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #444;
}

.form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 0.75rem;
}

.btn-primary {
    margin-bottom: 1.5rem;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}

/* Alert Styling */
#successAlert, #errorAlert {
    display: none;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    min-width: 300px;
    max-width: 90%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: none;
    animation: slideDown 0.3s ease-out;
    padding: 1rem;
    border-radius: 8px;
}

#successAlert {
    background-color: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

#errorAlert {
    background-color: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

@keyframes slideDown {
    from {
        transform: translate(-50%, -100%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

.department-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.department-tab {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: all 0.3s ease;
}

.department-tab.active {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border: none;
}

.table {
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
}

.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    color: #444;
    font-weight: 600;
}

.dataTables_wrapper .btn-secondary {
    padding: 0.4rem 0.8rem;
    margin: 0.2rem;
    border-radius: 6px;
}

.delete-assignment {
    color: #dc3545;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    border-radius: 4px;
}

.delete-assignment:hover {
    color: #bd2130;
    background-color: rgba(220, 53, 69, 0.1);
}

.delete-assignment i {
    font-size: 1.1rem;
}


.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #0d6efd;
}

.stat-label {
    color: #6c757d;
    font-size: 14px;
}

/* Table Responsiveness */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 1rem;
    min-height: .01%;
    width: 100%;
}

#assignmentsTable {
    margin-bottom: 2rem;
}

/* Remove closesb section */
.closesb {
    display: none;
}

/* DataTables Custom Styling */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.3rem 0.6rem;
    margin: 0 0.2rem;
    border-radius: 4px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #007bff;
    color: white !important;
    border: none;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 0.4rem;
    margin-left: 0.5rem;
}

@media screen and (max-width: 768px) {
    /* Stack DataTable controls */
    .dataTables_wrapper .dt-buttons {
        text-align: center;
        margin-bottom: 1rem;
        width: 100%;
    }

    .dataTables_wrapper .dataTables_filter {
        text-align: center;
        margin-bottom: 1rem;
        width: 100%;
    }

    .dataTables_wrapper .dataTables_filter input {
        width: 80%;
        margin-left: 0;
        margin-top: 0.5rem;
    }

    /* Make table scrollable horizontally */
    .table-responsive {
        border: 0;
        width: 100%;
    }

    /* Option 1: Horizontal Scroll */
    .table {
        display: block;
        width: 100%;
        -webkit-overflow-scrolling: touch;
    }

    /* Option 2: Card View for Mobile */
    @media screen and (max-width: 576px) {
        /* Hide table header */
        .table thead {
            display: none;
        }

        /* Convert rows to cards */
        .table tbody tr {
            display: block;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Style cells as flex containers */
        .table tbody td {
            display: flex;
            padding: 0.5rem 0;
            border: none;
            align-items: center;
        }

        /* Add labels before cell content */
        .table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            width: 40%;
            min-width: 120px;
            color: #495057;
        }

        /* Center align the action column */
        .table tbody td:last-child {
            justify-content: flex-start;
            padding-top: 1rem;
            margin-top: 0.5rem;
            border-top: 1px solid #dee2e6;
        }

        /* Adjust delete button styling for mobile */
        .delete-assignment {
            width: 100%;
            padding: 0.75rem;
            background-color: #fff;
            border: 1px solid #dc3545;
            border-radius: 4px;
            color: #dc3545;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .delete-assignment:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .delete-assignment i {
            font-size: 1rem;
        }

        /* DataTables pagination mobile styling */
        .dataTables_wrapper .dataTables_paginate {
            text-align: center;
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 1rem;
            margin: 0;
        }

        /* DataTables info mobile styling */
        .dataTables_wrapper .dataTables_info {
            text-align: center;
            margin-top: 1rem;
        }

        /* DataTables length mobile styling */
        .dataTables_wrapper .dataTables_length {
            text-align: center;
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length select {
            width: auto;
            display: inline-block;
        }
    }

    /* Export buttons mobile styling */
    .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
    }

    .dt-buttons .btn {
        flex: 1;
        min-width: 120px;
        margin: 0 !important;
    }
}

/* Additional responsive breakpoints */
@media screen and (max-width: 992px) {
    .card-body {
        padding: 1rem;
    }

    .department-tabs {
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 0.5rem;
    }

    .department-tab {
        display: inline-block;
        float: none;
    }
}

/* Print styles */
@media print {
    .table {
        border-collapse: collapse !important;
    }
    
    .table td,
    .table th {
        background-color: #fff !important;
        border: 1px solid #dee2e6 !important;
    }

    .delete-assignment,
    .dt-buttons,
    .dataTables_filter,
    .dataTables_length,
    .dataTables_paginate {
        display: none !important;
    }
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .col-lg-4 .card {
        position: relative;
        height: auto;
        margin-bottom: 2rem;
    }

    .main-container {
        height: auto;
        padding: 1rem;
    }

    .dashboard-stats {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .department-tabs {
        flex-direction: column;
    }

    .department-tab {
        width: 100%;
        text-align: center;
    }

    #successAlert, #errorAlert {
        width: 90%;
        min-width: auto;
    }
}

.class-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.class-tab {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: all 0.3s ease;
}

.class-tab.active {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border: none;
}
    </style>
</head>
<body>

<div class="alert alert-success" id="successAlert" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Assignment saved successfully!
                        </div>
                        <div class="alert alert-danger" id="errorAlert" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Error occurred while saving assignment.
                        </div>
    <div class="main-container">
        <div class="row g-4">
            <!-- Left Column - Assignment Form -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="m-0">Assign Faculty</h3>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-stats">
                            <div class="stat-card">
                                <div class="stat-number" id="totalAssignments">0</div>
                                <div class="stat-label">Total Assignments</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="totalFaculty">0</div>
                                <div class="stat-label">Faculty Members</div>
                            </div>
                        </div>

                        <form id="assignFacultyForm">
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="COT">COT</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="faculty" class="form-label">Faculty</label>
                                <select class="form-select" id="faculty" name="faculty_id" required>
                                    <option value="">Choose Faculty...</option>
                                    <?php 
                                    $faculty = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name 
                                                            FROM faculty_list 
                                                            WHERE department = 'COT' 
                                                            ORDER BY lastname ASC");
                                    while($row = $faculty->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="class" class="form-label">Class</label>
                                <select class="form-select" id="class" name="class_id" required>
                                    <option value="">Choose Class...</option>
                                    <?php 
                                    $classes = $conn->query("SELECT id, CONCAT(level, ' - ', section) as class_name 
                                                            FROM class_list 
                                                            WHERE department = 'COT'
                                                            ORDER BY level ASC");
                                    while($row = $classes->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['class_name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" name="subject_id" required>
                                    <option value="">Choose Subject...</option>
                                    <?php 
                                    $subjects = $conn->query("SELECT id, CONCAT(code, ' - ', subject) as subject_name 
                                                            FROM subject_list 
                                                            WHERE department = 'COT'
                                                            ORDER BY subject ASC");
                                    while($row = $subjects->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['subject_name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i>Assign Faculty
                            </button>
                        </form>

                        <div class="alert alert-success" id="successAlert" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Assignment saved successfully!
                        </div>
                        <div class="alert alert-danger" id="errorAlert" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Error occurred while saving assignment.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Assignments Table -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="m-0">Current Assignments</h3>
                        <p class="text-muted mb-0">COT Department Assignments</p>
                    </div>
                    <div class="card-body">
                        <!-- Add class filter tabs before the table -->
                        <div class="class-tabs mb-3">
                            <?php 
                            $class_query = "SELECT DISTINCT CONCAT(level, ' - ', section) as class_name 
                                            FROM class_list 
                                            WHERE department = 'COT'
                                            ORDER BY level ASC";
                            $class_result = $conn->query($class_query);
                            if ($class_result && $class_result->num_rows > 0):
                            ?>
                                <button class="class-tab active" data-class="all">All Classes</button>
                                <?php while($class = $class_result->fetch_assoc()): ?>
                                    <button class="class-tab" data-class="<?php echo htmlspecialchars($class['class_name']) ?>">
                                        <?php echo htmlspecialchars($class['class_name']) ?>
                                    </button>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="assignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assignmentsList">
                                    <?php 
                                    // First, let's check if the query is valid
                                    $assignments_query = "
                                        SELECT 
                                            af.id,
                                            CONCAT(f.firstname, ' ', f.lastname) as faculty_name,
                                            CONCAT(c.level, ' - ', c.section) as class_name,
                                            CONCAT(s.code, ' - ', s.subject) as subject_name
                                        FROM faculty_assignments af
                                        INNER JOIN faculty_list f ON f.id = af.faculty_id
                                        INNER JOIN class_list c ON c.id = af.class_id
                                        INNER JOIN subject_list s ON s.id = af.subject_id
                                        WHERE f.department = 'COT'
                                        ORDER BY faculty_name ASC
                                    ";
                                    
                                    // Add error checking
                                    $assignments = $conn->query($assignments_query);
                                    if (!$assignments) {
                                        echo "Error in query: " . $conn->error;
                                    } else {
                                        while($row = $assignments->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $row['faculty_name'] ?></td>
                                            <td><?php echo $row['class_name'] ?></td>
                                            <td><?php echo $row['subject_name'] ?></td>
                                            <td>
                                                <button class="delete-assignment" data-id="<?php echo $row['id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

    <!-- Keep the existing JavaScript code here -->
   <script>
    $(document).ready(function() {
        // Initialize DataTable with class filtering
        let assignmentsTable = $('#assignmentsTable').DataTable({
            responsive: true,
            autoWidth: true,
            order: [[0, 'asc']],
            columnDefs: [
                { width: '30%', targets: 0 }, // Faculty
                { width: '30%', targets: 1 }, // Class
                { width: '30%', targets: 2 }, // Subject
                { width: '10%', targets: 3, orderable: false }  // Action
            ],
            language: {
                searchPlaceholder: "Search records...",
                search: "",
                lengthMenu: "_MENU_ per page"
            }
        });

        // Handle class filter clicks
        $('.class-tab').on('click', function() {
            $('.class-tab').removeClass('active');
            $(this).addClass('active');
            
            let selectedClass = $(this).data('class');
            
            if (selectedClass === 'all') {
                assignmentsTable.column(1).search('').draw();
            } else {
                assignmentsTable.column(1).search(selectedClass).draw();
            }
        });

        // Handle delete assignment
        $(document).on('click', '.delete-assignment', function() {
            if(confirm('Are you sure you want to delete this assignment?')) {
                let id = $(this).data('id');
                $.ajax({
                    url: 'ajax.php?action=delete_assignment_faculty',
                    method: 'POST',
                    data: {id: id},
                    success: function(response) {
                        if(response == 1) {
                            location.reload(); // Reload the page to refresh the table
                            $('#successAlert').text('Assignment deleted successfully!').fadeIn().delay(2000).fadeOut();
                        } else {
                            $('#errorAlert').text('Error deleting assignment').fadeIn().delay(2000).fadeOut();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error deleting assignment:", error);
                        $('#errorAlert').text('Error deleting assignment').fadeIn().delay(2000).fadeOut();
                    }
                });
            }
        });

        // Update dashboard stats
        function updateDashboardStats() {
            $.ajax({
                url: 'ajax.php?action=get_dashboard_stats_cot',
                method: 'POST',
                data: { department: 'COT' },
                success: function(response) {
                    try {
                        const stats = JSON.parse(response);
                        if (stats.status === 'success') {
                            $('#totalAssignments').text(stats.totalAssignments);
                            $('#totalFaculty').text(stats.totalFaculty);
                        }
                    } catch (e) {
                        console.error("Error parsing dashboard stats:", e);
                    }
                }
            });
        }

        // Initialize the page
        updateDashboardStats();
    });


    </script>
</body>
</html>