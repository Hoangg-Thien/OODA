<?php
header('Content-Type: application/json');
require '../config/connect.php';

// Lấy tất cả sản phẩm
$sql = "
    SELECT 
        sp.product_id,
        sp.product_name,
        sp.product_image,
        sp.product_status,
        sp.product_price,
        sp.hidden,
        sp.category_id,
        sp.supplier_id,
        lsp.name_type AS category_name,
        ncc.supplier_name AS supplier_name
    FROM sanpham sp
    LEFT JOIN loaisanpham lsp ON lsp.category_id = sp.category_id
    LEFT JOIN nhacungcap ncc ON ncc.supplier_id = sp.supplier_id
    ORDER BY sp.product_id
";

$result = $conn->query($sql);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>
