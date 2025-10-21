<?php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'c07db'; // ⚙️ tên database của bạn

    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");
    }

    // ✅ Trả về kết nối thô (nếu cần dùng ngoài class)
    public function getConnection() {
        return $this->conn;
    }

    // ✅ Thêm hàm query() để gọi truy vấn trực tiếp
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // ✅ Thêm hàm escape() để chống SQL Injection
    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }

    // ✅ Đóng kết nối
    public function close() {
        $this->conn->close();
    }
}
?>
