<?php include('./db_connect.php'); ?>

<div class="dashboard-container">
    <!-- Header Section -->
   

    <div class="row">
        <!-- Available Subjects Panel -->
        <div class="col-md-4">
            <div class="sticky-panel">
                <div class="content-card">
                    <div class="card-header">
                        <h5 class="card-title">Available Subjects</h5>
                    </div>
                    <div class="card-body">
                        <!-- Department filters -->
                        <div class="filter-chips mb-3">
                            <button class="chip active" data-department="">All</button>
                            <?php
                            $departments = $conn->query("SELECT DISTINCT department FROM subject_list WHERE department IS NOT NULL ORDER BY department");
                            while($dept = $departments->fetch_assoc()):
                            ?>
                            <button class="chip" data-department="<?php echo htmlspecialchars($dept['department']) ?>">
                                <?php echo htmlspecialchars($dept['department']) ?>
                            </button>
                            <?php endwhile; ?>
                        </div>

                        <!-- Search input -->
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="subjectSearch" placeholder="Search subjects...">
                        </div>

                        <!-- Subjects list -->
                        <div id="availableSubjects" class="subject-list">
                            <?php
                            $subjects = $conn->query("SELECT * FROM subject_list ORDER BY department, code ASC");
                            while($row = $subjects->fetch_assoc()):
                            ?>
                            <div class="subject-item" draggable="true" 
                                 data-id="<?php echo $row['id'] ?>"
                                 data-code="<?php echo htmlspecialchars($row['code']) ?>"
                                 data-name="<?php echo htmlspecialchars($row['subject']) ?>"
                                 data-department="<?php echo htmlspecialchars($row['department']) ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-grip-vertical handle mr-2 d-none d-md-inline"></i>
                                        <span class="subject-code"><?php echo htmlspecialchars($row['code']) ?></span>
                                        <span class="subject-name"><?php echo htmlspecialchars($row['subject']) ?></span>
                                    </div>
                                    <!-- Add mobile-only assign button -->
                                    <button class="btn btn-sm btn-primary assign-subject d-md-none" 
                                            data-id="<?php echo $row['id'] ?>"
                                            data-code="<?php echo htmlspecialchars($row['code']) ?>"
                                            data-name="<?php echo htmlspecialchars($row['subject']) ?>">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Assignments Panel -->
        <div class="col-md-8">
            <div class="content-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Class Subject Assignments</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="classFilter" class="modern-select">
                                <option value="">All Classes</option>
                                <?php
                                $classes = $conn->query("SELECT cl.id, cl.department, CONCAT(cl.curriculum,' ',cl.level,' - ',cl.section) as class_name 
                                                       FROM class_list cl 
                                                       ORDER BY cl.curriculum ASC, cl.level ASC");
                                while($row = $classes->fetch_assoc()):
                                ?>
                                <option value="<?php echo $row['id'] ?>" 
                                        data-department="<?php echo htmlspecialchars($row['department']) ?>">
                                    <?php echo htmlspecialchars($row['class_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                            <button id="printAssignments" class="btn btn-secondary ml-2">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="classAssignments" class="assignments-container">
                        <!-- Classes will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add mobile class selection modal -->
<div class="modal fade" id="mobileAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Subject to Class</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Select a class to assign <strong id="selectedSubject"></strong> to:</p>
                <select id="mobileClassSelect" class="form-control">
                    <option value="">Choose a class...</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAssign">Assign</button>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 2rem;
    background-color: #f8f9fa;
    min-height: 100vh;
}

.header-card {
    background: linear-gradient(135deg, #2193b0, #6dd5ed);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: white;
}

.content-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
    transition: transform 0.2s;
    height: 100%;
}

.content-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
}

.card-title {
    color:#1F2D3D;
            font-size: 18px;
    margin: 0;
    font-weight: 600;
}

/* Filter Chips */
.filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.chip {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 1px solid #2193b0;
    background: transparent;
    color: #2193b0;
    transition: all 0.3s;
    font-size: 0.9rem;
    cursor: pointer;
    outline: none;
}

.chip:focus {
    box-shadow: 0 0 0 2px rgba(33, 147, 176, 0.2);
    border-color: #2193b0;
    outline: none;
}

.chip.active, 
.chip:hover {
    background: #2193b0;
    color: white;
    border-color: #2193b0;
}

/* Optional: Add a different style for active + focus state */
.chip.active:focus {
    box-shadow: 0 0 0 2px rgba(33, 147, 176, 0.3);
}

/* Search Input */
.search-container {
    position: relative;
    margin-bottom: 1rem;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    transition: all 0.3s;
}

.search-input:focus {
    border-color: #2193b0;
    box-shadow: 0 0 0 3px rgba(33, 147, 176, 0.1);
}

/* Subject Items */
.subject-item {
    padding: 1rem;
    margin: 0.5rem 0;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    cursor: move;
    transition: all 0.3s;
}

.subject-item:hover {
    background: #f8f9fa;
    border-color: #2193b0;
}

/* Dropzone */
.class-dropzone {
    min-height: 100px;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    margin: 1rem 0;
    padding: 1rem;
    transition: all 0.3s;
}

.class-dropzone.dragover {
    background: rgba(33, 147, 176, 0.1);
    border-color: #2193b0;
}

/* Modern Select */
.modern-select {
    padding: 0.5rem 2rem 0.5rem 1rem;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    background: white;
    color: #2c3e50;
    appearance: none;
    cursor: pointer;
    transition: all 0.3s ease;
    outline: none;
    
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%232193b0' d='M6 8.825L1.175 4 2.238 2.938 6 6.7l3.763-3.763L10.825 4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.7rem center;
    background-size: 0.8rem;
}

.modern-select:focus {
    border-color: #2193b0;
    box-shadow: 0 0 0 3px rgba(33, 147, 176, 0.1);
}

.modern-select:hover {
    border-color: #2193b0;
}

/* Style for the options within the select */
.modern-select option {
    padding: 0.5rem;
    background: white;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .header-content {
        text-align: center;
    }
    
    .filter-chips {
        justify-content: center;
    }
    
    .subject-item {
        padding: 0.75rem;
        margin: 0.5rem 0;
    }
    
    .subject-code {
        display: block;
        font-weight: bold;
        margin-bottom: 0.25rem;
    }
    
    .btn.assign-subject {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .modal-dialog {
        margin: 1rem;
    }
}

@media (min-width: 768px) {
    .sticky-panel {
        position: sticky;
        top: 1rem;
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
    }
}

/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    .dashboard-container,
    .assignments-container,
    .assignments-container * {
        visibility: visible;
    }
    
    .dashboard-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 0;
        background: white;
    }
    
    .no-print,
    .btn,
    .modern-select,
    .col-md-4,
    .card-header button,
    .card-header select {
        display: none !important;
    }
    
    .col-md-8 {
        width: 100% !important;
    }
    
    .content-card {
        box-shadow: none;
        border: none;
    }
    
    .card-header {
        border-bottom: 2px solid #000;
    }
    
    .card-title {
        font-size: 24px;
        margin-bottom: 20px;
    }
    
    .class-dropzone {
        border: 1px solid #000;
        margin: 15px 0;
        page-break-inside: avoid;
    }
}
</style>

