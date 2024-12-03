<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Assignments</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h2 {
            color: #1a202c;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 1rem;
        }

        .filter-group label {
            font-weight: 500;
            color: #2d3748;
            font-size: 1rem;
        }

        select {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: .9rem;
            background-color: white;
            transition: all 0.3s ease;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234a5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }

        select:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }

        .section-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .section-card:hover {
            transform: translateY(-2px);
        }

        .section-header {
            background: #f8fafc;
            padding: 1.25rem;
            border-bottom: 1px solid #e2e8f0;
           
        }

        .section-header h4 {
            color: #1a202c;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
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
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }

        .search-container {
            position: relative;
            width: 100%;
        }

        #searchInput {
            width: 100%;
            padding: 0.75rem;
            padding-right: 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: .9rem;
            background-color: white;
            transition: all 0.3s ease;
        }

        #searchInput:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #4a5568;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Room Assignments</h2>
        
        <div class="filters">
            <div class="filter-group">
                <label for="searchInput">Search Class</label>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search by class name...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
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
                    $dept_query = "SELECT DISTINCT department FROM class_list 
                                  WHERE department LIKE 'COT%' 
                                  OR department = 'COR IRREGULAR'
                                  ORDER BY department";
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
                WHERE cl.department LIKE 'COT%'
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
                        echo '<table class="table faculty-table" 
                            data-academic-year="' . ($section['academic_year_id'] ?? '') . '"
                            data-semester="' . ($section['semester'] ?? '') . '">';
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
                    }

                    // Get students in this section
                    $student_query = "SELECT * FROM student_list 
                                    WHERE class_id = {$section['id']} 
                                    ORDER BY lastname, firstname";
                    $student_result = $conn->query($student_query);
                    
                    if ($student_result->num_rows > 0) {
                        echo '<h5 class="mt-4">Students:</h5>';
                        echo '<table class="table">';
                        echo '<thead><tr>';
                        echo '<th>Student ID</th>';
                        echo '<th>Name</th>';
                        echo '</tr></thead><tbody>';
                        
                        while ($student = $student_result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $student['school_id'] . '</td>';
                            echo '<td>' . $student['lastname'] . ', ' . $student['firstname'] . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    }
                    
                    echo '</div></div>';
                }
            } else {
                echo '<div class="no-results">No sections found</div>';
            }
            ?>
        </div>
    </div>

    <script>
        function filterSections() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
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
                const cardCurriculum = card.querySelector('.section-header h4').textContent.split(' ')[0].toLowerCase();
                
                // Check if the card matches the filters
                const matchesDept = !selectedDept || cardDept === selectedDept;
                const matchesYear = !selectedYear || cardYear === selectedYear;
                const matchesSemester = !selectedSemester || cardSemester === selectedSemester;
                const matchesCurriculum = !selectedCurriculum || cardCurriculum === selectedCurriculum;
                
                // Modified search to only look at curriculum/class name
                const matchesSearch = !searchTerm || cardCurriculum.includes(searchTerm);
                
                if (matchesDept && matchesYear && matchesSemester && matchesCurriculum && matchesSearch) {
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
                const cardCurriculum = card.querySelector('.section-header h4').textContent.split(' ')[0].toLowerCase();
                
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

        // Add search input event listener
        document.getElementById('searchInput').addEventListener('input', filterSections);

        // Initial setup
        updateCurriculumOptions(document.getElementById('departmentFilter').value);
        filterSections();
    </script>
</body>
</html>