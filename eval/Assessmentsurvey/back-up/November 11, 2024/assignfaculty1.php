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
                <h3>Current Assignments</h3>
            </div>
            <div class="card-body">
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
                            <!-- Data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
       $(document).ready(function() {
    // Initialize DataTable
    let assignmentsTable;

    // Function to check for existing assignment
    async function checkExistingAssignment(faculty_id, class_id, subject_id) {
        try {
            const response = await $.ajax({
                url: 'ajax.php?action=check_assignment',
                method: 'POST',
                data: { faculty_id, class_id, subject_id },
                dataType: 'json'
            });
            
            if (response.status === 'error') {
                throw new Error(response.message);
            }
            
            return response.status === 'exists';
        } catch (error) {
            console.error('Error checking assignment:', error);
            throw error;
        }
    }


    // Load current assignments
    function loadAssignments() {
        $.ajax({
            url: 'ajax.php?action=load_assignments',
            method: 'GET',
            success: function(response) {
                $('#assignmentsList').html(response);
                
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable('#assignmentsTable')) {
                    $('#assignmentsTable').DataTable().destroy();
                }
                
                // Initialize new DataTable
                assignmentsTable = $('#assignmentsTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"p>>' +
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
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="fas fa-columns"></i> Columns',
                            className: 'btn btn-secondary btn-sm'
                        }
                    ],
                    order: [[0, 'asc']],
                    language: {
                        search: "Search assignments:",
                        lengthMenu: "Show _MENU_ entries per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ assignments",
                        paginate: {
                            first: 'First',
                            previous: '<i class="fa fa-angle-left"></i>',
                            next: '<i class="fa fa-angle-right"></i>',
                            last: 'Last'
                        }
                    }
                });
            }
        });
    }

    // Initial load
    loadAssignments();

    // Handle form submission with validation
    $('#assignFacultyForm').on('submit', async function(e) {
        e.preventDefault();
        
        const faculty_id = $('#faculty').val();
        const class_id = $('#class').val();
        const subject_id = $('#subject').val();

        // Validate required fields
        if (!faculty_id || !class_id || !subject_id) {
            $('#errorAlert').text('Please fill in all required fields!').fadeIn().delay(3000).fadeOut();
            return;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...');
        
        try {
            // Check for existing assignment
            const exists = await checkExistingAssignment(faculty_id, class_id, subject_id);
            
            if (exists) {
                // Show error message for duplicate assignment
                $('#errorAlert').text('This assignment already exists!').fadeIn().delay(3000).fadeOut();
                submitBtn.prop('disabled', false).text('Assign Faculty');
                return;
            }
            
            // If no duplicate, proceed with saving
            const response = await $.ajax({
                url: 'ajax.php?action=save_assignment',
                method: 'POST',
                data: $(this).serialize()
            });
            
            if (response == 1) {
                $('#successAlert').text('Assignment saved successfully!').fadeIn().delay(2000).fadeOut();
                $('#assignFacultyForm')[0].reset();
                loadAssignments();
            } else {
                $('#errorAlert').text('Error occurred while saving assignment.').fadeIn().delay(2000).fadeOut();
            }
        } catch (error) {
            console.error('Error:', error);
            $('#errorAlert').text('An unexpected error occurred.').fadeIn().delay(2000).fadeOut();
        } finally {
            submitBtn.prop('disabled', false).text('Assign Faculty');
        }
    });

    // Handle delete assignment
    $(document).on('click', '.delete-assignment', function() {
        if(confirm('Are you sure you want to delete this assignment?')) {
            let id = $(this).data('id');
            $.ajax({
                url: 'ajax.php?action=delete_assignment',
                method: 'POST',
                data: {id: id},
                success: function(response) {
                    if(response == 1) {
                        loadAssignments();
                        $('#successAlert').text('Assignment deleted successfully!').fadeIn().delay(2000).fadeOut();
                    } else {
                        $('#errorAlert').text('Error deleting assignment.').fadeIn().delay(2000).fadeOut();
                    }
                },
                error: function() {
                    $('#errorAlert').text('An error occurred while deleting the assignment.').fadeIn().delay(2000).fadeOut();
                }
            });
        }
    });

    // Add visual feedback for form fields
    $('#faculty, #class, #subject').on('change', function() {
        $(this).removeClass('is-invalid');
        // Clear any existing error messages when user makes changes
        $('#errorAlert').fadeOut();
    });
});
    </script>
</body>
</html>