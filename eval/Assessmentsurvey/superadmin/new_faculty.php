<?php
// Assume these variables are set from your database or previous form submission
$id = isset($id) ? $id : '';
$school_id = isset($school_id) ? $school_id : '';
$firstname = isset($firstname) ? $firstname : '';
$lastname = isset($lastname) ? $lastname : '';
$department = isset($department) ? $department : '';
$email = isset($email) ? $email : '';
$avatar = isset($avatar) ? $avatar : '';
?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add New Faculty</h5>
        </div>
        <div class="card-body">
            <form action="" id="manage_faculty" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="school_id" class="form-label">School ID</label>
                            <input type="text" name="school_id" id="school_id" class="form-control" required value="<?php echo $school_id; ?>">
                            <div class="invalid-feedback">Please provide a School ID.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" required value="<?php echo $firstname; ?>">
                            <div class="invalid-feedback">Please provide a First Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" required value="<?php echo $lastname; ?>">
                            <div class="invalid-feedback">Please provide a Last Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" name="department" id="department" class="form-control" required value="<?php echo $department; ?>">
                            <div class="invalid-feedback">Please provide a Department.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required value="<?php echo $email; ?>">
                            <div class="invalid-feedback">Please provide a valid Email.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" value="instructoreval2024" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Default password is 'instructoreval2024'. Please change it after first login.</small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="cpass" class="form-label">Confirm Password</label>
                            <input type="password" name="cpass" id="cpass" class="form-control" value="instructoreval2024" required>
                            <div class="invalid-feedback">Passwords do not match.</div>
                        </div>
                        <div class="form-group mb-4">
                            <label for="avatar" class="form-label">Profile Picture</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar-preview me-3">
                                    <img src="<?php echo $avatar ? 'assets/uploads/'.$avatar : 'assets/img/default-avatar.png' ?>" 
                                         alt="Avatar" id="cimg" class="rounded-square" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="avatar-upload">
                                    <label for="avatar" class="btn btn-outline-primary mb-0">
                                        <i class="bi bi-upload me-2"></i>Upload New Picture
                                    </label>
                                    <input type="file" class="form-control d-none" id="avatar" name="img" accept="image/*" onchange="displayImg(this,$(this))">
                                    <div class="form-text mt-2">Recommended: Square image, at least 200x200 pixels</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-secondary" onclick="location.href = 'index.php?page=faculty_list'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-preview img {
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
}

.avatar-preview img:hover {
    border-color: #007bff;
}

.avatar-upload label {
    cursor: pointer;
    transition: all 0.3s ease;
}

.avatar-upload label:hover {
    background-color: #007bff;
    color: white;
}
</style>

<script>
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

    // Password toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Password matching
    document.getElementById('cpass').addEventListener('input', function() {
        if (this.value !== document.getElementById('password').value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // Trigger file input when the "Upload New Picture" button is clicked
        $('.avatar-upload label').on('click', function(e) {
            e.preventDefault();
            $('#avatar').click();
        });

        // Display file name when a file is selected
        $('#avatar').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('.avatar-upload .form-text').text('Selected: ' + fileName);
            } else {
                $('.avatar-upload .form-text').text('Recommended: Square image, at least 200x200 pixels');
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
                            alert_toast('Data successfully saved.',"success");
                            setTimeout(function(){
                                location.replace('index.php?page=faculty_list')
                            },750)
                        } else if(resp == 2){
                            $('#email').addClass("is-invalid");
                            $('#email').next('.invalid-feedback').text('Email already exists.');
                            end_load();
                        } else if(resp == 3){
                            $('#school_id').addClass("is-invalid");
                            $('#school_id').next('.invalid-feedback').text('School ID already exists.');
                            end_load();
                        }
                    }
                });
            }
        });
    });
</script>