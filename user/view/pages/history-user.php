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

/**
 * 🔧 Hàm loại bỏ dấu tiếng Việt
 */
function removeVietnameseAccents($str) {
    $trans = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a',
        'â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a',
        'ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e',
        'ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o',
        'ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o',
        'ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u',
        'ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
        'đ'=>'d',
        'À'=>'A','Á'=>'A','Ạ'=>'A','Ả'=>'A','Ã'=>'A',
        'Â'=>'A','Ầ'=>'A','Ấ'=>'A','Ậ'=>'A','Ẩ'=>'A','Ẫ'=>'A',
        'Ă'=>'A','Ằ'=>'A','Ắ'=>'A','Ặ'=>'A','Ẳ'=>'A','Ẵ'=>'A',
        'È'=>'E','É'=>'E','Ẹ'=>'E','Ẻ'=>'E','Ẽ'=>'E',
        'Ê'=>'E','Ề'=>'E','Ế'=>'E','Ệ'=>'E','Ể'=>'E','Ễ'=>'E',
        'Ì'=>'I','Í'=>'I','Ị'=>'I','Ỉ'=>'I','Ĩ'=>'I',
        'Ò'=>'O','Ó'=>'O','Ọ'=>'O','Ỏ'=>'O','Õ'=>'O',
        'Ô'=>'O','Ồ'=>'O','Ố'=>'O','Ộ'=>'O','Ổ'=>'O','Ỗ'=>'O',
        'Ơ'=>'O','Ờ'=>'O','Ớ'=>'O','Ợ'=>'O','Ở'=>'O','Ỡ'=>'O',
        'Ù'=>'U','Ú'=>'U','Ụ'=>'U','Ủ'=>'U','Ũ'=>'U',
        'Ư'=>'U','Ừ'=>'U','Ứ'=>'U','Ự'=>'U','Ử'=>'U','Ữ'=>'U',
        'Ỳ'=>'Y','Ý'=>'Y','Ỵ'=>'Y','Ỷ'=>'Y','Ỹ'=>'Y',
        'Đ'=>'D'
    ];
    return strtr($str, $trans);
}

/**
 * 🟢 Hàm chuyển trạng thái sang class CSS tương ứng
 */
function getStatusClass($status) {
    $status = strtolower(removeVietnameseAccents(trim($status)));
    switch ($status) {
        case 'chua xac nhan':
            return 'pending';      // 🟡
        case 'da xac nhan':
            return 'confirmed';    // 🔵
        case 'da huy':
            return 'cancelled';    // 🔴
        case 'giao thanh cong':
        case 'hoan thanh':
        case 'da giao':
            return 'delivered';    // 🟢
        default:
            return 'pending';
    }
}
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
                    <?php $statusClass = getStatusClass($row['order_status']); ?>
                    <tr>
                        <td>#<?= htmlspecialchars($row['order_id']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['order_date'])) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['PaymentMethod']) ?></td>
                        <td><?= number_format($row['total'], 0, ',', '.') ?>₫</td>
                        <td>
                            <span class="status <?= $statusClass ?>">
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

<style>
/* ===== Trạng thái ===== */
.status {
  padding: 5px 10px;
  border-radius: 5px;
  color: white;
  font-size: 0.9em;
  font-weight: 600;
  white-space: nowrap;
  display: inline-block;
  text-transform: capitalize;
}

/* 🎨 Màu trạng thái */
.status.pending {
  background-color: #efb11e; /* Chưa xác nhận */
}

.status.confirmed {
  background-color: #3c97e6; /* Đã xác nhận */
}

.status.cancelled {
  background-color: #e54432; /* Đã hủy */
}

.status.delivered {
  background-color: #4CAF50; /* Giao thành công */
}

/* ===== Không có đơn ===== */
.no-order {
  text-align: center;
  color: #777;
  font-size: 16px;
  margin-top: 30px;
}

.order-table th, .order-table td {
  padding: 23px 6px;
  border: 1px solid #e0e0e0;
  text-align: center;
}

</style>
