<?php
session_start();


require 'connect.php';

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

$product_id = $_GET['id'] ?? '';

if (empty($product_id)) {
    die("Không tìm thấy ID sản phẩm.");
}


$sql = sprintf(
    "SELECT product_id, product_name, product_price, product_image, productnolog_link FROM sanpham WHERE product_id = '%s'",
    mysqli_real_escape_string($conn, $product_id)
);
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Lỗi truy vấn SQL: " . mysqli_error($conn));
}

if ($row = mysqli_fetch_assoc($result)) {
    $product_name = $row['product_name'];
    $product_price = number_format($row['product_price'], 0, ',', '.') . ' VND';
    $product_image = $row['product_image'];
} else {
    die("Không tìm thấy sản phẩm.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
     <link rel="stylesheet" href="../styles/index.css">
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
    <title>Chi tiết sản phẩm</title>
    
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


    <div class="sea-fruit-container" >  
        <div>  
            <div class="sea-fruit">SEA FRUITS</div>  
        </div>  

        <div style="display: flex; align-items: center; padding: 10px 20px;">  
            <div class="product-category">DANH MỤC SẢN PHẨM 
                <ul>
                    <li><a href="./declious-fruits-nologin.php">Trái cây ngon </a></li>
                    <li><a href="./Vietnamese-fruits-nolog.php">Trái cây Việt  </a></li>
                    <li><a href="./Imported-fruits-nologin.php">Trái cây Nhập Khẩu </a></li>
                </ul>
            </div>  
            
            <div class="menu">  
                <a href="/index.php">Trang chủ</a>  
                <a href="./introduce.php">Giới thiệu</a>  
                <a href="./news.php">Tin tức</a>  
                <a href="./contact.php">Liên hệ</a> 
                <a href="./cartusernologin.php" target="_blank" class="cart-icon" title="Go to Cart">
  <i class="fas fa-shopping-cart"></i>  
                    <span id="cart-count" style="margin-left: 5px; font-weight: bold;">0</span>  
                </a>
            </div> 
             
           <div class="search-container">
                <form action="./searchProducts-nologin.php" method="GET">
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
                <form action="./toggleSearch-nologin.php" method="GET">
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
            <div class="dropdown ms-3" style="font-size: 1.1rem;">
    <a href="#" class="dropdown-toggle d-flex align-items-center text-dark text-decoration-none" id="guestDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user me-2"></i>
        <span>Xin chào, <strong>Khách</strong>!</span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="guestDropdown">
        <li><a class="dropdown-item" href="./login-user.php" target="_blank">Đăng nhập</a></li>
        <li><a class="dropdown-item" href="./regis.php" target="_blank">Đăng ký</a></li>
    </ul>
</div>
        </div>  
    </div> 
    

<?php
echo '
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="../img/' . $product_image . '" class="img-fluid rounded-start" alt="' . $product_name . '">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h2 class="card-title text-primary">' . $product_name . '</h2>
                    <p class="card-text text-danger fw-bold" style="font-size: 1.5rem;">Giá: ' . $product_price . '</p>
                    
                        <input type="hidden" name="product_id" value="' . $product_id . '">
                        
                        <button class="add-to-cart-btn" title="Thêm vào giỏ hàng" onclick="confirmAddToCart(\'' . $row['product_id'] . '\')">
                                <i class="fas fa-cart-plus fa-lg"></i>
                            </button>
                    
                </div>
            </div>
        </div>
    </div>
</div>';
?>


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
                    <li><a href="/index.php">Trang chủ</a></li>
                    <li><a href="./introduce.php">Giới thiệu</a></li>
                    <li><a href="./news.php">Tin tức</a></li>
                    <li><a href="./contact.html">Liên hệ</a></li>
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
</body>

<script>
    
  const input = document.getElementById("searchInput");
    const suggestBox = document.getElementById("suggestBox");
    
    input.addEventListener("keyup", function () {
        const query = input.value.trim();
        if (query.length > 0) {
            fetch(`suggest.php?term=${encodeURIComponent(query)}`)
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


    function confirmAddToCart(productId) {
        if (confirm("Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng. Bạn có muốn đăng nhập không?" )) {
            window.location.href = "../user/login-user.php";
        } else {
            alert("Bạn đã hủy thêm sản phẩm vào giỏ hàng.");
            addToCart(productId);   
        }
    }

    document.getElementById("toggleSearch").addEventListener("click", function () {
        document.getElementById("searchModal").style.display = "flex";
    });

    document.querySelector(".close").addEventListener("click", function () {
        document.getElementById("searchModal").style.display = "none";
    });

    // Đóng khi nhấn ra ngoài modal
    window.onclick = function (event) {
        let modal = document.getElementById("searchModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
</script>
</html>