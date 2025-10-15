<?php
session_start();

// Thêm dòng này để sử dụng kết nối từ connect.php
include("connect.php");

// Đọc dữ liệu JSON từ request
$data = json_decode(file_get_contents("php://input"), true);

// Nếu không có dữ liệu từ fetch() (tức là đang mở trực tiếp từ trình duyệt), thì dùng dữ liệu giả để test
if (!is_array($data)) {
    $data = [
        'product_id' => 'F002',  // 🔁 THAY bằng product_id thật trong DB
        'action' => 'add'
    ];
}

$product_id = $data['product_id'];
$action = $data['action'];

// Tạo session nếu chưa có
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

switch ($action) {
    case 'add':
        // Sử dụng mysqli thay vì PDO
        $stmt = $conn->prepare("SELECT * FROM sanpham WHERE product_id = ?");
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $imageName = $product['product_image'] ?? '';
            $serverPath = $_SERVER['DOCUMENT_ROOT'] . "/web-final/User/img/" . $imageName;
            $webPath = "/web-final/User/img/" . $imageName;
   
            // Tiếp tục logic gán ảnh như trước
            if (file_exists($serverPath)) {
                $imagePath = $webPath;
            } else {
                $imagePath = '/web-final/User/img/no-image.jpg';
            }

            // Lưu vào session
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['product_name'],
                    'price' => $product['product_price'],
                    'image' => $imagePath,
                    'quantity' => 1,
                    'product_link' => $product['product_link']
                ];
            }

            // Đảm bảo header được đặt trước khi xuất dữ liệu
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cart_count' => count($_SESSION['cart']),
                'product' => [
                    'name' => $product['product_name'],
                    'image' => $imagePath
                ]
            ]);
        } else {
            // Trả về lỗi nếu không tìm thấy sản phẩm
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ]);
        }
        break;

    case 'increase':
        $_SESSION['cart'][$product_id]['quantity']++;
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        break;

    case 'decrease':
        if ($_SESSION['cart'][$product_id]['quantity'] > 1) {
            $_SESSION['cart'][$product_id]['quantity']--;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        break;

    case 'remove':
        unset($_SESSION['cart'][$product_id]);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        break;
}

exit;