<script>
$(document).ready(function() {
    loadClassAssignments();

    // Initialize drag and drop
    initializeDragAndDrop();

    // Search functionality
    $('#subjectSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.subject-item').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
    });

    // Class filter
    $('#classFilter').change(function() {
        loadClassAssignments($(this).val());
    });

    // Department filter click handler
    $('.filter-chips .chip').click(function() {
        $('.filter-chips .chip').removeClass('active');
        $(this).addClass('active');
        
        const department = $(this).data('department');
        
        // Filter both subjects and classes
        filterSubjects(department);
        filterClasses(department);
        
        // Load assignments for the selected department
        loadClassAssignmentsByDepartment(department);
    });

    // Filter classes based on department
    function filterClasses(department) {
        $('#classFilter option').each(function() {
            if ($(this).val() === '') return true; // Skip the "All Classes" option
            
            const classDepartment = $(this).data('department');
            if (!department || classDepartment === department) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Reset the class filter to "All Classes"
        $('#classFilter').val('');
    }

    // Updated subject filtering function
    function filterSubjects(department) {
        const searchTerm = $('#subjectSearch').val().toLowerCase();
        
        $('.subject-item').each(function() {
            const text = $(this).text().toLowerCase();
            const subjectDepartment = $(this).data('department');
            
            const matchesSearch = text.includes(searchTerm);
            const matchesDepartment = !department || subjectDepartment === department;
            
            $(this).toggle(matchesSearch && matchesDepartment);
        });
    }

    // Handle mobile assign button click
    $(document).on('click', '.assign-subject', function(e) {
        e.preventDefault();
        const subjectId = $(this).data('id');
        const subjectCode = $(this).data('code');
        const subjectName = $(this).data('name');
        
        // Update modal with subject info
        $('#selectedSubject').text(`${subjectCode} - ${subjectName}`);
        
        // Populate class select with current options from main class filter
        $('#mobileClassSelect').html($('#classFilter').html());
        
        // Store subject data for later use
        $('#mobileAssignModal').data('subject-id', subjectId);
        
        // Show modal
        $('#mobileAssignModal').modal('show');
    });

    // Handle confirm assign button click
    $('#confirmAssign').click(function() {
        const subjectId = $('#mobileAssignModal').data('subject-id');
        const classId = $('#mobileClassSelect').val();
        
        if (!classId) {
            showToast('Please select a class', 'warning');
            return;
        }
        
        // Use existing assign function
        assignSubjectToClass(subjectId, classId);
        
        // Close modal
        $('#mobileAssignModal').modal('hide');
    });

    // Add print handler
    $('#printAssignments').click(function() {
        // Update the header with current filter information
        const selectedClass = $('#classFilter option:selected').text();
        const selectedDepartment = $('.filter-chips .chip.active').text();
        
        // Create a temporary header for printing
        const printHeader = $('<div class="print-header text-center mb-4">')
            .append(`<h3>Class Subject Assignments</h3>`)
            .append(`<p>Class: ${selectedClass}</p>`)
            .append(`<p>Department: ${selectedDepartment}</p>`)
            .append(`<p>Date: ${new Date().toLocaleDateString()}</p>`);
        
        // Temporarily add the header
        $('#classAssignments').prepend(printHeader);
        
        // Print the document
        window.print();
        
        // Remove the temporary header
        $('.print-header').remove();
    });
});

