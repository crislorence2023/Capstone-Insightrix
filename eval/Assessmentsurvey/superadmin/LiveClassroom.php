<?php include('./db_connect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Management</title>
   
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Updated styles to match student list */
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            
        }
        
        
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin: 20px;
            overflow: hidden;
            width: auto;
            margin-top: 1rem;
        }
        
        .card-header {
            background: none;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .card-header h4 {
            margin: 0;
            color: #1F2D3D;
            font-size: 24px;
            font-weight: 600;
        }
        
        /* Updated resource sections */
        .resource-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: #f8f9fa;
            color: #495057;
            border-radius: 6px;
            text-transform: uppercase;
        }
        
        .draggable-item {
            padding: 12px 15px;
            margin: 8px 0;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: move;
            font-size: 1rem;
            transition: background 0.2s ease;
            position: relative;
            will-change: transform;
        }
        
        /* Updated drop zones */
        .drop-zone {
            min-height: 150px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            background: #fff;
        }
        
        /* Updated button styles */
        .btn-group .btn {
            padding: 8px 16px;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .btn-outline-primary {
            border-color: #dee2e6;
            color: #495057;
        }
        
        .btn-outline-primary:hover,
        .btn-outline-primary.active {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #1F2D3D;
        }
        
        /* Form controls */
        .form-control {
            padding: 10px 15px;
            height: auto;
            border-radius: 6px;
            font-size: 1rem;
            border: 1px solid #dee2e6;
        }
        
        /* Subject-faculty assignment styling */
        .subject-assignment {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .faculty-assigned {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 8px;
            font-size: 1rem;
        }
        
        /* Action buttons */
        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            min-width: 32px;
        }
        
        .remove-item,
        .remove-assignment,
        .remove-faculty {
            padding: 4px 10px;
            font-size: 1rem;
        }
        
        /* Scrollbar styling */
        .draggable-list::-webkit-scrollbar {
            width: 8px;
        }
        
        .draggable-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .draggable-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .draggable-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Additional styles to match student list while preserving existing structure */
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin: 20px;
            overflow: hidden;
        }

        .card-header h4 {
            color: #1F2D3D;
            font-size: 24px;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
        }

        .draggable-item {
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            padding: 12px 15px;
            margin: 8px 0;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: move;
            transition: background 0.2s ease;
            position: relative;
            will-change: transform;
        }

        .resource-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .btn-group .btn {
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            font-weight: 600;
        }

        .faculty-assigned {
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
        }

        .remove-item,
        .remove-assignment,
        .remove-faculty {
            font-size: 1rem;
        }

        /* Preserve existing structure while updating specific elements */
        #class_students,
        #class_assignments {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #fff;
        }

        .subject-assignment .card {
            margin: 0;
            box-shadow: none;
        }

        .dashboard-container {
            padding: 2rem;
            background-color: #f8f9fa;
            min-height: 100vh;
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
            color: #1F2D3D;
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

        .chip.active, 
        .chip:hover {
            background: #2193b0;
            color: white;
            border-color: #2193b0;
        }

        /* Draggable Items */
        .draggable-item {
            padding: 1rem;
            margin: 0.5rem 0;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            cursor: move;
            transition: all 0.3s;
        }

        .draggable-item:hover {
            background: #f8f9fa;
            border-color: #2193b0;
        }

        /* Drop Zones */
        .drop-zone {
            min-height: 100px;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            margin: 1rem 0;
            padding: 1rem;
            transition: all 0.3s;
        }

        .drop-zone.dragover {
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
        }

        .modern-select:focus {
            border-color: #2193b0;
            box-shadow: 0 0 0 3px rgba(33, 147, 176, 0.1);
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .draggable-item {
                padding: 0.75rem;
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

        /* Resources Card Styling */
        .resources-card {
            position: sticky;
            top: 20px;  /* Adjust this value based on your needs */
            height: calc(100vh - 40px);  /* Adjust based on your needs */
            display: flex;
            flex-direction: column;
        }

        .scrollable-resources {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        /* Custom Scrollbar for Resources */
        .scrollable-resources::-webkit-scrollbar {
            width: 8px;
        }

        .scrollable-resources::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .scrollable-resources::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .scrollable-resources::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Remove individual scrollbars */
        .draggable-list {
            overflow: visible;
        }

        /* Ensure card header stays fixed */
        .resources-card .card-header {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .resources-card {
                height: auto;
                max-height: 500px; /* Adjust for mobile */
            }
        }

        /* Accordion Styles */
        .accordion-item {
            border: 1px solid #dee2e6;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .accordion-button {
            padding: 1rem;
            font-weight: 600;
            color: #333;
            background-color: #f8f9fa;
            border: none;
        }

        .accordion-button:not(.collapsed) {
            color: #2193b0;
            background-color: rgba(33, 147, 176, 0.1);
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .accordion-body {
            padding: 1rem;
            background-color: #fff;
        }

        /* Update draggable items within accordion */
        .accordion .draggable-item {
            margin: 0.5rem 0;
        }

        /* Ensure draggable functionality works with accordion */
        .ui-draggable-dragging {
            z-index: 9999;
        }

        /* Custom Accordion Styles */
        .custom-accordion {
            border-radius: 8px;
            overflow: hidden;
        }

        .accordion-section {
            margin-bottom: 8px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
        }

        .accordion-header {
            padding: 15px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #333;
            transition: all 0.3s ease;
        }

        .accordion-header:hover {
            background: #e9ecef;
        }

        .accordion-header.active {
            background: #e9ecef;
            color: #2193b0;
        }

        .accordion-icon {
            width: 12px;
            height: 12px;
            position: relative;
            transition: transform 0.3s ease;
        }

        .accordion-icon::before,
        .accordion-icon::after {
            content: '';
            position: absolute;
            background: #666;
            transition: all 0.3s ease;
        }

        .accordion-icon::before {
            width: 2px;
            height: 12px;
            left: 5px;
            top: 0;
        }

        .accordion-icon::after {
            width: 12px;
            height: 2px;
            left: 0;
            top: 5px;
        }

        .accordion-header.active .accordion-icon::before {
            transform: rotate(90deg);
            opacity: 0;
        }

        .accordion-content {
            display: none;
            padding: 15px;
            background: #fff;
            border-top: 1px solid #dee2e6;
        }

        /* Ensure draggable items work properly within accordion */
        .accordion-content .draggable-item {
            margin: 8px 0;
        }

        .ui-draggable-dragging {
            z-index: 9999;
        }

        .ui-draggable-dragging {
            transition: none !important;
            animation: none !important;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }

        /* Updated Resources Card Styling */
        .resources-card {
            position: sticky;
            top: 20px;  /* Adjust this value based on your needs */
            height: calc(100vh - 40px);  /* Adjust based on your needs */
            display: flex;
            flex-direction: column;
        }

        /* Updated Class Students Container */
        #class_students {
            max-height: 300px;  /* Adjust this value based on your needs */
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #fff;
        }

        /* Add custom scrollbar for class students */
        #class_students::-webkit-scrollbar {
            width: 8px;
        }

        #class_students::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        #class_students::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        #class_students::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Ensure the classroom setup card doesn't create unnecessary space */
        .resource-section {
            margin-bottom: 1rem;
        }

        /* Adjust spacing in classroom setup */
        .card-body {
            padding: 1rem;
            
        }

        .resource-title {
            margin-bottom: 0.5rem;
        }

        /* Ensure proper spacing between sections */
        .resource-section + .resource-section {
            margin-top: 1rem;
        }

        /* Add these to your existing styles */
        .draggable-item {
            position: relative;
        }

        .check-indicator {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #28a745;
            font-weight: bold;
        }

        .draggable-item.assigned {
            opacity: 0.7;
        }

        .draggable-item.assigned:hover {
            opacity: 1;
        }

        /* Add to your existing styles */
        .department-filters {
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Add these new styles */
        .class-selection-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 1200px;  /* Adjust this value based on your needs */
            margin: 0 auto;
            padding: 0 15px;
        }

        .class-selection-card {
            width: 100%;
            background: none;
            border: none;
            box-shadow: none;
        }

        .class-selection-card .card-body {
            padding: 1rem 0;
        }

        /* Style the select dropdown */
        .class-selector-wrapper {
            width: 100%;
            max-width: 400px;
            margin: 0 auto 0 auto;
        }

        .class-selector-wrapper select {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background-color: transparent;
            font-size: 1rem;
            color: #495057;
        }

        /* Style the department filters */
        .department-filters {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .department-filters .btn {
            background: none;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .department-filters .btn:hover,
        .department-filters .btn.active {
            background-color: #f8f9fa;
            border-color: #2193b0;
            color: #2193b0;
        }

        /* Remove card styling from container */
        .class-selection-container .card {
            background: none;
            border: none;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-3">
            <!-- Class Selection -->
            <div class="col-12 mb-3">
                <div class="class-selection-container">
                    <div class="class-selection-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="class-selector-wrapper">

                                    <div class="btn-group department-filters mb-2" role="group">
                                        <button type="button" class="btn btn-outline-primary active" data-department="all">All</button>
                                        <?php 
                                        $departments = $conn->query("SELECT * FROM department_list ORDER BY name ASC");
                                        while($row = $departments->fetch_assoc()):
                                        ?>
                                        <button type="button" class="btn btn-outline-primary" data-department="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></button>
                                        <?php endwhile; ?>
                                    </div>
                                        <select class="form-control" id="class_selector">
                                            <option value="">Select Class</option>
                                            <?php 
                                            $classes = $conn->query("SELECT * FROM class_list 
                                                               ORDER BY curriculum ASC, level ASC, section ASC");
                                            while($row = $classes->fetch_assoc()):
                                            ?>
                                            <option value="<?php echo $row['id'] ?>" data-department="<?php echo $row['department'] ?>">
                                                <?php echo $row['curriculum'].' '.$row['level'].'-'.$row['section'] ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Resources -->
            <div class="col-md-4">
                <div class="card resources-card">
                    <div class="card-header">
                        <h4 style="color: #333;">Available Resources</h4>
                    </div>
                    <div class="card-body scrollable-resources" style="background: #ffffff;">
                        <!-- Custom Accordion -->
                        <div class="custom-accordion">
                            <!-- Students Section -->
                            <div class="accordion-section">
                                <div class="accordion-header active">
                                    <span>Students</span>
                                    <i class="accordion-icon"></i>
                                </div>
                                <div class="accordion-content" style="display: block;">
                                    <div class="draggable-list" id="available_students">
                                        <?php 
                                        $students = $conn->query("SELECT * FROM student_list ORDER BY lastname ASC, firstname ASC");
                                        if($students->num_rows > 0):
                                            while($row = $students->fetch_assoc()):
                                        ?>
                                        <div class="draggable-item student-item" data-id="<?php echo $row['id'] ?>" data-type="student">
                                            <?php echo $row['lastname'].', '.$row['firstname'] ?>
                                        </div>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                        <div class="text-center text-muted p-3">No students in database</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                              <!-- Subjects Section -->
                              <div class="accordion-section">
                                <div class="accordion-header">
                                    <span>Subjects</span>
                                    <i class="accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="draggable-list" id="available_subjects">
                                        <?php 
                                        $subjects = $conn->query("SELECT * FROM subject_list ORDER BY subject ASC");
                                        while($row = $subjects->fetch_assoc()):
                                        ?>
                                        <div class="draggable-item subject-item" data-id="<?php echo $row['id'] ?>" data-type="subject">
                                            <?php echo $row['code'].' - '.$row['subject'] ?>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Faculty Section -->
                            <div class="accordion-section">
                                <div class="accordion-header">
                                    <span>Faculty</span>
                                    <i class="accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="draggable-list" id="available_faculty">
                                        <?php 
                                        $faculty = $conn->query("SELECT * FROM faculty_list ORDER BY lastname ASC, firstname ASC");
                                        while($row = $faculty->fetch_assoc()):
                                        ?>
                                        <div class="draggable-item faculty-item" data-id="<?php echo $row['id'] ?>" data-type="faculty">
                                            <?php echo $row['lastname'].', '.$row['firstname'] ?>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>

                          
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classroom Setup -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Classroom Setup</h4>
                    </div>
                    <div class="card-body">
                        <!-- Class Students -->
                        <div class="resource-section">
                            <div class="resource-title">Class Students</div>
                            <div class="drop-zone" id="class_students"></div>
                        </div>

                        <!-- Class Subjects and Faculty -->
                        <div class="resource-section mt-3">
                            <div class="resource-title">Subject-Faculty Assignments</div>
                            <div class="drop-zone" id="class_assignments">
                                <div class="assignment-instructions">
                                    Drag a subject first, then drag a faculty member to assign them to the subject
                                </div>
                            </div>
                        </div>

                        <!-- Deploy Button -->
                        <button class="btn btn-success btn-block mt-3" id="deploy_btn">
                            <i class="fa fa-save"></i> Save and Deploy Classroom
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    <!-- Add jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Custom Accordion functionality
            $('.accordion-header').click(function() {
                const $header = $(this);
                const $content = $header.next('.accordion-content');
                const $section = $header.parent('.accordion-section');
                
                // Close other sections
                $('.accordion-section').not($section).find('.accordion-header').removeClass('active');
                $('.accordion-section').not($section).find('.accordion-content').slideUp(300);
                
                // Toggle current section
                $header.toggleClass('active');
                $content.slideToggle(300, function() {
                    // Reinitialize draggable items after animation
                    initializeDraggable();
                });
            });

            // Initialize draggable items with improved handling
            function initializeDraggable() {
                $(".draggable-item").draggable({
                    helper: "clone",
                    revert: false,         // Disable revert animation
                    cursor: "move",
                    appendTo: "parent",
                    zIndex: 100,
                    opacity: 0.8,          // Slightly more solid
                    distance: 2,           // Shorter distance to start drag
                    scroll: false,
                    refreshPositions: true,// More accurate positioning
                    start: function(event, ui) {
                        $(ui.helper).css({
                            'transition': 'none',    // Disable CSS transitions while dragging
                            'transform': 'none'      // Remove any transforms
                        });
                    },
                    drag: function(event, ui) {
                        // Smooth movement by rounding positions
                        ui.position.left = Math.round(ui.position.left);
                        ui.position.top = Math.round(ui.position.top);
                    }
                });
            }

            // Initial draggable setup
            initializeDraggable();

            // Your existing droppable and other functionality...
            $("#class_students").droppable({
                accept: ".student-item",
                classes: {
                    "ui-droppable-hover": "hover"
                },
                drop: function(event, ui) {
                    if (!$('#class_selector').val()) {
                        alert('Please select a class first before adding students.');
                        return false;
                    }
                    handleStudentDrop(ui.draggable);
                }
            });

            $("#class_assignments").droppable({
                accept: ".subject-item, .faculty-item",
                classes: {
                    "ui-droppable-hover": "hover"
                },
                drop: function(event, ui) {
                    if (!$('#class_selector').val()) {
                        alert('Please select a class first before adding assignments.');
                        return false;
                    }
                    handleAssignmentDrop(ui.draggable);
                }
            });

            // Handle class selection change
            $('#class_selector').change(function() {
                loadClassDetails($(this).val());
            });

            // Handle deploy button
            $('#deploy_btn').click(function(e) {
                e.preventDefault();
                
                const classId = $('#class_selector').val();
                const className = $('#class_selector option:selected').text();
                
                // Check if class is selected
                if (!classId) {
                    alert('Please select a class first.');
                    return;
                }

                // Get counts of students and assignments
                const studentCount = $('#class_students .student-item').length;
                const assignmentCount = $('#class_assignments .subject-assignment').length;
                
                // Check for complete subject-faculty assignments
                const incompleteAssignments = $('#class_assignments .faculty-placeholder:contains("Drag faculty here")').length;
                
                // Validation checks
                if (studentCount === 0) {
                    alert('Please add at least one student to the class before deploying.');
                    return;
                }
                
                if (assignmentCount === 0) {
                    alert('Please add at least one subject-faculty assignment before deploying.');
                    return;
                }
                
                if (incompleteAssignments > 0) {
                    alert('Please assign faculty members to all subjects before deploying.');
                    return;
                }
                
                // Create confirmation message
                const confirmMessage = `Are you sure you want to deploy this classroom setup?\n\n` +
                    `Class: ${className}\n` +
                    `Students: ${studentCount}\n` +
                    `Subject-Faculty Assignments: ${assignmentCount}\n\n` +
                    `Note: Please assign correctly.`;

                // Show confirmation dialog
                if (confirm(confirmMessage)) {
                    saveClassroom();
                }
            });
        });

        function resetChecks() {
            $('.draggable-item').removeClass('assigned');
            $('.draggable-item').find('.check-indicator').remove();
        }

        function handleStudentDrop(item) {
            const studentId = item.data('id');
            const studentName = item.text();
            
            // Check if student is already added
            if ($(`#class_students .student-item[data-id="${studentId}"]`).length > 0) {
                alert(`${studentName} is already added to this class!`);
                return false;
            }
            
            // Add student with a success indicator
            const studentElement = $(`<div class="draggable-item student-item" data-id="${studentId}">
                ${studentName}
                <button class="btn btn-sm btn-danger float-right remove-item">×</button>
            </div>`);
            
            // Add check mark to original item in available resources
            item.addClass('assigned');
            if (!item.find('.check-indicator').length) {
                item.append('<span class="check-indicator">✓</span>');
            }
            
            $('#class_students').append(studentElement);
        }

        function handleAssignmentDrop(item) {
            const itemType = item.data('type');
            const itemId = item.data('id');
            const itemName = item.text();

            if (itemType === 'subject') {
                // Check if subject is already assigned
                if ($(`#class_assignments .subject-assignment[data-subject-id="${itemId}"]`).length > 0) {
                    alert(`${itemName} is already assigned to this class!`);
                    return false;
                }
                
                const assignmentElement = $(`<div class="subject-assignment mb-2" data-subject-id="${itemId}">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="subject-name">${itemName}</h6>
                            <div class="faculty-placeholder">Drag faculty here</div>
                            <button class="btn btn-sm btn-danger float-right remove-assignment">×</button>
                        </div>
                    </div>
                </div>`);
                
                // Add check mark to original subject item
                item.addClass('assigned');
                if (!item.find('.check-indicator').length) {
                    item.append('<span class="check-indicator">✓</span>');
                }
                
                $('#class_assignments').append(assignmentElement);
            } else if (itemType === 'faculty') {
                const subjectAssignment = $(event.target).closest('.subject-assignment');
                const facultyId = itemId;
                
                // Check if this subject already has a faculty assigned
                if (subjectAssignment.find('.faculty-assigned').length > 0) {
                    alert(`This subject already has an instructor assigned. Please remove the current instructor first.`);
                    return false;
                }
                
                const placeholder = subjectAssignment.find('.faculty-placeholder');
                if (placeholder.length > 0) {
                    placeholder.html(`
                        <div class="faculty-assigned" data-faculty-id="${itemId}">
                            ${itemName}
                            <button class="btn btn-sm btn-warning float-right remove-faculty">×</button>
                        </div>
                    `);
                    
                    // Add check mark to original faculty item but don't prevent reuse
                    if (!item.find('.check-indicator').length) {
                        item.append('<span class="check-indicator">✓</span>');
                    }
                }
            }
        }

        function loadClassDetails(classId) {
            // Reset all checks when changing classes
            resetChecks();
            
            if (!classId) {
                $('#class_students').html('<div class="text-center text-muted">Please select a class</div>');
                $('#class_assignments').html('<div class="assignment-instructions">Drag a subject first, then drag a faculty member to assign them to the subject</div>');
                return;
            }
            
            $.ajax({
                url: 'ajax.php?action=get_class_students',
                method: 'POST',
                data: { class_id: classId },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            // Handle assigned students (show only students in this class)
                            $('#class_students').empty();
                            if (data.assigned_empty) {
                                $('#class_students').html(
                                    '<div class="text-center text-muted p-3">No students assigned to this class</div>'
                                );
                            } else {
                                data.assigned_students.forEach(student => {
                                    $('#class_students').append(`
                                        <div class="draggable-item student-item" data-id="${student.id}" data-type="student">
                                            ${student.lastname}, ${student.firstname}
                                            <button class="btn btn-sm btn-danger float-right remove-item">×</button>
                                        </div>
                                    `);
                                });
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                }
            });
        }

        function saveClassroom() {
            const classId = $('#class_selector').val();
            const currentAcademicYear = <?php 
                $current_academic = $conn->query("SELECT id FROM academic_list WHERE status = 1 ORDER BY id DESC LIMIT 1");
                echo $current_academic->num_rows > 0 ? $current_academic->fetch_assoc()['id'] : 0;
            ?>;
            
            // Collect students and assignments
            const students = [];
            $('#class_students .student-item').each(function() {
                students.push($(this).data('id'));
            });

            const assignments = [];
            $('#class_assignments .subject-assignment').each(function() {
                const subjectId = $(this).data('subject-id');
                const facultyId = $(this).find('.faculty-assigned').data('faculty-id');
                if (subjectId && facultyId) {
                    assignments.push({
                        subject_id: subjectId,
                        faculty_id: facultyId
                    });
                }
            });

            // Save via AJAX
            $.ajax({
                url: 'ajax.php?action=save_classroom_assignment',
                method: 'POST',
                data: {
                    class_id: classId,
                    academic_year_id: currentAcademicYear,
                    students: students,
                    assignments: assignments
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        if (data.message === 'update' && data.existing_assignments) {
                            if (confirm('This class already has assignments. Are you sure you want to update them?')) {
                                alert('Classroom assignments updated successfully!');
                                resetChecks();
                                location.reload();
                            }
                        } else {
                            alert('Classroom setup saved successfully!');
                            resetChecks();
                            location.reload();
                        }
                    } else {
                        alert('Error saving classroom setup: ' + data.message);
                    }
                }
            });
        }

        // Add event handlers for remove buttons
        $(document).on('click', '.remove-item', function() {
            const item = $(this).closest('.draggable-item');
            const itemId = item.data('id');
            // Remove check from original item
            $(`.draggable-item[data-id="${itemId}"]`).removeClass('assigned').find('.check-indicator').remove();
            item.remove();
        });

        $(document).on('click', '.remove-assignment', function() {
            const assignment = $(this).closest('.subject-assignment');
            const subjectId = assignment.data('subject-id');
            // Remove check from original subject item
            $(`.subject-item[data-id="${subjectId}"]`).removeClass('assigned').find('.check-indicator').remove();
            // Remove check from faculty if assigned
            const facultyId = assignment.find('.faculty-assigned').data('faculty-id');
            if (facultyId) {
                $(`.faculty-item[data-id="${facultyId}"]`).removeClass('assigned').find('.check-indicator').remove();
            }
            assignment.remove();
        });

        $(document).on('click', '.remove-faculty', function() {
            const facultyAssigned = $(this).closest('.faculty-assigned');
            const facultyId = facultyAssigned.data('faculty-id');
            
            // Only remove the check indicator if this faculty isn't assigned elsewhere
            if ($(`#class_assignments .faculty-assigned[data-faculty-id="${facultyId}"]`).length <= 1) {
                $(`.faculty-item[data-id="${facultyId}"]`).find('.check-indicator').remove();
            }
            
            facultyAssigned.parent().html('Drag faculty here');
        });

        // Department filtering
        $('.department-filters .btn').click(function() {
            // Update active button
            $('.department-filters .btn').removeClass('active');
            $(this).addClass('active');
            
            const departmentId = $(this).data('department');
            const departmentName = $(this).text(); // Get the department name from button text
            
            // Filter class options
            if (departmentId === 'all') {
                $('#class_selector option').show();
            } else {
                $('#class_selector option').each(function() {
                    if ($(this).val() === '') return; // Skip the "Select Class" option
                    if ($(this).data('department') === departmentName) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
            
            // Reset class selection if currently selected class is not in filtered department
            const selectedOption = $('#class_selector option:selected');
            if (selectedOption.is(':hidden')) {
                $('#class_selector').val('');
                $('#class_students').html('<div class="text-center text-muted">Please select a class</div>');
            }
            
            if (departmentId === 'all') {
                // Show all items
                $('.draggable-item').show();
            } else {
                // Hide all items first
                $('.draggable-item').hide();
                
                // Show only items from selected department
                $.ajax({
                    url: 'ajax.php?action=get_department_resources',
                    method: 'POST',
                    data: { department_id: departmentId },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.status === 'success') {
                                // Show matching students
                                data.students.forEach(studentId => {
                                    $(`.student-item[data-id="${studentId}"]`).show();
                                });
                                
                                // Show matching faculty
                                data.faculty.forEach(facultyId => {
                                    $(`.faculty-item[data-id="${facultyId}"]`).show();
                                });
                                
                                // Show matching subjects
                                data.subjects.forEach(subjectId => {
                                    $(`.subject-item[data-id="${subjectId}"]`).show();
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>