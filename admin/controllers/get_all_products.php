<?php
header('Content-Type: application/json');
require '../config/connect.php';

// Lấy tất cả sản phẩm, bao gồm cả sản phẩm bị ẩn
$result = $conn->query("SELECT * FROM sanpham");

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>
