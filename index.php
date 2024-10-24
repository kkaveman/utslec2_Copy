<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            display: flex;
            gap: 30px;
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
        }

        .card p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        a {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        a:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card:nth-child(1) {
            animation: fadeIn 0.6s ease forwards;
        }

        .card:nth-child(2) {
            animation: fadeIn 0.6s 0.2s ease forwards;
            opacity: 0;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Already Have an Account?</h2>
            <p>Welcome back! Sign in to access your account and continue your journey with us.</p>
            <a href="login.php">Sign In</a>
        </div>

        <div class="card">
            <h2>New Here?</h2>
            <p>Join our community today! Create an account to get started and explore all our features.</p>
            <a href="Register.php">Sign Up</a>
        </div>
    </div>
</body>
</html>