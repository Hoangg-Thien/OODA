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
    <title>Đăng Nhập</title>
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
  
<style>/* Container căn giữa form */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 70vh;
    font-family: Arial, sans-serif;
}

/* Bố cục của form */
.login_container {
    width: 500px;
    padding: 50px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Tiêu đề */
.login_title span {
    display: block;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}

/* Icon bên phải input */
.icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #333;
    cursor: pointer;
}

/* Nút đăng nhập */
.input-submit {
    width: 100%;
    padding: 10px;
    background-color: green;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}

.input-submit:hover {
    background-color: darkgreen;
}

/* Đăng ký */
.signup {
    text-align: center;
    margin-top: 10px;
}

.signup a {
    color: blue;
    text-decoration: none;
}

.signup a:hover {
    text-decoration: underline;
}
.input_wrapper {
    position: relative;
    margin-bottom: 20px;
}

/* Label bên trái */
.label-left {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

/* Input */
.input_field {
    width: 100%;
    padding: 10px 40px 10px 10px; /* chừa bên phải cho icon */
    font-size: 16px;
    border: 1px solid #999;
    border-radius: 4px;
}

/* Icon bên phải */
.icon-right {
    position: absolute;
    right: 10px;
    top: 65%;
    transform: translateY(-50%);
    color: #333;
    cursor: pointer;
}

/* Icon toggle mắt sẽ lệch vào thêm */
.toggle-password {
    right: 10px;
}

</style>

      <div class="container">
        <div class="login_container">
            <div class="login_title">
                <span>Đăng nhập</span>
            </div>

            <form action="login.php" method="POST">
                <div class="input_wrapper">
    <label for="user" class="label-left">Tên Đăng Nhập</label>
    <input type="text" name="user_name" id="user" class="input_field" required>
    <i class="fa-regular fa-user icon-right"></i>
</div>

<div class="input_wrapper">
    <label for="password" class="label-left">Mật Khẩu</label>
    <input type="password" name="hashPass" id="password" class="input_field" required>
    <i class="fa-solid fa-lock icon-right"></i>
    <i class="fa-solid fa-eye-slash icon-right toggle-password" onclick="togglePasswordVisibility()" style="right: 35px;"></i>
</div>

                <div class="input_wrapper">
                    <button type="submit" name="submit" class="input-submit">Đăng Nhập</button>
                </div>
            </form>

            <div class="signup">
                <span>Bạn chưa có tài khoản? <a target="_blank" href="../user/regis.php">Đăng Ký Ngay</a></span>
            </div>
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
    function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }
</script>


</html>