<?php
require 'connect.php';
session_start();
$sql = "SELECT * FROM province";
$result = mysqli_query($conn, $sql);

if (isset($_POST['add_sale'])) {
    echo "<pre>";
    print_r($_POST);
    die();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon"> 
    <title>Đăng Ký Tài Khoản</title>
</head>
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

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 30px 0;
        }

        .page-link {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover:not(.disabled) {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .page-link.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .page-link.disabled {
            color: #ccc;
            pointer-events: none;
        }

        @media screen and (max-width: 768px) {
            .image-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        @media screen and (max-width: 480px) {
            .image-container {
                grid-template-columns: repeat(1, 1fr);
                gap: 15px;
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
                        <li><a href="./VietNamese-fruits-nolog.php">Trái cây Việt  </a></li>
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
            
            <!-- Dropdown Xin chào, Khách -->
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
        <style>
 body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f9f9f9;
  margin: 0;
  padding: 0;
}

.form-container {
  background-color: #fff;
  max-width: 480px;
  margin: 40px auto;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}

.form-container h2 {
  text-align: center;
  margin-bottom: 25px;
  color: #333;
}

.input-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 16px;
}


.input-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  color: #444;
}

.input-group input,
.input-group select {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s ease;
}

.input-group input:focus,
.input-group select:focus {
  border-color: #28a745;
  outline: none;
  box-shadow: 0 0 4px rgba(40, 167, 69, 0.3);
}

.input-group select {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  background-color: #fff;
  transition: border 0.3s ease;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg%20viewBox%3D'0%200%204%205'%20xmlns%3D'http%3A//www.w3.org/2000/svg'%3E%3Cpath%20d%3D'M2%200L0%202h4L2%200z'%20fill%3D'%23666'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 10px;
}

.input-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  color: #333;
}

.required-star {
    color: red;
    margin-left: 2px;
}



button[type="submit-1"] {
  width: 100%;
  background-color: #28a745;
  color: #fff;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button[type="submit-1"]:hover {
  background-color: #218838;
}

.form-container p {
  text-align: center;
  margin-top: 16px;
  color: #333;
}

.form-container a {
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
}

.form-container a:hover {
  text-decoration: underline;
}

        </style>
   <div class="form-container">
    <h2>Đăng Ký Tài Khoản</h2>
    <form id="registration-form" action="reg.php" method="POST">
        <div class="input-group">
            <label for="fullname">Họ và Tên <span class="required-star">*</span></label>
            <input type="text" id="fullname" name="fullname" required>
        </div>
        <div class="input-group">
            <label for="user_name">Tên Đăng Nhập <span class="required-star">*</span></label>
            <input type="text" id="user_name" name="user_name" required>
        </div>
        <div class="input-group">
            <label for="user_email">Email <span class="required-star">*</span></label>
            <input type="email" id="user_email" name="user_email" required>
        </div>
        <div class="input-group">
            <label for="hashPass">Mật Khẩu <span class="required-star">*</span></label>
            <input type="password" id="hashPass" name="hashPass" required>
        </div>
        <div class="input-group">
    <label for="confirmPassword">Xác Nhận Mật Khẩu <span class="required-star">*</span></label>
    <input type="password" id="confirmPassword" name="confirmPassword" required>
</div>

        <div class="input-group">
            <label for="phone">Số Điện Thoại <span class="required-star">*</span></label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        <div class="input-group">
            <label for="user_address">Địa Chỉ <span class="required-star">*</span></label>
            <input type="text" id="user_address" name="user_address" required>
        </div>
          <div class="input-group">
    <label for="province">
        Tỉnh/Thành phố <span class="required-star">*</span>
    </label>
    <select id="province" name="province" class="form-control" required style="width: 100%;">
        <option value="">Chọn một tỉnh</option>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <option value="<?php echo $row['province_id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
    </select>
</div>

<div class="input-group">
    <label for="district">
        Quận/Huyện <span class="required-star">*</span>
    </label>
    <select id="district" name="district" class="form-control" required style="width: 100%;">
        <option value="">Chọn một quận/huyện</option>
    </select>
</div>

          
          <button type="submit-1" name="submit-1">Đăng Ký</button>
          <p>Bạn đã có tài khoản? <a target="_blank" href="../user/login-user.php">Đăng nhập ngay</a></p>
        </form>
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
                    <li><a href="./contact.php">Liên hệ</a></li>
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
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const fields = {
        fullname: "Họ tên",
        user_name: "Tên đăng nhập",
        user_email: "Email",
        hashPass: "Mật khẩu",
        confirmPassword :"Xác Nhận Mật Khẩu",
        phone: "Số điện thoại",
        user_address: "Địa chỉ",
        district: "Quận/Huyện",
        province: "Tỉnh/Thành phố" // đã đổi từ "city" thành "province" cho đúng HTML
    };

    const errorMessages = {};

    Object.keys(fields).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input) return;

        const errorElement = document.createElement("p");
        errorElement.style.color = "red";
        errorElement.style.fontSize = "14px";
        errorElement.style.marginTop = "4px";
        errorElement.style.display = "none";

        // Chèn error message NGAY SAU input/select
        input.insertAdjacentElement("afterend", errorElement);
        errorMessages[field] = errorElement;

        input.addEventListener("input", () => validateField(field, input));
        input.addEventListener("blur", () => validateField(field, input));
    });

    function validateField(field, input) {
        const value = input.value.trim();
        let error = "";

        input.parentNode.classList.remove('error');

        if (!value) {
            error = "Ô này không được để trống!";
        } else {
            if (field === "user_email") {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) error = "Email không hợp lệ!";
            }

            if (field === "hashPass") {
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
                if (!passwordRegex.test(value)) {
                    error = "Mật khẩu phải có ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt!";
                }
            }

            if (field === "user_name") {
                const usernameRegex = /^[a-zA-Z0-9_-]+$/;
                if (!usernameRegex.test(value)) {
                    error = "Tên đăng nhập chỉ gồm chữ, số, _ hoặc -, không có khoảng trắng!";
                }
            }
        }

        if (error) {
            errorMessages[field].textContent = error;
            errorMessages[field].style.display = "block";
            input.parentNode.classList.add('error');
        } else {
            errorMessages[field].style.display = "none";
        }
    }

    form.addEventListener("submit", function (e) {
        let hasError = false;

        Object.keys(fields).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input) return;
            validateField(field, input);
            if (errorMessages[field].style.display === "block") hasError = true;
        });

        if (hasError) e.preventDefault();
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById("province");
    const districtSelect = document.getElementById("district");

    provinceSelect.addEventListener("change", function () {
        const provinceId = this.value;

        // Xóa các tùy chọn cũ trong danh sách quận/huyện
        districtSelect.innerHTML = '<option value="">Chọn một quận/huyện</option>';

        if (provinceId) {
            // Gửi yêu cầu AJAX để lấy danh sách quận/huyện
            fetch(`ajax_get_district.php?province_id=${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(district => {
                        const option = document.createElement("option");
                        option.value = district.id;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Lỗi khi tải quận/huyện:", error));
        }
    });
});
const input = document.getElementById("searchInput");
    const suggestBox = document.getElementById("suggestBox");
    
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
    function confirmAddToCart(productId) {
        if (confirm("Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng. Bạn có muốn đăng nhập không?" )) {
            window.location.href = "./user/login-user.php";
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