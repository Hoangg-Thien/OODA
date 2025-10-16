<?php
header('Content-Type: application/json');
require '../config/connect.php';

$product_id = $_POST['product_id'] ?? null;
$status = $_POST['status'] ?? null;

if ($product_id === null || $status === null) {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin sản phẩm"]);
    exit;
}

// Cho phép debug
error_log("Status received: [" . $status . "]");

// Kiểm tra xem sản phẩm có đang ẩn không
if (trim($status) === 'Ẩn') {
    // Nếu sản phẩm đang ẩn, cập nhật hidden = 1
    error_log("Setting hidden=1 for product: " . $product_id);
    $stmt = $conn->prepare("UPDATE sanpham SET hidden = 1 WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
} else {
    // Xóa sản phẩm nếu không phải trạng thái ẩn
    error_log("Deleting product: " . $product_id);
    $stmt = $conn->prepare("DELETE FROM sanpham WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Không thể xử lý sản phẩm: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
