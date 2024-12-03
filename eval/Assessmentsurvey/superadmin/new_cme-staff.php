<?php include './db_connect.php'; ?>
<div class="container py-4 rounded-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-light bg-gradient text-white py-3">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 flex-grow-1 text-bold">Add New CME Staff</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href = 'indexsuperadmin.php?page=cme_staff'">
                    Back to List
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="" id="manage_staff" class="needs-validation" novalidate>
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-6 border-end">
                        <div class="form-group mb-3">
                            <label for="firstname" class="form-label fw-semibold">First Name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" required>
                            <div class="invalid-feedback">Please provide a First Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="lastname" class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" required>
                            <div class="invalid-feedback">Please provide a Last Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                            <div class="invalid-feedback">Please provide a valid Email.</div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i data-lucide="eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please provide a Password.</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label fw-semibold">Profile Picture (Optional)</label>
                            <div class="d-flex align-items-center gap-5 p-3 bg-light rounded-3">
                                <div class="avatar-preview">
                                    <img src="assets/img/default-avatar.png" alt="Avatar" id="cimg" 
                                         class="rounded shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="avatar-upload flex-grow-1">
                                    <input type="file" class="form-control d-none" id="avatar" name="img" 
                                           accept="image/*" onchange="displayImg(this,$(this))">
                                    <div class="d-flex justify-content-center gap-4">
                                        <button type="button" class="btn btn-primary" onclick="$('#avatar').click()">
                                            <i data-lucide="upload-cloud" class="me-2"></i>Choose Photo
                                        </button>
                                    </div>
                                    <div class="form-text text-center mt-3" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                        Square image, 200x200px or larger
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-light me-2" onclick="location.href = 'index.php?page=cme_staff'">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Password toggle
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
            var formText = $('.avatar-upload .form-text');
            if (fileName) {
                var displayName = fileName.length > 20 ? 
                    fileName.substring(0, 17) + '...' : fileName;
                formText.text('Selected: ' + displayName);
                formText.attr('title', 'Selected: ' + fileName);
            } else {
                formText.text('Square image, 200x200px or larger');
                formText.attr('title', 'Square image, 200x200px or larger');
            }
        });

        // Form submission
        $('#manage_staff').submit(function(e) {
            e.preventDefault();
            if (this.checkValidity()) {
                $.ajax({
                    url: 'ajax.php?action=save_staff_cme',
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    success: function(resp) {
                        if(resp == 1) {
                            alert_toast('Staff successfully saved', 'success');
                            setTimeout(function() {
                                location.href = 'index.php?page=cme_staff';
                            }, 750);
                        } else if(resp == 2) {
                            alert_toast('Email already exists', 'warning');
                        }
                    }
                });
            }
            $(this).addClass('was-validated');
        });
    });
</script>