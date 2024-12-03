<?php
// Assume these variables are set from your database or previous form submission
$id = isset($id) ? $id : '';
$school_id = isset($school_id) ? $school_id : '';
$firstname = isset($firstname) ? $firstname : '';
$lastname = isset($lastname) ? $lastname : '';
$department = isset($department) ? $department : '';
$email = isset($email) ? $email : '';
$avatar = isset($avatar) ? $avatar : '';

$dept_query = "SELECT id, name FROM department_list ORDER BY name ASC";
$dept_result = $conn->query($dept_query);
?>

<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-light bg-gradient text-white py-3">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 flex-grow-1 text-bold">Add New Faculty</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href = 'indexsuperadmin.php?page=faculty_list'">
                    Back to List
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="" id="manage_faculty" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-6 border-end">
                        <h6 class="text-muted mb-4 text-bold">Basic Information</h6>
                        <div class="form-group mb-3">
                            <label for="school_id" class="form-label fw-semibold">School ID</label>
                            <input type="text" name="school_id" id="school_id" class="form-control" required value="<?php echo $school_id; ?>" placeholder="Enter school ID">
                            <div class="invalid-feedback">Please provide a School ID.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="firstname" class="form-label fw-semibold">First Name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" required value="<?php echo $firstname; ?>" placeholder="Enter first name">
                            <div class="invalid-feedback">Please provide a First Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="lastname" class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" required value="<?php echo $lastname; ?>" placeholder="Enter last name">
                            <div class="invalid-feedback">Please provide a Last Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="department" class="form-label fw-semibold">Department</label>
                            <br>
                            <select name="department" id="department" class="form-select" required>
                                <option value="">Select Department</option>
                                <?php while($row = $dept_result->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($row['name']); ?>" 
                                        <?php echo ($department == $row['name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="invalid-feedback">Please select a Department.</div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-4 text-bold">Account Settings</h6>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>" placeholder="Enter email address (optional)">
                            <div class="invalid-feedback">Please provide a valid Email address if entering one.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" value="instructoreval2024" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i data-lucide="eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Default password: 'instructoreval2024'</small>
                        </div>
                          
                        <div class="form-group mb-4">
                            <label for="cpass" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="cpass" id="cpass" class="form-control" value="instructoreval2024" required>
                            <div class="invalid-feedback">Passwords do not match.</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label fw-semibold">Profile Picture (Optional)</label>
                            <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-3">
                                <div class="avatar-preview">
                                    <img src="<?php echo $avatar ? 'assets/uploads/'.$avatar : 'assets/img/default-avatar.png' ?>" 
                                         alt="Avatar" id="cimg" class="rounded shadow-sm" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="avatar-upload flex-grow-1">
                                    <input type="file" class="form-control d-none" id="avatar" name="img" accept="image/*" onchange="displayImg(this,$(this))">
                                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="$('#avatar').click()">
                                        <i data-lucide="upload-cloud" class="me-2"></i>Choose Photo
                                    </button>
                                    <div class="form-text text-center">Square image, 200x200px or larger</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="text-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-light me-2" onclick="location.href = 'index.php?page=faculty_list'">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Faculty</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #007bff;
    --primary-hover: #0056b3;
}

.card {
    transition: box-shadow 0.3s ease;
}

.avatar-preview {
    flex-shrink: 0;
    padding: 0.5rem;
}

.avatar-preview img {
    border: 3px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.avatar-upload {
    padding: 0.5rem;
}

.avatar-preview img:hover {
    border-color: var(--primary-color);
}

.form-control, .form-select {
    border: 1px solid #dee2e6;
    padding: 0.6rem 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
}

.invalid-feedback {
    font-size: 0.85rem;
}
.form-select{
    width: 100%;
    border-radius: 5px;
}

@media (max-width: 768px) {
    .border-end {
        border: none !important;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Initialize Lucide icons (only for upload-cloud icon)
    lucide.createIcons();

    // Bootstrap form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        const icon = this.querySelector('i');
        if (type === 'password') {
            icon.setAttribute('data-lucide', 'eye');
        } else {
            icon.setAttribute('data-lucide', 'eye-off');
        }
        lucide.createIcons();
    });

   

    // Password matching validation
    document.getElementById('cpass').addEventListener('input', function() {
        if (this.value !== document.getElementById('password').value) {
            this.setCustomValidity('Passwords do not match');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });

    // Image preview with fade effect
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').fadeOut(200, function() {
                    $(this).attr('src', e.target.result).fadeIn(200);
                });
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // File input handling
        $('#avatar').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                // Truncate filename if it's too long (e.g., more than 20 characters)
                if (fileName.length > 20) {
                    fileName = fileName.substring(0, 17) + '...';
                }
                $('.avatar-upload .form-text').text('Selected: ' + fileName);
            } else {
                $('.avatar-upload .form-text').text('Square image, 200x200px or larger');
            }
        });

        // Form submission
        $('#manage_faculty').submit(function(e){
            e.preventDefault();
            if (this.checkValidity()) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=save_faculty',
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    success: function(resp){
                        if(resp == 1){
                            alert_toast('Faculty member successfully added!', "success");
                            setTimeout(function(){
                                location.replace('index.php?page=faculty_list')
                            },750)
                        } else if(resp == 2){
                            $('#email').addClass("is-invalid");
                            $('#email').next('.invalid-feedback').text('This email address is already registered.');
                            end_load();
                        } else if(resp == 3){
                            $('#school_id').addClass("is-invalid");
                            $('#school_id').next('.invalid-feedback').text('This School ID is already in use.');
                            end_load();
                        } else if(resp == 4){
                            alert_toast('Failed to upload image. Please try again.', "error");
                            end_load();
                        } else {
                            alert_toast('An error occurred. Please try again.', "error");
                            end_load();
                        }
                    },
                    error: function(xhr, status, error) {
                        alert_toast('An error occurred: ' + error, "error");
                        end_load();
                    }
                });
            }
        });
    });
</script>