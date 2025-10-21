<?php
require '../classes/Database.php'; // 🔹 Sử dụng class Database thay cho connect.php

class UserController {
    private $conn;

    // 🔹 Hàm khởi tạo, nhận kết nối từ bên ngoài
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // 🔹 Hàm cập nhật thông tin người dùng
    public function updateUser($data) {
        // Kiểm tra xem username có tồn tại không
        if (empty($data['username'])) {
            return ["status" => "error", "message" => "Thiếu tên người dùng"];
        }

        $sql = "UPDATE nguoidung SET 
                    fullname = ?, 
                    user_address = ?, 
                    user_email = ?, 
                    phone = ?, 
                    user_role = ?, 
                    user_status = ?,
                    district = ?,
                    province = ?
                WHERE user_name = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi chuẩn bị truy vấn: " . $this->conn->error];
        }

        $stmt->bind_param(
            "sssssssss",
            $data['fullname'],
            $data['address'],
            $data['email'],
            $data['phone'],
            $data['role'],
            $data['status'],
            $data['district'],
            $data['province'],
            $data['username']
        );

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Cập nhật thành công"];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ["status" => "error", "message" => "Lỗi: " . $error];
        }
    }
}

// 🔹 Khi có request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Khởi tạo kết nối CSDL
    $db = new Database();
    $conn = $db->getConnection();

    // Lấy dữ liệu từ form (hoặc fetch)
    $data = [
        'username' => $_POST['username'] ?? '',
        'fullname' => $_POST['fullname'] ?? '',
        'address'  => $_POST['address'] ?? '',
        'email'    => $_POST['email'] ?? '',
        'phone'    => $_POST['phone'] ?? '',
        'role'     => $_POST['role'] ?? '',
        'status'   => $_POST['status'] ?? '',
        'district' => $_POST['district'] ?? '',
        'province' => $_POST['province'] ?? ''
    ];

    // Gọi class xử lý
    $userController = new UserController($conn);
    $response = $userController->updateUser($data);

    // Trả về JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    $conn->close();
}
?>
