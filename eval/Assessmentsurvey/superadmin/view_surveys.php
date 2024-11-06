<?php
include 'db_connect.php';

// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 1){
    header("location:../login.php");
    exit;
}

// Fetch all evaluations
$evaluations = $conn->query("SELECT e.*, s.school_id as student_school_id, 
                            CONCAT(s.firstname, ' ', s.lastname) as student_name, 
                            CONCAT(f.firstname, ' ', f.lastname) as faculty_name, 
                            sub.code as subject_code, sub.subject,
                            c.department, CONCAT(c.curriculum, ' ', c.level, ' - ', c.section) as class
                            FROM evaluation_list e 
                            LEFT JOIN student_list s ON e.student_id = s.id
                            LEFT JOIN faculty_list f ON e.faculty_id = f.id
                            LEFT JOIN subject_list sub ON e.subject_id = sub.id
                            LEFT JOIN class_list c ON s.class_id = c.id
                            ORDER BY c.department, e.date_taken DESC");

// Group evaluations by department
$evaluations_by_department = [];
while ($row = $evaluations->fetch_assoc()) {
    $dept = $row['department'] ? $row['department'] : 'Unassigned';
    $evaluations_by_department[$dept][] = $row;
}

// Function to generate table for a department
function generate_department_table($dept_name, $dept_evaluations) {
    global $conn;
    $i = 1;
    ?>
    <div class="department-container mb-4">
        <h3><?php echo $dept_name != 'Unassigned' ? $dept_name . ' Department' : 'Unassigned Department'; ?></h3>
        <table class="table table-bordered table-striped evaluation-table">
            <thead class="thead-dark">
                <tr>
                    <th width="50">
                        <input type="checkbox" class="select-all-dept" data-dept="<?php echo htmlspecialchars($dept_name); ?>">
                    </th>
                    <th>Date Taken</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Faculty</th>
                    <th>Subject</th>
                    <th>Ratings</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dept_evaluations as $row): 
                    // Calculate average rating
                    $answers = $conn->query("SELECT * FROM evaluation_answers WHERE evaluation_id = {$row['evaluation_id']}");
                    $total_rating = 0;
                    $count = 0;
                    while($answer = $answers->fetch_assoc()){
                        $total_rating += $answer['rate'];
                        $count++;
                    }
                    $avg_rating = $count > 0 ? number_format($total_rating / $count, 2) : 'N/A';
                ?>
                <tr>
                    <td>
                        <input type="checkbox" class="evaluation-checkbox" 
                               data-dept="<?php echo htmlspecialchars($dept_name); ?>"
                               value="<?php echo $row['evaluation_id']; ?>">
                    </td>
                    <td><?php echo date("M d, Y h:i A", strtotime($row['date_taken'])); ?></td>
                    <td><?php echo $row['student_school_id'] . ' - ' . $row['student_name']; ?></td>
                    <td><?php echo $row['class']; ?></td>
                    <td><?php echo $row['faculty_name']; ?></td>
                    <td><?php echo $row['subject_code'] . ' - ' . $row['subject']; ?></td>
                    <td><?php echo $avg_rating; ?></td>
                    <td><?php echo htmlspecialchars($row['comment']); ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['evaluation_id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Surveys</title>
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .evaluation-table th:first-child,
        .evaluation-table td:first-child {
            width: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h2 class="text-center mt-4 mb-4">Survey Results</h2>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <input type="text" id="searchInput" class="form-control w-75" placeholder="Search for students, faculty, subjects...">
            <button class="btn btn-danger" id="deleteAllSelected">Delete All Selected</button>
        </div>

        <?php
        // Generate tables for each department
        foreach ($evaluations_by_department as $dept => $dept_evaluations) {
            generate_department_table($dept, $dept_evaluations);
        }
        ?>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {

        $('.select-all-dept').change(function() {
            var dept = $(this).data('dept');
            var isChecked = $(this).prop('checked');
            $('.evaluation-checkbox[data-dept="' + dept + '"]').prop('checked', isChecked);
        });

        // Handle delete selected for specific department
        $('.delete-selected-dept').click(function() {
            var dept = $(this).data('dept');
            var selectedIds = [];
            
            $('.evaluation-checkbox[data-dept="' + dept + '"]:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if(selectedIds.length === 0) {
                alert('Please select evaluations to delete.');
                return;
            }

            if(confirm('Are you sure you want to delete ' + selectedIds.length + ' selected evaluations from ' + dept + '?')) {
                deleteSelectedEvaluations(selectedIds);
            }
        });

        // Handle delete all selected button
        $('#deleteAllSelected').click(function() {
            var selectedIds = [];
            
            $('.evaluation-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if(selectedIds.length === 0) {
                alert('Please select evaluations to delete.');
                return;
            }

            if(confirm('Are you sure you want to delete all ' + selectedIds.length + ' selected evaluations?')) {
                deleteSelectedEvaluations(selectedIds);
            }
        });

        $('.delete-btn').click(function() {
            var evaluationId = $(this).data('id');
            if(confirm('Are you sure you want to delete this survey?')) {
                $.ajax({
                    url: 'ajax.php?action=delete_evaluation',
                    type: 'POST',
                    data: {evaluation_id: evaluationId},
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert('An error occurred while deleting the evaluation.');
                    }
                });
            }
        });

        function deleteSelectedEvaluations(ids) {
            $.ajax({
                url: 'ajax.php?action=delete_multiple_evaluations',
                type: 'POST',
                data: {evaluation_ids: ids},
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('An error occurred while deleting the evaluations.');
                }
            });
        }

        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.evaluation-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });

            // Hide/show department containers based on search results
            $('.department-container').each(function() {
                var hasVisibleRows = $(this).find('tbody tr:visible').length > 0;
                $(this).toggle(hasVisibleRows);
            });
        });
    });
    </script>
</body>
</html>