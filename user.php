<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        $password = mysqli_real_escape_string($this->db->getConnection(), $password);
        $query = "SELECT password FROM users WHERE username='$username'";
        $result = mysqli_query($this->db->getConnection(), $query);
        $hashPassword = mysqli_fetch_assoc($result);

        if (password_verify($password, $hashPassword['password'])) {
            session_start();
            $_SESSION['username'] = $username;
            header('location: dashboard.php');
        } else {
            echo '<script>alert("Wrong username/password combination")</script>';
        }
    }

    public function logout() {
        session_start();
        unset($_SESSION['username']);
        session_destroy();
        header("location: login.php");
        exit;
    }

    public function register($username, $password) {
        $username = mysqli_real_escape_string($this->db->getConnection(), $username);
        $password = mysqli_real_escape_string($this->db->getConnection(), $password);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password) VALUES('$username', '$passwordHash')";
        mysqli_query($this->db->getConnection(), $query);

        header('location: login.php');
    }
}
?>
