<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

session_start();

if (isset($_POST['submit'])) {
    $username = trim($_POST['user_name']);
    $password = trim($_POST['hashPass']);

    $stmt = $conn->prepare("SELECT fullname, user_name, hashPass, user_role, user_status 
                            FROM nguoidung 
                            WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($fullname, $db_username, $db_password, $role, $status);
        $stmt->fetch();

        if ($status !== 'Hoạt động') {
            echo "<script>
                    alert('Tài khoản của bạn đã bị khóa!');
                    window.location.href='login.php';
                  </script>";
            exit();
        }

        if (password_verify($password, $db_password)) {
            $_SESSION['user_name'] = $db_username;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['user_role'] = $role;

            echo "<script>
                    alert('🎉 Đăng nhập thành công!');
                    window.location.href='/OODA/index.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('❌ Sai mật khẩu!'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('⚠️ Tên đăng nhập không tồn tại!'); window.location.href='login.php';</script>";
        exit();
    }

    $stmt->close();
}
$conn->close();
?>
