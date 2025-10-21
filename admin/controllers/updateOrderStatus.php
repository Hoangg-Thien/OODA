<?php
require '../classes/Database.php';
$db = new Database();
$conn = $db->getConnection();
header('Content-Type: application/json');

class OrderController {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // 🧩 Hàm chính xử lý yêu cầu POST
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(false, 'Phương thức không được hỗ trợ');
        }

        $order_id = $_POST['order_id'] ?? '';
        $status = $_POST['status'] ?? '';

        if (empty($order_id) || empty($status)) {
            return $this->response(false, 'Thiếu thông tin cần thiết');
        }

        // Kiểm tra đơn hàng tồn tại
        $orderData = $this->getOrderById($order_id);
        if (!$orderData) {
            return $this->response(false, 'Không tìm thấy đơn hàng cần cập nhật');
        }

        $current_status = $orderData['order_status'];
        $status_order = [
            'Chưa xác nhận' => 1,   
            'Đã xác nhận' => 2,   
            'Giao thành công' => 3,   
            'Đã hủy' => 4    
        ];

        // Ràng buộc logic trạng thái
        if ($current_status === 'Giao thành công' && $status === 'Đã hủy') {
            return $this->response(false, 'Đơn hàng đã giao thành công không thể hủy');
        }

        if (isset($status_order[$current_status]) && isset($status_order[$status])) {
            if ($status_order[$status] < $status_order[$current_status] && $status !== 'Đã hủy') {
                return $this->response(false, 'Không thể cập nhật trạng thái đơn hàng ngược lại trạng thái trước đó');
            }
        } else {
            return $this->response(false, 'Trạng thái không hợp lệ: Current=' . $current_status . ', New=' . $status);
        }

        // Cập nhật trạng thái
        if ($this->updateOrderStatus($order_id, $status)) {
            return $this->response(true, 'Cập nhật trạng thái đơn hàng thành công');
        } else {
            return $this->response(false, 'Lỗi khi cập nhật: ' . mysqli_error($this->conn));
        }
    }

    // 🔍 Hàm lấy thông tin đơn hàng
    private function getOrderById($order_id) {
        $order_id = mysqli_real_escape_string($this->conn, $order_id);
        $sql = "SELECT * FROM hoadon WHERE order_id = '$order_id'";
        $result = mysqli_query($this->conn, $sql);
        return ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result) : null;
    }

    // 💾 Hàm cập nhật trạng thái
    private function updateOrderStatus($order_id, $status) {
        $order_id = mysqli_real_escape_string($this->conn, $order_id);
        $status = mysqli_real_escape_string($this->conn, $status);
        $sql = "UPDATE hoadon SET order_status = '$status' WHERE order_id = '$order_id'";
        return mysqli_query($this->conn, $sql);
    }

    // 📦 Hàm phản hồi JSON
    private function response($success, $message) {
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }
}

// 🏁 Chạy chương trình
$controller = new OrderController($conn);
$controller->handleRequest();
