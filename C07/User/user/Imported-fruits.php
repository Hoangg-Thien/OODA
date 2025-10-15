<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    // Nếu chưa đăng nhập thì chuyển hướng về trang chính
    header("Location: Imported-fruits-nologin.php");
    exit();
}
?>
<!DOCTYPE html>  
<html lang="vi">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../styles/index.css">  
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon"> 
    <title>Trái cây Nhập Khẩu </title>  
    <style>  
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .page-link {
            margin: 0 5px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        .pagination .page-link {
            display: inline-block;
            margin: 0 5px;
            padding: 6px 12px;
            background: #ddd;
            color: #000;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.2s;
        }

        .pagination .page-link:hover {
            background: #aaa;
            color: #fff;
        }

        .pagination .page-link.active {
            background: #4CAF50;
            color: #fff;
            font-weight: bold;
        }

        .pagination .page-link.disabled {
            background: #eee;
            color: #aaa;
            pointer-events: none;
        }

        .image-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .fruit-background {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 350px;
            text-align: center;
            transition: transform 0.3s;
            background-color: white;
            border-radius: 12px;
            border: 1px solid #eee;
            padding: 15px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .fruit-background img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .fruit-background .caption {
            width: 100%;
            padding: 10px 0;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fruit-background .icons {
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
            padding: 10px 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .fruit-background:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .fruit-background:hover .icons {
            opacity: 1;
        }

        .icons a,
        .icons button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: green;
            color: white;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .icons a:hover,
        .icons button:hover {
            background-color: darkgreen;
            transform: scale(1.1);
        }

        @media screen and (max-width: 768px) {
            .image-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                padding: 15px;
            }
        }

        @media screen and (max-width: 480px) {
            .image-container {
                grid-template-columns: repeat(1, 1fr);
                gap: 15px;
                padding: 10px;
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

        <div style="display: flex; align-items: center; padding: 10px 20px;">  
            <div class="product-category">DANH MỤC SẢN PHẨM
                <ul>
                    <li><a href="./declious-fruits.php">Trái cây ngon </a></li>
                    <li><a href="./Vietnamese-fruits.php">Trái cây Việt  </a></li>
                    <li><a href="./Imported-fruits.php">Trái cây Nhập Khẩu </a></li>
                </ul>
            </div>  
            <div class="menu">  
                <a href="../user/userlogin.php" >Trang chủ</a>  
                <a href="../user/introducelogin.php">Giới thiệu</a>  
                <a href="../user/newslogin.php">Tin tức</a>  
                <a href="../user/contactlogin.php">Liên hệ</a>   
                <a href="../user/cart-user.php" target="_blank" class="cart-icon" title="Go to Cart">  
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
        <div class="list-product">  
            <h1>TRÁI CÂY NHẬP KHẨU</h1>  
        </div>  
        <?php
include("connect.php");

// Số sản phẩm mỗi trang
$limit = 6;

// Trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Offset tính từ trang
$start = ($page - 1) * $limit;

// Tổng số sản phẩm với điều kiện lọc
$totalQuery = "SELECT COUNT(*) AS total FROM sanpham WHERE product_type = 'Trái cây Nhập Khẩu'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// Truy vấn sản phẩm theo trang
$sql = "SELECT * FROM sanpham WHERE product_type = 'Trái cây Nhập Khẩu' LIMIT $start, $limit";
$result = $conn->query($sql);

// Hiển thị sản phẩm
if ($result->num_rows > 0) {
    echo '<div class="image-container">';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="fruit-background">
            <img src="../img/' . $row['product_image'] . '" alt="' . htmlspecialchars($row['product_name']) . '">
            <div class="caption">
                ' . htmlspecialchars($row['product_name']) . '<br>
                ' . number_format($row['product_price'], 0, ',', '.') . ' VND
            </div>
            <div class="icons">
                <a href="product-details.php?id=' . $row['product_id'] . '" class="info-icon" title="Xem thông tin chi tiết">
        <i class="fa-solid fa-circle-info fa-lg"></i>
      </a>
               <button class="add-to-cart"
                    data-id="' . $row['product_id'] . '"
                    data-name="' . htmlspecialchars($row['product_name']) . '"
                    data-price="' . $row['product_price'] . '"
                     data-link=" ' .  $row['product_link'] . '">
                    <i class="fas fa-cart-plus fa-lg"></i>
                </button>
            </div>
        </div>';
    }
    echo '</div>';
} else {
    echo "<p>Không có sản phẩm nào.</p>";
}

// Hiển thị phân trang
echo '<div class="pagination" style="margin-top: 20px; text-align: center;">';

// Nút "Trang trước"
if ($page > 1) {
    echo '<a href="?page=' . ($page - 1) . '" class="page-link">&lt</a>';
} else {
    echo '<span class="page-link disabled">&lt</span>';
}

// Các số trang
for ($i = 1; $i <= $totalPages; $i++) {
    echo '<a href="?page=' . $i . '" class="page-link ' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
}

// Nút "Trang sau"
if ($page < $totalPages) {
    echo '<a href="?page=' . ($page + 1) . '" class="page-link">&gt</a>';
} else {
    echo '<span class="page-link disabled">&gt</span>';
}

echo '</div>';

$conn->close();
?>

    
    </div>
        
    </div>
    <div class="policy-container" >
        <div >
            <img src="../img/policy1.png" alt="policy1">
            <div style="margin-left: -30px;padding: 10px;font-weight: 700;font-size: 20px;">Giao hàng miễn phí</div>
            <div style="margin-left:-20px;color:#444444;font-size: 14px;">Với đơn hàng hơn 300.000đ</div>
        </div>
        <div>
            <img src="../img/policy2.png" alt="policy2">
            <div style="margin-left: -20px;padding: 10px;font-weight: 700;font-size: 20px;">Hỗ trợ 24/7</div>
            <div style="margin-left:-20px;color:#444444;font-size: 14px;">Nhanh chóng thuận tiện</div>
        </div>
        <div>
            <img src="../img/policy3.jpg" alt="policy3">
            <div style="margin-left: -10px;padding: 10px;font-weight: 700;font-size: 20px;">Đổi trả trong 3 ngày</div>
            <div style="margin-left:-20px;color:#444444;font-size: 14px;">Hấp dẫn chưa từng có</div>
        </div>
        <div >
            <img src="../img/policy4.png" alt="policy2">
            <div style="margin-left: -10px;padding: 10px;font-weight: 700;font-size: 20px;">Giá tiêu chuẩn</div>
            <div style="margin-left:-10px;color:#444444;font-size: 14px;">Tiết kiệm 10% giá thị trường</div>
        </div>
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
                    <li><a href="../user/userlogin.php">Trang chủ</a></li>
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
const productLink = this.dataset.link;
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