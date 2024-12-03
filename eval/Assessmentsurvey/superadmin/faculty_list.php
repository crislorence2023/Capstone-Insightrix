<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty List</title>
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

.card-header h3 {
    margin: 0;
    color:#1F2D3D;
            font-size: 20px;
            font-weight: 600;
}

.btn-add-faculty {
    background: #28a745;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    white-space: nowrap;
    margin-left: auto;
    border: 1px solid transparent;
    font-size: 0.9rem;
}

.btn-add-faculty:hover {
    background: #2ed454;  /* Slightly lighter and more vibrant green */
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);  /* Green-tinted shadow */
    border-color: #28a745;  /* Adds subtle border on hover */
	color: white;
}

.btn-add-faculty:active {
    background: #218838;  /* Darker green for click state */
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

        .faculty-table {
            width: 100%;
            border-collapse: collapse;
            background-color: transparent;
            min-width: 800px;  /* Minimum width to ensure readability */
        }

        .faculty-table th,
        .faculty-table td {
            padding: 1rem;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            white-space: nowrap;
        }

        .faculty-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .faculty-table tbody tr:hover {
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
    /* Styles for larger desktops */
    .btn-add-faculty {
        margin-left: auto; /* Adjust positioning for larger screens */
       
    }
}

/* For extra-large screens (1440px and above) */
@media (min-width: 1440px) {
    /* Styles for extra-large desktops */
    .btn-add-faculty {
        margin-left: auto;
        font-size: 1rem;
    }
}

/* For ultra-wide screens (1920px and above) */
@media (min-width: 1920px) {
    /* Styles for ultra-wide screens */
    .btn-add-faculty {
        margin-left: auto;
        font-size: 1.1rem;
        padding: 10px 20px;
    }
}

        /* Mobile specific styles */
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

    .btn-add-faculty {
        margin: 0 auto;
    }
            .card {
                margin: 10px;
            }

            .card-header {
                padding: 1rem;
            }

            .department-container {
                padding: 10px;
            }

            .faculty-table td, 
            .faculty-table th {
                padding: 0.75rem;
            }

            /* Maintain button layout on mobile */
            .action-buttons {
                flex-direction: row;
                gap: 4px;
            }

            /* Add visual indicator for horizontal scroll */
            .table-responsive {
                position: relative;
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
            <h3 text-bold>Faculty List</h3>
            <a href="./index.php?page=new_faculty" class="btn-add-faculty">
                <i class="fa fa-plus"></i> Add New Faculty
            </a>
        </div>
        <div class="card-body">
            <?php
            $departments = array();
            $faculty = array();
            
            $faculty_query = $conn->query("SELECT *, concat(firstname,' ',lastname) as name FROM faculty_list ORDER BY department, concat(firstname,' ',lastname) ASC");
            while($faculty_row = $faculty_query->fetch_assoc()) {
                $dept = $faculty_row['department'];
                if (!in_array($dept, $departments)) {
                    $departments[] = $dept;
                }
                $faculty[$dept][] = $faculty_row;
            }
            
            foreach($departments as $dept):
                if (isset($faculty[$dept])):
                    $i = 1;
            ?>
                <div class="department-container">
                    <h3><?php echo $dept != '' ? $dept . ' Department' : 'Unassigned Department'; ?></h3>
                    <div class="table-responsive">
                        <table class="faculty-table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($faculty[$dept] as $row): ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo $row['school_id']; ?></td>
                                    <td><?php echo ucwords($row['name']); ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view view_faculty" data-id="<?php echo $row['id']; ?>">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="./index.php?page=edit_faculty&id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn-action btn-delete delete_faculty" data-id="<?php echo $row['id']; ?>">
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
            ?>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('.view_faculty').click(function(){
                uni_modal("<i class='fa fa-id-card'></i> Faculty Details","<?php echo $_SESSION['login_view_folder'] ?>view_faculty.php?id="+$(this).attr('data-id'))
            })
            
            $('.delete_faculty').click(function(){
                _conf("Are you sure to delete this faculty?","delete_faculty",[$(this).attr('data-id')])
            })
            
            $('.faculty-table').dataTable()
        })

        function delete_faculty($id){
            start_load()
            $.ajax({
                url:'ajax.php?action=delete_faculty',
                method:'POST',
                data:{id:$id},
                success:function(resp){
                    if(resp==1){
                        alert_toast("Data successfully deleted",'success')
                        setTimeout(function(){
                            location.reload()
                        },1500)
                    }
                }
            })
        }
    </script>
</body>
</html>