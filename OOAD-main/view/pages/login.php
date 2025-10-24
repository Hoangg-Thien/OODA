<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #d7f7d4, #b6e8b0);
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .login_container {
            max-width: 420px;
            margin: 80px auto;
            background: #fff;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .login_title span {
            display: block;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 25px;
        }

        .input_wrapper {
            position: relative;
            margin-bottom: 22px;
        }

        .label-left {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        .input_field {
            width: 100%;
            padding: 12px 40px 12px 14px;
            font-size: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            outline: none;
            transition: 0.3s;
        }

        .input_field:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76,175,80,0.2);
        }

        .icon-right {
            position: absolute;
            top: 38px;
            right: 14px;
            color: #888;
            cursor: pointer;
        }

        .input-submit {
            width: 100%;
            padding: 14px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .input-submit:hover {
            background: #43a047;
            transform: translateY(-2px);
        }

        .signup {
            text-align: center;
            margin-top: 20px;
            color: #555;
            font-size: 14px;
        }

        .signup a {
            color: #4CAF50;
            font-weight: 600;
            text-decoration: none;
        }

        .signup a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login_container">
    <div class="login_title">
        <span>Đăng Nhập</span>
    </div>

    <form action="checkLogin.php" method="POST">
        <div class="input_wrapper">
            <label for="user" class="label-left">Tên đăng nhập</label>
            <input type="text" name="user_name" id="user" class="input_field" placeholder="Nhập tên tài khoản..." required>
            <i class="fa-regular fa-user icon-right"></i>
        </div>

        <div class="input_wrapper">
            <label for="password" class="label-left">Mật khẩu</label>
            <input type="password" name="hashPass" id="password" class="input_field" placeholder="Nhập mật khẩu..." required>
            <i class="fa-solid fa-lock icon-right" style="right: 45px;"></i>
            <i class="fa-solid fa-eye-slash icon-right toggle-password" id="togglePassword"></i>
        </div>

        <div class="input_wrapper">
            <button type="submit" name="submit" class="input-submit">Đăng Nhập</button>
        </div>
    </form>

    <div class="signup">
        Bạn chưa có tài khoản? <a href="./regis.php" target="_blank">Đăng ký ngay</a>
    </div>
</div>

<script>
const togglePassword = document.getElementById('togglePassword');
const passwordField = document.getElementById('password');

togglePassword.addEventListener('click', () => {
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    togglePassword.classList.toggle('fa-eye');
    togglePassword.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>
