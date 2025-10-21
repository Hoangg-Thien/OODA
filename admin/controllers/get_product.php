<?php
header('Content-Type: application/json');
require '../classes/Database.php';
require '../classes/Product.php';

  // ✅ Tạo đối tượng Database và lấy kết nối
    $db = new Database();
    $conn = $db->getConnection();
    
try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo json_encode(["error" => "Không có product ID"]);
        exit;
    }

    $product = new Product($conn);
    $data = $product->getProductById($_GET['id']);
    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
