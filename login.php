<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
 
</head>
<body>
    <h1>Login</h1>
    <form name="LoginForm" method="post" action="login_proses.php">
        <input name="username" type="text" placeholder="Username or email" required /><br>
        <input name="pass" type="password" placeholder="Password" required /><br>
      
        <button type="submit">Submit</button>
    </form>
    <h3>dont have account?</h3><br>
    <a href="register.php">register</a>
</body>
</html>
