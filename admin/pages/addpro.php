<?php
session_name('ADMINSESSID');
session_start();

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_name']) || empty($_SESSION['user_name'])) {
    // Không cho phép truy cập trực tiếp, chuyển hướng về trang đăng nhập
    header("Location: /admin/index.php");
    exit();
}

// Kiểm tra xem người dùng có quyền admin không
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Nhân viên') {
    // Không có quyền admin, chuyển hướng về trang chính
    header("Location: /admin/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="./stylescss/addpro.css">
    <link rel="stylesheet" href="./stylescss/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
    <button class="toggle-sidebar" id="toggleSidebar"><i class="fas fa-bars"></i></button>
    <div class="sidebar" id="sidebar">
        <nav>
            <ul style="margin-bottom: 10px;">
                <li class="user-info">
                    <div class="img-edit">
                        <img class="img-head" src="../img/admin.jpg" alt="User Image"> 
                    </div>

                    <?php if (isset($_SESSION['fullname'])): ?>
            <div> Chào mừng trở lại, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>! </div>
        <?php endif; ?>
                </li>
            </ul>
        </nav>

        <hr>

        <a class="icon-denim" href="./usermanage.php" target="_self"> <i class="fa-solid fa-user-shield"></i></i> Quản lí người dùng</a>
        <a class="icon-denim" href="./order.php" target="_self"> <i class="fa-solid fa-cart-shopping"></i> Quản lý đơn hàng</a>
        <a class="icon-denim" href="./prolist.php" target="_self"><i class="fa-solid fa-box-archive"></i> Tất cả sản phẩm</a>
        <a class="icon-denim icon-denim-active" href="./addpro.php" target="_self"> <i class="fa-solid fa-cart-plus"></i> Thêm sản phẩm</a>
        <a class="icon-denim" href="./satistics.php" target="_self"> <i class="fa-solid fa-chart-column"></i> Thống kê tình hình</a>
        <a class="icon-denim" href="../index.php" target="_self"><i class="fa-solid fa-user-xmark"></i> Đăng xuất</a>
    </div>

    <hr>

    <div class="main" id="main">
        <header>
            <div class="web-header">
               <a href=""> <img src="../img/mau-thiet-ke-logo-trai-cay-SPencil-Agency-7.png" alt="mau-thiet-ke-logo-trai-cay-SPencil-Agency-7"></a>
            </div>
        </header>

        <div class="order-management">
            <h1 class="header-h1">Thêm sản phẩm</h1>
        </div>

        <div class="container">
            
            <form method="POST" enctype="multipart/form-data" action = "../controllers/save_product.php">
                <div class="form-group-row">
                    <div class="form-group">
                        <label for="product-code">Mã sản phẩm</label>
                        <input type="text" id="product-code" name="product-code">
                    </div>
                    <div class="form-group">
                        <label for="product-name">Tên sản phẩm</label>
                        <input type="text" id="product-name" name="product-name">
                    </div>
                    <div class="form-group">
                        <label for="type">Danh mục</label>
                            <select id="type" name="type">
                                <option value="">-- Chọn danh mục --</option>
                                <option value="Trái cây Ngon">Trái cây Ngon</option>
                                <option value="Trái cây Việt">Trái cây Việt</option>
                                <option value="Trái cây Nhập Khẩu">Trái cây Nhập Khẩu</option>
                            </select>
                    </div>
                </div>
                
                <div class="form-group-row">
                    <div class="form-group">
                        <label for="price">Giá bán</label>
                        <input type="text" id="price" name="price">
                    </div>
                    <div class="form-group">
                        <label for="status">Tình trạng</label>
                        <select id="status" name="status">
                            <option value="">-- Chọn tình trạng --</option>
                            <option value="Hiển thị">Hiển thị</option>
                            <option value="Ẩn">Ẩn</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product-image">Ảnh sản phẩm</label>
                        <div class="image-upload-container">
                            <input  type="file" id="product-image" name="product-image" accept="image/*" onchange="previewImage(this)"> 
                            <div id="image-preview" class="image-preview">
                                <p>Hình ảnh</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="reset" class="btn-cancel" onclick=""><i class="fa-solid fa-rotate-right"></i> Hủy bỏ</button>
                    <button type="submit" class="btn-save" onclick=""><i class="fa-solid fa-download"></i> Lưu lại</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/product.js"></script>
    <script>
         $(document).ready(function() {
        $("#toggleSidebar").click(function() {
            $("#sidebar").toggleClass("active");
        });
        
        $(document).click(function(event) {
            if (!$(event.target).closest('#sidebar, #toggleSidebar').length && $('#sidebar').hasClass('active')) {
                $("#sidebar").removeClass("active");
            }
        });
    });
       </script>
    
</body>
</html>
