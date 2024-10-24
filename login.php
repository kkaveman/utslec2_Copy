<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
        }

        button {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        h3 {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-weight: 500;
            font-size: 16px;
        }

        a {
            display: block;
            text-align: center;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #764ba2;
        }

        /* Animasi untuk input fields */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        input:invalid:not(:placeholder-shown) {
            border-color: #ff6b6b;
            animation: shake 0.3s ease;
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
    <div class="container">
        <h1>Welcome Back</h1>
        <form name="LoginForm" method="post" action="login_proses.php">
            <input name="username" type="text" placeholder="Username or email" required />
            <input name="pass" type="password" placeholder="Password" required />
            <button type="submit">Sign In</button>
        </form>
        <h3>Don't have an account?</h3>
        <a href="register.php">Register Now</a>
    </div>
</body>
</html>