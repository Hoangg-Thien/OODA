<?php
session_start();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/');
}

require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

// ✅ Kiểm tra tham số ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Không tìm thấy sản phẩm!'); window.location.href='productList.php';</script>";
    exit;
}

$product_id = $_GET['id']; // vì product_id là VARCHAR như 'F001'

// ✅ Truy vấn sản phẩm theo ID
$sql = "SELECT * FROM sanpham WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='productList.php';</script>";
    exit;
}

$product = $result->fetch_assoc();
$isLoggedIn = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);

include __DIR__ . '/../layout/header.php';
?>

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="row g-0 align-items-center">
            <!-- Hình ảnh -->
            <div class="col-md-5 text-center p-4">
                <img src="<?= BASE_URL ?>asset/img/<?= htmlspecialchars($product['product_image']) ?>"
                     alt="<?= htmlspecialchars($product['product_name']) ?>"
                     class="img-fluid rounded"
                     style="max-height: 400px; object-fit: contain;">
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="col-md-7">
                <div class="card-body p-4">
                    <h2 class="card-title text-success mb-3">
                        <?= htmlspecialchars($product['product_name']) ?>
                    </h2>
                    <p class="text-danger fw-bold" style="font-size: 1.6rem;">
                        <?= number_format($product['product_price'], 0, ',', '.') ?> ₫
                    </p>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($product['product_description'] ?: 'Chưa có mô tả.')) ?></p>
                    <div class="mt-4">
                        <!-- Thêm vào giỏ hàng -->
                    <?php if ($isLoggedIn): ?>
                        <button class="add-to-cart-btn" title="Thêm vào giỏ hàng" 
                                onclick="addToCart('<?= $product['product_id'] ?>')">
                            <i class="fas fa-cart-plus fa-lg"></i>
                        </button>
                    <?php else: ?>
                        <button class="add-to-cart-btn" 
                                title="Bạn cần đăng nhập để thêm vào giỏ hàng"
                                onclick="confirmLogin()">
                            <i class="fas fa-cart-plus fa-lg"></i>
                        </button>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function addToCart(productId) {
    fetch('cart_handle.php', {  
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: productId,
            action: 'add'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Thêm vào giỏ hàng thành công!',
                text: data.product.name + ' đã được thêm.',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: data.message || 'Không thể thêm sản phẩm.'
            });
        }
    })
    .catch(err => console.error('Fetch error:', err));
}

function confirmLogin() {
    Swal.fire({
        icon: 'warning',
        title: 'Bạn cần đăng nhập!',
        text: 'Vui lòng đăng nhập để mua hàng.',
        showCancelButton: true,
        confirmButtonText: 'Đăng nhập ngay',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= BASE_URL ?>view/pages/login.php";
        }
    });
}
</script>

<?php
$stmt->close();
$conn->close();
include __DIR__ . '/../layout/footer.php';
?>
