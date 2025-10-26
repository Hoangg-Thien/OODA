<?php
session_start();
require_once __DIR__ . '/../../classes/Database.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/OODA/');
}

// 🔒 Kiểm tra đăng nhập
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem thông tin tài khoản!'); window.location.href='login.php';</script>";
    exit();
}

$user_name = $_SESSION['user_name'];
$db = new Database();
$conn = $db->connect();

// 🔍 Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM nguoidung WHERE user_name = ?");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<script>alert('Không tìm thấy người dùng!'); window.location.href='index.php';</script>";
    exit();
}

// 💾 Cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['user_email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['user_address']);
    $district = trim($_POST['district']);
    $province = trim($_POST['province']);

    $update = $conn->prepare("UPDATE nguoidung SET fullname=?, user_email=?, phone=?, user_address=?, district=?, province=? WHERE user_name=?");
    $update->bind_param("sssssss", $fullname, $email, $phone, $address, $district, $province, $user_name);

    if ($update->execute()) {
        echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='user-info.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật thông tin!');</script>";
    }
}

include __DIR__ . '/../layout/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>user/asset/styles/userinfo.css">

<div class="profile-container">
    <h1>👤 Thông tin tài khoản</h1>

    <form method="POST" class="profile-form">
        <div class="form-group">
            <label>Tên đăng nhập:</label>
            <input type="text" value="<?= htmlspecialchars($user['user_name']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Họ và tên:</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại:</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>

        <div class="form-group">
            <label>Địa chỉ:</label>
            <input type="text" name="user_address" value="<?= htmlspecialchars($user['user_address']) ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label>Quận / Huyện:</label>
                <input type="text" name="district" value="<?= htmlspecialchars($user['district']) ?>" required>
            </div>
            <div class="form-group half">
                <label>Tỉnh / Thành phố:</label>
                <input type="text" name="province" value="<?= htmlspecialchars($user['province']) ?>" required>
            </div>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-save">💾 Lưu thay đổi</button>
            <a href="<?= BASE_URL ?>index.php" class="btn-cancel">⬅️ Quay lại</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
