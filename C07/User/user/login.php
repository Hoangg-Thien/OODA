<?php
session_start();
include 'connect.php';


if(isset($_POST['submit'])) {
    $username = trim($_POST['user_name']); 
    $password = $_POST['hashPass']; 

    $stmt = $conn->prepare("SELECT fullname, user_name, hashPass, user_email, user_status FROM nguoidung WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (trim($row['user_status']) == 'Đã khóa') {
            echo "<script>alert('Tài khoản của bạn đã bị khóa!'); window.location.href='login-user.php';</script>";
            exit();
        }
        
        if(password_verify($password, $row['hashPass'])) { 
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['user_email'] = $row['user_email']; 

            echo "<script>alert('Đăng nhập thành công!'); window.location.href='./userlogin.php';</script>";
            exit();
        } else {
            echo "<script>alert('Sai mật khẩu!'); window.location.href='login-user.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Tài khoản không tồn tại!'); window.location.href='login-user.php';</script>";
        exit();
    }
}
?>