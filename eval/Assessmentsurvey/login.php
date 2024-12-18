<?php
    session_start();
    include('./db_connect.php');
    ob_start();
    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
    ob_end_flush();
    if(isset($_SESSION['login_id'])) header("location:index.php?page=home");

    function escape_template($string) {
        // Remove potential Twig/template syntax
        $string = preg_replace('/{[{%].*?[%}]}/', '', $string);
        // Escape special characters
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insightrix | LOGIN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<link rel="icon" href="logo/evalucator-nobg2.png" type="image/x-icon">
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
        .login-container{
          width: 25%;
        }
        h1 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 2rem;
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
       
        .password-container {
            position: relative;
            
        }
        .email-container{
          position: relative;
          
        }
        .password-container input {
            padding-right: 40px;
          
            
        }

      
      

        .dropdown {
    position: relative;
  
    
}

.dropdown select {
  
    padding-right: 30px; /* Leave space for the icon */
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
    font-size: 24px; /* Adjust icon size */
}
/* Change the letter-spacing of the select element */
#droplist {
    letter-spacing: 1.5px; /* Adjust the letter-spacing as needed */
    
    
}

#droplist option{
  letter-spacing: 1.5px; /* Adjust the letter-spacing as needed */
}

/* Option-specific letter-spacing */




        .password-container .toggle-password {
            position: absolute;
            z-index: 1;
            right: 20px;
            margin-top: 1.7rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 20px;
        }

          .password-container .toggle-password svg {
              width: 20px;
              height: 20px;
              fill: #666;
          }
          
          button {
            background-color: #12686e;
            color: white;
            border: none;
            cursor: pointer;
        }
        .forgot-password {

            color: #12686e;
            text-decoration: none;
            font-size: 1rem;
            display: block;
            margin-bottom: 1rem;
        }
        .remember-me {
            
            display: flex;
            align-items: center;
            
        }
        #remember{
          margin-top: 10px;
          width: 1.3rem;
          border-radius: 4px;
        }

        .remember-me input {
            
            margin-right: 0.5rem;
            
        }
        .request-account {
            
            text-align: center;
            
            font-size: 1rem;
        }
        .terms {
            text-align: center;
            font-size: .8rem;
            margin-top: 2.5rem;
        }

        .error-box {
            display: none;
            background-color: #ffebee;
            border: 1px solid #ef5350;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 15px;
            letter-spacing: 0.5px;
           
            animation: fadeIn 0.05s ease-in-out;
            
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-box.show {
            display: block;
            
            
        }
        #droplist {
            letter-spacing: 1.5px;
        }

        #droplist option{
            letter-spacing: 1.5px;
        }

       

        input:focus, select:focus {
            outline: none;
            border: 1px solid #ccc;
            box-shadow: 0 0 0 2px rgba(18, 104, 110, 0.2);
        }
        .validation-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
            display: none;
            letter-spacing: 0.5px;
            padding-left: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .input-group {
            margin-bottom: 1rem;
            position: relative;
        }
        
        .input-group input {
            margin-bottom: 0;
        }
        
        .input-error {
            border-color: #dc3545 !important;
            background-color: #fff;
            box-shadow: none !important;
        }
        
        .input-error:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .validation-message.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #remember:checked {
    border: none;
    box-shadow: none;
    outline: none;
}
#remember {
    border: none;
    box-shadow: none;
    outline: none;
}

        .input-group1 {
                    margin-bottom: .1rem;
                }
        
        .input-group1 input {
            margin-bottom: 0.1rem;
        }


        .sumbit{
            margin-bottom: .1rem;
        }
        
        

