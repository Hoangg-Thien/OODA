<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require '../classes/Database.php';
require '../classes/Product.php';

  // ✅ Tạo đối tượng Database và lấy kết nối
    $db = new Database();
    $conn = $db->getConnection();
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product = new Product($conn);
        $product->updateProduct($_POST, $_FILES);
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>
