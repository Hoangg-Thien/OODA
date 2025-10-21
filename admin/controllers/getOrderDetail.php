<?php
require '../classes/Database.php';
$db = new Database();
$conn = $db->getConnection();
header('Content-Type: application/json');

class OrderDetailController {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // 🧩 Hàm xử lý yêu cầu GET
    public function handleRequest() {
        if (!isset($_GET['order_id'])) {
            return $this->response(false, 'Không có mã đơn hàng được cung cấp');
        }

        $order_id = $_GET['order_id'];
        $order = $this->getOrderDetail($order_id);

        if ($order) {
            return $this->response(true, 'Lấy thông tin thành công', $order);
        } else {
            return $this->response(false, 'Không tìm thấy thông tin đơn hàng');
        }
    }

    // 📦 Hàm lấy thông tin đơn hàng (bao gồm sản phẩm)
    private function getOrderDetail($order_id) {
        $sql = "SELECT hd.*, nd.fullname, nd.user_name, nd.user_address, nd.district, nd.province
                FROM hoadon hd
                LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                WHERE hd.order_id = ?";

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "s", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result || mysqli_num_rows($result) === 0) {
            mysqli_stmt_close($stmt);
            return null;
        }

        $order = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        // 🔹 Lấy chi tiết sản phẩm
        $order['products'] = $this->getOrderProducts($order_id);
        $order['total_amount'] = $this->calculateTotal($order['products']);

        // 🔹 Bổ sung địa chỉ nếu thiếu
        if (empty($order['address']) && !empty($order['user_address'])) {
            $order['address'] = $order['user_address'];
        }

        return $order;
    }

    // 🛒 Hàm lấy danh sách sản phẩm trong đơn hàng
    private function getOrderProducts($order_id) {
        $sql = "SELECT cthd.*, sp.product_name, sp.product_price 
                FROM chitiethoadon cthd
                LEFT JOIN sanpham sp ON cthd.product_id = sp.product_id
                WHERE cthd.order_id = ?";

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, "s", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $products = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($detail = mysqli_fetch_assoc($result)) {
                $products[] = $detail;
            }
        }

        mysqli_stmt_close($stmt);
        return $products;
    }

    // 💰 Hàm tính tổng tiền
    private function calculateTotal($products) {
        $total = 0;
        foreach ($products as $item) {
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $total += $item['product_price'] * $quantity;
        }
        return $total;
    }

    // 🧾 Hàm phản hồi JSON
    private function response($success, $message, $data = null) {
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}

// 🏁 Khởi tạo controller và xử lý request
$controller = new OrderDetailController($conn);
$controller->handleRequest();
