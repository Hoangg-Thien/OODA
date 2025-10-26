<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OODA/');
}
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
include __DIR__ . '/../layout/header.php';
?>

<div class="list-product">
    <h1>TRÁI CÂY NGON</h1>
</div>

<?php
require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

// ✅ Cấu hình phân trang
$limit = 6; // số sản phẩm mỗi trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// ✅ Đếm tổng số sản phẩm thuộc loại "Trái Cây Ngon"
$countSql = "
    SELECT COUNT(*) AS total
    FROM sanpham
    JOIN loaisanpham ON sanpham.category_id = loaisanpham.category_id
    WHERE loaisanpham.name_type = 'Trái Cây Ngon'
";
$countResult = $conn->query($countSql);
$totalRow = $countResult->fetch_assoc();
$totalProducts = (int)$totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// ✅ Lấy danh sách sản phẩm cho trang hiện tại
$sql = "
    SELECT sanpham.*
    FROM sanpham
    JOIN loaisanpham ON sanpham.category_id = loaisanpham.category_id
    WHERE loaisanpham.name_type = 'Trái Cây Ngon'
    LIMIT ?, ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="image-container">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="fruit-background">
                <img src="<?= BASE_URL ?>user/asset/img/<?= htmlspecialchars($row['product_image']) ?>" 
                     alt="<?= htmlspecialchars($row['product_name']) ?>">
                <div class="caption">
                    <?= htmlspecialchars($row['product_name']) ?><br>
                    <?= number_format($row['product_price'], 0, ',', '.') ?> VND
                </div>
                <div class="icons">
                    <a href="<?= BASE_URL ?>/user/view/pages/productDetails.php?id=<?= $row['product_id'] ?>" 
                       class="info-icon" title="Xem thông tin chi tiết">
                        <i class="fa-solid fa-circle-info fa-lg"></i>
                    </a>
                    <button class="add-to-cart-btn" onclick="handleAddToCart('<?= $row['product_id'] ?>')">
                        <i class="fas fa-cart-plus fa-lg"></i>
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; margin-top:20px;">Không có sản phẩm nào thuộc loại này.</p>
    <?php endif; ?>
</div>

<!-- ✅ PHÂN TRANG -->
<?php if ($totalPages > 1): ?>
    <div class="pagination" style="margin-top: 20px; text-align: center;">
        <!-- Nút Previous -->
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="page-link">&lt;</a>
        <?php else: ?>
            <span class="page-link disabled">&lt;</span>
        <?php endif; ?>

        <!-- Các số trang -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <!-- Nút Next -->
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="page-link">&gt;</a>
        <?php else: ?>
            <span class="page-link disabled">&gt;</span>
        <?php endif; ?>
    </div>
<?php endif; ?>

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

     // ✅ Giả lập thêm sản phẩm (bạn có thể thay bằng AJAX thực tế)
    let cartCount = parseInt(localStorage.getItem("cartCount") || "0");
    cartCount++;
    localStorage.setItem("cartCount", cartCount);

    // Gửi sự kiện thông báo cho header cập nhật
    window.dispatchEvent(new Event("cartUpdated"));
    
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
