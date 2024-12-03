<?php
include 'db_connect.php';
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
   
}

#errorAlert {
    background-color: #f8d7da;
    color: #721c24;
   
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
    width: 100%;
    /* Add shadow indicators for scroll */
    background: 
        linear-gradient(to right, white 30%, rgba(255,255,255,0)),
        linear-gradient(to right, rgba(255,255,255,0), white 70%) 100% 0,
        radial-gradient(farthest-side at 0 50%, rgba(0,0,0,.2), rgba(0,0,0,0)),
        radial-gradient(farthest-side at 100% 50%, rgba(0,0,0,.2), rgba(0,0,0,0)) 100% 0;
    background-repeat: no-repeat;
    background-size: 40px 100%, 40px 100%, 14px 100%, 14px 100%;
    background-attachment: local, local, scroll, scroll;
}

@media screen and (max-width: 768px) {
    /* Force table to not be like tables anymore */
    .table {
        display: block;
        width: 100%;
        min-width: 750px; /* Minimum width to ensure content is readable */
    }
    
    /* Ensure header stays in place */
    .table thead {
        display: table-header-group;
    }
    
    .table tbody {
        display: table-row-group;
    }
    
    .table tr {
        display: table-row;
    }
    
    .table th,
    .table td {
        display: table-cell;
        padding: 0.75rem !important;
    }
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
            align-items: flex-start;
            justify-content: flex-start;
            width: 100%;
        }

        /* Add labels before cell content */
        .table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            width: 120px;
            min-width: 120px;
            color: #495057;
            margin-right: 10px;
        }

        /* Style the cell content */
        .table tbody td span {
            flex: 1;
            padding-left: 10px;
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

/* Mobile table adjustments */
@media screen and (max-width: 768px) {
    .table tbody td {
        display: flex;
        padding: 0.5rem !important;
        border: none;
        border-bottom: 1px solid #eee;
        align-items: center !important;
        text-align: left !important;
        min-height: 40px;
        position: relative;
    }

    .table tbody td::before {
        content: attr(data-label);
        width: 35%;
        font-weight: 600;
        color: #495057;
        padding-right: 0.5rem;
        text-align: left !important;
    }

    .table tbody td span {
        width: 65%;
        display: inline-block;
        word-break: break-word;
        text-align: left !important;
        padding-left: 0.5rem;
    }

    /* Special handling for the action column */
    .table tbody td:last-child {
        border-bottom: none;
        justify-content: flex-start !important;
        padding: 0.5rem !important;
    }

    .table tbody td:last-child::before {
        content: "Action";
        width: 35%;
    }

    .delete-assignment {
        width: auto;
        margin-left: 35%;
        padding: 0.4rem 0.8rem;
    }

    /* Card styling for mobile rows */
    .table tbody tr {
        padding: 0 !important;
        margin-bottom: 0.5rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    /* DataTable controls adjustments */
    .dataTables_wrapper .dataTables_filter input {
        margin: 0.25rem 0;
    }

    .dataTables_wrapper .dt-buttons {
        margin-bottom: 0.5rem;
    }

    /* Adjust padding in card body for mobile */
    .card-body {
        padding: 0.75rem;
    }

    /* Adjust table responsive container */
    .table-responsive {
        margin-bottom: 0.5rem;
        padding: 0;
    }
}

/* Additional mobile optimizations */
@media screen and (max-width: 576px) {
    .main-container {
        padding: 0.5rem;
    }

    .card {
        margin-bottom: 0.5rem;
    }

    .card-header {
        padding: 0.75rem;
    }

    /* Adjust button spacing */
    .dt-buttons .btn {
        padding: 0.4rem 0.8rem;
        margin: 0.1rem !important;
    }
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
                                    <option value="">Choose Department...</option>
                                    <?php 
                                    $departments = $conn->query("SELECT * FROM department_list ORDER BY name ASC");
                                    while($row = $departments->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="faculty" class="form-label">Faculty</label>
                                <select class="form-select" id="faculty" name="faculty_id" required>
                                    <option value="">Choose Faculty...</option>
                                    <?php 
                                    $faculty = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM faculty_list ORDER BY lastname ASC");
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
                                    $classes = $conn->query("SELECT id, CONCAT(level, ' - ', section, ' (', curriculum, ')') as class_name FROM class_list ORDER BY level ASC");
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
                                    $subjects = $conn->query("SELECT id, CONCAT(code, ' - ', subject) as subject_name FROM subject_list ORDER BY subject ASC");
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
                        <p class="text-muted mb-0">Data is Based Per Semester</p>
                    </div>
                    <div class="card-body">
                        <div class="department-tabs" id="departmentTabs">
                            <!-- Department tabs will be dynamically loaded here -->
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="assignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Department</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assignmentsList">
                                    <!-- Data will be loaded here via AJAX -->
                                </tbody>
                            </table>

                            <div class="closesb">
                                <p>Close the sidebar to have more view</p>
                            </div>
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
    // Initialize DataTable variable
    let assignmentsTable;
    let currentDepartment = '';

    // Load departments into tabs
    function loadDepartmentTabs() {
        $.ajax({
            url: 'ajax.php?action=get_departments_faculty',
            method: 'GET',
            success: function(response) {
                try {
                    const departments = JSON.parse(response);
                    let tabsHtml = '<div class="department-tab active" data-department="all">All Departments</div>';
                    departments.forEach(dept => {
                        tabsHtml += `<div class="department-tab" data-department="${dept.name}">${dept.name}</div>`;
                    });
                    $('#departmentTabs').html(tabsHtml);
                } catch (e) {
                    console.error("Error parsing departments:", e);
                    $('#errorAlert').text('Error loading departments').fadeIn().delay(2000).fadeOut();
                }
            },
            error: function() {
                $('#errorAlert').text('Failed to load departments').fadeIn().delay(2000).fadeOut();
            }
        });
    }

    // Handle department selection change
    $('#department').change(function() {
        const department = $(this).val();
        currentDepartment = department;

        // Enable/disable and load faculty dropdown
        $('#faculty').prop('disabled', !department);
        if (department) {
            $.ajax({
                url: 'ajax.php?action=get_faculty_by_department',
                method: 'POST',
                data: { department: department },
                success: function(response) {
                    $('#faculty').html('<option value="">Choose Faculty...</option>' + response);
                    $('#faculty').prop('disabled', false);
                },
                error: function() {
                    $('#errorAlert').text('Failed to load faculty list').fadeIn().delay(2000).fadeOut();
                }
            });
        }

        // Load classes for department
        $.ajax({
            url: 'ajax.php?action=get_classes_by_department',
            method: 'POST',
            data: { department: department },
            success: function(response) {
                $('#class').html('<option value="">Choose Class...</option>' + response);
                $('#class').prop('disabled', !department);
            },
            error: function() {
                $('#errorAlert').text('Failed to load class list').fadeIn().delay(2000).fadeOut();
            }
        });

        // Load subjects for department
        $.ajax({
            url: 'ajax.php?action=get_subjects_by_department',
            method: 'POST',
            data: { department: department },
            success: function(response) {
                $('#subject').html('<option value="">Choose Subject...</option>' + response);
                $('#subject').prop('disabled', !department);
            },
            error: function() {
                $('#errorAlert').text('Failed to load subject list').fadeIn().delay(2000).fadeOut();
            }
        });
    });

    // Handle department tab clicks
    $(document).on('click', '.department-tab', function() {
        $('.department-tab').removeClass('active');
        $(this).addClass('active');
        const department = $(this).data('department');
        loadAssignments(department);
    });

    // Function to initialize DataTable
    function initializeDataTable(data) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#assignmentsTable')) {
            $('#assignmentsTable').DataTable().destroy();
        }

        // Empty the table body
        $('#assignmentsList').html(data);

        // Initialize new DataTable
        assignmentsTable = $('#assignmentsTable').DataTable({
            responsive: false,
            scrollX: false,
            autoWidth: false,
            dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copy',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-secondary btn-sm'
                }
            ],
            order: [[0, 'asc']],
            columnDefs: [
                { width: '25%', targets: 0, className: 'text-nowrap' }, // Faculty
                { width: '25%', targets: 1, className: 'text-nowrap' }, // Class
                { width: '20%', targets: 2, className: 'text-nowrap' }, // Subject
                { width: '20%', targets: 3, className: 'text-nowrap' }, // Department
                { width: '10%', targets: 4, className: 'text-nowrap' }  // Action
            ],
            language: {
                searchPlaceholder: "Search records...",
                search: "",
                lengthMenu: "_MENU_ per page"
            }
        });

        // Remove responsive class from table wrapper
        $('#assignmentsTable_wrapper').removeClass('dt-responsive');
    }

    // Function to load assignments
    function loadAssignments(department = 'all') {
        $.ajax({
            url: 'ajax.php?action=load_assignments_faculties',
            method: 'POST',
            data: { department: department },
            success: function(response) {
                $('#assignmentsList').html(response);
                
                // Add data-label attributes and wrap content in span
                $('#assignmentsTable tbody tr').each(function() {
                    $(this).find('td').each(function(index) {
                        let headerText = $('#assignmentsTable thead th').eq(index).text();
                        $(this).attr('data-label', headerText);
                        
                        // Don't wrap the content of the last column (actions)
                        if (!$(this).is(':last-child')) {
                            let content = $(this).html();
                            $(this).html('<span>' + content + '</span>');
                        }
                    });
                });

                initializeDataTable(response);
                updateDashboardStats();
            },
            error: function() {
                $('#errorAlert').text('Failed to load assignments').fadeIn().delay(2000).fadeOut();
            }
        });
    }

    // Function to save assignment
    function saveAssignment() {
        const formData = $('#assignFacultyForm').serialize();
        $.ajax({
            url: 'ajax.php?action=save_assignment_faculty',
            method: 'POST',
            data: formData,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if(result.status === 'success') {
                        $('#successAlert').text('Assignment saved successfully!').fadeIn().delay(2000).fadeOut();
                        $('#assignFacultyForm')[0].reset();
                        loadAssignments($('.department-tab.active').data('department'));
                        updateDashboardStats();
                    } else {
                        $('#errorAlert').text(result.message || 'Error saving assignment').fadeIn().delay(2000).fadeOut();
                    }
                } catch(e) {
                    console.error("Error parsing response:", e);
                    $('#errorAlert').text('Invalid server response').fadeIn().delay(2000).fadeOut();
                }
            },
            error: function() {
                $('#errorAlert').text('Failed to save assignment').fadeIn().delay(2000).fadeOut();
            }
        });
    }

    // Handle form submission
    $('#assignFacultyForm').on('submit', function(e) {
        e.preventDefault();
        if (!validateForm()) {
            return false;
        }

        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);

        $.ajax({
            url: 'ajax.php?action=check_assignment_faculty',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    let result = JSON.parse(response);
                    if(result.status === 'success') {
                        saveAssignment();
                    } else {
                        $('#errorAlert').text(result.message).fadeIn().delay(2000).fadeOut();
                    }
                } catch (e) {
                    console.error("Parse error:", e);
                    $('#errorAlert').text('Error: Invalid server response').fadeIn().delay(2000).fadeOut();
                }
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });

    // Form validation function
    function validateForm() {
        const department = $('#department').val();
        const faculty = $('#faculty').val();
        const classId = $('#class').val();
        const subject = $('#subject').val();

        if (!department) {
            $('#errorAlert').text('Please select a department').fadeIn().delay(2000).fadeOut();
            return false;
        }
        if (!faculty) {
            $('#errorAlert').text('Please select a faculty member').fadeIn().delay(2000).fadeOut();
            return false;
        }
        if (!classId) {
            $('#errorAlert').text('Please select a class').fadeIn().delay(2000).fadeOut();
            return false;
        }
        if (!subject) {
            $('#errorAlert').text('Please select a subject').fadeIn().delay(2000).fadeOut();
            return false;
        }
        return true;
    }

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
                        loadAssignments($('.department-tab.active').data('department'));
                        $('#successAlert').text('Assignment deleted successfully!').fadeIn().delay(2000).fadeOut();
                        updateDashboardStats();
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

    // Function to update dashboard statistics
    function updateDashboardStats() {
    $.ajax({
        url: 'ajax.php?action=get_dashboard_stats',
        method: 'GET',
        success: function(response) {
            try {
                const stats = JSON.parse(response);
                if (stats.status === 'success') {
                    // Update basic stats
                    $('#totalAssignments').text(stats.totalAssignments);
                    $('#totalFaculty').text(stats.totalFaculty);
                    
                    // Update department statistics
                    if (stats.departmentStats) {
                        let deptHtml = '';
                        for (const [dept, data] of Object.entries(stats.departmentStats)) {
                            deptHtml += `
                                <div class="department-stat">
                                    <h5>${dept}</h5>
                                    <div>Assignments: ${data.assignments}</div>
                                    <div>Faculty: ${data.faculty}</div>
                                </div>
                            `;
                        }
                        $('#departmentStats').html(deptHtml);
                    }

                    // Update recent assignments
                    if (stats.recentAssignments) {
                        let recentHtml = '<div class="recent-assignments">';
                        stats.recentAssignments.forEach(assignment => {
                            recentHtml += `
                                <div class="recent-assignment">
                                    <div>${assignment.faculty_name}</div>
                                    <div>${assignment.department}</div>
                                    <div>${assignment.assigned_at}</div>
                                </div>
                            `;
                        });
                        recentHtml += '</div>';
                        $('#recentAssignments').html(recentHtml);
                    }
                } else {
                    console.error("Error updating dashboard stats:", stats.message);
                }
            } catch (e) {
                console.error("Error parsing dashboard stats:", e);
            }
        },
        error: function() {
            console.error("Failed to fetch dashboard stats");
        }
    });
}

    // Initialize the page
    loadDepartmentTabs();
    loadAssignments();
    updateDashboardStats();
});


    </script>
</body>
</html>