<?php
header('Content-Type: application/json');
require '../config/connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Không có product ID"]);
    exit;
}

$product_id = $_GET['id'];

$query = $conn->prepare("SELECT * FROM sanpham WHERE product_id = ?");
$query->bind_param("s", $product_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Không tìm thấy sản phẩm"]);
}

$conn->close();
?>
