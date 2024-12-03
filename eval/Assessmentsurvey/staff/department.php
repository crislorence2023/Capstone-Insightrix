<?php
include 'db_connect.php';

if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $check = $conn->query("SELECT * FROM department_list WHERE name = '$name'")->num_rows;
    if($check > 0){
        $msg = "<div class='alert alert-danger'>Department already exists!</div>";
    } else {
        $insert = $conn->query("INSERT INTO department_list (name, description) VALUES ('$name', '$description')");
        if($insert){
            $msg = "<div class='alert alert-success'>Department added successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error adding department!</div>";
        }
    }
}

// Fetch departments
$departments = $conn->query("SELECT * FROM department_list ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .table {
            margin-bottom: 0;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .department-form {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="card-title mb-0">Manage Departments</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($msg)) echo $msg; ?>
                        
                        <!-- Add Department Form -->
                        <form method="POST" class="department-form mb-4">
                            <div class="mb-3">
                                <label for="name" class="form-label">Department Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Add Department</button>
                        </form>

                        <!-- Departments List -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Department Name</th>
                                        <th>Description</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $departments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['date_created'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>