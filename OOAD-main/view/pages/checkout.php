<?php
session_start();
require_once __DIR__ . '/../../classes/Database.php';

// ⚙️ BASE URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/');
}

// 🔒 KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location.href='login.php';</script>";
    exit();
}

// 🧩 KẾT NỐI DATABASE
$db = new Database();
$conn = $db->connect();

// 🧑‍💼 LẤY THÔNG TIN NGƯỜI DÙNG
$user_name = $_SESSION['user_name'];
$stmt = $conn->prepare("SELECT fullname, user_email, phone, user_address, district, province FROM nguoidung WHERE user_name = ?");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<script>alert('Không tìm thấy thông tin người dùng.'); window.location.href='login.php';</script>";
    exit();
}

// 🛒 LẤY GIỎ HÀNG
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<script>alert('Giỏ hàng của bạn đang trống!'); window.location.href='cart.php';</script>";
    exit();
}

// 🧾 XỬ LÝ THANH TOÁN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $district = trim($_POST['district']);
    $province = trim($_POST['province']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $paymentMethod = ($_POST['payment_method'] === 'bank_transfer') ? "Chuyển khoản" : "Thanh toán khi nhận hàng";

    // TÍNH TỔNG TIỀN
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $orderStatus = "Chưa xác nhận";
    $orderDate = date("Y-m-d H:i:s");

    // ✅ THÊM ĐƠN HÀNG VÀO BẢNG orders
    $sql = "INSERT INTO orders (order_status, order_date, user_name, district, province, PaymentMethod, phone, address, customerName)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $orderStatus, $orderDate, $user_name, $district, $province, $paymentMethod, $phone, $address, $name);
    $stmt->execute();

    $order_id = $conn->insert_id; // 🆔 Lấy mã đơn hàng vừa tạo

    // ✅ THÊM CHI TIẾT SẢN PHẨM VÀO BẢNG order_details
    $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);

    foreach ($cart as $pid => $item) {
        $quantity = $item['quantity'];
        $price = $item['price'];
        $subtotal = $price * $quantity;
        $stmt_detail->bind_param("iiidd", $order_id, $pid, $quantity, $price, $subtotal);
        $stmt_detail->execute();
    }

    // ✅ XÓA GIỎ HÀNG SAU KHI ĐẶT
    unset($_SESSION['cart']);

    // ✅ CHUYỂN HƯỚNG SANG TRANG HÓA ĐƠN
    echo "<script>
        alert('Đặt hàng thành công! Mã đơn hàng: #$order_id');
        window.location.href = 'invoice.php?id=$order_id';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thanh toán</title>
  <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="<?= BASE_URL ?>asset/styles/payment.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container">
  <h1>THANH TOÁN</h1>

  <form method="POST">
    <div class="top-links">
      <a href="<?= BASE_URL ?>index.php" class="btn-home">🏠 Trang chủ</a>
      <a href="<?= BASE_URL ?>view/pages/cart-user.php" class="btn-cart">🛒 Giỏ hàng</a>
    </div>

    <!-- 1️⃣ THÔNG TIN NGƯỜI NHẬN -->
    <div class="section">
      <h3>Thông tin người nhận</h3>
      <label><input type="checkbox" id="useDefaultInfo" onclick="fillDefaultInfo()"> Sử dụng thông tin mặc định</label>

      <div class="input-group"><label>Họ và tên</label><input type="text" id="name" name="name" required></div>
      <div class="input-group"><label>Địa chỉ</label><input type="text" id="address" name="address" required></div>
      <div class="input-group"><label>Quận/Huyện</label><input type="text" id="district" name="district" required></div>
      <div class="input-group"><label>Thành phố</label><input type="text" id="province" name="province" required></div>
      <div class="input-group"><label>Số điện thoại</label><input type="text" id="phone" name="phone" required></div>
      <div class="input-group"><label>Email</label><input type="email" id="email" name="email" required></div>
    </div>

    <hr>

    <!-- 2️⃣ PHƯƠNG THỨC THANH TOÁN -->
    <div class="section">
      <h3>Chọn phương thức thanh toán</h3>

      <label class="payment-option">
        <input type="radio" name="payment_method" value="bank_transfer" onchange="togglePaymentMethod()" checked>
        Chuyển khoản ngân hàng
      </label>

      <div class="bank-transfer-info">
        <div class="payment-flex">
          <div class="qr-code"><img src="../img/qr-code.jpg" alt="QR chuyển khoản"></div>
          <div class="bank-info">
            <h4>Thông tin tài khoản</h4>
            <table>
              <tr><td>Ngân hàng:</td><td><strong>MB Bank</strong></td></tr>
              <tr><td>Số tài khoản:</td><td><strong>002471993</strong></td></tr>
              <tr><td>Chủ tài khoản:</td><td><strong>Ngô Nguyễn Thành Nhân</strong></td></tr>
              <tr><td>Chi nhánh:</td><td>Hồ Chí Minh</td></tr>
            </table>
          </div>
        </div>
      </div>

      <label class="payment-option">
        <input type="radio" name="payment_method" value="cod" onchange="togglePaymentMethod()">
        Thanh toán khi nhận hàng
      </label>

      <div class="cod-info" style="display:none;">
        <p><b>Lưu ý:</b> Vui lòng chuẩn bị sẵn tiền và kiểm tra kỹ hàng trước khi thanh toán.</p>
      </div>
    </div>

    <!-- 3️⃣ NÚT ĐẶT HÀNG -->
    <button type="submit" class="submit-btn">Đặt hàng</button>
  </form>
</div>

<script>
function fillDefaultInfo() {
  const isChecked = document.getElementById('useDefaultInfo').checked;
  const data = {
    name: "<?= addslashes($user['fullname'] ?? '') ?>",
    address: "<?= addslashes($user['user_address'] ?? '') ?>",
    district: "<?= addslashes($user['district'] ?? '') ?>",
    province: "<?= addslashes($user['province'] ?? '') ?>",
    phone: "<?= addslashes($user['phone'] ?? '') ?>",
    email: "<?= addslashes($user['user_email'] ?? '') ?>"
  };
  for (const [key, value] of Object.entries(data)) {
    const input = document.getElementById(key);
    if (isChecked) { input.value = value; input.readOnly = true; }
    else { input.value = ""; input.readOnly = false; }
  }
}

function togglePaymentMethod() {
  const method = document.querySelector('input[name="payment_method"]:checked').value;
  document.querySelector('.bank-transfer-info').style.display = method === 'bank_transfer' ? 'block' : 'none';
  document.querySelector('.cod-info').style.display = method === 'cod' ? 'block' : 'none';
}
</script>

</body>
</html>
