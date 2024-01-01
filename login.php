<?php
require_once 'Database.php';
require_once 'User.php';

$db = new Database();
$user = new User($db);

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user->login($username, $password);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
    <body class="login-bg">
        <div class="container">
            <h2>Login</h2>
            <form method="post" action="login.php">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit" name="login">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Signup here</a></p>
        </div>
    </body>
</html>

