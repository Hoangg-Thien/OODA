<?php
require '../config/connect.php';

$user_name = $_POST['username'];
$action = $_POST['action'];

$status = ($action === "lock") ? "Đã khóa" : "Hoạt động";

$sql = "UPDATE nguoidung SET user_status = ? WHERE user_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $status, $user_name);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success", 
        "message" => "Đã cập nhật trạng thái người dùng thành " . $status
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Lỗi: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?> 