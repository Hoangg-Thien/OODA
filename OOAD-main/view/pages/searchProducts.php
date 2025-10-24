<?php
// ✅ Đường dẫn tuyệt đối cho toàn project
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/'); // Thư mục gốc dự án
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);
include __DIR__ . '/../layout/header.php';

require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

/**
 * ✅ Hàm loại bỏ dấu tiếng Việt
 */
function removeAccents($str) {
    $str = strtolower($str);
    $str = preg_replace([
        "/[àáạảãâầấậẩẫăằắặẳẵ]/u",
        "/[èéẹẻẽêềếệểễ]/u",
        "/[ìíịỉĩ]/u",
        "/[òóọỏõôồốộổỗơờớợởỡ]/u",
        "/[ùúụủũưừứựửữ]/u",
        "/[ỳýỵỷỹ]/u",
        "/[đ]/u"
    ], [
        "a", "e", "i", "o", "u", "y", "d"
    ], $str);
    return $str;
}

// ✅ Tìm kiếm sản phẩm
if (isset($_GET['search'])) {
    $keyword = trim($_GET['search']);

    $sql = "SELECT * FROM sanpham WHERE product_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $keyword . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="list-product">
        <h1>KẾT QUẢ TÌM KIẾM</h1>
    </div>

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
            <p>Không tìm thấy sản phẩm nào phù hợp với từ khóa: <em><?= htmlspecialchars($keyword) ?></em></p>
        <?php endif; ?>
    </div>

    <?php
    $stmt->close();
} else {
    echo "<p>Vui lòng nhập từ khóa tìm kiếm.</p>";
}

$conn->close();
include __DIR__ . '/../layout/footer.php';
?>

<!-- ✅ SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function addToCart(productId) {
    fetch('<?= BASE_URL ?>view/pages/cart_handle.php', {
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
