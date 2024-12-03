<?php
include './db_connect.php';

// Fetch departments for dropdown
$department_query = "SELECT * FROM department_list ORDER BY name ASC";
$department_result = $conn->query($department_query);

// Get parameters
$academic_id = isset($_GET['academic_id']) ? $_GET['academic_id'] : null;
$department_id = isset($_GET['department_id']) ? $_GET['department_id'] : null;

// Fetch academic years for dropdown
$academic_query = "SELECT * FROM academic_list ORDER BY year DESC, semester DESC";
$academic_result = $conn->query($academic_query);

// Main query to get evaluation status
$query = "
    SELECT 
        cl.curriculum,
        cl.level,
        cl.section,
        cl.department,
        sl.id as student_id,
        sl.school_id,
        sl.firstname,
        sl.lastname,
        f.firstname as faculty_fname,
        f.lastname as faculty_lname,
        s.code as subject_code,
        s.subject as subject_name,
        fa.semester,
        CASE 
            WHEN el.evaluation_id IS NOT NULL THEN 'Submitted'
            ELSE 'Pending'
        END as status,
        el.date_taken
    FROM student_list sl
    INNER JOIN class_list cl ON sl.class_id = cl.id
    INNER JOIN faculty_assignments fa ON cl.id = fa.class_id
    INNER JOIN faculty_list f ON fa.faculty_id = f.id
    INNER JOIN subject_list s ON fa.subject_id = s.id
    LEFT JOIN evaluation_list el ON (
        sl.id = el.student_id AND
        fa.faculty_id = el.faculty_id AND
        fa.subject_id = el.subject_id AND
        fa.academic_year_id = el.academic_id
    )
    WHERE fa.is_active = 1";

if ($academic_id) {
    $query .= " AND fa.academic_year_id = ?";
}
if ($department_id) {
    $query .= " AND cl.department = ?";
}

$query .= " ORDER BY cl.curriculum, cl.level, cl.section, sl.lastname, sl.firstname";

