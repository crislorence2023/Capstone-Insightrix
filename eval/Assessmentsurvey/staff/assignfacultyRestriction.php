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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .draggable-list {
            min-height: 150px;
            border: 2px dashed #ccc;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .draggable-item {
            background: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 5px 0;
            cursor: move;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .draggable-item:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .drag-over {
            background: #e9ecef;
            border: 2px dashed #007bff;
        }
        
        .assignment-card {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .draggable-list {
            min-height: 150px;
            border: 2px dashed #ccc;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .faculty-sidebar {
            position: sticky;
            top: 20px; /* Adjust based on your needs */
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
        
        .department-section {
            margin-bottom: 15px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .department-header {
            padding: 10px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .department-content {
            padding: 10px;
        }

        .department-assignments {
            margin-bottom: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .department-assignments-header {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
        
        .department-assignments-content {
            padding: 15px;
        }
        
        .assignment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            padding: 10px;
        }
        
        .assignment-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
        }
        
        .empty-department {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="faculty-sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Available Faculty</h5>
                        </div>
                        <div class="card-body">
                            <div class="filter-section">
                                <div class="mb-2">
                                    <input type="text" class="form-control" id="facultySearch" placeholder="Search faculty...">
                                </div>
                                <div class="mb-2">
                                    <select class="form-select" id="departmentFilter">
                                        <option value="">All Departments</option>
                                        <?php 
                                        $departments = $conn->query("SELECT DISTINCT department FROM faculty_list WHERE department IS NOT NULL ORDER BY department ASC");
                                        while($row = $departments->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $row['department'] ?>"><?php echo $row['department'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="facultyListContainer">
                                <?php 
                                $departments = $conn->query("SELECT DISTINCT department FROM faculty_list WHERE department IS NOT NULL ORDER BY department ASC");
                                while($dept = $departments->fetch_assoc()):
                                ?>
                                <div class="department-section" data-department="<?php echo $dept['department'] ?>">
                                    <div class="department-header">
                                        <span><?php echo $dept['department'] ?></span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="department-content">
                                        <div class="draggable-list">
                                            <?php 
                                            $faculty = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM faculty_list WHERE department = '{$dept['department']}' ORDER BY lastname ASC");
                                            while($row = $faculty->fetch_assoc()):
                                            ?>
                                            <div class="draggable-item" draggable="true" data-type="faculty" data-id="<?php echo $row['id'] ?>" data-department="<?php echo $dept['department'] ?>">
                                                <i class="fas fa-user mr-2"></i> <?php echo $row['name'] ?>
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Class & Subject Assignments</h5>
            </div>
            <div class="card-body">
            <div class="row mb-3">
    <div class="col-md-4">
        <select class="form-select" id="assignmentDepartmentFilter">
            <option value="">All Departments</option>
            <?php 
            $departments = $conn->query("SELECT DISTINCT department FROM class_list WHERE department IS NOT NULL ORDER BY department ASC");
            while($row = $departments->fetch_assoc()):
            ?>
            <option value="<?php echo $row['department'] ?>"><?php echo $row['department'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="classFilter">
            <option value="">All Classes</option>
            <?php 
            $classes = $conn->query("SELECT id, CONCAT(level, ' - ', section) as name, department FROM class_list ORDER BY level ASC");
            while($row = $classes->fetch_assoc()):
            ?>
            <option value="<?php echo $row['id'] ?>" data-department="<?php echo $row['department'] ?>"><?php echo $row['name'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="subjectFilter">
            <option value="">All Subjects</option>
            <?php 
            $subjects = $conn->query("SELECT id, CONCAT(code, ' - ', subject) as name, department FROM subject_list ORDER BY subject ASC");
            while($row = $subjects->fetch_assoc()):
            ?>
            <option value="<?php echo $row['id'] ?>" data-department="<?php echo $row['department'] ?>"><?php echo $row['name'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
</div>
                <div id="assignmentContainer">
                    <!-- Assignments will be loaded here by department -->
                </div>
            </div>
        </div>
    </div>

   
</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Department filter for faculty list
    $('#departmentFilter').on('change', function() {
        const department = $(this).val();
        if (department) {
            $('.department-section').hide();
            $(`.department-section[data-department="${department}"]`).show();
        } else {
            $('.department-section').show();
        }
    });

    // Department accordion functionality
    $('.department-header').on('click', function() {
        const content = $(this).next('.department-content');
        const icon = $(this).find('i');
        
        content.slideToggle();
        icon.toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Enhanced faculty search
    $('#facultySearch').on('input', function() {
        const searchValue = $(this).val().toLowerCase();
        $('.draggable-item').each(function() {
            const facultyName = $(this).text().toLowerCase();
            const shouldShow = facultyName.includes(searchValue);
            $(this).toggle(shouldShow);
            
            // Show/hide department sections based on whether they have visible faculty
            const departmentSection = $(this).closest('.department-section');
            const hasVisibleFaculty = departmentSection.find('.draggable-item:visible').length > 0;
            departmentSection.toggle(hasVisibleFaculty);
        });
    });

    // Initialize drag and drop functionality
    function initializeDragAndDrop() {
        const draggableItems = document.querySelectorAll('.draggable-item');
        const dropZones = document.querySelectorAll('.assignment-dropzone');

        draggableItems.forEach(item => {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
        });

        dropZones.forEach(zone => {
            zone.addEventListener('dragover', handleDragOver);
            zone.addEventListener('dragleave', handleDragLeave);
            zone.addEventListener('drop', handleDrop);
        });
    }

    function handleDragStart(e) {
        e.target.classList.add('dragging');
        e.dataTransfer.setData('text/plain', JSON.stringify({
            id: e.target.dataset.id,
            type: e.target.dataset.type
        }));
    }

    function handleDragEnd(e) {
        e.target.classList.remove('dragging');
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        if (data.type === 'faculty') {
            const facultyId = data.id;
            const classId = e.currentTarget.dataset.class;
            const subjectId = e.currentTarget.dataset.subject;

            saveAssignment(facultyId, classId, subjectId);
        }
    }

    // Assignment department filter handler
    $('#assignmentDepartmentFilter').on('change', function() {
        const selectedDepartment = $(this).val();
        
        // Filter class dropdown
        $('#classFilter option').each(function() {
            const classDepartment = $(this).data('department');
            if (!selectedDepartment || classDepartment === selectedDepartment || $(this).val() === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Filter subject dropdown
        $('#subjectFilter option').each(function() {
            const subjectDepartment = $(this).data('department');
            if (!selectedDepartment || subjectDepartment === selectedDepartment || $(this).val() === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Reset class and subject selections if they're not in the selected department
        const currentClass = $('#classFilter option:selected');
        const currentSubject = $('#subjectFilter option:selected');
        
        if (selectedDepartment && currentClass.data('department') !== selectedDepartment) {
            $('#classFilter').val('');
        }
        if (selectedDepartment && currentSubject.data('department') !== selectedDepartment) {
            $('#subjectFilter').val('');
        }
        
        loadAssignments();
    });

    // Load assignments function
    function loadAssignments() {
        $('.loading').css('display', 'flex');
        const classId = $('#classFilter').val();
        const subjectId = $('#subjectFilter').val();
        const department = $('#assignmentDepartmentFilter').val();

        $.ajax({
            url: 'ajax.php?action=load_assignments_grid',
            method: 'POST',
            data: {
                class_id: classId,
                subject_id: subjectId,
                department: department
            },
            success: function(response) {
                $('#assignmentContainer').html(response);
                initializeDragAndDrop();
                $('.loading').hide();
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                $('#assignmentContainer').html('<div class="alert alert-danger">Error loading assignments. Please try again.</div>');
                $('.loading').hide();
            }
        });
    }

    // Save assignment function
    function saveAssignment(facultyId, classId, subjectId) {
        $('.loading').css('display', 'flex');

        $.ajax({
            url: 'ajax.php?action=save_assignment',
            method: 'POST',
            data: {
                faculty_id: facultyId,
                class_id: classId,
                subject_id: subjectId
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        loadAssignments(); // Refresh the grid after successful save
                    } else {
                        alert(result.message || 'Error saving assignment');
                        $('.loading').hide();
                    }
                } catch (e) {
                    console.error("Parse error:", e);
                    alert('Error processing response');
                    $('.loading').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                alert('Error saving assignment');
                $('.loading').hide();
            }
        });
    }

    // Delete assignment handler
    $(document).on('click', '.delete-assignment', function() {
        if (confirm('Are you sure you want to remove this assignment?')) {
            const id = $(this).data('id');
            $('.loading').css('display', 'flex');

            $.ajax({
                url: 'ajax.php?action=delete_assignment',
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            loadAssignments(); // Refresh the grid after successful delete
                        } else {
                            alert(result.message || 'Error deleting assignment');
                            $('.loading').hide();
                        }
                    } catch (e) {
                        console.error("Parse error:", e);
                        alert('Error processing response');
                        $('.loading').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    alert('Error deleting assignment');
                    $('.loading').hide();
                }
            });
        }
    });

    // Filter change handlers
    $('#classFilter, #subjectFilter').on('change', function() {
        loadAssignments();
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initial load
    loadAssignments();
});
</script>