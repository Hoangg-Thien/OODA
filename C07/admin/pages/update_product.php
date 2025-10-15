<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');


require "connect.php";

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$product_status = $_POST['product_status'];
$product_type = $_POST['product_type'];
$delete_image = isset($_POST['delete_image']) && $_POST['delete_image'] === 'true';
$imagePath = "";

// Lấy giá trị hidden hiện tại của sản phẩm
$sql = "SELECT hidden FROM sanpham WHERE product_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$current_hidden = $row['hidden'] ?? 1;

// Chỉ đặt hidden = 0 khi trạng thái là "Hiển thị"
// Nếu trạng thái là "Ẩn", giữ nguyên giá trị hidden hiện tại
$hidden_value = ($product_status === 'Hiển thị') ? 0 : $current_hidden;
error_log("Setting hidden=$hidden_value for product $product_id with status: $product_status (current hidden: $current_hidden)");

// Nếu xóa ảnh
if ($delete_image) {
    // Lấy đường dẫn ảnh hiện tại từ cơ sở dữ liệu
    $sql = "SELECT product_image FROM sanpham WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $currentImagePath = $row['product_image'];

    // Xóa file ảnh khỏi máy chủ
    if (file_exists($currentImagePath)) {
        unlink($currentImagePath);
    }

    // Cập nhật cơ sở dữ liệu để xóa đường dẫn ảnh và cập nhật hidden
    $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, product_type=?, product_image='', hidden=? WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssss", $product_name, $product_price, $product_status, $product_type, $hidden_value, $product_id);
}
// Nếu có ảnh mới upload
else if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
    $imgName = time() . "_" . basename($_FILES['product_image']['name']);
    $targetDir = "../img/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $targetPath = $targetDir . $imgName;
    move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath);
    $imagePath = $targetPath;

    $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, product_type=?, product_image=?, hidden=? WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssss", $product_name, $product_price, $product_status, $product_type, $imagePath, $hidden_value, $product_id);
}
// Không đổi ảnh
else {
    $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, product_type=?, hidden=? WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssis", $product_name, $product_price, $product_status, $product_type, $hidden_value, $product_id);
}

// Thực thi và trả kết quả JSON
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi SQL: " . $stmt->error]);
}

$conn->close();
?>