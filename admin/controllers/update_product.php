<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require '../config/connect.php';

// Input từ form
$product_id = $_POST['product_id'] ?? '';
$product_name = $_POST['product_name'] ?? '';
$product_price = $_POST['product_price'] ?? 0;
$product_status = $_POST['product_status'] ?? '';
$product_type_name = $_POST['product_type'] ?? '';
$delete_image = isset($_POST['delete_image']) && $_POST['delete_image'] === 'true';

// Map tên loại -> category_id
$mapped_category_id = null;
if ($product_type_name !== '') {
    $mapStmt = $conn->prepare("SELECT category_id FROM loaisanpham WHERE name_type = ? LIMIT 1");
    $mapStmt->bind_param("s", $product_type_name);
    $mapStmt->execute();
    $mapRes = $mapStmt->get_result();
    if ($row = $mapRes->fetch_assoc()) {
        $mapped_category_id = (int)$row['category_id'];
    }
    $mapStmt->close();
}

// Lấy hidden hiện tại
$stmt = $conn->prepare("SELECT hidden, product_image FROM sanpham WHERE product_id=?");
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$current_hidden = $row['hidden'] ?? 1;
$current_image_name = $row['product_image'] ?? '';
$stmt->close();

// Quy tắc hidden: Hiển thị => 0, còn lại giữ nguyên
$hidden_value = ($product_status === 'Hiển thị') ? 0 : $current_hidden;

// Chuẩn hóa cập nhật ảnh
$new_image_name = '';

if ($delete_image) {
    // Xóa file ảnh nếu tồn tại
    if (!empty($current_image_name)) {
        $absolutePath = realpath(__DIR__ . '/../img');
        $pathToDelete = $absolutePath ? ($absolutePath . DIRECTORY_SEPARATOR . $current_image_name) : ('../img/' . $current_image_name);
        if (is_file($pathToDelete)) {
            @unlink($pathToDelete);
        }
    }

    // Cập nhật: xóa tên ảnh trong DB
    $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, hidden=?, product_image=''" . ($mapped_category_id !== null ? ", category_id=?" : "") . " WHERE product_id=?";
    if ($mapped_category_id !== null) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsiss", $product_name, $product_price, $product_status, $hidden_value, $mapped_category_id, $product_id);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssis", $product_name, $product_price, $product_status, $hidden_value, $product_id);
    }
}
elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
    // Upload ảnh mới: lưu chỉ tên file vào DB
    $imgName = time() . "_" . basename($_FILES['product_image']['name']);
    $targetDir = __DIR__ . '/../img/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $targetPath = $targetDir . $imgName;
    move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath);
    $new_image_name = $imgName;

    $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, hidden=?, product_image=?" . ($mapped_category_id !== null ? ", category_id=?" : "") . " WHERE product_id=?";
    if ($mapped_category_id !== null) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsssis", $product_name, $product_price, $product_status, $hidden_value, $new_image_name, $mapped_category_id, $product_id);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsssis", $product_name, $product_price, $product_status, $hidden_value, $new_image_name, $product_id);
    }
}
else {
    // Không đổi ảnh
    $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, hidden=?" . ($mapped_category_id !== null ? ", category_id=?" : "") . " WHERE product_id=?";
    if ($mapped_category_id !== null) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsiis", $product_name, $product_price, $product_status, $hidden_value, $mapped_category_id, $product_id);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsis", $product_name, $product_price, $product_status, $hidden_value, $product_id);
    }
}

if ($stmt && $stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    $err = $stmt ? $stmt->error : $conn->error;
    echo json_encode(["success" => false, "message" => "Lỗi SQL: " . $err]);
}

$conn->close();
?>