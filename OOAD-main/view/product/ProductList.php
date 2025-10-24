<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/'); // Thư mục gốc dự án
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);
include __DIR__ . '/../layout/header.php';
?>

<div class="list-product">
    <h1>DANH SÁCH SẢN PHẨM</h1>
</div>

<?php
require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

// Cấu hình phân trang
$limit = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Lấy tổng số sản phẩm
$totalQuery = "SELECT COUNT(*) AS total FROM sanpham";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// Lấy sản phẩm theo trang
$sql = "SELECT * FROM sanpham LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="image-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="fruit-background">
                <!-- Ảnh sản phẩm -->
                <img src="<?= BASE_URL ?>asset/img/<?= htmlspecialchars($row['product_image']) ?>" 
                     alt="<?= htmlspecialchars($row['product_name']) ?>">

                <div class="caption">
                    <?= htmlspecialchars($row['product_name']) ?><br>
                    <?= number_format($row['product_price'], 0, ',', '.') ?> VND
                </div>

                <div class="icons">
                    <!-- Xem chi tiết -->
                    <a href="<?= BASE_URL ?>view/pages/productDetails.php?id=<?= $row['product_id'] ?>" 
                        class="info-icon" title="Xem thông tin chi tiết">
                        <i class="fa-solid fa-circle-info fa-lg"></i>
                    </a>

                    <!-- Thêm vào giỏ hàng -->
                    <?php if ($isLoggedIn): ?>
                        <button class="add-to-cart-btn" title="Thêm vào giỏ hàng" 
                                onclick="addToCart('<?= $row['product_id'] ?>')">
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
        <?php endwhile; ?>
    <?php else: ?>
        <p>Không có sản phẩm nào.</p>
    <?php endif; ?>
</div>

<!-- ✅ Phân trang -->
<div class="pagination" style="margin-top: 20px; text-align: center;">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="page-link">&lt;</a>
    <?php else: ?>
        <span class="page-link disabled">&lt;</span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>" class="page-link">&gt;</a>
    <?php else: ?>
        <span class="page-link disabled">&gt;</span>
    <?php endif; ?>
</div>

<!-- ✅ Thư viện SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function addToCart(productId) {
    fetch('view/pages/cart_handle.php', {
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
        text: 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.',
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
