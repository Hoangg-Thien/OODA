<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connect.php';

if (isset($_POST['submit-1'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['user_name']);
    $email = trim($_POST['user_email']);
    $password = $_POST['hashPass'];
    $confirmPassword = $_POST['confirmPassword'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['user_address']);
    $province_id = trim($_POST['province']);  // giữ nguyên ID
    $district_id = trim($_POST['district']);  // giữ nguyên ID
    $user_role = "Khách hàng";
    $user_status = "Hoạt động";

    // Lấy tên tỉnh/thành phố từ ID (nếu cần sử dụng để hiển thị)
    $province_name = '';
    $province_query = $conn->prepare("SELECT name FROM province WHERE province_id = ?");
    $province_query->bind_param("i", $province_id);
    $province_query->execute();
    $province_query->bind_result($province_name);
    $province_query->fetch();
    $province_query->close();

    // Lấy tên quận/huyện từ ID (nếu cần sử dụng để hiển thị)
    $district_name = '';
    $district_query = $conn->prepare("SELECT name FROM district WHERE district_id = ?");
    $district_query->bind_param("i", $district_id);
    $district_query->execute();
    $district_query->bind_result($district_name);
    $district_query->fetch();
    $district_query->close();

    // Kiểm tra tên đăng nhập hoặc email đã tồn tại
    $stmt = $conn->prepare("SELECT user_name FROM nguoidung WHERE user_name = ? OR user_email = ?");
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Tên đăng nhập hoặc email đã tồn tại.'); window.location.href='regis.php';</script>";
        $stmt->close();
        exit();
    }
    $stmt->close();
    
    if ($password !== $confirmPassword) {
    echo "<script>alert('Mật khẩu và xác nhận mật khẩu không khớp.'); window.location.href='regis.php';</script>";
    exit();
}
    // Mã hóa mật khẩu
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Chèn dữ liệu vào bảng nguoidung với ID (không phải tên)
    $stmt = $conn->prepare("INSERT INTO nguoidung (fullname, user_name, user_email, hashPass, phone, user_address, user_role, user_status, province, district) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("ssssssssss", $fullname, $username, $email, $hashed_password, $phone, $address, $user_role, $user_status, $province_name, $district_name);

    if ($stmt->execute()) {
        echo "<script>
                alert('Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...');
                window.location.href='login-user.php';
              </script>";
    } else {
        error_log("Lỗi đăng ký: " . $stmt->error);
        echo "<script>alert('Lỗi khi đăng ký. Vui lòng thử lại!'); window.location.href='regis.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
