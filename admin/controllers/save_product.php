<?php
require_once '../classes/Database.php';
require_once '../classes/Product.php'; // giả sử bạn đặt class trong thư mục "classes"

  // ✅ Tạo đối tượng Database và lấy kết nối
    $db = new Database();
    $conn = $db->getConnection();
try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $product = new Product($conn);
        $product->addProduct($_POST, $_FILES['product-image']);
        header("Location: ../pages/addpro.php?success");
        exit();
    }
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
