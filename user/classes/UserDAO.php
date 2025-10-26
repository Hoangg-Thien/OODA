<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class UserDAO {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM khachhang WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new User(
                $row['makh'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['fullname']
            );
        }
        return null;
    }

    public function addUser($username, $password, $email, $fullname) {
        $sql = "INSERT INTO khachhang (username, password, email, fullname) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $password, $email, $fullname);
        return $stmt->execute();
    }

    public function close() {
        $this->conn->close();
    }
}
?>
