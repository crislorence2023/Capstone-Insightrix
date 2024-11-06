<?php
    session_start();
    include('./db_connect.php');
    ob_start();
    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
    ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="./logo/Evalucator-nobg2.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insightrix | Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            font-family: "Montserrat", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #F9FEFF;
        }
        .forgot-container {
            width: 25%;
        }
        h1 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }
        .description {
            margin-bottom: 1.5rem;
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        input, button {
            letter-spacing: 1.5px;
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            font-size: 15px;
            border-radius: 10px;
            box-sizing: border-box;
            height: 3rem;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 5px;
            display: none;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .success-message {
            color: #155724;
            background-color: #d4edda;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 5px;
            display: none;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .dropdown {
            position: relative;
            margin-bottom: 1rem;
        }
        .dropdown select {
            padding-right: 30px;
            -webkit-appearance: none;
            letter-spacing: 1.5px;
            width: 100%;
            padding-left: 1rem;
            border: 1px solid #ccc;
            font-size: 15px;
            border-radius: 10px;
            box-sizing: border-box;
            height: 3rem;
        }
        .dropdown .material-icons {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 24px;
        }
        button {
            background-color: #12686e;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0e5458;
        }
        .back-to-login {
            text-align: center;
            margin-top: 1rem;
        }
        .back-to-login a {
            color: #12686e;
            text-decoration: none;
        }
        .verification-section, .new-password-section {
            display: none;
        }
        .password-requirements {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .password-field {
        position: relative;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .password-field input {
        padding-right: 45px; /* Increased padding to accommodate the icon */
        margin-bottom: 0;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        background: none;
        border: none;
        padding: 5px;
        width: auto;
        height: auto;
        margin: 0;
    }

    .password-toggle:hover {
        background-color: transparent;
        color: #12686e;
    }

    .password-toggle .material-icons {
        font-size: 20px;
        pointer-events: none;
    }
    </style>
</head>
<body>
    <div class="forgot-container">
        <h1>Forgot Password</h1>
        
        <!-- Email Section -->
        <div class="email-section">
    <div class="description">
        Enter your email address and select your account type. We'll send you a verification code.
    </div>
    
    <form id="email-form">
        <div id="error-message" class="error-message"></div>
        <div id="success-message" class="success-message"></div>
        
        <input type="email" id="email" name="email" placeholder="Email Address" required>
        
        <div class="dropdown">
            <select name="login" id="droplist" required>
                <option value="3">Student</option>
                <option value="2">Faculty/instructor</option>
            </select>
            <span class="material-icons">arrow_drop_down</span>
        </div>

        <button type="submit" id="email-submit">Send Verification Code</button>
    </form>
</div>

<!-- Verification Section -->
<div class="verification-section">
    <div class="description">
        Enter the verification code sent to your email.
    </div>
    
    <form id="verification-form">
        <div id="verification-error" class="error-message"></div>
        <input type="text" id="verification-code" name="code" placeholder="Enter Verification Code" required>
        <button type="submit" id="verification-submit">Verify Code</button>
    </form>
</div>

<div class="new-password-section">
            <div class="description">
                Enter your new password.
            </div>
            
            <form id="password-form">
                <div id="password-error" class="error-message"></div>
                <div class="password-requirements">
                    Password must contain at least 8 characters, including uppercase, lowercase, numbers, and special characters.
                </div>
                <div class="password-field">
                    <input type="password" id="new-password" name="password" placeholder="New Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('new-password')">
                        <span class="material-icons">visibility_off</span>
                    </button>
                </div>
                <div class="password-field">
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm-password')">
                        <span class="material-icons">visibility_off</span>
                    </button>
                </div>
                <button type="submit" id="password-submit">Update Password</button>
            </form>
        </div>

        <div class="back-to-login">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('.material-icons');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility_off';
            }
        }
    $(document).ready(function() {


        

    let userEmail = '';
    let userType = '';

    // Email Form Submission
    $('#email-form').submit(function(e) {
        e.preventDefault();
        const errorMessage = $('#error-message');
        const successMessage = $('#success-message');

        userEmail = $('#email').val();
        userType = $('#droplist').val();

        // Show loading state for email form button
        $(this).find('button[type="submit"]').prop('disabled', true).text('Sending...');

        $.ajax({
            url: 'ajax.php?action=forgot_password',
            method: 'POST',
            data: $(this).serialize(),
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                errorMessage.text('An error occurred: ' + error).show();
                successMessage.hide();
            },
            success: function(resp) {
                console.log("Server response:", resp);
                if (resp == 1) {
                    successMessage.text('Verification code has been sent to your email.').show();
                    errorMessage.hide();
                    $('.email-section').hide();
                    $('.verification-section').show();
                } else if (resp == 2) {
                    errorMessage.text('Email address not found.').show();
                    successMessage.hide();
                } else {
                    errorMessage.text('An error occurred. Please try again later. (Code: ' + resp + ')').show();
                    successMessage.hide();
                }
            },
            complete: function() {
                // Reset email form button state
                $('#email-form button[type="submit"]').prop('disabled', false).text('Send Verification Code');
            }
        });
    });

    // Verification Form Submission
    $('#verification-form').submit(function(e) {
        e.preventDefault();
        const verificationError = $('#verification-error');

        // Show loading state for verification form button
        $(this).find('button[type="submit"]').prop('disabled', true).text('Verifying...');

        $.ajax({
            url: 'ajax.php?action=verify_reset_code',
            method: 'POST',
            data: {
                code: $('#verification-code').val(),
                email: userEmail,
                login: userType
            },
            success: function(resp) {
                if (resp == 1) {
                    $('.verification-section').hide();
                    $('.new-password-section').show();
                } else if (resp == 2) {
                    verificationError.text('Invalid or expired verification code.').show();
                } else {
                    verificationError.text('An error occurred. Please try again later.').show();
                }
            },
            complete: function() {
                // Reset verification form button state
                $('#verification-form button[type="submit"]').text('Verify Code');
                $('#verification-form button[type="submit"]').prop('disabled', false);
            }
        });
    });

    // Password Form Submission
    $('#password-form').submit(function(e) {
    e.preventDefault();
    const passwordError = $('#password-error');

    const password = $('#new-password').val();
    const confirmPassword = $('#confirm-password').val();

    // Show loading state
    $(this).find('button[type="submit"]').prop('disabled', true).text('Updating...');

    // Password validation
    if (password !== confirmPassword) {
        passwordError.text('Passwords do not match.').show();
        $(this).find('button[type="submit"]').prop('disabled', false).text('Update Password');
        return;
    }

    if (password.length < 8) {
        passwordError.text('Password must be at least 8 characters long.').show();
        $(this).find('button[type="submit"]').prop('disabled', false).text('Update Password');
        return;
    }

    if (!password.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])/)) {
        passwordError.text('Password must contain uppercase, lowercase, numbers, and special characters.').show();
        $(this).find('button[type="submit"]').prop('disabled', false).text('Update Password');
        return;
    }

    $.ajax({
        url: 'ajax.php?action=forgot_update_password',
        method: 'POST',
        data: {
            email: userEmail,
            login: userType,
            password: password
        },
        success: function(resp) {
            if (resp == 1) {
                alert('Password updated successfully. Please login with your new password.');
                window.location.href = 'login.php';
            } else if (resp == 3) {
                passwordError.text('Cannot use current password. Please choose a different password.').show();
            } else {
                passwordError.text('An error occurred. Please try again later.').show();
            }
        },
        complete: function() {
            $('#password-form button[type="submit"]').prop('disabled', false).text('Update Password');
        }
    });
});

    // Clear messages when user starts typing
    $('input, select').on('input change', function() {
        $('.error-message, .success-message').hide();
    });
});
</script>

</body>
</html>