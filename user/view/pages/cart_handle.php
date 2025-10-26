<?php
session_start();
require_once __DIR__ . '/../../classes/Database.php';

// ✅ Kết nối Database
$db = new Database();
$conn = $db->connect();

header('Content-Type: application/json');

// ✅ Đọc dữ liệu JSON từ Fetch API
$data = json_decode(file_get_contents("php://input"), true);

// ✅ Kiểm tra dữ liệu đầu vào
if (!isset($data['action']) || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu yêu cầu']);
    exit;
}

$product_id = $data['id']; // 🟢 đồng bộ với cart.php
$action = $data['action'];

// ✅ Nếu chưa có giỏ hàng thì khởi tạo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    // 🛒 Thêm sản phẩm vào giỏ
    case 'add':
        $stmt = $conn->prepare("SELECT * FROM sanpham WHERE product_id = ?");
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            // ✅ Đường dẫn ảnh sản phẩm
            $imageName = $product['product_image'] ?? '';
            $serverPath = $_SERVER['DOCUMENT_ROOT'] . "/user/asset/img/" . $imageName;
            $webPath = "/user/asset/img/" . $imageName;

            $imagePath = file_exists($serverPath)
                ? $webPath
                : '/user/asset/img/no-image.jpg';

            // ✅ Nếu sản phẩm đã có thì tăng số lượng
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']++;
            } else {
                // ✅ Nếu chưa có thì thêm mới
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['product_name'],
                    'price' => $product['product_price'],
                    'image' => $imagePath,
                    'quantity' => 1,
                    'product_link' => $product['productnolog_link'] ?? '#'
                ];
            }

            echo json_encode([
                'success' => true,
                'cart_count' => count($_SESSION['cart']),
                'product' => [
                    'name' => $product['product_name'],
                    'image' => $imagePath
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        }
        break;

    // ➕ Tăng số lượng
    case 'increase':
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        }
        echo json_encode(['success' => true]);
        break;

    // ➖ Giảm số lượng
    case 'decrease':
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']--;
            if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        echo json_encode(['success' => true]);
        break;

    // ❌ Xóa sản phẩm
    case 'remove':
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
        break;
}

exit;
