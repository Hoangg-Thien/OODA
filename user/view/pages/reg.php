<?php
// ⚡ Bật báo lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ⚡ Kết nối CSDL
require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("❌ Kết nối CSDL thất bại.");
}

if (isset($_POST['submit-1'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['user_name']);
    $email = trim($_POST['user_email']);
    $password = $_POST['hashPass'];
    $confirmPassword = $_POST['confirmPassword'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['user_address']);
    $province_id = $_POST['province'];
    $district_id = $_POST['district'];
    $user_role = "Khách hàng";
    $user_status = "Hoạt động";

    // ✅ Lấy tên tỉnh/thành
    $province_stmt = $conn->prepare("SELECT name FROM province WHERE province_id = ?");
    $province_stmt->bind_param("i", $province_id);
    $province_stmt->execute();
    $province_stmt->bind_result($province_name);
    $province_stmt->fetch();
    $province_stmt->close();

    // ✅ Lấy tên quận/huyện
    $district_stmt = $conn->prepare("SELECT name FROM district WHERE district_id = ?");
    $district_stmt->bind_param("i", $district_id);
    $district_stmt->execute();
    $district_stmt->bind_result($district_name);
    $district_stmt->fetch();
    $district_stmt->close();

    // ✅ Kiểm tra trùng username/email
    $check_stmt = $conn->prepare("SELECT user_name FROM nguoidung WHERE user_name = ? OR user_email = ?");
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Tên đăng nhập hoặc email đã tồn tại!'); window.location.href='regis.php';</script>";
        exit;
    }
    $check_stmt->close();

    // ✅ Kiểm tra mật khẩu
    if ($password !== $confirmPassword) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!'); window.location.href='regis.php';</script>";
        exit;
    }

    // ✅ Hash mật khẩu
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // ✅ Thêm người dùng
    $insert_stmt = $conn->prepare("
        INSERT INTO nguoidung (fullname, user_name, user_email, hashPass, phone, user_address, user_role, user_status, province, district)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insert_stmt->bind_param("ssssssssss", 
        $fullname, $username, $email, $hashed_password, 
        $phone, $address, $user_role, $user_status, 
        $province_name, $district_name
    );

    if ($insert_stmt->execute()) {
        echo "<script>
            alert('🎉 Đăng ký thành công! Chuyển đến trang đăng nhập...');
            window.location.href = './login.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('Đăng ký thất bại: " . addslashes($insert_stmt->error) . "');</script>";
    }

    $insert_stmt->close();
    $conn->close();
}
?>
