<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <script>
        function validateForm() {
            var password = document.forms["registerForm"]["pass"].value;
            var confirmPassword = document.forms["registerForm"]["confirm_pass"].value;

            if(password.length <6){
                alert("Passwords has to be atleast 6 characters!");
                return false;  // Prevent form submission

            }
            
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;  // Prevent form submission
            }
            
            return true;
        }
    </script>
</head>
<body>
    <h1>Register</h1>
    <form name="registerForm" method="post" action="register_proses.php" onsubmit="return validateForm()">
        <input name="fname" type="text" placeholder="First Name" required /><br>
        <input name="lname" type="text" placeholder="Last Name" required /><br>
        <input name="email" type="email" placeholder="Email" required /><br>
        <input name="pass" type="password" placeholder="Password" required /><br>
        <input name="confirm_pass" type="password" placeholder="Confirm Password" required />

        <button type="submit">Submit</button>
    </form>
    <h3>have account?</h3><br>
    <a href="login.php">login</a>
</body>
</html>
