<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
            box-sizing: border-box;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin: 20px;
            overflow: hidden;
            width: auto;
        }

        .card-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card-header h2 {
            margin: 0;
        }

        .btn-add-student {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
            margin-left: 30rem;
            border: 1px solid transparent;
        }

        .btn-add-student:hover {
            background: #2ed454;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
            border-color: #28a745;
            color: white;
        }

        .btn-add-student:active {
            background: #218838;
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.1);
        }

        .department-container {
            padding: 20px;
            overflow-x: auto;
        }

        .department-container h3 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            background-color: transparent;
            min-width: 800px;
        }

        .student-table th,
        .student-table td {
            padding: 1rem;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            white-space: nowrap;
        }

        .student-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .student-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            min-width: max-content;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            min-width: 32px;
        }

        .btn-view { background: #17a2b8; color: white; }
        .btn-edit { background: #ffc107; color: white; }
        .btn-delete { background: #dc3545; color: white; }

        .btn-action:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .department-divider {
            border: 0;
            height: 1px;
            background: #e9ecef;
            margin: 2rem 0;
        }

        @media (min-width: 1200px) {
            .btn-add-student {
                margin-left: auto;
            }
        }

        @media (min-width: 1440px) {
            .btn-add-student {
                margin-left: 55rem;
                font-size: 1.2rem;
            }
        }

        @media (min-width: 1920px) {
            .btn-add-student {
                margin-left: 55rem;
                font-size: 1.4rem;
                padding: 14px 28px;
            }
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                justify-content: center;
                text-align: center;
                padding: 1rem;
            }

            .card-header h2 {
                width: 100%;
                text-align: center;
            }

            .btn-add-student {
                margin: 0 auto;
            }

            .card {
                margin: 10px;
            }

            .department-container {
                padding: 10px;
            }

            .student-table td, 
            .student-table th {
                padding: 0.75rem;
            }

            .action-buttons {
                flex-direction: row;
                gap: 4px;
            }

            .table-responsive::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 5px;
                background: linear-gradient(to left, rgba(0,0,0,0.05), transparent);
                pointer-events: none;
            }
        }
    </style>
</head>
<body>
    <?php include 'db_connect.php' ?>
    <div class="card">
        <div class="card-header">
            <h4>Student List</h4>
            <a href="staff_cme_index.php?page=new_student" class="btn-add-student">
                <i class="fa fa-plus"></i> Add New Student
            </a>
        </div>
        <div class="card-body">
            <?php
            $departments = array();
            $students = array();
            
            $class_query = $conn->query("SELECT id, concat(curriculum,' ',level,' - ',section) as `class`, department FROM class_list");
            while($class_row = $class_query->fetch_assoc()) {
                $departments[$class_row['id']] = [
                    'class' => $class_row['class'],
                    'department' => $class_row['department']
                ];
            }
            
            $student_query = $conn->query("SELECT *, concat(firstname,' ',lastname) as name FROM student_list ORDER BY class_id, concat(firstname,' ',lastname) ASC");
            while($student_row = $student_query->fetch_assoc()) {
                $dept = isset($departments[$student_row['class_id']]) ? $departments[$student_row['class_id']]['department'] : 'Unidentified';
                $students[$dept][] = $student_row;
            }
            
            foreach($students as $dept => $dept_students):
                if ($dept == 'CME'):
                    $i = 1;
            ?>
                <div class="department-container">
                    <h3><?php echo $dept . ' Department'; ?></h3>
                    <div class="table-responsive">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Current Class</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($dept_students as $row): ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo $row['school_id']; ?></td>
                                    <td><?php echo ucwords($row['name']); ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo isset($departments[$row['class_id']]) ? $departments[$row['class_id']]['class'] : "N/A"; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view view_student" data-id="<?php echo $row['id']; ?>" disabled style="opacity: 0.5; cursor: not-allowed;">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="staff_cme_index.php?page=edit_student&id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn-action btn-delete delete_student" type="button" data-id="<?php echo $row['id']; ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr class="department-divider">
            <?php 
                endif;
            endforeach;
            
            if (isset($students['Unidentified'])):
                $i = 1;
            ?>
                <div class="department-container">
                    <h3>Unidentified Department</h3>
                    <div class="table-responsive">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Current Class</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($students['Unidentified'] as $row): ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo $row['school_id']; ?></td>
                                    <td><?php echo ucwords($row['name']); ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo isset($departments[$row['class_id']]) ? $departments[$row['class_id']]['class'] : "N/A"; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view view_student" data-id="<?php echo $row['id']; ?>" disabled style="opacity: 0.5; cursor: not-allowed;">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="staff_cme_index.php?page=edit_student&id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn-action btn-delete delete_student" type="button" data-id="<?php echo $row['id']; ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            // Remove or comment out the view_student click handler since it's disabled
            /*$('.view_student').click(function(){
                uni_modal("<i class='fa fa-id-card'></i> Student Details","<?php echo $_SESSION['login_view_folder'] ?>view_student.php?id="+$(this).attr('data-id'))
            })*/
            
            $('.delete_student').click(function(){
                var id = $(this).attr('data-id');
                if(confirm("Are you sure to delete this student?")){
                    delete_student(id);
                }
            })
            
            $('.student-table').dataTable()
        })

        function delete_student($id){
            $.ajax({
                url:'ajax.php?action=delete_student',
                method:'POST',
                data:{id:$id},
                success:function(resp){
                    if(resp==1){
                        alert("Data successfully deleted");
                        location.reload();
                    } else {
                        alert("Error deleting data");
                    }
                },
                error: function(){
                    alert("Error occurred while deleting");
                }
            })
        }
    </script>
</body>
</html>