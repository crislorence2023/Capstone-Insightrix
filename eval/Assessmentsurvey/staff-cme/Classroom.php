<?php 
include 'db_connect.php';

// Get summary statistics
$summary_query = $conn->query("
    SELECT 
        COUNT(DISTINCT d.id) as total_departments,
        COUNT(DISTINCT cl.id) as total_classes,
        COUNT(DISTINCT sl.id) as total_students
    FROM department_list d
    LEFT JOIN class_list cl ON d.name = cl.department
    LEFT JOIN student_list sl ON cl.id = sl.class_id
");
$summary = $summary_query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Class Lists</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body{
            font-family: "Montserrat";
        }
        .summary-section {
            
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
           
        }
        .summary-card {
            background: white;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .summary-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .department-section {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
        }
        .department-header {
            background-color: white;
            color: teal;
            padding: 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .department-header:hover {
            background-color: #f8f9fa;
        }
        .department-content {
            padding: 20px;
            display: none;
        }
        .class-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #fff;
        }
        .class-header {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .student-table {
            margin-top: 15px;
        }
        .title {
            margin: 1rem;
            color: Black;
            font-size: 1.5rem;
            text-align: center;
        }
        .accordion-icon {
            transition: transform 0.3s;
        }
        .accordion-icon.active {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="title">
        <h2>CLASSROOM MANAGEMENT</h2>
    </div>

    <!-- Summary Section -->
    <div class="container-fluid py-4">
        <div class="summary-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-number"><?php echo $summary['total_departments'] ?></div>
                        <div class="summary-label">Total Departments</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-number"><?php echo $summary['total_classes'] ?></div>
                        <div class="summary-label">Total Classes</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-number"><?php echo $summary['total_students'] ?></div>
                        <div class="summary-label">Total Students</div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        $dept_query = $conn->query("
            SELECT DISTINCT d.id, d.name as department_name,
                   COUNT(DISTINCT cl.id) as class_count,
                   COUNT(DISTINCT sl.id) as student_count
            FROM department_list d
            LEFT JOIN class_list cl ON d.name = cl.department
            LEFT JOIN student_list sl ON cl.id = sl.class_id
            GROUP BY d.id
            ORDER BY d.name
        ");

        while($department = $dept_query->fetch_assoc()):
        ?>
        <div class="department-section">
            <div class="department-header" onclick="toggleDepartment(this)">
                <div>
                    <h3 class="mb-0"><?php echo $department['department_name'] ?></h3>
                    <small class="text-muted">
                        Classes: <?php echo $department['class_count'] ?> | 
                        Students: <?php echo $department['student_count'] ?>
                    </small>
                </div>
                <i class="accordion-icon">▼</i>
            </div>
            
            <div class="department-content">
                <?php 
                $class_query = $conn->query("
                    SELECT cl.*, 
                        COUNT(DISTINCT sl.id) as student_count
                    FROM class_list cl
                    LEFT JOIN student_list sl ON cl.id = sl.class_id
                    WHERE cl.department = '{$department['department_name']}'
                    GROUP BY cl.id
                    ORDER BY cl.curriculum, cl.level, cl.section
                ");

                while($class = $class_query->fetch_assoc()):
                ?>
                <div class="class-section">
                <button class="btn btn-info view-subjects-btn ml-2" data-class="<?php echo $class['id'] ?>" data-department="<?php echo $department['department_name'] ?>">
    View Subjects
</button>

                    <!-- Rest of the class section code remains the same -->
                    <div class="class-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><?php echo $class['curriculum'].' - '.$class['level'].' - '.$class['section'] ?></h4>
                                <p class="mb-0">Total Students: <?php echo $class['student_count'] ?></p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-primary add-student-btn" data-class="<?php echo $class['id'] ?>">
                                    Add Student
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Student Form -->
                    <div class="add-student-form" id="form_<?php echo $class['id'] ?>" style="display:none;">
                        <form class="student-add-form mb-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="school_id" placeholder="Enter Student School ID" required>
                                    <input type="hidden" name="class_id" value="<?php echo $class['id'] ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="submit">Add</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Students Table -->
                    <div class="student-table">
                        <table class="table table-bordered table-striped student-list-table">
                            <thead>
                                <tr>
                                    <th>School ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $students = $conn->query("
                                    SELECT * FROM student_list 
                                    WHERE class_id = '{$class['id']}' 
                                    ORDER BY lastname ASC, firstname ASC
                                ");
                                while($student = $students->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $student['school_id'] ?></td>
                                    <td><?php echo $student['lastname'].', '.$student['firstname'] ?></td>
                                    <td><?php echo $student['email'] ?></td>
                                    <td><?php echo date('M d, Y', strtotime($student['date_created'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-student" 
                                                data-id="<?php echo $student['id'] ?>"
                                                data-name="<?php echo $student['firstname'].' '.$student['lastname'] ?>">
                                            View
                                        </button>
                                        <button class="btn btn-sm btn-danger remove-student" 
                                                data-id="<?php echo $student['id'] ?>"
                                                data-name="<?php echo $student['firstname'].' '.$student['lastname'] ?>">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endwhile; ?>

    
    </div>
    <div class="modal fade" id="subjectsModal" tabindex="-1" role="dialog" aria-labelledby="subjectsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectsModalLabel">Class Subjects and Instructors</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadingSpinner" class="text-center" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="subjectsTableContainer">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Description</th>
                                <th>Instructor</th>
                                <th>Schedule</th>
                            </tr>
                        </thead>
                        <tbody id="subjectsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <!-- JavaScript Dependencies -->
<!-- Core JS Libraries -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- FontAwesome for icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>


<!-- And the corresponding CSS in your head section -->

    
    <script>
 $(document).ready(function() {
    // Initialize DataTables
    $('.student-list-table').DataTable({
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]]
    });

    // Toggle Add Student Form
    $('.add-student-btn').click(function() {
        const classId = $(this).data('class');
        $(`#form_${classId}`).slideToggle();
    });

    // Handle Add Student Form Submission
    $('.student-add-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax.php?action=add_student_to_class',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if (resp == 1) {
                    alert("Student successfully added to class");
                    location.reload();
                } else if (resp == 2) {
                    alert("Student not found");
                } else if (resp == 3) {
                    alert("Student is already in this class");
                } else {
                    alert("An error occurred");
                }
            }
        });
    });

    // Handle Remove Student
    $('.remove-student').click(function() {
        const studentId = $(this).data('id');
        const studentName = $(this).data('name');

        if (confirm(`Are you sure you want to remove ${studentName} from this class?`)) {
            $.ajax({
                url: 'ajax.php?action=remove_student_from_class',
                method: 'POST',
                data: { id: studentId },
                success: function(resp) {
                    if (resp == 1) {
                        alert("Student removed from class");
                        location.reload();
                    } else {
                        alert("An error occurred");
                    }
                }
            });
        }
    });

    // Handle View Student
    $('.view-student').click(function() {
        const studentId = $(this).data('id');
        const studentName = $(this).data('name');
        // You can customize this to show more details or redirect to a student profile page
        alert(`Viewing student: ${studentName}`);
    });

    // Initialize Subjects DataTable
    let subjectsTable = null;

    // Handle View Subjects Button Click
    $(document).on('click', '.view-subjects-btn', function() {
        const classId = $(this).data('class');
        const department = $(this).data('department');
        
        // Show loading spinner and clear previous content
        $('#loadingSpinner').show();
        $('#subjectsTableBody').empty();
        
        // Show the modal
        $('#subjectsModal').modal('show');

        // Update modal title with class information
        const classInfo = $(this).closest('.class-section').find('h4').text();
        $('#subjectsModalLabel').text(`Subjects for ${classInfo}`);

        // Load subjects data
        $.ajax({
            url: 'ajax.php?action=get_class_subjects',
            method: 'POST',
            data: { 
                class_id: classId, 
                department: department 
            },
            dataType: 'json',
            success: function(response) {
                $('#loadingSpinner').hide();

                if (response.status === 'error') {
                    showSubjectsError(response.message);
                    return;
                }

                const subjects = response.data;

                if (!subjects || subjects.length === 0) {
                    showNoSubjectsMessage();
                    return;
                }

                displaySubjects(subjects);
            },
            error: function(xhr, status, error) {
                $('#loadingSpinner').hide();
                console.error('AJAX Error:', error);
                showSubjectsError('Failed to load subjects data. Please try again.');
            }
        });
    });

    // Helper function to show error message
    function showSubjectsError(message) {
        $('#subjectsTableBody').html(`
            <tr>
                <td colspan="5" class="text-center text-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>${message}
                </td>
            </tr>
        `);
    }

    // Helper function to show no subjects message
    function showNoSubjectsMessage() {
        $('#subjectsTableBody').html(`
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-info-circle mr-2"></i>No subjects found for this class
                </td>
            </tr>
        `);
    }

    // Helper function to display subjects
    function displaySubjects(subjects) {
        if (subjectsTable) {
            subjectsTable.destroy();
        }

        let tableContent = '';
        subjects.forEach(function(item) {
            const instructorName = item.instructor_firstname && item.instructor_lastname ?
                `${item.instructor_firstname} ${item.instructor_lastname}` :
                '<em class="text-muted">No instructor assigned</em>';

            tableContent += `
                <tr>
                    <td>${item.code || ''}</td>
                    <td>${item.name || ''}</td>
                    <td>${item.description || ''}</td>
                    <td>${instructorName}</td>
                    <td>${item.schedule || '<em class="text-muted">Not set</em>'}</td>
                </tr>
            `;
        });

        $('#subjectsTableBody').html(tableContent);

        // Initialize DataTable for subjects
        subjectsTable = $('#subjectsTableContainer table').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
            ordering: true,
            responsive: true,
            language: {
                emptyTable: "No subjects available",
                zeroRecords: "No matching subjects found"
            },
            dom: '<"top"lf>rt<"bottom"ip><"clear">'
        });
    }

    // Handle modal close - destroy DataTable
    $('#subjectsModal').on('hidden.bs.modal', function() {
        if (subjectsTable) {
            subjectsTable.destroy();
            subjectsTable = null;
        }
    });

    // Department Accordion Toggle Function
    window.toggleDepartment = function(element) {
        const content = element.nextElementSibling;
        const icon = element.querySelector('.accordion-icon');
        
        // Toggle the content
        $(content).slideToggle(300);
        
        // Toggle the icon
        icon.classList.toggle('active');
    };

    // Initialize all departments as collapsed
    $('.department-content').hide();
});
    </script>
</body>
</html>