<?php
require_once 'Database.php';
require_once 'User.php';

$db = new Database();
$user = new User($db);

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user->register($username, $password);
}

?>
<!--<!DOCTYPE html>
<html>
    <head>
        <title>Signup</title>
    </head>
    <body>
        <form method="post" action="signup.php">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <button type="submit" name="register">Signup</button>
        </form>
    </body>
</html>
-->
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script>
    function validateForm() {
        var username = document.forms["signupForm"]["username"].value;
        var password = document.forms["signupForm"]["password"].value;
        if (username == "" || password == "") {
            alert("All fields must be filled out");
            return false;
        }
    }
    </script>
</head>
<body>
<body class="signup-bg">
<div class="container">
    <h2>Signup</h2>
    <form name="signupForm" method="post" action="signup.php" onsubmit="return validateForm()">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <button type="submit" name="register">Signup</button>
    </form>
    <br>
    <button onclick="location.href='login.php';">Already have an account? Login here</button>
</div>
</body>
</html>
