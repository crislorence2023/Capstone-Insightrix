<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Faculty to Classes</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
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

    <style>
        body{
            font-family: 'Monstseratt', sans-serif;
        }
        .card {
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .alert {
            display: none;
            margin-top: 20px;
        }
        .dataTables_wrapper {
            padding: 15px 0;
        }
        .dt-buttons .btn {
            margin-right: 5px;
        }
        .department-tab {
            cursor: pointer;
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .department-tab.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Assign Faculty to Classes</h3>
            </div>
            <div class="card-body">
            <form id="assignFacultyForm">
                    <div class="mb-3">
                        <label for="department" class="form-label">Select Department</label>
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
                <form id="assignFacultyForm">
                    <div class="mb-3">
                        <label for="faculty" class="form-label">Select Faculty</label>
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
                        <label for="class" class="form-label">Select Class</label>
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
                        <label for="subject" class="form-label">Select Subject</label>
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

                    <button type="submit" class="btn btn-primary">Assign Faculty</button>
                </form>

                <div class="alert alert-success" id="successAlert">
                    Assignment saved successfully!
                </div>
                <div class="alert alert-danger" id="errorAlert">
                    Error occurred while saving assignment.
                </div>
            </div>
        </div>

        <!-- Display Current Assignments -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Current Assignments by Department</h3>
            </div>
            <div class="card-body">
                <div class="department-tabs mb-3" id="departmentTabs">
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
                </div>
            </div>
        </div>
    </div>
                            </div>


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
                }
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
                }
            });
        }

        // Load classes for department
        $.ajax({
            url: 'ajax.php?action=get_classes_by_department',
            method: 'POST',
            data: { department: department },
            success: function(response) {
                $('#class').prop('disabled', !department);
                $('#class').html('<option value="">Choose Class...</option>' + response);
            }
        });

        // Load subjects for department
        $.ajax({
            url: 'ajax.php?action=get_subjects_by_department',
            method: 'POST',
            data: { department: department },
            success: function(response) {
                $('#subject').prop('disabled', !department);
                $('#subject').html('<option value="">Choose Subject...</option>' + response);
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
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            responsive: true,
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
            order: [[0, 'asc']]
        });
    }

    // Modified loadAssignments function to handle department filtering
    function loadAssignments(department = 'all') {
        $.ajax({
            url: 'ajax.php?action=load_assignments_faculties',
            method: 'POST',
            data: { department: department },
            success: function(response) {
                initializeDataTable(response);
            },
            error: function(xhr, status, error) {
                console.error("Error loading assignments:", error);
                $('#errorAlert').text('Error loading assignments').fadeIn().delay(2000).fadeOut();
            }
        });
    }

    // Modified save assignment function
    function saveAssignment() {
        const formData = $('#assignFacultyForm').serialize();
        $.ajax({
            url: 'ajax.php?action=save_assignment_faculty',
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response == 1) {
                    $('#successAlert').fadeIn().delay(2000).fadeOut();
                    $('#assignFacultyForm')[0].reset();
                    loadAssignments($('.department-tab.active').data('department'));
                } else {
                    $('#errorAlert').text('Error saving assignment').fadeIn().delay(2000).fadeOut();
                }
            }
        });
    }

    // Initialize the page
    loadDepartmentTabs();
    loadAssignments();

    // Handle form submission
    $('#assignFacultyForm').on('submit', function(e) {
        e.preventDefault();
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
                        loadAssignments(); // Reload the table after successful delete
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
});

    </script>
</body>
</html>