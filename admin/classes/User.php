<?php
class User {
    private $conn;
    private $table = "nguoidung";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy thông tin người dùng theo tên đăng nhập
    public function getUserByUsername($username) {
        $username = $this->conn->real_escape_string($username);
        $sql = "SELECT * FROM {$this->table} WHERE user_name = '$username' LIMIT 1";
        $result = $this->conn->query($sql);
        return $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }
     // 🧱 Hàm thêm người dùng mới
public function addUser($data) {
    $required = ['username', 'password', 'fullname', 'phone', 'email', 'role'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Vui lòng điền đầy đủ các trường thông tin bắt buộc");
        }
    }

    $user_name = trim($data['username']);
    $user_pass = $data['password'];
    $fullname = trim($data['fullname']);
    $phone = trim($data['phone']);
    $user_address = trim($data['address'] ?? '');
    $district = trim($data['district'] ?? '');
    $province = trim($data['province'] ?? '');
    $user_email = trim($data['email']);
    $user_role = trim($data['role']);
    $user_status = 'Hoạt động';

    // Kiểm tra username đã tồn tại
    $check_sql = "SELECT user_name FROM nguoidung WHERE user_name = ?";
    $check_stmt = $this->conn->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Lỗi prepare() kiểm tra username: " . $this->conn->error);
    }

    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        throw new Exception("Tên người dùng đã tồn tại");
    }
    $check_stmt->close();

    // Mã hóa mật khẩu
    $hashPass = password_hash($user_pass, PASSWORD_DEFAULT);

    // Thêm người dùng mới
    $sql = "INSERT INTO nguoidung 
        (user_name, hashPass, fullname, phone, user_address, district, province, user_email, user_role, user_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi prepare() khi thêm user: " . $this->conn->error);
    }

    $stmt->bind_param(
        "ssssssssss",
        $user_name,
        $hashPass,
        $fullname,
        $phone,
        $user_address,
        $district,
        $province,
        $user_email,
        $user_role,
        $user_status
    );

    if (!$stmt->execute()) {
        throw new Exception("Lỗi khi thêm người dùng: " . $stmt->error);
    }

    $stmt->close();
    return true;
}

}
?>
