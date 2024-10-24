<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account | Event Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 40px 20px;
            position: relative;
            overflow-y: auto; /* Enable vertical scrolling */
        }

        /* Animated background */
        .background-shapes {
            position: fixed; /* Changed to fixed */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none; /* Prevent shapes from interfering with scrolling */
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite;
        }

        .shape:nth-child(1) {
            width: 150px;
            height: 150px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 100px;
            height: 100px;
            top: 20%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 15%;
            right: 25%;
            animation-delay: 4s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(15px, 15px) rotate(90deg); }
            50% { transform: translate(0, 25px) rotate(180deg); }
            75% { transform: translate(-15px, 15px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            margin: 20px 0; /* Add margin to ensure space at top and bottom */
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section i {
            font-size: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 600;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 18px;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
        }

        .password-strength {
            height: 5px;
            background: #e1e1e1;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            padding-left: 45px;
        }

        button {
            width: 100%;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        button:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(100, 100);
                opacity: 0;
            }
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 24px;
            }

            input, button {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="background-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="container">
        <div class="logo-section">
            <i class="fas fa-calendar-alt"></i>
            <h1>Create Account</h1>
            <p class="subtitle">Join our event platform community</p>
        </div>

        <form name="registerForm" method="post" action="register_proses.php" onsubmit="return validateForm()">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input name="fname" type="text" placeholder="First Name" required />
            </div>
            
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input name="lname" type="text" placeholder="Last Name" required />
            </div>
            
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input name="email" type="email" placeholder="Email" required />
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input name="pass" type="password" placeholder="Password" required onkeyup="checkPasswordStrength(this.value)" />
                <div class="password-strength" id="passwordStrength"></div>
                <div class="password-requirements">
                    Password must be at least 6 characters with letters and numbers
                </div>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input name="confirm_pass" type="password" placeholder="Confirm Password" required />
            </div>

            <button type="submit">
                Create Account
                <span class="ripple"></span>
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>

    <script>
        function validateForm() {
            var password = document.forms["registerForm"]["pass"].value;
            var confirmPassword = document.forms["registerForm"]["confirm_pass"].value;
            
            if(password.length < 6) {
                alert("Password must be at least 6 characters!");
                return false;
            }
            
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            
            return true;
        }

        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            if(password.length >= 6) strength += 25;
            if(password.match(/[a-z]+/)) strength += 25;
            if(password.match(/[A-Z]+/)) strength += 25;
            if(password.match(/[0-9]+/)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if(strength <= 25) {
                strengthBar.style.background = '#ff4d4d';
            } else if(strength <= 50) {
                strengthBar.style.background = '#ffd700';
            } else if(strength <= 75) {
                strengthBar.style.background = '#90EE90';
            } else {
                strengthBar.style.background = '#00ff00';
            }
        }
    </script>
</body>
</html>