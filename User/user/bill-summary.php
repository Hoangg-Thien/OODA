
<?php
session_start();
require_once('connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_name'])) {
    header("Location: login-user.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// Lấy thông tin người dùng
$user_query = "SELECT * FROM nguoidung WHERE user_name = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Lấy danh sách hóa đơn
$sql = "SELECT h.order_id as invoice_id, h.order_status as order_status, h.order_date, h.PaymentMethod as payment_method, 
        CONCAT(h.address, ', ', h.district, ', ', h.province) as shipping_address, h.phone,
        GROUP_CONCAT(CONCAT(s.product_name, '|', c.quantity, '|', s.product_price, '|', s.product_image) SEPARATOR '||') as order_items
                 FROM hoadon h
        JOIN chitiethoadon c ON h.order_id = c.order_id
        JOIN sanpham s ON c.product_id = s.product_id
        WHERE h.user_name = ?
        GROUP BY h.order_id
                 ORDER BY h.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$invoices = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tóm Tắt Hóa Đơn - SEA FRUITS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
    
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
    <style>

.status-unconfirmed {
    background-color: #cce5ff; /* Xanh dương nhạt */
    color: #004085;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.status-cancelled {
    background-color: #f8d7da; /* Hồng nhạt */
    color: #721c24;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.status-completed {
    background-color: #d4edda; /* Xanh lá nhạt */
    color: #155724;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}


.invoice-item {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
        
        /* Header styles */
        .search-container {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        #searchBox {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 80%;
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        #searchResults {
            border: 1px solid #ccc;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: none;
        }

        #searchResults a {
            display: block;
            padding: 5px;
            color: #333;
            text-decoration: none;
            margin-bottom: 5px;
        }

        #searchResults a:hover {
            text-decoration: underline;
        }

        #priorityFruits {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f0f0f0;
            display: none;
            border-radius: 5px;
        }

        #priorityFruits ul {
            list-style-type: none;
            padding: 0;
        }

        #priorityFruits li {
            margin-bottom: 8px;
        }

        #priorityFruits a {
            color: blue;
            text-decoration: none;
        }

        #priorityFruits a:hover {
            text-decoration: underline;
        }

        .search-result {
            display: block;
            margin: 5px 0;
            color: #0066cc;
            text-decoration: none;
        }

        .search-result:hover {
            text-decoration: underline;
        }

        .empty {
            color: red;
            font-weight: bold;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }

        .dropdown.active .dropdown-menu {
            display: block;
        }

        .dropdown-button i {
            color: #333;
        }

        .dropdown-button span {
            color: #333;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #555;
        }

        .close:hover {
            color: red;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: green;
            color: white;
            border: none;
            padding: 8px 12px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .autocomplete-suggestions {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            background-color: white;
            position: absolute;
            z-index: 1000;
            width: 250px;
        }

        .autocomplete-suggestions div {
            padding: 8px;
            cursor: pointer;
        }

        .autocomplete-suggestions div:hover {
            background-color: #f0f0f0;
        }

        .result-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Invoice styles */
        .invoice-container {
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

        .invoice-filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1.5rem;
            border: 2px solid var(--primary-color);
            border-radius: 20px;
            background: none;
            color: var(--primary-color);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: var(--primary-color);
            color: white;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .invoice-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .invoice-item {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .invoice-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .invoice-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .invoice-number {
            font-weight: 600;
            color: var(--primary-color);
        }

        .invoice-date {
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .invoice-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-group {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .detail-label {
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .detail-value {
            font-weight: 500;
        }

        .invoice-items {
            margin-top: 1rem;
        }

        .item-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
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

        .item-price {
            color: var(--primary-color);
            font-weight: 500;
        }

        .invoice-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border-color);
            font-weight: 600;
        }

        .invoice-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
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

        .view-btn {
            background: var(--primary-color);
            color: white;
        }

        .download-btn {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .page-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .invoice-container {
                margin: 1rem;
                padding: 1rem;
            }

            .invoice-title {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .invoice-filters {
                justify-content: center;
            }

            .invoice-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .invoice-details {
                grid-template-columns: 1fr;
            }

            .invoice-actions {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }
        /* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    transition: all 0.3s ease;
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 5px;
    position: relative;
    animation: modalfade 0.3s;
}

@keyframes modalfade {
    from {transform: translateY(-50px); opacity: 0;}
    to {transform: translateY(0); opacity: 1;}
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 15px;
    top: 10px;
}

.close:hover {
    color: black;
}

.modal-content label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
}

.modal-content select {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.modal-content button[type="submit"] {
    background-color: green;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 15px;
}

.modal-content button[type="submit"]:hover {
    background-color: darkgreen;
}

/* Show modal when active */
.modal.show {
    display: block;
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
        <div style="display: flex; align-items: center; padding: 10px;">
         <div class="product-category">DANH MỤC SẢN PHẨM 
             <ul>
                    <li><a href="declious-fruits.php">Trái cây ngon</a></li>
                    <li><a href="Vietnamese-fruits.php">Trái cây Việt</a></li>
                    <li><a href="Imported-fruits.php">Trái cây nhập khẩu</a></li>
             </ul>
         </div>
         
         <div class="menu">  
                <a href="./userlogin.php">Trang chủ</a>  
                <a href="./introducelogin.php">Giới thiệu</a>  
                <a href="./newslogin.php">Tin tức</a>  
                <a href="./contactlogin.php">Liên hệ</a> 
                <a href="./cart-user.php" target="_blank" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>  
                    <span id="cart-count" style="margin-left: 5px; font-weight: bold;">
    <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
</span>
             </a>
         </div>
        <div class="search-container">
                <form action="./searchProducts.php" method="GET">
                <!-- Tìm kiếm đơn giản -->
                <div>
                    <input type="text" name="search" id="searchInput" placeholder="Nhập tên sản phẩm..." autocomplete="off" required>
                    <div id="suggestBox" class="autocomplete-suggestions"></div>
                    <button type="submit">Tìm kiếm</button>
                    <button type="button" id="toggleSearch">Tìm kiếm nâng cao</button>
                </div>
            
                <!-- Kết quả -->
                <div id="searchResults"></div>
            
                <div id="priorityFruits" class="hidden">
                    <ul>
                    </ul>
                </div>   
                </form>
                <!-- Popup tìm kiếm nâng cao -->
                <form action="./toggleSearch.php" method="GET">
    <div id="searchModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Tìm kiếm nâng cao</h2>

            <label>Khoảng giá:</label>
            <select id="priceRange" name="priceRange">
                <option value="">Tất cả</option>
                <option value="30-45">30k-45k</option>
                <option value="100-200">100k-200k</option>
            </select>

            <label>Sắp xếp:</label>
            <select id="sortedList" name="sortedList">
                <option value="asc">A->Z</option>
                <option value="desc">Z->A</option>
            </select>

            <button type="submit">Lọc</button>
        </div>
    </div>  
</form>

            </div>
            <?php if (isset($_SESSION['user_name'])): ?>
    <div class="dropdown ms-3" style="font-size: 1.1rem;">
        <a href="#" class="dropdown-toggle d-flex align-items-center text-dark text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user me-2"></i>
            <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="./userinfo.php">Tài khoản</a></li>
            <li><a class="dropdown-item" href="./history-user.php">Lịch sử</a></li>
            <li><a class="dropdown-item" href="./bill-summary.php">Tóm tắt hóa đơn</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="./logout.php">Đăng xuất</a></li>
        </ul>
    </div>
<?php endif; ?>


        </div>  
    </div> 

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="invoice-title">
                <h1>Tóm Tắt Hóa Đơn</h1>
            </div>
        </div>

        <div class="invoice-list">
            <?php
            if ($invoices && $invoices->num_rows > 0) {
                while ($invoice = $invoices->fetch_assoc()) {
                    $order_items = explode('||', $invoice['order_items']);
                    $total = 0;
            ?>
                <div class="invoice-item">
                    <div class="invoice-header">
                        <div class="invoice-info">
                            <span class="invoice-number">Hóa đơn #<?php echo $invoice['invoice_id']; ?></span>
                            <span class="invoice-date">Ngày đặt: <?php echo date('d/m/Y', strtotime($invoice['order_date'])); ?></span>
                           

                        </div>
                        <?php
$orderStatus = strtolower($invoice['order_status']);
$statusText = '';
$statusClass = '';

switch ($orderStatus) {
    case 'Chưa xác nhận':
        $statusText = 'Chưa xác nhận';
        $statusClass = 'status-unconfirmed';
        break;
    case 'Đã hủy':
        $statusText = 'Đã hủy';
        $statusClass = 'status-cancelled';
        break;
    case 'Giao thành công':
        $statusText = 'Giao thành công';
        $statusClass = 'status-completed';
        break;
    default:
        $statusText = ucfirst($orderStatus);
        $statusClass = 'status-unconfirmed';
}
?>

<span class="invoice-status <?php echo $statusClass; ?>">
    Tình trạng: <?php echo $statusText; ?>
</span>

                    </div>
                    <div class="invoice-details">
                        <div class="detail-group">
                            <span class="detail-label">Phương thức thanh toán</span>
                            <span class="detail-value"><?php echo $invoice['payment_method']; ?></span>
                        </div>
                        <div class="detail-group">
                            <span class="detail-label">Địa chỉ giao hàng</span>
                            <span class="detail-value"><?php echo $invoice['shipping_address']; ?></span>
                        </div>
                        <div class="detail-group">
                            <span class="detail-label">Số điện thoại</span>
                            <span class="detail-value"><?php echo $invoice['phone']; ?></span>
                        </div>
                    </div>
                    <div class="invoice-items">
                        <div class="item-list">
                            <?php
                            foreach ($order_items as $item) {
                                list($name, $quantity, $price, $image) = explode('|', $item);
                                $subtotal = $price * $quantity;
                                $total += $subtotal;
                            ?>
                                <div class="item">
                                    <div class="item-info">
                                        <img src="../img/<?php echo $image; ?>" 
                                             alt="<?php echo $name; ?>" class="item-image">
                                        <span class="item-name"><?php echo $name; ?></span>
                                    </div>
                                    <span class="item-price"><?php echo number_format($subtotal); ?>đ</span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="invoice-total">
                            <span>Tổng cộng</span>
                            <span><?php echo number_format($total); ?>đ</span>
                        </div>
                    </div>
                 
                </div>
            <?php
                }
            } else {
                echo '<p class="no-invoices">Bạn chưa có hóa đơn nào.</p>';
            }
            ?>
        </div>

        <div class="pagination">
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">4</button>
            <button class="page-btn">5</button>
            <button class="page-btn">></button>
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
                    <li><a href="./userlogin.php">Trang chủ</a></li>
                    <li><a href="./introducelogin.php">Giới thiệu</a></li>
                    <li><a href="./newslogin.php">Tin tức</a></li>
                    <li><a href="./contactlogin.php">Liên hệ</a></li>
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
    // JavaScript for filtering invoices
    function filterInvoices(status, event) {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Add active class to clicked button
        if (event && event.target) {
            event.target.classList.add('active');
        }

        // Get all invoice items
        const invoices = document.querySelectorAll('.invoice-item');

        invoices.forEach(invoice => {
            const invoiceStatus = invoice.querySelector('.invoice-status');

            if (status === 'all') {
                invoice.style.display = 'block';
            } else if (status === 'completed' && invoiceStatus.classList.contains('status-completed')) {
                invoice.style.display = 'block';
            } else if (status === 'pending' && invoiceStatus.classList.contains('status-pending')) {
                invoice.style.display = 'block';
            } else if (status === 'cancelled' && invoiceStatus.classList.contains('status-cancelled')) {
                invoice.style.display = 'block';
            } else {
                invoice.style.display = 'none';
            }
        });
    }

    // JavaScript for viewing invoice details
    function viewInvoiceDetails(invoiceId) {
        window.location.href = `invoice-detail.php?id=${invoiceId}`;
    }

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.querySelector('.dropdown-button');
        const dropdown = document.querySelector('.dropdown');

        if (dropdownBtn) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent window click from firing
                const dropdown = this.parentElement;
                dropdown.classList.toggle('active');
            });
        }

        if (dropdown) {
            window.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('searchModal');
    const btn = document.getElementById('toggleSearch');
    const span = document.getElementsByClassName('close')[0];

    if (btn) {
        btn.onclick = function() {
            modal.style.display = "block";
        };
    }

    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        };
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    // Add-to-cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = this.dataset.price;

            fetch('/User/user/cart-handle.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    product_name: productName,
                    product_price: productPrice
                })
            }).then(res => res.json())
              .then(data => {
                  if (data.success) {
                      document.querySelector('#cart-count').innerText = data.cart_count;

                      const notification = document.createElement('div');
                      notification.textContent = 'Đã thêm vào giỏ hàng';
                      Object.assign(notification.style, {
                          position: 'fixed',
                          top: '50%',
                          left: '50%',
                          transform: 'translate(-50%, -50%)',
                          backgroundColor: '#4CAF50',
                          color: '#fff',
                          padding: '16px 28px',
                          borderRadius: '10px',
                          boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
                          zIndex: 9999,
                          fontSize: '16px',
                          fontWeight: '500'
                      });
                      document.body.appendChild(notification);

                      setTimeout(() => {
                          document.body.removeChild(notification);
                      }, 2000);
                  } else {
                      alert(data.message);
                  }
              });
        });
    });

    // Autocomplete search
    const input = document.getElementById("searchInput");
    const suggestBox = document.getElementById("suggestBox");

    if (input && suggestBox) {
        input.addEventListener("keyup", function () {
            const query = input.value.trim();
            if (query.length > 0) {
                fetch(`./suggest.php?term=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestBox.innerHTML = "";
                        data.forEach(item => {
                            const div = document.createElement("div");
                            div.textContent = item;
                            div.onclick = () => {
                                input.value = item;
                                suggestBox.innerHTML = "";
                            };
                            suggestBox.appendChild(div);
                        });
                    });
            } else {
                suggestBox.innerHTML = "";
            }
        });

        document.addEventListener("click", function (e) {
            if (e.target !== input) {
                suggestBox.innerHTML = "";
            }
        });
    }

    // Dropdown
    const dropdownBtn = document.querySelector('.dropdown-button');
    if (dropdownBtn) {
        dropdownBtn.addEventListener('click', function() {
            const dropdown = this.parentElement;
            dropdown.classList.toggle('active');
        });

        window.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    }
});
</script>

</body>
</html> 