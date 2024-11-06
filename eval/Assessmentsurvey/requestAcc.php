<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insightrix | Request Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        body {
            font-family: "Montserrat", sans-serif;
            background-color: #F9FEFF;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .back-button {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #12686e;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 0;
            font-size: 0.9rem;
            font-family: inherit;
        }

        .back-button:hover {
            color: #0a4c51;
        }
        
        h1 {
            color: #12686e;
            text-align: center;
            font-size: 1.8rem;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.3s ease;
            box-sizing: border-box;
            letter-spacing: 1.5px;
        }
        
        input:focus, select:focus {
            border-color: #12686e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(18, 104, 110, 0.1);
        }

        textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.3s ease;
            box-sizing: border-box;
            resize: vertical;
            min-height: 100px;
            letter-spacing: 1.5px;
        }

        textarea:focus {
            border-color: #12686e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(18, 104, 110, 0.1);
        }
        .message{
            margin-top: 2rem;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <button onclick="window.location.href='login.php'" class="back-button">
                <span class="material-icons">arrow_back</span>
                Back to Login
            </button>
           
        </div>
        <div class="message">
            <p>To Request an Account Contact your Department.</p>
            </div>
           
        
       
    </div>
</body>
</html>