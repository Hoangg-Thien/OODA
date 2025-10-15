<?php
require 'connect.php';

// Kiểm tra dữ liệu nhận được
if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['fullname']) || 
   empty($_POST['phone']) || empty($_POST['email']) || empty($_POST['role'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Vui lòng điền đầy đủ các trường thông tin bắt buộc"
    ]);
    exit;
}

$user_name = $_POST['username'];
$user_pass = $_POST['password'];
$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$user_address = $_POST['address'];
$district = $_POST['district'];
$province = $_POST['province'];
$user_email = $_POST['email'];
$user_role = $_POST['role'];

// Mã hóa mật khẩu
$hashPass = password_hash($user_pass, PASSWORD_DEFAULT);

// Kiểm tra username đã tồn tại chưa
$check_sql = "SELECT * FROM nguoidung WHERE user_name = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $user_name);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "status" => "error", 
        "message" => "Tên người dùng đã tồn tại"
    ]);
} else {
    // Thêm người dùng mới với trạng thái mặc định là Hoạt động
    $sql = "INSERT INTO nguoidung (user_name, hashPass, fullname, phone, user_address, district, province, user_email, user_role, user_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Hoạt động')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $user_name, $hashPass, $fullname, $phone, $user_address, $district, $province, $user_email, $user_role);
    
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success", 
            "message" => "Thêm người dùng thành công"
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Lỗi: " . $stmt->error
        ]);
    }
    $stmt->close();
}

$check_stmt->close();
$conn->close();
?> 