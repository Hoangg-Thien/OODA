<?php
header('Content-Type: application/json');
require '../classes/Database.php';
require '../classes/Product.php';


// Nhận dữ liệu từ request
$product_id = $_POST['product_id'] ?? null;
$status = $_POST['status'] ?? null;

// ✅ Tạo đối tượng Database và lấy kết nối
$db = new Database();
$conn = $db->getConnection();

// Khởi tạo class và xử lý
$product = new Product($conn);
$product->setData($product_id, $status);

echo $product->process();

$conn->close();
?>
