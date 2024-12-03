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

.card-header h2 {
    margin: 0;
}

.btn-add-faculty {
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
        margin-left: 55rem; /* Further adjust positioning */
        font-size: 1.2rem; /* Increase font size */
    }
}

/* For ultra-wide screens (1920px and above) */
@media (min-width: 1920px) {
    /* Styles for ultra-wide screens */
    .btn-add-faculty {
        margin-left: 55rem; /* Adjust margin for ultra-wide displays */
        font-size: 1.4rem; /* Make text larger */
        padding: 14px 28px; /* Increase button padding */
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

        /* Update the modal styles */
       

        /* Ensure the modal appears above other elements */
     
    </style>
</head>
<body>
    <?php include 'db_connect.php' ?>
    <div class="card">
        <div class="card-header">
            <h2>Faculty List</h2>
            <a href="./staff_coe_index.php?page=new_faculty" class="btn-add-faculty">
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
                if ($dept === 'COE' && isset($faculty[$dept])):
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
                                            <!-- Commenting out the view button -->
                                            <!-- <button class="btn-action btn-view view_faculty" data-id="<?php echo $row['id']; ?>">
                                                <i class="fa fa-eye"></i>
                                            </button> -->
                                            <a href="./staff_cme_index.php?page=edit_faculty&id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
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
            // Commenting out the view faculty event handler
            // $('.view_faculty').click(function(){
            //     uni_modal("<i class='fa fa-id-card'></i> Faculty Details","staff_cme_index.php?page=view_faculty&id="+$(this).attr('data-id'))
            // });
            
            // Delete faculty
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

        // Update your existing script with this modified uni_modal function
        function uni_modal($title = '', $url = '', $size = "") {
            start_load()
            $.ajax({
                url: $url,
                error: err => {
                    console.log()
                    alert("An error occurred")
                },
                success: function(resp) {
                    if (resp) {
                        $('#uni_modal .modal-title').html($title)
                        $('#uni_modal .modal-body').html(resp)
                        if ($size != '') {
                            $('#uni_modal .modal-dialog').addClass($size)
                        } else {
                            $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
                        }
                        $('#uni_modal').modal('show')
                        end_load()
                    }
                }
            })
        }
    </script>

    <!-- Add this modal container at the end of your body tag -->
    <div class="modal fade" id="uni_modal" role='dialog'>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
</body>
</html>