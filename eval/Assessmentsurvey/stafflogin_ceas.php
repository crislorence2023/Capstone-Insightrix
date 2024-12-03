<?php
    session_start();
    
    // Add cache control headers
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    
    // Only clear session if explicitly logged out
    if(isset($_GET['logout']) && $_GET['logout'] == 1) {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        session_destroy();
        session_start();
    }
    
    // Redirect if already logged in
    if(isset($_SESSION['login_id']) && isset($_SESSION['login_department'])) {
        $college = $_SESSION['login_department'];
        switch($college) {
            case 'COE':
                header("location:staff_coe_index.php?page=home");
                break;
            case 'CEAS':
                header("location:staff_ceas_index.php?page=home");
                break;
            case 'CME':
                header("location:staff_cme_index.php?page=home");
                break;
            case 'COT':
                header("location:staffindex.php?page=home");
                break;
            default:
                // Invalid department, clear session and stay on login
                $_SESSION = array();
                session_destroy();
                session_start();
                break;
        }
        exit();
    }
    
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insightrix | STAFF LOGIN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="logo/evalucator.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
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
        .login-container {
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
        .email-container {
            position: relative;
        }
        .password-container input {
            padding-right: 40px;
        }
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
        button[type="submit"] {
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
        #remember {
            margin-top: 10px;
            width: 1.3rem;
            border-radius: 4px;
        }
        .remember-me input {
            width: auto;
            margin-right: 0.5rem;
        }
        .request-account {
            text-align: center;
            font-size: 1rem;
        }
        .terms {
            text-align: center;
            font-size: .8rem;
            margin-top: 1.5rem;
        }

        .college-container select {
    width: 100%;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    font-size: 15px;
    border-radius: 10px;
    box-sizing: border-box;
    height: 3rem;
    background-color: white;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Insightrix | <span style="color: teal;">STAFF LOGIN</span></h1>
        <form id="login-form">
            <input type="hidden" name="college" value="CEAS">
            <div class="email-container">
                <input type="text" id="identifier" name="identifier" placeholder="Staff ID/ Email" required>
            </div>
            
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                </button>
            </div>
            
            <a href="forgot.php" class="forgot-password">Forgot Password?</a>
            <div class="college-container">
   <select id="college" name="college" required>
       <option value="CEAS">CEAS</option>
   </select>
</div>
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me?</label>
            </div>

       
            
            <button type="submit">LOGIN</button>
        </form>
        
        <div class="request-account">
            Don't have an account? <a href="#" onclick="requestAccount()">REQUEST.</a>
        </div>
        <div class="terms">
            By Logging in you have accepted our <a href="#">Terms and Privacy</a>.
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {
    // Handle login form submission
    $('#login-form').submit(function (e) {
        e.preventDefault();

        // Show loading indicator (optional)
        $('button[type="submit"]').prop('disabled', true).html('Logging in...');

        $.ajax({
            url: 'ajax.php?action=login4_ceas',
            method: 'POST',
            data: $(this).serialize(),
            error: (err) => {
                console.log(err);
                alert("An error occurred while connecting to the server.");
                $('button[type="submit"]').prop('disabled', false).html('LOGIN');
            },
            success: function (resp) {
                resp = resp.trim();
                console.log('Server response:', resp);

                switch(resp) {
                    case 'COE':
                        window.location.href = 'staff_coe_index.php?page=home';
                        break;
                    case 'CEAS':
                        window.location.href = 'staff_ceas_index.php?page=home';
                        break;
                    case 'CME':
                        window.location.href = 'staff_cme_index.php?page=home';
                        break;
                    case 'COT':
                        window.location.href = 'staffindex.php?page=home';
                        break;
                    case '2':
                        alert("Invalid credentials. Please check your Staff ID/Email and password.");
                        break;
                    case '4':
                        alert("Please select your department before logging in.");
                        break;
                    case '5':
                        alert("You are not authorized to access this department's portal. Please select your assigned department.");
                        break;
                    case '6':
                        alert("Invalid department selection.");
                        break;
                    default:
                        alert("An unexpected error occurred. Please try again.");
                        break;
                }
                
                // Re-enable submit button
                $('button[type="submit"]').prop('disabled', false).html('LOGIN');
            }
        });
    });

    // Password toggle functionality
    $('.toggle-password').click(function () {
        const passwordField = $(this).siblings('input');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);

        // Update icon based on password visibility
        const path = $(this).find('path');
        if (type === 'text') {
            path.attr('d', 'M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z');
        } else {
            path.attr('d', 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z');
        }
    });

    // Request account functionality
    function requestAccount() {
        alert("Please contact the administrator to request an account.");
    }
});

    </script>
</body>
</html>