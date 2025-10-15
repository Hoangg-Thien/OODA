<?php
require 'connect.php';

$user_name = $_POST['username'];

$sql = "SELECT * FROM nguoidung WHERE user_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    echo json_encode($user_data);
} else {
    echo json_encode(["status" => "error", "message" => "Không tìm thấy thông tin người dùng"]);
}

$stmt->close();
$conn->close();
?> 