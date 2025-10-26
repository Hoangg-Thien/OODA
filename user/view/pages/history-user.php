<?php
session_start();
require_once __DIR__ . '/../../classes/Database.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/OODA/');
}

// 🔒 Kiểm tra đăng nhập
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem lịch sử đơn hàng!'); window.location.href='login.php';</script>";
    exit();
}

$user_name = $_SESSION['user_name'];
$db = new Database();
$conn = $db->connect();

// 🧾 Lấy danh sách đơn hàng của người dùng
$sql = "
    SELECT h.order_id, h.name, h.order_date, h.PaymentMethod, h.order_status, 
           SUM(c.quantity * c.unit_price) AS total
    FROM hoadon h
    JOIN chitiethoadon c ON h.order_id = c.order_id
    WHERE h.name = ?
    GROUP BY h.order_id, h.name, h.order_date, h.PaymentMethod, h.order_status
    ORDER BY h.order_date DESC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

include __DIR__ . '/../layout/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>user/asset/styles/history-user.css">

<div class="history-container">
    <h1>📜 Lịch sử đơn hàng</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Người nhận</th>
                    <th>Phương thức</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
           <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>#<?= htmlspecialchars($row['order_id']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['order_date'])) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['PaymentMethod']) ?></td>
            <td><?= number_format($row['total'], 0, ',', '.') ?>₫</td>
            <td>
                <span class="status <?= strtolower(str_replace(' ', '-', $row['order_status'])) ?>">
                    <?= htmlspecialchars($row['order_status']) ?>
                </span>
            </td>
            <td>
                <a href="<?= BASE_URL ?>user/view/pages/invoice.php?id=<?= $row['order_id'] ?>" class="btn-detail">
                    🔍 Xem chi tiết
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

        </table>
    <?php else: ?>
        <p class="no-order">Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../layout/footer.php';
$stmt->close();
$conn->close();
?>
