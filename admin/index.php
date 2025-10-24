<?php
require './classes/Database.php';
require './classes/User.php';
require './classes/Auth.php';

// Khởi tạo kết nối và các lớp
$db = new Database();
$userModel = new User($db->getConnection());
$auth = new Auth($userModel);

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    if ($auth->login($username, $password)) {
        header("Location: index.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

// Lấy thông báo lỗi hoặc thành công
$showAlert = $auth->hasLoginSuccess();
$error_message = $auth->getError();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        /* --- CSS giữ nguyên --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-image: url('./img/background.jpg'); background-size: cover; background-position: center; }
        .login-container { display: flex; width: 800px; height: 400px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .left-panel { width: 40%; background-color: #4e7de1; color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px; }
        .right-panel { width: 60%; background-color: white; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .right-panel h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .input-group { margin-bottom: 20px; position: relative; }
        .input-group input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background-color: #f8f8f8; }
        .login-btn { background-color: #4e7de1; color: white; border: none; width: 100%; padding: 12px; border-radius: 5px; cursor: pointer; font-size: 16px; transition: all 0.3s ease; }
        .login-btn:hover { background-color: #3d6ad0; }
    </style>

    <script>
        const loginSuccess = <?= json_encode($showAlert) ?>;
        if (loginSuccess) {
            alert("Đăng nhập thành công");
            window.location.replace("./pages/usermanage.php");
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="left-panel">
            <h2>Welcome!</h2>
        </div>
        <div class="right-panel">
            <h2>Đăng nhập</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <input id="user" name="user" type="text" placeholder="Tên đăng nhập" required>
                </div>
                <div class="input-group">
                    <input id="pass" name="pass" type="password" placeholder="Mật khẩu" required>
                </div>

                <?php if ($error_message): ?>
                    <div style="color: red; text-align: center; margin-bottom: 10px;">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <button type="submit" name="login" class="login-btn">Đăng nhập</button>
            </form>
        </div>
    </div>
</body>
</html>