@media screen and (max-width: 768px) {
    .login-container {
        width: 65%;
        max-width: 350px;
        padding: 20px;
        margin: 0 auto;
        box-sizing: border-box;
    }

    h1 {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    input, button, select {
        width: 100%;
        height: 2.6rem;
        font-size: 13px;
        box-sizing: border-box;
    }

    .password-container .toggle-password {
        right: 10px;
    }

    .forgot-password {
        text-align: center;
    }

    .remember-me {
        margin: 15px 0;
    }

    .request-account{
        font-size: 0.9rem;
    }
    .terms {
        font-size: 0.7rem;
        margin-top: 3rem;
        text-align: center;
    }
    .forgot-password {

     text-align: left;
}
input-group1 {
                    margin-bottom: .1rem;
                }
        
        .input-group1 input {
            margin-bottom: 0.1rem;
        }
    
}

.password-container .toggle-password {
            position: absolute;
            z-index: 1;
            right: 20px;
            margin-top: 1.5rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 20px;
        }

@media screen and (max-width: 480px) {
    .login-container {
        width: 70%;
        max-width: 300px;
        padding: 15px;
        box-sizing: border-box;
    }

    h1 {
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
    }

    .error-box {
        font-size: 13px;
        padding: 10px;
    }

    .validation-message {
        font-size: 0.75rem;
    }

    .remember-me label {
        font-size: 0.9rem;
    }
    .forgot-password {

    text-align: left;
    }

    .terms {
        font-size: 0.7rem;
        margin-top: 3rem;
        text-align: center;
    }
    .remember-me {
        margin-top: 2px;
        margin-bottom: 5px; /* Adjusted for mobile view */
    }

    .password-container .toggle-password {
            position: absolute;
            z-index: 1;
            right: 20px;
            margin-top: 1.5rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 20px;
        }

   

    
}

@media screen and (max-width: 320px) {
    .login-container {
        width: 75%;
        max-width: 280px;
        padding: 15px;
    }

    h1 {
        font-size: 1.6rem;
    }

    .request-account {
        font-size: 0.8rem;
    }
    .terms {
        font-size: 0.7rem;
        margin-top: 3rem;
        text-align: center;
    }
}

.submit button {
    position: relative;
    transition: all 0.3s;
}

.submit button.loading {
    color: transparent;
}

.submit button.loading::after {
    content: '...';
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 20px;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { content: '.'; }
    33% { content: '..'; }
    66% { content: '...'; }
}

@media screen and (max-width: 768px) {
    .login-container {
        width: 90%;
        padding: 20px;  /* Reduced padding */
        margin: 0 15px;  /* Added margin */
    }

    input, button, select {
        height: 2.8rem;
        font-size: 14px;
    }

    .submit button {
        margin-top: 10px;  /* Added spacing */
    }
}

       

       
      

    </style>
</head>
<body>
    
    
<div class="login-container">
        <h1>Insightrix | <span style="color: teal;">LOGIN</span></h1>

        <div class="error-box" id="error-message"></div>
        <form action="" id="login-form" novalidate>
            <?php
            // Generate CSRF token if not exists
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?php echo escape_template($_SESSION['csrf_token']); ?>">
            
            <div class="input-group">
                <div class="email-container">
                    <input type="text" id="identifier" name="identifier" placeholder="School ID/ Email">
                    <div class="validation-message" id="identifier-error">Please enter your School ID or Email</div>
                </div>
            </div>
            
            <div class="input-group">
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Password">
                    <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                        </svg>
                    </button>
                    <div class="validation-message" id="password-error">Please enter your password</div>
                </div>
            </div>

            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
            
            <div class="input-group1">
                <div class="dropdown">
                    <select name="login" id="droplist" required>
                        <option value="3">Student</option>
                        <option value="2">Instructor</option>
                    </select>
                    <span class="material-icons">arrow_drop_down</span>
                </div>
                <div class="validation-message" id="login-error">Please select a user type</div>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me?</label>
            </div>
            <div class="submit">
            <button type="submit">LOGIN</button>
            </div>
           
        </form>
        
        <div class="request-account">
            Don't have an account? <a href="requestAcc.php" style="color: teal" onclick="requestAccount()">REQUEST.</a>
        </div>
        
        <div class="terms">
            By Logging in you have accepted our <a href="#" style="color: teal">Terms and Privacy</a>.
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function(){
    let formSubmitted = false;

    // Custom validation function
    function validateField(field, showError = false) {
        const $field = $(field);
        const $error = $(`#${field.id}-error`);
        
        if (!field.value.trim()) {
            if (showError || formSubmitted) {
                $field.addClass('input-error');
                $error.addClass('show');
            }
            return false;
        } else {
            $field.removeClass('input-error');
            $error.removeClass('show');
            return true;
        }
    }

    function showError(message) {
        const errorBox = $('#error-message');
        errorBox.text(message);
        errorBox.addClass('show');
        
        setTimeout(() => {
            errorBox.removeClass('show');
        }, 5000);
    }
    
    // Form submission
    $('#login-form').submit(function(e) {
        e.preventDefault();
        formSubmitted = true;
        
        const $submitButton = $('#login-form button[type="submit"]');
        $submitButton.addClass('loading');
        
        $.ajax({
            url: 'ajax.php?action=login',
            method: 'POST',
            data: $(this).serialize(),
            error: function(err) {
                console.log(err);
                $submitButton.removeClass('loading');
                showError('An error occurred. Please try again.');
            },
            success: function(resp) {
                if(resp == 'invalid_token') {
                    showError('Invalid request. Please refresh the page and try again.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else if(resp == 1) {
                    location.href = 'index.php?page=home';
                } else if(resp == 2) {
                    showError('Username or password is incorrect.');
                } else if(resp == 3) {
                    location.href = 'change_password.php';
                } else if(resp == 4) {
                    showError('Too many failed attempts. Please try again after 15 minutes.');
                }
                $submitButton.removeClass('loading');
            }
        });
    });

    // Password visibility toggle
    $('.toggle-password').click(function() {
        const passwordField = $(this).siblings('input');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        
        $(this).find('svg').attr('viewBox', type === 'password' ? '0 0 24 24' : '0 0 24 24');
        $(this).find('path').attr('d', type === 'password' 
            ? 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z'
            : 'M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z'
        );
    });
});
    </script>
</body>
</html>