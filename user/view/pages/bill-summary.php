<?php
session_start();
require_once __DIR__ . '/../../classes/Database.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/OODA/');
}

// 🔒 Kiểm tra đăng nhập
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem tóm tắt hóa đơn!'); window.location.href='login.php';</script>";
    exit();
}

$user_name = $_SESSION['user_name'];
$db = new Database();
$conn = $db->connect();

// ✅ 1. Tổng số đơn hàng
$sql_total_orders = "SELECT COUNT(*) AS total_orders FROM hoadon WHERE name = ?";
$stmt = $conn->prepare($sql_total_orders);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$total_orders = $stmt->get_result()->fetch_assoc()['total_orders'];

// ✅ 2. Tổng tiền đã chi
$sql_total_spent = "
    SELECT SUM(c.quantity * c.unit_price) AS total_spent
    FROM hoadon h
    JOIN chitiethoadon c ON h.order_id = c.order_id
    WHERE h.name = ?
";
$stmt = $conn->prepare($sql_total_spent);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$total_spent = $stmt->get_result()->fetch_assoc()['total_spent'] ?? 0;

// ✅ 3. Số đơn theo trạng thái
$statuses = ['Chưa xác nhận', 'Đã xác nhận', 'Đã hủy'];
$status_counts = [];

foreach ($statuses as $status) {
    $sql = "SELECT COUNT(*) AS count FROM hoadon WHERE name = ? AND order_status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_name, $status);
    $stmt->execute();
    $status_counts[$status] = $stmt->get_result()->fetch_assoc()['count'];
}

include __DIR__ . '/../layout/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/user/asset/styles/bill-summary.css">

<div class="summary-container">
    <h1>📊 TÓM TẮT HÓA ĐƠN</h1>

    <div class="summary-grid">
        <div class="card total">
            <h2><?= $total_orders ?></h2>
            <p>Tổng số đơn hàng</p>
        </div>

        <div class="card money">
            <h2><?= number_format($total_spent, 0, ',', '.') ?>₫</h2>
            <p>Tổng tiền đã chi</p>
        </div>

        <div class="card pending">
            <h2><?= $status_counts['Chưa xác nhận'] ?></h2>
            <p>Chưa xác nhận</p>
        </div>

        <div class="card success">
            <h2><?= $status_counts['Đã xác nhận'] ?></h2>
            <p>Đã xác nhận</p>
        </div>

        <div class="card cancel">
            <h2><?= $status_counts['Đã hủy'] ?></h2>
            <p>Đã hủy</p>
        </div>
    </div>

    <div class="btn-container">
        <a href="<?= BASE_URL ?>view/pages/history-user.php" class="btn">📜 Xem lịch sử đơn hàng</a>
        <a href="<?= BASE_URL ?>index.php" class="btn back">🏠 Quay lại trang chủ</a>
    </div>
</div>

<?php
include __DIR__ . '/../layout/footer.php';
$conn->close();
?>