function loadClassAssignments(classId = '') {
    $.ajax({
        url: 'ajax.php?action=get_class_assignments',
        method: 'POST',
        data: { class_id: classId },
        success: function(response) {
            $('#classAssignments').html(response);
            initializeDropZones();
        }
    });
}

function initializeDragAndDrop() {
    const draggables = document.querySelectorAll('.subject-item');
    draggables.forEach(draggable => {
        draggable.addEventListener('dragstart', e => {
            draggable.classList.add('dragging');
            e.dataTransfer.setData('text/plain', draggable.dataset.id);
        });

        draggable.addEventListener('dragend', () => {
            draggable.classList.remove('dragging');
        });
    });
}

function initializeDropZones() {
    const dropZones = document.querySelectorAll('.class-dropzone');
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('dragover');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('dragover');
        });

        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('dragover');
            
            const subjectId = e.dataTransfer.getData('text/plain');
            const classId = zone.dataset.classId;
            
            assignSubjectToClass(subjectId, classId);
        });
    });
}

function assignSubjectToClass(subjectId, classId) {
    // Get the subject details from the dragged element
    const subjectElement = document.querySelector(`.subject-item[data-id="${subjectId}"]`);
    const subjectCode = subjectElement.dataset.code;
    const subjectName = subjectElement.dataset.name;

    $.ajax({
        url: 'ajax.php?action=save_class_subject',
        method: 'POST',
        data: {
            subject_id: subjectId,
            class_id: classId,
            subject_code: subjectCode,
            subject_name: subjectName
        },
        success: function(response) {
            if(response == 1) {
                showToast('Subject assigned successfully', 'success');
                loadClassAssignments($('#classFilter').val());
            } else if(response == 2) {
                showToast('Subject is already assigned to this class', 'warning');
            } else {
                showToast('Failed to assign subject', 'error');
                console.log('Server response:', response); // For debugging
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error); // For debugging
            showToast('Failed to assign subject', 'error');
        }
    });
}

function removeSubject(assignmentId) {
    Swal.fire({
        title: 'Remove Subject',
        text: 'Are you sure you want to remove this subject?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax.php?action=delete_class_subject',
                method: 'POST',
                data: { id: assignmentId },
                success: function(response) {
                    if(response == 1) {
                        showToast('Subject removed successfully', 'success');
                        loadClassAssignments($('#classFilter').val());
                    } else {
                        showToast('Failed to remove subject', 'error');
                    }
                }
            });
        }
    });
}

function showToast(message, icon) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    Toast.fire({
        icon: icon,
        title: message
    });
}

function updateClassFilter(department) {
    // If department is empty (All selected), fetch all classes
    if (!department) {
        $.ajax({
            url: 'ajax.php?action=get_classes_by_department',
            method: 'POST',
            data: { department: '' },  // Explicitly send empty department
            success: function(response) {
                $('#classFilter').html('<option value="">All Classes</option>' + response);
                loadClassAssignments($('#classFilter').val());
            }
        });
    } else {
        // Department specific filter
        $.ajax({
            url: 'ajax.php?action=get_classes_by_department',
            method: 'POST',
            data: { department: department },
            success: function(response) {
                $('#classFilter').html('<option value="">All Classes</option>' + response);
                loadClassAssignments($('#classFilter').val());
            }
        });
    }
}

function loadClassAssignmentsByDepartment(department) {
    $.ajax({
        url: 'ajax.php?action=get_class_assignments',
        method: 'POST',
        data: { 
            department: department
        },
        success: function(response) {
            $('#classAssignments').html(response);
            initializeDropZones();
        }
    });
}
</script>