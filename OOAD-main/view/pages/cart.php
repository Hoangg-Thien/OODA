<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/');
}
session_start();

include __DIR__ . '/../layout/header.php';

// Lấy giỏ hàng từ session (nếu có)
$cart = $_SESSION['cart'] ?? [];
$total = 0;
$isLoggedIn = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);
?>

<div class="cart-container" style="padding: 30px; min-height: 500px; width: 900px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; margin-bottom: 30px; font-size: 26px; color: #333;">Giỏ hàng của bạn</h2>

    <?php if (!$isLoggedIn): ?>
        <!-- ⚠️ Chưa đăng nhập -->
        <div style="text-align: center; padding: 60px;">
            <img src="<?= BASE_URL ?>asset/img/shopping-cart.jpg" alt="Giỏ hàng trống" style="width: 120px; opacity: 0.6; margin-bottom: 20px;">
            <h3 style="color: #555;">Chưa có sản phẩm nào!</h3>
            <p style="margin: 10px 0;">Hãy đăng nhập để thêm sản phẩm vào giỏ hàng.</p>
            <button onclick="window.location.href='<?= BASE_URL ?>view/pages/login.php'" 
                    style="padding: 12px 24px; background-color: #4CAF50; color: white; border: none; border-radius: 6px; cursor: pointer;">
                Đăng nhập ngay
            </button>
            <button onclick="window.location.href='<?= BASE_URL ?>index.php'" 
                    style="padding: 12px 24px; background-color: #ccc; color: black; border: none; border-radius: 6px; margin-left: 10px; cursor: pointer;">
                Về trang chủ
            </button>
        </div>

    <?php elseif (empty($cart)): ?>
        <!-- 🛒 Đã đăng nhập nhưng giỏ trống -->
        <div style="text-align: center; padding: 60px;">
            <img src="<?= BASE_URL ?>asset/img/shopping-cart.jpg" alt="Giỏ hàng trống" style="width: 120px; opacity: 0.6; margin-bottom: 20px;">
            <h3 style="color: #555;">Hiện không có sản phẩm nào trong giỏ hàng.</h3>
            <button onclick="window.location.href='<?= BASE_URL ?>index.php'" 
                    style="padding: 12px 24px; background-color: #4CAF50; color: white; border: none; border-radius: 6px; cursor: pointer;">
                Về trang mua sắm
            </button>
        </div>

    <?php else: ?>
        <!-- ✅ Hiển thị bảng giỏ hàng -->
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f7f7f7;">
                    <th style="padding: 12px; border: 1px solid #ddd;">Sản phẩm</th>
                    <th style="padding: 12px; border: 1px solid #ddd;">Đơn giá</th>
                    <th style="padding: 12px; border: 1px solid #ddd;">Số lượng</th>
                    <th style="padding: 12px; border: 1px solid #ddd;">Thành tiền</th>
                    <th style="padding: 12px; border: 1px solid #ddd;">Xoá</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $id => $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd;"><?= htmlspecialchars($item['name']) ?></td>
                        <td style="padding: 12px; border: 1px solid #ddd;"><?= number_format($item['price']) ?>₫</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">
                            <button onclick="updateCart('decrease', '<?= $id ?>')" style="padding: 4px 10px;">-</button>
                            <?= $item['quantity'] ?>
                            <button onclick="updateCart('increase', '<?= $id ?>')" style="padding: 4px 10px;">+</button>
                        </td>
                        <td style="padding: 12px; border: 1px solid #ddd;"><?= number_format($subtotal) ?>₫</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align:center;">
                            <button onclick="if(confirm('Bạn có chắc muốn xoá sản phẩm này không?')) updateCart('remove', '<?= $id ?>')" 
                                    style="color: red; border: none; background: transparent; cursor: pointer;">X</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="3" style="text-align: right; font-weight: bold;">Tổng cộng:</td>
                    <td colspan="2" style="font-weight: bold; color: #2e7d32;"><?= number_format($total) ?>₫</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 30px;">
            <button onclick="window.location.href='<?= BASE_URL ?>view/pages/checkout.php'" 
                    style="padding: 12px 24px; background-color: #4CAF50; color: white; font-size: 16px; border: none; border-radius: 6px; cursor: pointer;">
                Thanh toán
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- ✅ JS cập nhật giỏ hàng -->
<script>
function updateCart(action, productId) {
    fetch('<?= BASE_URL ?>view/pages/cart_handle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: action, id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload(); // ✅ Cập nhật lại trang khi thành công
        } else {
            alert(data.message || "Có lỗi xảy ra!");
        }
    })
    .catch(err => console.error("Lỗi:", err));
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
