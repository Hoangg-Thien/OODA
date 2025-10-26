<?php
// file: view/layout/header.php

// ✅ Đường dẫn tuyệt đối cho toàn project
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/OODA/');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Biến kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ✅ Font Awesome + Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/user/asset/styles/index.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/user/asset/styles/grid.css">

    <!-- ✅ Favicon -->
    <link rel="shortcut icon" href="<?= BASE_URL ?>/user/asset/img/favicon.png" type="image/x-icon">

    <title>Tiệm trái cây</title>

    <!-- ✅ Style pagination -->
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: 1px solid #ddd;
            border-radius: 50%;
            color: #333;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.25s ease;
            background-color: #fff;
        }
        .page-link:hover {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
            transform: translateY(-2px);
        }
        .page-link.active {
            background-color: #4CAF50;
            color: #fff;
            border-color: #4CAF50;
            box-shadow: 0 2px 5px rgba(76, 175, 80, 0.4);
        }
        .page-link.disabled {
            color: #aaa;
            border-color: #eee;
            background-color: #f9f9f9;
            pointer-events: none;
        }
        /* 🔔 Hiệu ứng nhảy cho icon giỏ hàng */
        @keyframes cart-bounce {
        0%   { transform: scale(1); }
        30%  { transform: scale(1.3); }
        50%  { transform: scale(0.9); }
        70%  { transform: scale(1.15); }
        100% { transform: scale(1); }
        }

        .cart-bounce {
        animation: cart-bounce 0.5s ease;
        }

    </style>
</head>
<body>

    <header>
        <!-- ✅ Ảnh trái cây -->
        <a href="#" class="fruit">
            <img src="<?= BASE_URL ?>/user/asset/img/carrotheader.png" alt="Cà rốt" /> Cà rốt
        </a>
        <a href="#" class="fruit">
            <img src="<?= BASE_URL ?>/user/asset/img/potatoheader.png" alt="Khoai tây" /> Khoai tây
        </a>
        <a href="#" class="fruit">
            <img src="<?= BASE_URL ?>/user/asset/img/watermelonheader.png" alt="Dưa hấu" /> Dưa hấu
        </a>
        <a href="#" class="fruit">
            <img src="<?= BASE_URL ?>/user/asset/img/orangeheader.png" alt="Cam" /> Cam
        </a>
        <a href="#" class="fruit">
            <img src="<?= BASE_URL ?>/user/asset/img/duagangheader.png" alt="Đu đủ" /> Đu đủ
        </a>
        <a href="#" class="fruit">
            <img src="<?= BASE_URL ?>/user/asset/img/tomatoheader.png" alt="Cà chua" /> Cà chua
        </a>
    </header>

    <div class="sea-fruit-container">
        <div><div class="sea-fruit">SEA FRUITS</div></div>

        <div style="display: flex; align-items: center; padding: 10px 20px;">
            <!-- ✅ Danh mục sản phẩm -->
            <div class="product-category">DANH MỤC SẢN PHẨM
                <ul>
                    <li><a href="<?= BASE_URL ?>user/view/pages/declious-fruit.php">Trái cây ngon</a></li>
                    <li><a href="<?= BASE_URL ?>user/view/pages/VietNam-fruit.php">Trái cây Việt</a></li>
                    <li><a href="<?= BASE_URL ?>user/view/pages/imported-fruit.php">Trái cây nhập khẩu</a></li>
                </ul>
            </div>

            <!-- ✅ Menu -->
            <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

            <div class="menu">
                <a href="<?= BASE_URL ?>index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?>">Trang chủ</a>
                <a href="<?= BASE_URL ?>user/view/pages/introduce.php" class="<?= ($currentPage === 'introduce.php') ? 'active' : '' ?>">Giới thiệu</a>
                <a href="<?= BASE_URL ?>user/view/pages/news.php" class="<?= ($currentPage === 'news.php') ? 'active' : '' ?>">Tin tức</a>
                <a href="<?= BASE_URL ?>user/view/pages/contact.php" class="<?= ($currentPage === 'contact.php') ? 'active' : '' ?>">Liên hệ</a>
                <a href="<?= BASE_URL ?>user/view/pages/cart.php" target="_blank" class="cart-icon" title="Go to Cart">
                    <i class="fas fa-shopping-cart"></i>
                </a>
            </div>

            <!-- ✅ Tìm kiếm -->
            <div class="search-container">
                <form action="<?= BASE_URL ?>user/view/pages/searchProducts.php" method="GET">
                    <div>
                        <input type="text" name="search" id="searchInput" placeholder="Nhập tên sản phẩm..." autocomplete="off" required>
                        <div id="suggestBox" class="autocomplete-suggestions"></div>
                        <button type="submit">Tìm kiếm</button>
                    </div>
                </form>

            <!-- ✅ Dropdown khách -->
            <?php if ($isLoggedIn): ?>
    <div class="dropdown ms-3" style="font-size: 1.1rem;">
        <a href="#" class="dropdown-toggle d-flex align-items-center text-dark text-decoration-none"
           id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-2"></i>
            <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>user/view/pages/user-info.php">Tài khoản</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>user/view/pages/history-user.php">Lịch sử</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>user/view/pages/bill-summary.php">Tóm tắt hóa đơn</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>user/view/pages/logout.php">Đăng xuất</a></li>
        </ul>
    </div>
<?php else: ?>
    <div class="dropdown ms-3" style="font-size: 1.1rem;">
        <a href="#" class="dropdown-toggle d-flex align-items-center text-dark text-decoration-none"
           id="guestDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user me-2"></i>
            <span>Xin chào, <strong>Khách</strong>!</span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="guestDropdown">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>user/view/pages/login.php">Đăng nhập</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>user/view/pages/regis.php">Đăng ký</a></li>
        </ul>
    </div>
<?php endif; ?>
        </div>
        </div>
    </div>




</body>
</html>
