<?php
session_start();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/OODA/');
}

require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

// ✅ Kiểm tra mã hóa đơn
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Không tìm thấy hóa đơn!'); window.location.href='index.php';</script>";
    exit;
}

$order_id = intval($_GET['id']);

// ✅ Lấy thông tin hóa đơn từ bảng `hoadon`
$sql_invoice = "SELECT * FROM hoadon WHERE order_id = ?";
$stmt_invoice = $conn->prepare($sql_invoice);
$stmt_invoice->bind_param("i", $order_id);
$stmt_invoice->execute();
$result_invoice = $stmt_invoice->get_result();

if ($result_invoice->num_rows === 0) {
    echo "<script>alert('Hóa đơn không tồn tại!'); window.location.href='index.php';</script>";
    exit;
}

$invoice = $result_invoice->fetch_assoc();

// ✅ Lấy chi tiết sản phẩm trong hóa đơn (bảng chitiethoadon)
$sql_detail = "
    SELECT cthd.*, sp.product_name 
    FROM chitiethoadon cthd
    JOIN sanpham sp ON cthd.product_id = sp.product_id
    WHERE cthd.order_id = ?
";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("i", $order_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();

include __DIR__ . '/../layout/header.php';
?>

<div class="invoice-container" style="max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <h2 style="text-align:center; color:#2e7d32; margin-bottom:20px;">🧾 HÓA ĐƠN THANH TOÁN</h2>

    <div style="margin-bottom:20px;">
        <p><strong>Mã đơn hàng:</strong> #<?= htmlspecialchars($invoice['order_id']) ?></p>
        <p><strong>Ngày lập:</strong> <?= htmlspecialchars($invoice['order_date']) ?></p>
        <p><strong>Tên tài khoản:</strong> <?= htmlspecialchars($invoice['name']) ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($invoice['phone']) ?></p>
        <p><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($invoice['address']) ?>, <?= htmlspecialchars($invoice['district']) ?>, <?= htmlspecialchars($invoice['province']) ?></p>
        <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($invoice['PaymentMethod']) ?></p>
        <p><strong>Trạng thái đơn hàng:</strong> <?= htmlspecialchars($invoice['order_status']) ?></p>
    </div>

    <h4>Chi tiết sản phẩm:</h4>
    <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th style="padding:10px; border:1px solid #ccc;">Sản phẩm</th>
                <th style="padding:10px; border:1px solid #ccc;">Số lượng</th>
                <th style="padding:10px; border:1px solid #ccc;">Đơn giá</th>
                <th style="padding:10px; border:1px solid #ccc;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            while ($row = $result_detail->fetch_assoc()):
                $total += $row['total_amount'];
            ?>
            <tr>
                <td style="padding:10px; border:1px solid #ccc;"><?= htmlspecialchars($row['product_name']) ?></td>
                <td style="padding:10px; border:1px solid #ccc; text-align:center;"><?= $row['quantity'] ?></td>
                <td style="padding:10px; border:1px solid #ccc;"><?= number_format($row['unit_price'], 0, ',', '.') ?>₫</td>
                <td style="padding:10px; border:1px solid #ccc;"><?= number_format($row['total_amount'], 0, ',', '.') ?>₫</td>
            </tr>
            <?php endwhile; ?>
            <tr style="background:#fafafa; font-weight:bold;">
                <td colspan="3" style="text-align:right;">Tổng cộng:</td>
                <td><?= number_format($total, 0, ',', '.') ?>₫</td>
            </tr>
        </tbody>
    </table>

    <div style="text-align:center; margin-top:30px;">
        <button onclick="window.print()" 
                style="background-color:#4CAF50; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer;">
            🖨️ In hóa đơn
        </button>
        <button onclick="window.location.href='<?= BASE_URL ?>index.php'" 
                style="background-color:#888; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; margin-left:10px;">
            ⬅️ Quay lại trang chủ
        </button>
    </div>
</div>

<?php
include __DIR__ . '/../layout/footer.php';
$stmt_detail->close();
$stmt_invoice->close();
$conn->close();
?>
