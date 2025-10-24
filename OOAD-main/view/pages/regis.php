<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/');
}
require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();
$result = $conn->query("SELECT * FROM province");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #d7f7d4, #b6e8b0);
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 500px;
            margin: 60px auto;
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

        h2 {
            text-align: center;
            color: #2e7d32;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .input-group {
            margin-bottom: 18px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            color: #333;
        }

        .required-star {
            color: red;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76,175,80,0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #43a047;
            transform: translateY(-2px);
        }

        p {
            text-align: center;
            margin-top: 20px;
            color: #555;
            font-size: 14px;
        }

        p a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
        }

        p a:hover {
            text-decoration: underline;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 38px;
            color: #888;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Đăng Ký Tài Khoản</h2>

    <form id="registration-form" action="reg.php" method="POST">
        <div class="input-group">
            <label for="fullname">Họ và Tên <span class="required-star">*</span></label>
            <input type="text" id="fullname" name="fullname" placeholder="Nhập họ và tên..." required>
        </div>

        <div class="input-group">
            <label for="user_name">Tên Đăng Nhập <span class="required-star">*</span></label>
            <input type="text" id="user_name" name="user_name" placeholder="Nhập tên đăng nhập..." required>
        </div>

        <div class="input-group">
            <label for="user_email">Email <span class="required-star">*</span></label>
            <input type="email" id="user_email" name="user_email" placeholder="Nhập email của bạn..." required>
        </div>

        <div class="input-group password-wrapper">
            <label for="hashPass">Mật Khẩu <span class="required-star">*</span></label>
            <input type="password" id="hashPass" name="hashPass" placeholder="Nhập mật khẩu..." required>
            <i class="fa-solid fa-eye-slash toggle-password" id="togglePass1"></i>
        </div>

        <div class="input-group password-wrapper">
            <label for="confirmPassword">Xác Nhận Mật Khẩu <span class="required-star">*</span></label>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Nhập lại mật khẩu..." required>
            <i class="fa-solid fa-eye-slash toggle-password" id="togglePass2"></i>
        </div>

        <div class="input-group">
            <label for="phone">Số Điện Thoại <span class="required-star">*</span></label>
            <input type="tel" id="phone" name="phone" placeholder="Nhập số điện thoại..." required>
        </div>

        <div class="input-group">
            <label for="user_address">Địa Chỉ <span class="required-star">*</span></label>
            <input type="text" id="user_address" name="user_address" placeholder="Nhập địa chỉ của bạn..." required>
        </div>

        <div class="input-group">
            <label for="province">Tỉnh/Thành phố <span class="required-star">*</span></label>
            <select id="province" name="province" required>
                <option value="">Chọn một tỉnh</option>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <option value="<?= $row['province_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="input-group">
            <label for="district">Quận/Huyện <span class="required-star">*</span></label>
            <select id="district" name="district" required>
                <option value="">Chọn một quận/huyện</option>
            </select>
        </div>

        <button type="submit" name="submit-1">Đăng Ký</button>

        <p>Bạn đã có tài khoản? <a href="./login.php" target="_blank">Đăng nhập ngay</a></p>
    </form>
</div>

<script>
// Toggle hiển/ẩn mật khẩu
function setupToggle(id, inputId) {
    const toggle = document.getElementById(id);
    const input = document.getElementById(inputId);
    toggle.addEventListener('click', () => {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        toggle.classList.toggle('fa-eye');
        toggle.classList.toggle('fa-eye-slash');
    });
}
setupToggle('togglePass1', 'hashPass');
setupToggle('togglePass2', 'confirmPassword');

// Load quận/huyện khi chọn tỉnh
document.getElementById("province").addEventListener("change", function() {
    var provinceId = this.value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_district.php?province_id=" + provinceId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("district").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
});
</script>

</body>
</html>
