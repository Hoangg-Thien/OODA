<?php
include("connect.php");

if (isset($_GET['term'])) {
    $term = $_GET['term'] . "%"; // 👈 chỉ bắt đầu bằng ký tự nhập vào
    $sql = "SELECT product_name FROM sanpham WHERE product_name LIKE ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['product_name'];
    }
    
    echo json_encode($suggestions);
}
?>