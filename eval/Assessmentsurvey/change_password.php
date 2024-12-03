<?php
session_start();
if(!isset($_SESSION['login_id'])) {
    header("location:login.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insightrix | Change Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Montserrat", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #F9FEFF;
            padding: 20px;
        }

        .change-password-container {
            width: 25%;
            min-width: 320px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .welcome-text {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        input {
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

        button {
            letter-spacing: 1.5px;
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: none;
            font-size: 15px;
            border-radius: 10px;
            box-sizing: border-box;
            height: 3rem;
            background-color: #12686e;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0e5156;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .verification-status {
            margin: 1rem 0;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        #timer {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin: 0.5rem 0;
        }

        .hidden {
            display: none;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 0.9rem;
        }

        .password-container {
            position: relative;
        }

        .password-container input {
            padding-right: 40px;
        }

        .logout-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <a href="ajax.php?action=logout" class="logout-button">Logout</a>
    <div class="change-password-container">
        <h1>Insightrix | <span style="color: teal;">SETUP</span></h1>
        <p class="welcome-text">Welcome! To ensure account security, please set up your email for password recovery and change your default password.</p>
        
        <form id="change-password-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group verification-section">
                <label for="verification_code">Verification Code</label>
                <input type="text" id="verification_code" name="verification_code" placeholder="Enter verification code" required>
                <button type="button" id="send-code" class="btn btn-secondary">Send Verification Code</button>
                <div id="timer"></div>
                <button type="button" id="verify-code" class="btn btn-info">Verify Code</button>
            </div>

            <div class="verification-status"></div>

            <div id="password-fields" class="hidden">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                </div>

                <button type="submit">Update Password</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>

 window.history.pushState(null, null, window.location.href);
    window.onpopstate = function() {
        // Clear any session data
        $.ajax({
            url: 'ajax.php?action=logout',
            method: 'POST',
            success: function() {
                window.location.replace('login.php');
            },
            error: function() {
                window.location.replace('login.php');
            }
        });
    };

    $(document).ready(function(){
    // Prevent going back
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = function() {
        // Clear session and redirect to login
        $.ajax({
            url: 'ajax.php?action=logout_change_password',
            method: 'POST',
            success: function(resp) {
                if(resp == 1) {
                    window.location.replace('login.php');
                }
            },
            error: function() {
                window.location.replace('login.php');
            }
        });
    };


        let isVerified = false;
        let timerInterval;
        const COOLDOWN_TIME = 60; // Cooldown time in seconds
        let timeLeft = 0;

        function startTimer() {
            const sendCodeBtn = $('#send-code');
            timeLeft = COOLDOWN_TIME;
            sendCodeBtn.prop('disabled', true);
            sendCodeBtn.text('Resend Verification Code?');
            
            updateTimerDisplay();
            
            timerInterval = setInterval(() => {
                timeLeft--;
                updateTimerDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    sendCodeBtn.prop('disabled', false);
                    $('#timer').text('');
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const formattedTime = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            $('#timer').text(`Resend code in ${formattedTime}`);
        }

        

        $('#send-code').click(function() {
            let email = $('#email').val();
            if (!email) {
                alert("Please enter your email address.");
                return;
            }

            $.ajax({
                url: 'ajax.php?action=send_verification',
                method: 'POST',
                data: { email: email },
                success: function(resp) {
                    switch(parseInt(resp)) {
                        case 1:
                            alert("Verification code sent to your email.");
                            startTimer();
                            break;
                        case 2:
                            alert("Failed to update verification code. Please try again.");
                            break;
                        case 3:
                            alert("Failed to send verification code. Please try again.");
                            break;
                        case 4:
                            alert("This email address is already registered in the system. Please use a different email.");
                            $('#email').val('');
                            break;
                        default:
                            alert("An error occurred. Please try again.");
                    }
                },
                error: function() {
                    alert("Connection error. Please try again.");
                }
            });
        });

        $('#verify-code').click(function() {
            let code = $('#verification_code').val();
            
            if (!code) {
                alert("Please enter verification code.");
                return;
            }

            $.ajax({
                url: 'ajax.php?action=verify_code',
                method: 'POST',
                data: { verification_code: code },
                success: function(resp) {
                    if(resp == 1) {
                        isVerified = true;
                        $('.verification-status').html('<div class="alert alert-success">Verification successful! You can now change your password.</div>');
                        $('#password-fields').removeClass('hidden');
                        clearInterval(timerInterval);
                        $('#timer').text('');
                        $('#send-code').prop('disabled', true);
                    } else {
                        isVerified = false;
                        $('.verification-status').html('<div class="alert alert-danger">Incorrect verification code or code has expired. Please try again.</div>');
                    }
                },
                error: function() {
                    alert("Connection error. Please try again.");
                }
            });
        });

        $('#change-password-form').submit(function(e){
            e.preventDefault();
            
            if (!isVerified) {
                alert("Please verify your email first.");
                return;
            }

            let newPass = $('#new_password').val();
            let confirmPass = $('#confirm_password').val();

            if (newPass !== confirmPass) {
                alert("Passwords do not match!");
                return;
            }

            let submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: 'ajax.php?action=change_password',
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp) {
                    switch(parseInt(resp)) {
                        case 1:
                            alert("Password and email updated successfully!");
                            // Redirect to login page and clear history
                            window.location.replace('login.php'); // Replace with your login page URL
                            break;
                        case 2:
                            alert("Password does not meet requirements. It must be at least 8 characters long, contain uppercase and lowercase letters, numbers, and special characters.");
                            break;
                        case 3:
                            alert("This email address is already registered to another user. Please use a different email address.");
                            break;
                        default:
                            alert("An error occurred. Please try again.");
                    }
                },
                error: function() {
                    alert("Connection error. Please try again.");
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                }
            });
        });

        // Additional security: Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Prevent going back using keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.key === 'Backspace' || e.key === 'Back') && e.target === document.body) {
                e.preventDefault();
                window.location.replace('login.php'); // Replace with your login page URL
            }
        });
    });
    </script>
</body>
</html>