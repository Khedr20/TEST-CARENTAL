<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbName = 'carental';
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->dbName);

        if (!$this->conn) {
            die("Error connecting to the database: " . mysqli_connect_error());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
