<?php
session_start();
require_once('connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$user_query = "SELECT * FROM nguoidung WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Lấy thông tin hóa đơn chi tiết
if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];
    $invoice_query = "SELECT h.*, 
                     GROUP_CONCAT(CONCAT(s.ten_sanpham, '|', ct.soluong, '|', ct.gia) SEPARATOR '||') as order_items
                     FROM hoadon h
                     LEFT JOIN chitiethoadon ct ON h.invoice_id = ct.invoice_id
                     LEFT JOIN sanpham s ON ct.product_id = s.product_id
                     WHERE h.invoice_id = ? AND h.user_id = ?
                     GROUP BY h.invoice_id";
    $stmt = $conn->prepare($invoice_query);
    $stmt->bind_param("ii", $invoice_id, $user_id);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();
} else {
    header("Location: invoice-summary.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Hóa Đơn - SEA FRUITS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../styles/news.css">
    <link rel="stylesheet" href="../styles/grid.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
    <style>
        /* Header styles */
        header {
            background-color: #fff;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
        }

        .fruit {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.3s ease;
        }

        .fruit:hover {
            transform: translateY(-5px);
        }

        .fruit img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 0.5rem;
        }

        .sea-fruit-container {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .sea-fruit {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .product-category {
            position: relative;
            margin-right: 2rem;
        }

        .product-category ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .product-category li {
            padding: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .product-category li:hover {
            background-color: #f0f0f0;
        }

        .fruit-list {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 1rem;
            z-index: 1000;
            min-width: 200px;
        }

        .fruit-list h4 {
            margin: 0 0 1rem 0;
            color: #333;
        }

        .fruit-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .fruit-list li {
            padding: 0.5rem;
        }

        .fruit-list a {
            color: #333;
            text-decoration: none;
            display: block;
        }

        .fruit-list a:hover {
            color: #4CAF50;
        }

        .menu {
            display: flex;
            gap: 1rem;
            margin-right: 2rem;
        }

        .menu a {
            color: #333;
            text-decoration: none;
            padding: 0.5rem;
            transition: color 0.3s ease;
        }

        .menu a:hover {
            color: #4CAF50;
        }

        .cart-icon {
            position: relative;
            color: #333;
            text-decoration: none;
        }

        .search-container {
            display: flex;
            align-items: center;
            margin-right: 2rem;
        }

        #searchBox {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 0.5rem;
            width: 200px;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            color: #333;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
            border-radius: 5px;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }

        .dropdown.active .dropdown-menu {
            display: block;
        }

        /* Invoice detail styles */
        .invoice-detail-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .invoice-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .invoice-title h1 {
            margin: 0;
            color: var(--primary-color);
            font-size: 2rem;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-group {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .info-label {
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 500;
        }

        .invoice-items {
            margin-top: 2rem;
        }

        .item-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .item-name {
            font-weight: 500;
        }

        .item-quantity, .item-price, .item-total {
            text-align: right;
        }

        .invoice-total {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border-color);
        }

        .total-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            text-align: right;
        }

        .total-label {
            color: var(--light-text);
        }

        .total-value {
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .invoice-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .back-btn {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .download-btn {
            background: var(--primary-color);
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .invoice-detail-container {
                margin: 1rem;
                padding: 1rem;
            }

            .item {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .item-quantity, .item-price, .item-total {
                text-align: left;
            }

            .invoice-actions {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<header>
     <a href="#" class="fruit">  
         <img src="../img/carrotheader.png" alt="Cà rốt" />  
         Cà rốt  
     </a>  
     <a href="#" class="fruit">  
         <img src="../img/potatoheader.png" alt="Khoai tây" />  
         Khoai tây  
     </a>  
     <a href="#" class="fruit">  
         <img src="../img/watermelonheader.png" alt="Dưa hấu" />  
         Dưa hấu  
     </a>  
     <a href="#" class="fruit">  
         <img src="../img/orangeheader.png" alt="Trái cam" />  
         Cam  
     </a>  
     <a href="#" class="fruit">  
         <img src="../img/duagangheader.png" alt="Đu đủ" />  
         Đu đủ  
     </a>  
     <a href="#" class="fruit">  
         <img src="../img/tomatoheader.png" alt="Cà chua" />  
         Cà chua  
     </a>  
 </header>  
 <div class="sea-fruit-container">  
     <div>  
         <div class="sea-fruit">SEA FRUITS</div>  
     </div>
     <div style="display: flex; align-items: center; padding: 10px 20px;">  
         <div class="product-category">DANH MỤC SẢN PHẨM 
             <ul>
                 <li onmouseover="showFruits('Trái cây ngon')" onmouseout="hideFruits()">Trái cây ngon</li>
                 <li onmouseover="showFruits('Trái cây Việt')" onmouseout="hideFruits()">Trái cây Việt</li>
                 <li onmouseover="showFruits('Trái cây nhập khẩu')" onmouseout="hideFruits()">Trái cây nhập khẩu</li>
             </ul>
         </div>
         
         <div class="fruit-list" id="fruitList" style="display: none;">
             <h4 id="categoryTitle"></h4>
             <ul id="fruitItems">
                 <!-- Trái cây sẽ được thêm bằng JavaScript -->
             </ul>
         </div>
         <div class="menu">  
             <a href="../index.php">Trang chủ</a>  
             <a href="./introducelogin.php">Giới thiệu</a>  
             <a href="./newslogin.php">Tin tức</a>  
             <a href="./contactlogin.php">Liên hệ</a> 
             <a href="./cart-user.php" target="_blank" class="cart-icon" title="Giỏ hàng">
                <i class="fas fa-shopping-cart"></i>  
                    <span id="cart-count" style="margin-left: 5px; font-weight: bold;">
    <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
</span>
             </a>
         </div>
         <div class="search-container">
             <form method="GET" action="">
                 <input type="text" id="searchBox" name="search" placeholder="Tìm kiếm sản phẩm..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                 <button type="submit"><i class="fas fa-search"></i> Tìm kiếm</button>
             </form>
         </div>
     
         <div class="dropdown">
             <button class="dropdown-button">
                 <i class="fa-solid fa-user" style="margin-right: 10px;"></i>
                 <span>Hi, <?php echo isset($user['fullname']) ? htmlspecialchars($user['fullname']) : 'User'; ?></span>
             </button>
             <div class="dropdown-menu">
                 <?php if ($user): ?>
                     <a href="../user/userinfo.php">Tài khoản</a>
                     <a href="../user/history-user.php">Lịch sử</a>
                     <a href="../user/invoice-summary.php">Tóm tắt hóa đơn</a>
                     <a href="../user/usernologin.php">Đăng xuất</a>
                 <?php else: ?>
                     <a href="../user/login.php">Đăng nhập</a>
                     <a href="../user/register.php">Đăng ký</a>
                 <?php endif; ?>
             </div>
         </div>
     </div>
 </div>

    <div class="invoice-detail-container">
        <div class="invoice-header">
            <div class="invoice-title">
                <h1>Chi Tiết Hóa Đơn #<?php echo $invoice['invoice_id']; ?></h1>
            </div>
        </div>

        <div class="invoice-info">
            <div class="info-group">
                <span class="info-label">Ngày đặt hàng</span>
                <span class="info-value"><?php echo date('d/m/Y', strtotime($invoice['order_date'])); ?></span>
            </div>
            <div class="info-group">
                <span class="info-label">Trạng thái</span>
                <span class="info-value status-<?php echo strtolower($invoice['status']); ?>">
                    <?php 
                    switch($invoice['status']) {
                        case 'completed':
                            echo 'Hoàn thành';
                            break;
                        case 'pending':
                            echo 'Đang xử lý';
                            break;
                        case 'cancelled':
                            echo 'Đã hủy';
                            break;
                    }
                    ?>
                </span>
            </div>
            <div class="info-group">
                <span class="info-label">Phương thức thanh toán</span>
                <span class="info-value"><?php echo $invoice['payment_method']; ?></span>
            </div>
            <div class="info-group">
                <span class="info-label">Địa chỉ giao hàng</span>
                <span class="info-value"><?php echo $invoice['shipping_address']; ?></span>
            </div>
            <div class="info-group">
                <span class="info-label">Số điện thoại</span>
                <span class="info-value"><?php echo $invoice['phone']; ?></span>
            </div>
        </div>

        <div class="invoice-items">
            <div class="item-list">
                <div class="item" style="font-weight: bold; border-bottom: 2px solid var(--border-color);">
                    <div class="item-info">Sản phẩm</div>
                    <div class="item-quantity">Số lượng</div>
                    <div class="item-price">Đơn giá</div>
                    <div class="item-total">Thành tiền</div>
                </div>
                <?php
                $order_items = explode('||', $invoice['order_items']);
                $total = 0;
                foreach ($order_items as $item) {
                    list($name, $quantity, $price) = explode('|', $item);
                    $subtotal = $price * $quantity;
                    $total += $subtotal;
                ?>
                    <div class="item">
                        <div class="item-info">
                            <img src="../img/<?php echo strtolower(str_replace(' ', '-', $name)); ?>.jpg" 
                                 alt="<?php echo $name; ?>" class="item-image">
                            <span class="item-name"><?php echo $name; ?></span>
                        </div>
                        <div class="item-quantity"><?php echo $quantity; ?></div>
                        <div class="item-price"><?php echo number_format($price); ?>đ</div>
                        <div class="item-total"><?php echo number_format($subtotal); ?>đ</div>
                    </div>
                <?php } ?>
            </div>

            <div class="invoice-total">
                <div class="total-group">
                    <span class="total-label">Tổng cộng</span>
                    <span class="total-value"><?php echo number_format($total); ?>đ</span>
                </div>
            </div>
        </div>

        <div class="invoice-actions">
            <button class="action-btn back-btn" onclick="window.location.href='invoice-summary.php'">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </button>
            <button class="action-btn download-btn" onclick="downloadInvoice(<?php echo $invoice['invoice_id']; ?>)">
                <i class="fas fa-download"></i>
                Tải xuống
            </button>
        </div>
    </div>

    <div class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Về chúng tôi</h3>
                <p>Sea Fruits - Nơi cung cấp trái cây tươi ngon, chất lượng cao với giá cả hợp lý.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Liên kết nhanh</h3>
                <ul>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="introducelogin.php">Giới thiệu</a></li>
                    <li><a href="newslogin.php">Tin tức</a></li>
                    <li><a href="contactlogin.php">Liên hệ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Dịch vụ</h3>
                <ul>
                    <li><a href="#">Giao hàng nhanh</a></li>
                    <li><a href="#">Đổi trả dễ dàng</a></li>
                    <li><a href="#">Thanh toán an toàn</a></li>
                    <li><a href="#">Bảo hành chất lượng</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Liên hệ</h3>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận 1, TP.HCM</li>
                    <li><i class="fas fa-phone"></i> Hotline: 0123456789</li>
                    <li><i class="fas fa-envelope"></i> Email: AboutUs@gmail.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Sea Fruits. All rights reserved.</p>
        </div>
    </div>

    <script>
        // JavaScript for dropdown menu
        document.querySelector('.dropdown-button').addEventListener('click', function() {
            const dropdown = this.parentElement;
            dropdown.classList.toggle('active');
        });
        
        window.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.dropdown');
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        // JavaScript for product categories
        function showFruits(category) {
            let fruitList = document.getElementById("fruitList");
            let categoryTitle = document.getElementById("categoryTitle");
            let fruitItems = document.getElementById("fruitItems");

            categoryTitle.textContent = category;
            fruitItems.innerHTML = "";

            const fruits = {
                "Trái cây ngon": [
                    { name: "Dâu tây", link: "product-detail.php?name=dautay" },
                    { name: "Mận hậu", link: "product-detail.php?name=manhau" },
                    { name: "Xoài cát", link: "product-detail.php?name=xoaicat" },
                    { name: "Dưa hấu", link: "product-detail.php?name=duahau" },
                    { name: "Chôm chôm", link: "product-detail.php?name=chomchom" },
                    { name: "Ổi xá lị", link: "product-detail.php?name=oilaxi" }
                ],
                "Trái cây Việt": [
                    { name: "Mít Thái", link: "product-detail.php?name=mitthai" },
                    { name: "Sầu riêng Ri6", link: "product-detail.php?name=saurieng" },
                    { name: "Bưởi da xanh", link: "product-detail.php?name=buoidx" },
                    { name: "Bòn Bon", link: "product-detail.php?name=bonbon" },
                    { name: "Quýt đường", link: "product-detail.php?name=quytduong" },
                    { name: "Mận Hà Nội", link: "product-detail.php?name=manhn" }
                ],
                "Trái cây nhập khẩu": [
                    { name: "Nho Mỹ", link: "product-detail.php?name=nhomy" },
                    { name: "Táo Nhật", link: "product-detail.php?name=taonhat" },
                    { name: "Lê Hàn Quốc", link: "product-detail.php?name=lehan" },
                    { name: "Cherry Úc", link: "product-detail.php?name=cherryuc" },
                    { name: "Kiwi", link: "product-detail.php?name=kiwi" },
                    { name: "Lựu Ai Cập", link: "product-detail.php?name=luuaicap" }
                ]
            };

            if (fruits[category]) {
                fruits[category].forEach(fruit => {
                    let li = document.createElement("li");
                    let a = document.createElement("a");
                    a.textContent = fruit.name;
                    a.href = fruit.link;
                    li.appendChild(a);
                    fruitItems.appendChild(li);
                });
                fruitList.style.display = "block";
            }
        }

        function hideFruits() {
            let fruitList = document.getElementById("fruitList");
            fruitList.style.display = "none";
        }

        // JavaScript for downloading invoice
        function downloadInvoice(invoiceId) {
            window.location.href = `download-invoice.php?id=${invoiceId}`;
        }
    </script>
</body>
</html> 