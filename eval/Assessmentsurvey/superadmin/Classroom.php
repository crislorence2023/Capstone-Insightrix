<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Assignments</title>
    <!-- Add Montserrat font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 24px;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: 500;
            color: #555;
        }

        select, input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }

        select:focus, input:focus {
            border-color: #666;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding-right: 35px;
        }

        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background: #f8f8f8;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .card-header h4 {
            margin-bottom: 8px;
            color: #333;
        }

        .card-header p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 600;
            color: #444;
            background: #f8f8f8;
        }

        tr:hover {
            background: #f5f5f5;
        }

        /* Add Font Awesome CSS */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

        .no-results {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #666;
        }

        .no-results i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Classroom Assignments</h2>
        
        <div class="filters">
            <div class="filter-group">
                <label for="departmentFilter">Department</label>
                <select id="departmentFilter">
                    <option value="">All Departments</option>
                    <?php
                    $dept_query = "SELECT DISTINCT department FROM class_list WHERE department != ''";
                    $dept_result = $conn->query($dept_query);
                    while ($dept = $dept_result->fetch_assoc()) {
                        echo "<option value='" . $dept['department'] . "'>" . $dept['department'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="searchInput">Search Classes</label>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by curriculum, section...">
                    <span class="search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
            </div>
        </div>

        <div id="classroomList">
            <?php
            include 'db_connect.php';

            // Modified query to include department
            $class_query = "SELECT DISTINCT cl.id, cl.curriculum, cl.level, cl.section, cl.department,
                    a.year as academic_year, ca.semester
                    FROM classroom_assignments ca
                    JOIN class_list cl ON ca.class_id = cl.id
                    JOIN academic_list a ON ca.academic_year_id = a.id
                    ORDER BY cl.department, a.year DESC, cl.curriculum, cl.level, cl.section";
            
            $class_result = $conn->query($class_query);

            if ($class_result->num_rows > 0) {
                while ($class = $class_result->fetch_assoc()) {
                    echo '<div class="card mb-4" data-department="' . $class['department'] . '">';
                    echo '<div class="card-header">';
                    echo '<h4>' . $class['curriculum'] . ' ' . $class['level'] . '-' . $class['section'] . '</h4>';
                    echo '<p class="mb-0">Department: ' . $class['department'] . ' | Academic Year: ' . $class['academic_year'] . ' | Semester: ' . $class['semester'] . '</p>';
                    echo '</div>';
                    echo '<div class="card-body">';

                    // Get subjects and faculty for this class
                    $subjects_query = "SELECT s.code as subject_code, s.subject, 
                                            f.firstname as faculty_fname, f.lastname as faculty_lname,
                                            ca.semester
                                     FROM classroom_assignments ca
                                     JOIN subject_list s ON ca.subject_id = s.id
                                     JOIN faculty_list f ON ca.faculty_id = f.id
                                     WHERE ca.class_id = {$class['id']}
                                     ORDER BY s.code";
                    
                    $subjects_result = $conn->query($subjects_query);
                    
                    if ($subjects_result->num_rows > 0) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-hover">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Subject Code</th>';
                        echo '<th>Subject</th>';
                        echo '<th>Instructor</th>';
                        echo '<th>Semester</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        
                        while ($subject = $subjects_result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $subject['subject_code'] . '</td>';
                            echo '<td>' . $subject['subject'] . '</td>';
                            echo '<td>' . $subject['faculty_fname'] . ' ' . $subject['faculty_lname'] . '</td>';
                            echo '<td>' . $subject['semester'] . '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo '<p class="text-muted">No subjects assigned</p>';
                    }

                    // Get students in this class
                    $student_query = "SELECT * FROM student_list WHERE class_id = {$class['id']} ORDER BY lastname, firstname";
                    $student_result = $conn->query($student_query);
                    
                    if ($student_result->num_rows > 0) {
                        echo '<h5 class="mt-4">Students List:</h5>';
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-hover">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Student ID</th>';
                        echo '<th>Name</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        
                        while ($student = $student_result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $student['school_id'] . '</td>';
                            echo '<td>' . $student['lastname'] . ', ' . $student['firstname'] . '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo '<p class="text-muted mt-4">No students enrolled</p>';
                    }
                    
                    echo '</div></div>';
                }
            } else {
                echo '<div class="alert alert-info">No classroom assignments found</div>';
            }
            ?>
        </div>
    </div>

    <script>
        document.getElementById('departmentFilter').addEventListener('change', filterCards);
        document.getElementById('searchInput').addEventListener('input', filterCards);
        
        function filterCards() {
            const selectedDept = document.getElementById('departmentFilter').value.toLowerCase();
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.card[data-department]');
            let visibleCards = 0;
            
            cards.forEach(card => {
                const cardDept = card.dataset.department.toLowerCase();
                const cardText = card.textContent.toLowerCase();
                const matchesDept = !selectedDept || cardDept === selectedDept;
                const matchesSearch = !searchText || cardText.includes(searchText);
                
                if (matchesDept && matchesSearch) {
                    card.style.display = 'block';
                    visibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Handle no results message
            const existingNoResults = document.querySelector('.no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            if (visibleCards === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = `
                    <i class="fas fa-search"></i>
                    <p>No matching results found</p>
                `;
                document.getElementById('classroomList').appendChild(noResults);
            }
        }
    </script>
</body>
</html>