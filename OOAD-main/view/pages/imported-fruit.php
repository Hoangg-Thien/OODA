<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/');
}
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
include __DIR__ . '/../layout/header.php';
?>

<div class="list-product">
    <h1>TRÁI CÂY NHẬP KHẨU</h1>
</div>

<?php
require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

$limit = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

$totalQuery = "SELECT COUNT(*) AS total FROM sanpham WHERE product_type = 'Trái cây Nhập Khẩu'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);

$sql = "SELECT * FROM sanpham WHERE product_type = 'Trái cây Nhập Khẩu' LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="image-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="fruit-background">
            <img src="<?= BASE_URL ?>asset/img/<?= htmlspecialchars($row['product_image']) ?>" 
                 alt="<?= htmlspecialchars($row['product_name']) ?>">
            <div class="caption">
                <?= htmlspecialchars($row['product_name']) ?><br>
                <?= number_format($row['product_price'], 0, ',', '.') ?> VND
            </div>
            <div class="icons">
                <a href="<?= BASE_URL ?>User/user/product-details-nolog.php?id=<?= $row['product_id'] ?>" 
                   class="info-icon" title="Xem thông tin chi tiết">
                    <i class="fa-solid fa-circle-info fa-lg"></i>
                </a>
                <button class="add-to-cart-btn" onclick="handleAddToCart('<?= $row['product_id'] ?>')">
                    <i class="fas fa-cart-plus fa-lg"></i>
                </button>
            </div>
        </div>
    <?php endwhile; ?>
</div>

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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function handleAddToCart(productId) {
    const isLoggedIn = <?= json_encode($isLoggedIn) ?>;
    if (!isLoggedIn) {
        Swal.fire({
            title: "Bạn cần đăng nhập!",
            text: "Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Đăng nhập ngay",
            cancelButtonText: "Hủy",
            confirmButtonColor: "#28a745"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= BASE_URL ?>view/pages/login.php";

            }
        });
        return;
    }

    Swal.fire({
        title: "✅ Thêm thành công!",
        text: "Sản phẩm đã được thêm vào giỏ hàng.",
        icon: "success",
        confirmButtonColor: "#28a745"
    });
}
</script>

<?php
$stmt->close();
$conn->close();
include __DIR__ . '/../layout/footer.php';
?>
