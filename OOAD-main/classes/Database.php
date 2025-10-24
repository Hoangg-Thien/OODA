<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "c07db";
    private $conn;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");
        return $this->conn;
    }

    public function close() {
        if ($this->conn) $this->conn->close();
    }
}
