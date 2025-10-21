<?php
require '../classes/Database.php'; // 🔹 Gọi class Database

class UserController {
    private $conn;

    // 🔹 Hàm khởi tạo nhận kết nối
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // 🔹 Hàm cập nhật trạng thái người dùng
    public function updateUserStatus($username, $action) {
        if (empty($username) || empty($action)) {
            return ["status" => "error", "message" => "Thiếu dữ liệu cần thiết"];
        }

        // Xác định trạng thái theo action
        $status = ($action === "lock") ? "Đã khóa" : "Hoạt động";

        $sql = "UPDATE nguoidung SET user_status = ? WHERE user_name = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi chuẩn bị truy vấn: " . $this->conn->error];
        }

        $stmt->bind_param("ss", $status, $username);

        if ($stmt->execute()) {
            $stmt->close();
            return [
                "status" => "success",
                "message" => "Đã cập nhật trạng thái người dùng thành " . $status
            ];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return [
                "status" => "error",
                "message" => "Lỗi khi cập nhật: " . $error
            ];
        }
    }
}

// 🔹 Khi có request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Khởi tạo kết nối Database
    $db = new Database();
    $conn = $db->getConnection();

    // Nhận dữ liệu từ POST
    $username = $_POST['username'] ?? '';
    $action   = $_POST['action'] ?? '';

    // Gọi class xử lý
    $userController = new UserController($conn);
    $response = $userController->updateUserStatus($username, $action);

    // Trả về JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    $conn->close();
}
?>
