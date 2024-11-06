<?php
    session_start();
    include('./db_connect.php');
    ob_start();
    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
    ob_end_flush();
    if(isset($_SESSION['login_id'])) header("location:indexsuperadmin.php?page=home");
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insightrix | LOGIN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="logo/evalucator.png" type="image/x-icon">
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

       

       
      

    </style>
</head>
<body>
    
    
    <div class="login-container">
        <h1>Insightrix | <span style="color: teal;">LOGIN superadmin<span></h1>
        <form action="" id="login-form">
          <div class="email-container">
          <input type="text" id="identifier" name="identifier" placeholder="School ID/ Email" required>
          </div>
            
          <div class="password-container">
    <input type="password" name="password" placeholder="Password" required>
    <button type="button" class="toggle-password" aria-label="Toggle password visibility">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
        </svg>
    </button>
</div>
          
            <a href="forgot.php" class="forgot-password">Forgot Password?</a>
            

    

           
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
    $(document).ready(function(){
    $('#login-form').submit(function(e){
        e.preventDefault();
        $.ajax({
            url:'ajax.php?action=login3',
            method:'POST',
            data:$(this).serialize(),
            error:err=>{
                console.log(err)
            },
            success:function(resp){
                if(resp == 1){
                    location.href ='indexsuperadmin.php?page=home';
                } else if(resp == 2){
                    alert("Username or password is incorrect.");
                } else if(resp == 3){
                    // Use replace instead of href to prevent browser back button
                    window.location.replace('change_password.php');
                }
            }
        });
    });
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

        function requestAccount() {
            alert("Please contact the administrator to request an account.");
        }
    </script>
</body>
</html>