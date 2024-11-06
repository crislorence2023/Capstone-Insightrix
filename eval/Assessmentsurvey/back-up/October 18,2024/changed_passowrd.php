<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
        .hidden {
            display: none;
        }
        .verification-status {
            margin-top: 10px;
        }
        #timer {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Email and Change Password</h2>
        <form id="change-password-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="verification_code">Verification Code</label>
                <input type="text" class="form-control" id="verification_code" name="verification_code" required>
            </div>
            <button type="button" id="send-code" class="btn btn-secondary btn-block">Send Verification Code</button>
            <div id="timer" class="text-center"></div>
            <button type="button" id="verify-code" class="btn btn-info btn-block">Verify Code</button>
            <div class="verification-status"></div>
            <div id="password-fields" class="hidden">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Change Password</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    $(document).ready(function(){
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
                            location.href = 'index.php?page=home';
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
                complete: function() {
                    submitBtn.prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>