// Debug the query and parameters
if ($academic_id) {
    $params = array();
    $types = '';
    
    if ($academic_id) {
        $params[] = $academic_id;
        $types .= 'i';
    }
    
    if ($department_id) {
        $params[] = $department_id;
        $types .= 'i';
    }
    
    // Debug output (remove in production)
    error_log("Query: " . $query);
    error_log("Parameters: " . print_r($params, true));
    error_log("Types: " . $types);
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Status</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
        }
        
        .container-fluid {
            
        }
        
        /* Modern table styling */
        .table-responsive {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
            border: none;
        }
        
        /* Modern form controls */
        .form-select, .btn {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
        
        .btn-primary {
            background: #0d6efd;
            border: none;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
        }
        
        /* Status badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
        }
        
        /* Loading state */
        .loading {
            position: relative;
            opacity: 0.7;
            border-radius: 12px;
        }
        
        .loading:after {
            content: 'Loading...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.9);
            padding: 1rem 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* DataTables customization */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            padding: 0.4rem;
            border: 1px solid #dee2e6;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .dataTables_empty {
            padding: 2rem !important;
            font-size: 16px;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .dt-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        @media (max-width: 767px) {
            .dt-buttons {
                margin-bottom: 1rem;
                width: 100%;
            }
            
            .dt-buttons .btn {
                flex: 1;
                white-space: nowrap;
            }
            
            .dataTables_filter {
                width: 100%;
                margin-top: 1rem;
            }
            
            .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-12">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" name="academic_id" id="academic_id">
                        <option value="">Select Academic Year</option>
                        <?php while($row = $academic_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" 
                                    <?php echo ($academic_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo $row['year'] . ' - Semester ' . $row['semester']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="department_id" id="department_id">
                        <option value="">Select Department</option>
                        <?php while($row = $department_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" 
                                    <?php echo ($department_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="table-responsive">
        <table id="evaluationTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Faculty</th>
                    <th>Subject</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($academic_id) {
                    $params = array();
                    $types = '';
                    
                    if ($academic_id) {
                        $params[] = $academic_id;
                        $types .= 'i';
                    }
                    
                    if ($department_id) {
                        $params[] = $department_id;
                        $types .= 'i';
                    }
                    
                    $stmt = $conn->prepare($query);
                    if (!empty($params)) {
                        $stmt->bind_param($types, ...$params);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()):
                        $class_name = "{$row['curriculum']} {$row['level']}-{$row['section']}";
                        $student_name = "{$row['lastname']}, {$row['firstname']}";
                        $faculty_name = "{$row['faculty_lname']}, {$row['faculty_fname']}";
                        $subject = "({$row['subject_code']}) {$row['subject_name']}";
                ?>
                    <tr>
                        <td><?php echo $class_name; ?></td>
                        <td><?php echo $row['school_id']; ?></td>
                        <td><?php echo $student_name; ?></td>
                        <td><?php echo $faculty_name; ?></td>
                        <td><?php echo $subject; ?></td>
                        <td><?php echo $row['semester']; ?></td>
                        <td>
                            <span class="badge <?php echo $row['status'] == 'Submitted' ? 'bg-success' : 'bg-warning'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['date_taken'] ? date('Y-m-d H:i', strtotime($row['date_taken'])) : '-'; ?></td>
                    </tr>
                <?php 
                    endwhile;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#evaluationTable').DataTable({
        processing: true,
        pageLength: 25,
        order: [[0, 'asc'], [2, 'asc']],
        dom: '<"row mb-4"<"col-12 col-md-6"B><"col-12 col-md-6"f>>rtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary me-2 mb-2 mb-md-0',
                text: '<i class="fas fa-copy"></i> Copy',
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success me-2 mb-2 mb-md-0',
                text: '<i class="fas fa-file-excel"></i> Excel',
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger mb-2 mb-md-0',
                text: '<i class="fas fa-file-pdf"></i> PDF',
            }
        ],
        language: {
            emptyTable: "No evaluation data found for the selected filters",
            zeroRecords: "No matching records found",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });

    // Handle class level change
    $('#class_level').on('change', function() {
        const level = $(this).val();
        const section = $('#section');
        
        section.prop('disabled', !level);
        if (!level) {
            section.html('<option value="">Select Section</option>');
            return;
        }

        $.ajax({
            url: 'ajax.php?action=get_sections',
            method: 'POST',
            data: { level: level },
            success: function(response) {
                const sections = JSON.parse(response);
                let options = '<option value="">Select Section</option>';
                sections.forEach(function(section) {
                    options += `<option value="${section}">${section}</option>`;
                });
                section.html(options);
            }
        });
    });

    // Function to load evaluation status
    function loadEvaluationStatus() {
        const filters = {
            academic_id: $('#academic_id').val(),
            department_id: $('#department_id').val()
        };
        
        // Show loading state
        table.clear().draw();
        $('#evaluationTable').addClass('loading');
        
        $.ajax({
            url: 'ajax.php?action=get_evaluation_status',
            method: 'POST',
            data: filters,
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(row) {
                            const statusBadge = row.status === 'Submitted' ? 
                                '<span class="badge bg-success">Submitted</span>' : 
                                '<span class="badge bg-warning">Pending</span>';
                            
                            table.row.add([
                                row.class_name,
                                row.student_id,
                                row.student_name,
                                row.faculty_name,
                                row.subject,
                                row.semester,
                                statusBadge,
                                row.date_taken || '-'
                            ]);
                        });
                    }
                    table.draw();
                } else {
                    console.error('Error:', data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax Error:', error);
            },
            complete: function() {
                $('#evaluationTable').removeClass('loading');
            }
        });
    }

    // Load initial data
    loadEvaluationStatus();

    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadEvaluationStatus();
    });
});
</script>
</body>
</html>