<?php
header('Content-Type: application/json');
require '../classes/Database.php';
require '../classes/Product.php';

// ✅ Tạo đối tượng Database và lấy kết nối
$db = new Database();
$conn = $db->getConnection();

// Tạo đối tượng Product
$product = new Product($conn);

// Lấy danh sách sản phẩm
$products = $product->getAll();

// Xuất JSON
echo json_encode($products, JSON_UNESCAPED_UNICODE);

$conn->close();
?>
