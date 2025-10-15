<?php
session_start();
require 'connect.php' ;

// Lấy thông tin người dùng
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$user = null;

if (!empty($user_name)) {
$sql = "SELECT * FROM nguoidung WHERE user_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
}

// Lấy lịch sử mua hàng
$sql = "SELECT h.*, c.total_amount, c.quantity, c.unit_price, s.product_name, s.product_image 
        FROM hoadon h 
        JOIN chitiethoadon c ON h.order_id = c.order_id 
        JOIN sanpham s ON c.product_id = s.product_id 
        WHERE h.user_name = ? 
        ORDER BY h.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../styles/grid.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
    <title>Lịch Sử Mua Hàng - SEA FRUITS</title>
    <style>
        /* Order History Styles */
        .history-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .history-title {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .history-title i {
            color: #4CAF50;
        }

        .order-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            transition: transform 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
        }

        .order-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-date {
            color: #666;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .order-items {
            padding: 25px;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 25px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        .item-price {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 5px;
        }

        .item-quantity {
            color: #666;
            font-size: 0.95em;
        }

        .order-total {
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            text-align: right;
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }

        .no-orders {
            text-align: center;
            padding: 50px 20px;
            color: #666;
            font-size: 1.2em;
            background: #f8f9fa;
            border-radius: 15px;
            margin: 20px 0;
        }

        .no-orders i {
            font-size: 3em;
            color: #ddd;
            margin-bottom: 15px;
            display: block;
        }

        .order-number {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding: 0 20px 20px;
        }

        .order-action-btn {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .view-details-btn {
            background: #4CAF50;
            color: white;
        }

        .view-details-btn:hover {
            background: #45a049;
        }

        .cancel-order-btn {
            background: #dc3545;
            color: white;
        }

        .cancel-order-btn:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .order-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .item-image {
                width: 100%;
                height: 200px;
            }

            .order-actions {
                flex-direction: column;
            }

            .order-action-btn {
                width: 100%;
            }
        }

        /* Existing styles remain unchanged */
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
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
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

        /* News container styles */
        .news-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .featured-news h2, .news-category h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .featured-article {
            display: flex;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        .featured-article img {
            width: 45%;
            object-fit: cover;
        }
        .category-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .category-btn {
            padding: 8px 20px;
            border: 2px solid #4CAF50;
            background: white;
            color: #4CAF50;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .category-btn.active, .category-btn:hover {
            background: #4CAF50;
            color: white;
        }
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        .news-item {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .news-item:hover {
            transform: translateY(-5px);
        }
        .news-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .news-content {
            padding: 20px;
        }
        .news-tag {
            background: #4CAF50;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            display: inline-block;
            margin-bottom: 10px;
        }
        .news-content h4 {
            color: #333;
            margin: 10px 0;
            font-size: 1.2em;
        }
        .date {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .date i {
            margin-right: 5px;
        }
        .read-more {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .read-more:hover {
            text-decoration: underline;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .featured-article {
                flex-direction: column;
            }
            .featured-article img {
                width: 100%;
                height: 200px;
            }
            .category-buttons {
                justify-content: center;
            }
            .news-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animation and performance optimizations */
        .news-item {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading state */
        .news-container {
            min-height: 400px;
            position: relative;
        }
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Performance optimization */
        .news-grid {
            contain: content;
            will-change: transform;
        }

        /* Touch targets optimization */
        @media (pointer: coarse) {
            .category-btn,
            .read-more,
            .share-btn {
                min-height: 44px;
                min-width: 44px;
                padding: 12px 20px;
            }
            .article-meta span {
                padding: 8px 0;
            }
        }

        /* Read more button */
        .read-more-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .read-more-btn:hover {
            background-color: #45a049;
        }

        .modal {
            display: none; /* Ban đầu ẩn */
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
        /* Nội dung popup */
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
        /* Hiệu ứng mở popup */
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
        .dropdown-button i{
            color: #333;
        }
        .dropdown-button span{
            color: #333;
        }
        .modal {
    display: none; /* Ban đầu ẩn */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* đậm hơn 1 chút */
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px); /* làm nền sau hơi mờ */
}

/* Nội dung popup */
.modal-content {
    background: white;
    padding: 25px;
    border-radius: 16px; /* bo góc nhiều hơn cho mềm mại */
    width: 340px;
    text-align: center;
    position: relative;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25); /* bóng đổ đẹp và sâu hơn */
    border: 1px solid rgba(0, 0, 0, 0.1); /* viền nhẹ cho rõ form */
    animation: fadeIn 0.4s ease;
    transform: scale(1);
}

/* Animation */
@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}


        /* Nút đóng popup */
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

        /* Tùy chỉnh select */
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Nút lọc */
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

    <div class="history-container">
        <h2 class="history-title">
            <i class="fas fa-history"></i>
            Lịch Sử Mua Hàng
        </h2>
        
        <?php if ($orders->num_rows > 0): ?>
            <?php while($order = $orders->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-number">
                            <i class="fas fa-receipt"></i>
                            Mã đơn hàng: #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>
                        </div>
                        <div class="order-info">
                            <div class="order-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                            </div>
                            <div class="order-status <?php echo 'status-' . strtolower($order['order_status']); ?>">
                                <i class="fas fa-circle"></i>
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="order-items">
                        <div class="item">
                            <img src="../img/<?php echo htmlspecialchars($order['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($order['product_name']); ?>" 
                                 class="item-image">
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($order['product_name']); ?></div>
                                <div class="item-price">
                                    <i class="fas fa-tag"></i>
                                    Giá: <?php echo number_format($order['unit_price']); ?> VNĐ
                                </div>
                                <div class="item-quantity">
                                    <i class="fas fa-shopping-cart"></i>
                                    Số lượng: <?php echo $order['quantity']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="order-total">
                        <i class="fas fa-money-bill-wave"></i>
                        Tổng tiền: <?php echo number_format($order['total_amount']); ?> VNĐ
                    </div>
                    <div class="order-actions">
                        <a href="history-detail.php?id=<?php echo $order['order_id']; ?>" class="order-action-btn view-details-btn">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <?php if ($order['order_status'] == 'Pending'): ?>
                            <button class="order-action-btn cancel-order-btn">
                                <i class="fas fa-times"></i> Hủy đơn hàng
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-shopping-bag"></i>
                <p>Bạn chưa có đơn hàng nào.</p>
                <a href="./userlogin.php" class="order-action-btn view-details-btn" style="display: inline-block; margin-top: 15px;">
                    <i class="fas fa-shopping-cart"></i> Mua sắm ngay
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="logo" style="color:#444444;padding-bottom:30px ; height: 150px;">
        <img src="../img/seafruits-logo.png" alt="seafruits-logo">
        <div style="padding:10px;">
            <div>Hotline: 0123456789</div>
            <div>Email: AboutUs@gmail.com</div>
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