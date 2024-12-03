<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Assignments</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #1a202c;
            line-height: 1.5;
           
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        h2 {
            color: #1a202c;
            margin-bottom: 2rem;
            font-weight: 700;
            font-size: 1.875rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        

        .filters {
            background: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            border: 1px solid #e2e8f0;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .filter-group label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        select {
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            background-color: white;
            transition: all 0.2s ease;
            cursor: pointer;
            color: #2d3748;
            font-weight: 500;
        }

        select:hover {
            border-color: #cbd5e0;
        }

        select:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }

        .section-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .section-card:hover {
          
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-header h4 {
            color: #1a202c;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
        }

        .section-header p {
            color: #2d3748;
            font-size: 1rem;
            margin: 0.25rem 0;
        }

        .card-body {
            padding: 1.25rem;
        }

        .card-body h5 {
            color: #1a202c;
            font-size: 1rem;
            margin: 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 1rem;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            color: #1a202c;
        }

        th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #2d3748;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .no-results i {
            font-size: 2rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        .no-results p {
            font-size: 1.1rem;
        }

        .no-results .text-sm {
            font-size: 1rem;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .badge-primary {
            background-color: #ebf5ff;
            color: #4299e1;
        }

        .badge-secondary {
            background-color: #f0fff4;
            color: #48bb78;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .filters {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .export-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }

            .export-btn {
                flex: 1 1 calc(50% - 0.5rem);
                min-width: 120px;
                padding: 0.75rem;
                justify-content: center;
            }

            .section-card {
                margin-bottom: 1rem;
            }

            .section-header {
                padding: 1rem;
            }

            .section-header h4 {
                font-size: 1.1rem;
            }

            .card-body {
                padding: 1rem;
            }

            /* Make tables scrollable horizontally on mobile */
            .table-container {
                margin: 0 -1rem;
                padding: 0 1rem;
                width: calc(100% + 2rem);
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 0.5rem;
            }

            /* Adjust pagination for mobile */
            .pagination-container {
                flex-direction: column;
                gap: 0.75rem;
                align-items: center;
            }

            .pagination-controls {
                flex-wrap: wrap;
                justify-content: center;
            }

            .pagination-button {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }

            /* Show entries adjustments */
            .show-entries-wrapper {
                text-align: center;
                margin-bottom: 0.75rem;
            }

            .show-entries-wrapper label {
                justify-content: center;
            }
        }

        /* Add styles for very small screens */
        @media (max-width: 480px) {
            h2 {
                font-size: 1.25rem;
            }

            .export-btn {
                flex: 1 1 100%;
            }

            .section-header h4 {
                font-size: 1rem;
            }

            .section-header p {
                font-size: 0.9rem;
            }

            table {
                font-size: 0.8rem;
            }

            .pagination-button {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
        }

        /* Add these new styles for pagination */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            margin-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .pagination-info {
            color: #4a5568;
            font-size: 0.875rem;
        }

        .pagination-controls {
            display: flex;
            gap: 0.5rem;
        }

        .pagination-button {
            padding: 0.35rem 0.7rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: white;
            color: #4a5568;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .pagination-button:hover:not(:disabled) {
            background: #4299e1;
            color: white;
            border-color: #4299e1;
        }

        .pagination-button.active {
            background: #4299e1;
            color: white;
            border-color: #4299e1;
        }

        /* Update/add these table-related styles */
        .table-container {
            overflow-x: auto;
            margin-bottom: 1rem;
            background: white;
            border-radius: 8px;
        }

        table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
            margin: 0;
            font-size: 0.95rem;
            table-layout: fixed;
        }

        /* Define specific column widths for faculty table */
        .faculty-table th:nth-child(1) { width: 15%; } /* Subject Code */
        .faculty-table th:nth-child(2) { width: 25%; } /* Subject */
        .faculty-table th:nth-child(3) { width: 25%; } /* Instructor */
        .faculty-table th:nth-child(4) { width: 20%; } /* Academic Year */
        .faculty-table th:nth-child(5) { width: 15%; } /* Semester */

        /* Define specific column widths for student table */
        .student-table th:nth-child(1) { width: 10%; } /* # */
        .student-table th:nth-child(2) { width: 30%; } /* Student ID */
        .student-table th:nth-child(3) { width: 60%; } /* Name */

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #2d3748;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Update pagination container styles */
        .pagination-container {
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
            background: white;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .export-buttons {
            
            display: flex;
            gap: 0.5rem;
            
        }

        .export-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background: white;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .export-btn:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        .export-btn i {
            font-size: 1rem;
        }

        @media print {
            .filters, .export-buttons, .pagination-container, .show-entries-wrapper {
                display: none;
            }
            
            .section-card {
                break-inside: avoid;
                margin: 1rem 0;
                box-shadow: none;
            }
        }

        .show-entries-wrapper {
            margin-bottom: 1rem;
        }

        .show-entries-wrapper label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
            font-size: 0.875rem;
        }

        .entries-select {
            padding: 0.25rem 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background: white;
            color: #4a5568;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .export-btn span {
                display: none;  // Hide the text on mobile
            }
            
            .export-btn {
                padding: 0.5rem;  // Reduce padding for a more compact look
                min-width: auto;  // Allow buttons to be smaller
            }
            
            .export-buttons {
                justify-content: flex-end;  // Align buttons to the right
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Room Assignments</h2>
        
        <div class="filters">
            <div class="filter-group">
                <label for="academicYearFilter">Academic Year</label>
                <select id="academicYearFilter">
                    <option value="">All Academic Years</option>
                    <?php
                    $year_query = "SELECT DISTINCT id, year FROM academic_list ORDER BY year DESC";
                    $year_result = $conn->query($year_query);
                    while ($year = $year_result->fetch_assoc()) {
                        echo "<option value='" . $year['id'] . "'>" . $year['year'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="semesterFilter">Semester</label>
                <select id="semesterFilter">
                    <option value="">All Semesters</option>
                    <option value="1">First Semester</option>
                    <option value="2">Second Semester</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="departmentFilter">Department</label>
                <select id="departmentFilter">
                    <option value="">All Departments</option>
                    <?php
                    $dept_query = "SELECT DISTINCT department FROM class_list WHERE department = 'COE'";
                    $dept_result = $conn->query($dept_query);
                    while ($dept = $dept_result->fetch_assoc()) {
                        echo "<option value='" . $dept['department'] . "'>" . $dept['department'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="curriculumFilter">Class</label>
                <select id="curriculumFilter">
                    <option value="">All Classes</option>
                </select>
            </div>
            <div class="export-buttons">
                <button onclick="printTable()" class="export-btn">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </button>
                <button onclick="exportToExcel()" class="export-btn">
                    <i class="fas fa-file-excel"></i>
                    <span>Excel</span>
                </button>
                <button onclick="exportToCSV()" class="export-btn">
                    <i class="fas fa-file-csv"></i>
                    <span>CSV</span>
                </button>
                <button onclick="exportToPDF()" class="export-btn">
                    <i class="fas fa-file-pdf"></i>
                    <span>PDF</span>
                </button>
            </div>
        </div>

        <div id="sectionList">
            <?php
            // Modified query to include academic year and semester
            $section_query = "SELECT DISTINCT 
                    cl.id, cl.curriculum, cl.level, cl.section, cl.department,
                    fa.academic_year_id, fa.semester,
                    a.year as academic_year
                FROM class_list cl
                LEFT JOIN faculty_assignments fa ON cl.id = fa.class_id
                LEFT JOIN academic_list a ON fa.academic_year_id = a.id
                WHERE cl.department = 'COE'
                ORDER BY a.year DESC, fa.semester, cl.department, cl.curriculum, cl.level, cl.section";
            
            $section_result = $conn->query($section_query);

            if ($section_result->num_rows > 0) {
                while ($section = $section_result->fetch_assoc()) {
                    echo '<div class="section-card" 
                        data-department="' . $section['department'] . '"
                        data-academic-year="' . ($section['academic_year_id'] ?? '') . '"
                        data-semester="' . ($section['semester'] ?? '') . '">';
                    
                    echo '<div class="section-header">';
                    echo '<h4>' . $section['curriculum'] . ' ' . $section['level'] . '-' . $section['section'] . '</h4>';
                    echo '<p>Department: ' . $section['department'] . '</p>';
                    if ($section['academic_year']) {
                        echo '<p>Academic Year: ' . $section['academic_year'] . 
                             ' | Semester: ' . ($section['semester'] == 1 ? 'First' : 'Second') . '</p>';
                    }
                    echo '</div>';
                    echo '<div class="card-body">';

                    // Get faculty assignments for this section with academic year and semester
                    $faculty_query = "SELECT 
                            s.code as subject_code, s.subject,
                            f.firstname as faculty_fname, f.lastname as faculty_lname,
                            fa.semester, a.year as academic_year,
                            fa.academic_year_id, fa.is_active
                        FROM faculty_assignments fa
                        JOIN subject_list s ON fa.subject_id = s.id
                        JOIN faculty_list f ON fa.faculty_id = f.id
                        JOIN academic_list a ON fa.academic_year_id = a.id
                        WHERE fa.class_id = {$section['id']}
                        AND fa.is_active = 1
                        ORDER BY a.year DESC, fa.semester, s.code";
                    
                    $faculty_result = $conn->query($faculty_query);
                    
                    if ($faculty_result->num_rows > 0) {
                        echo '<h5>Faculty Assignments:</h5>';
                        echo '<div class="table-container">';  // Add wrapper div
                        echo '<table class="table faculty-table" 
                            data-academic-year="' . ($section['academic_year_id'] ?? '') . '"
                            data-semester="' . ($section['semester'] ?? '') . '"
                            data-items-per-page="5">'; // Add items-per-page attribute
                        echo '<thead><tr>';
                        echo '<th>Subject Code</th>';
                        echo '<th>Subject</th>';
                        echo '<th>Instructor</th>';
                        echo '<th>Academic Year</th>';
                        echo '<th>Semester</th>';
                        echo '</tr></thead><tbody>';
                        
                        while ($faculty = $faculty_result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $faculty['subject_code'] . '</td>';
                            echo '<td>' . $faculty['subject'] . '</td>';
                            echo '<td>' . $faculty['faculty_fname'] . ' ' . $faculty['faculty_lname'] . '</td>';
                            echo '<td>' . $faculty['academic_year'] . '</td>';
                            echo '<td>' . ($faculty['semester'] == 1 ? 'First' : 'Second') . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                        echo '<div class="pagination-container">
                                <div class="pagination-info"></div>
                                <div class="pagination-controls"></div>
                              </div>';
                        echo '</div>';
                    }

                    // Get students in this section
                    $student_query = "SELECT * FROM student_list 
                                    WHERE class_id = {$section['id']} 
                                    ORDER BY lastname, firstname";
                    $student_result = $conn->query($student_query);
                    
                    if ($student_result->num_rows > 0) {
                        echo '<h5 class="mt-4">Students:</h5>';
                        echo '<div class="table-container">';
                        echo '<table class="table student-table" data-items-per-page="10">';
                        echo '<thead><tr>';
                        echo '<th>#</th>';
                        echo '<th>Student ID</th>';
                        echo '<th>Name</th>';
                        echo '</tr></thead><tbody>';
                        
                        $counter = 1;
                        while ($student = $student_result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $counter++ . '</td>'; // Add counter
                            echo '<td>' . $student['school_id'] . '</td>';
                            echo '<td>' . $student['lastname'] . ', ' . $student['firstname'] . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                        echo '<div class="pagination-container">
                                <div class="pagination-info"></div>
                                <div class="pagination-controls"></div>
                              </div>';
                        echo '</div>';
                    }
                    
                    echo '</div></div>';
                }
            } else {
                echo '<div class="no-results">No sections found</div>';
            }
            ?>
        </div>
    </div>

    <template class="show-entries-template">
        <div class="show-entries-wrapper">
            <label>
                Show 
                <select class="entries-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                entries
            </label>
        </div>
    </template>

    <script>
        // Add this pagination class before the existing script
        class TablePagination {
            constructor(tableContainer) {
                this.container = tableContainer;
                this.table = this.container.querySelector('table');
                this.tbody = this.table.querySelector('tbody');
                this.rows = Array.from(this.tbody.querySelectorAll('tr'));
                this.itemsPerPage = parseInt(this.table.dataset.itemsPerPage) || 5;
                this.currentPage = 1;
                
                this.updateTotalPages();
                
                // Create pagination container if it doesn't exist
                if (!this.container.querySelector('.pagination-container')) {
                    const paginationContainer = document.createElement('div');
                    paginationContainer.className = 'pagination-container';
                    paginationContainer.innerHTML = `
                        <div class="pagination-info"></div>
                        <div class="pagination-controls"></div>
                    `;
                    this.container.appendChild(paginationContainer);
                }
                
                // Add show entries if it doesn't exist
                if (!this.container.querySelector('.show-entries-wrapper')) {
                    this.addShowEntries();
                }
                
                this.paginationInfo = this.container.querySelector('.pagination-info');
                this.paginationControls = this.container.querySelector('.pagination-controls');
                
                this.init();
            }

            updateTotalPages() {
                this.totalPages = Math.ceil(this.rows.length / this.itemsPerPage);
            }

            addShowEntries() {
                // Clone the template
                const template = document.querySelector('.show-entries-template');
                const showEntries = template.content.cloneNode(true);
                
                // Insert before the table
                this.container.insertBefore(showEntries, this.table);
                
                // Set initial value and add event listener
                const select = this.container.querySelector('.entries-select');
                select.value = this.itemsPerPage;
                
                select.addEventListener('change', (e) => {
                    this.itemsPerPage = parseInt(e.target.value);
                    this.currentPage = 1;
                    this.updateTotalPages();
                    this.updateTable();
                    this.renderControls();
                });
            }
            
            init() {
                this.updateTable();
                this.renderControls();
            }

            updateTable() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                
                this.rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });
                
                this.updateInfo();
            }

            updateInfo() {
                const start = (this.currentPage - 1) * this.itemsPerPage + 1;
                const end = Math.min(start + this.itemsPerPage - 1, this.rows.length);
                this.paginationInfo.textContent = 
                    `Showing ${start} to ${end} of ${this.rows.length} entries`;
            }

            renderControls() {
                this.paginationControls.innerHTML = '';
                
                // Previous button
                const prevButton = this.createButton('Previous', this.currentPage > 1);
                prevButton.addEventListener('click', () => this.goToPage(this.currentPage - 1));
                
                // Page buttons
                const buttons = [];
                for (let i = 1; i <= this.totalPages; i++) {
                    const button = this.createButton(i.toString(), true, i === this.currentPage);
                    button.addEventListener('click', () => this.goToPage(i));
                    buttons.push(button);
                }
                
                // Next button
                const nextButton = this.createButton('Next', this.currentPage < this.totalPages);
                nextButton.addEventListener('click', () => this.goToPage(this.currentPage + 1));
                
                // Append all buttons
                this.paginationControls.append(prevButton, ...buttons, nextButton);
            }

            createButton(text, enabled = true, active = false) {
                const button = document.createElement('button');
                button.textContent = text;
                button.className = 'pagination-button' + (active ? ' active' : '');
                button.disabled = !enabled;
                return button;
            }

            goToPage(page) {
                this.currentPage = page;
                this.updateTable();
                this.renderControls();
            }
        }

        // Modify the existing filterSections function to reinitialize pagination
        function filterSections() {
            const selectedDept = document.getElementById('departmentFilter').value.toLowerCase();
            const selectedYear = document.getElementById('academicYearFilter').value;
            const selectedSemester = document.getElementById('semesterFilter').value;
            const selectedCurriculum = document.getElementById('curriculumFilter').value;
            const cards = document.querySelectorAll('.section-card');
            let visibleCards = 0;
            
            cards.forEach(card => {
                const cardDept = card.dataset.department.toLowerCase();
                const cardYear = card.dataset.academicYear;
                const cardSemester = card.dataset.semester;
                const cardCurriculum = card.querySelector('.section-header h4').textContent.split(' ')[0];
                
                // Check if the card matches the filters
                const matchesDept = !selectedDept || cardDept === selectedDept;
                const matchesYear = !selectedYear || cardYear === selectedYear;
                const matchesSemester = !selectedSemester || cardSemester === selectedSemester;
                const matchesCurriculum = !selectedCurriculum || cardCurriculum === selectedCurriculum;
                
                if (matchesDept && matchesYear && matchesSemester && matchesCurriculum) {
                    card.style.display = 'block';
                    visibleCards++;
                    
                    // Also filter the faculty tables within this card
                    const facultyTables = card.querySelectorAll('.faculty-table');
                    facultyTables.forEach(table => {
                        const rows = table.querySelectorAll('tbody tr');
                        rows.forEach(row => {
                            const rowYear = row.querySelector('td:nth-child(4)').textContent;
                            const rowSemester = row.querySelector('td:nth-child(5)').textContent;
                            const semesterValue = rowSemester.includes('First') ? '1' : '2';
                            
                            const matchesTableYear = !selectedYear || rowYear === document.querySelector(`#academicYearFilter option[value="${selectedYear}"]`).textContent;
                            const matchesTableSemester = !selectedSemester || semesterValue === selectedSemester;
                            
                            row.style.display = (matchesTableYear && matchesTableSemester) ? '' : 'none';
                        });
                        
                        // Hide the table if no rows are visible
                        const visibleRows = Array.from(rows).some(row => row.style.display !== 'none');
                        table.closest('.card-body').style.display = visibleRows ? '' : 'none';
                    });
                } else {
                    card.style.display = 'none';
                }
            });

            // Update no results message
            const existingNoResults = document.querySelector('.no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            if (visibleCards === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = `
                    <i class="fas fa-search"></i>
                    <p>No matching sections found</p>
                    <p class="text-sm text-gray-500">Try adjusting your filters</p>
                `;
                document.getElementById('sectionList').appendChild(noResults);
            }

            // After filtering, reinitialize pagination for visible tables
            document.querySelectorAll('.table-container').forEach(container => {
                const card = container.closest('.section-card');
                if (card.style.display !== 'none') {
                    new TablePagination(container);
                }
            });
        }

        // Add this function to update curriculum options
        function updateCurriculumOptions(selectedDept = '') {
            const curriculumSelect = document.getElementById('curriculumFilter');
            
            // Clear existing options except the first one
            while (curriculumSelect.options.length > 1) {
                curriculumSelect.remove(1);
            }
            
            // Get all section cards
            const cards = document.querySelectorAll('.section-card');
            const curriculums = new Set();
            
            cards.forEach(card => {
                const cardDept = card.dataset.department.toLowerCase();
                const cardCurriculum = card.querySelector('.section-header h4').textContent.split(' ')[0];
                
                // If no department is selected or card matches selected department
                if (!selectedDept || cardDept === selectedDept.toLowerCase()) {
                    curriculums.add(cardCurriculum);
                }
            });
            
            // Add filtered curriculum options
            [...curriculums].sort().forEach(curriculum => {
                const option = document.createElement('option');
                option.value = curriculum;
                option.textContent = curriculum;
                curriculumSelect.appendChild(option);
            });
            
            // Restore saved value if it exists in new options
            const savedValue = localStorage.getItem('curriculumFilter');
            if (savedValue && [...curriculumSelect.options].some(opt => opt.value === savedValue)) {
                curriculumSelect.value = savedValue;
            } else {
                curriculumSelect.value = ''; // Reset to "All Classes" if saved value is not valid
                localStorage.removeItem('curriculumFilter');
            }
        }

        // Modify the event listeners section
        const filters = ['departmentFilter', 'academicYearFilter', 'semesterFilter', 'curriculumFilter'];
        filters.forEach(filterId => {
            const element = document.getElementById(filterId);
            element.addEventListener('change', () => {
                if (filterId === 'departmentFilter') {
                    updateCurriculumOptions(element.value);
                }
                filterSections();
                localStorage.setItem(filterId, element.value);
            });

            // Restore filter state from localStorage
            const savedValue = localStorage.getItem(filterId);
            if (savedValue) {
                element.value = savedValue;
            }
        });

        // Initial setup
        updateCurriculumOptions(document.getElementById('departmentFilter').value);
        filterSections();

        // Initialize pagination for all tables on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize pagination for all tables
            document.querySelectorAll('.table-container').forEach(container => {
                new TablePagination(container);
            });
        });

        // Add this function for Excel export
        function exportToExcel() {
            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            
            // Process each visible section card
            document.querySelectorAll('.section-card').forEach((card, index) => {
                if (card.style.display !== 'none') {
                    const sectionTitle = card.querySelector('.section-header h4').textContent;
                    const department = card.querySelector('.section-header p').textContent.replace('Department: ', '');
                    
                    // Create combined data array for the section
                    const sectionData = [];
                    
                    // Add section header
                    sectionData.push(['Section: ' + sectionTitle]);
                    sectionData.push(['Department: ' + department]);
                    sectionData.push([]); // Empty row for spacing
                    
                    // Process faculty table
                    const facultyTable = card.querySelector('.faculty-table');
                    if (facultyTable) {
                        sectionData.push(['Faculty Assignments']);
                        sectionData.push(['Subject Code', 'Subject', 'Instructor', 'Academic Year', 'Semester']);
                        
                        facultyTable.querySelectorAll('tbody tr').forEach(row => {
                            if (row.style.display !== 'none') {
                                const rowData = Array.from(row.cells).map(cell => cell.textContent.trim());
                                sectionData.push(rowData);
                            }
                        });
                        
                        sectionData.push([]); // Empty row for spacing
                    }
                    
                    // Process student table
                    const studentTable = card.querySelector('.student-table');
                    if (studentTable) {
                        sectionData.push(['Student List']);
                        sectionData.push(['#', 'Student ID', 'Name']);
                        
                        studentTable.querySelectorAll('tbody tr').forEach(row => {
                            if (row.style.display !== 'none') {
                                const rowData = Array.from(row.cells).map(cell => cell.textContent.trim());
                                sectionData.push(rowData);
                            }
                        });
                    }
                    
                    // Create worksheet and add to workbook
                    const ws = XLSX.utils.aoa_to_sheet(sectionData);
                    XLSX.utils.book_append_sheet(wb, ws, `Section ${index + 1}`);
                }
            });
            
            // Generate Excel file
            XLSX.writeFile(wb, 'room_assignments.xlsx');
        }

        // Print function
        function printTable() {
            window.print();
        }

        // CSV Export function
        function exportToCSV() {
            const csvRows = [];
            
            document.querySelectorAll('.section-card').forEach((card, index) => {
                if (card.style.display !== 'none') {
                    const sectionTitle = card.querySelector('.section-header h4').textContent;
                    const department = card.querySelector('.section-header p').textContent.replace('Department: ', '');
                    
                    // Add section header
                    csvRows.push([`Section: ${sectionTitle}`]);
                    csvRows.push([`Department: ${department}`]);
                    csvRows.push([]); // Empty row
                    
                    // Process faculty table
                    const facultyTable = card.querySelector('.faculty-table');
                    if (facultyTable) {
                        csvRows.push(['Faculty Assignments']);
                        csvRows.push(['Subject Code', 'Subject', 'Instructor', 'Academic Year', 'Semester']);
                        
                        facultyTable.querySelectorAll('tbody tr').forEach(row => {
                            if (row.style.display !== 'none') {
                                const rowData = Array.from(row.cells).map(cell => cell.textContent.trim());
                                csvRows.push(rowData);
                            }
                        });
                        csvRows.push([]); // Empty row
                    }
                    
                    // Process student table
                    const studentTable = card.querySelector('.student-table');
                    if (studentTable) {
                        csvRows.push(['Students']);
                        csvRows.push(['#', 'Student ID', 'Name']);
                        
                        studentTable.querySelectorAll('tbody tr').forEach(row => {
                            if (row.style.display !== 'none') {
                                const rowData = Array.from(row.cells).map(cell => cell.textContent.trim());
                                csvRows.push(rowData);
                            }
                        });
                        csvRows.push([]); // Empty row
                    }
                }
            });
            
            // Convert to CSV string
            const csvContent = csvRows.map(row => row.join(',')).join('\n');
            
            // Create and download CSV file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'room_assignments.csv';
            link.click();
        }

        // PDF Export function
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yOffset = 10;
            
            document.querySelectorAll('.section-card').forEach((card, index) => {
                if (card.style.display !== 'none') {
                    if (yOffset > 250) {
                        doc.addPage();
                        yOffset = 10;
                    }
                    
                    const sectionTitle = card.querySelector('.section-header h4').textContent;
                    const department = card.querySelector('.section-header p').textContent;
                    
                    // Add section header
                    doc.setFontSize(14);
                    doc.text(sectionTitle, 10, yOffset);
                    yOffset += 7;
                    doc.setFontSize(12);
                    doc.text(department, 10, yOffset);
                    yOffset += 10;
                    
                    // Process faculty table
                    const facultyTable = card.querySelector('.faculty-table');
                    if (facultyTable) {
                        const facultyData = [];
                        const facultyHeaders = ['Subject Code', 'Subject', 'Instructor', 'Academic Year', 'Semester'];
                        
                        facultyTable.querySelectorAll('tbody tr').forEach(row => {
                            if (row.style.display !== 'none') {
                                const rowData = Array.from(row.cells).map(cell => cell.textContent.trim());
                                facultyData.push(rowData);
                            }
                        });
                        
                        if (facultyData.length > 0) {
                            doc.text('Faculty Assignments:', 10, yOffset);
                            yOffset += 5;
                            doc.autoTable({
                                head: [facultyHeaders],
                                body: facultyData,
                                startY: yOffset,
                                margin: { left: 10 },
                                theme: 'grid'
                            });
                            yOffset = doc.lastAutoTable.finalY + 10;
                        }
                    }
                    
                    // Process student table
                    const studentTable = card.querySelector('.student-table');
                    if (studentTable) {
                        if (yOffset > 250) {
                            doc.addPage();
                            yOffset = 10;
                        }
                        
                        const studentData = [];
                        const studentHeaders = ['#', 'Student ID', 'Name'];
                        
                        studentTable.querySelectorAll('tbody tr').forEach(row => {
                            if (row.style.display !== 'none') {
                                const rowData = Array.from(row.cells).map(cell => cell.textContent.trim());
                                studentData.push(rowData);
                            }
                        });
                        
                        if (studentData.length > 0) {
                            doc.text('Students:', 10, yOffset);
                            yOffset += 5;
                            doc.autoTable({
                                head: [studentHeaders],
                                body: studentData,
                                startY: yOffset,
                                margin: { left: 10 },
                                theme: 'grid'
                            });
                            yOffset = doc.lastAutoTable.finalY + 10;
                        }
                    }
                }
            });
            
            doc.save('room_assignments.pdf');
        }
    </script>
</body>
</html>