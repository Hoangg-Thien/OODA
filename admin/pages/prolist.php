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
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Quản lý') {
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
    <title>Tất cả sản phẩm</title>
    <link rel="stylesheet" href="./stylescss/prolist.css">
    <link rel="stylesheet" href="./stylescss/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        body.modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }
    </style>
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

        <ul class="sidebar-menu">
        <a class="icon-denim" href="./usermanage.php" target="_self"> <i class="fa-solid fa-user-shield"></i></i> Quản lí người dùng</a>
        <a class="icon-denim" href="./order.php" target="_self"> <i class="fa-solid fa-cart-shopping"></i> Quản lý đơn hàng</a>
        <a class="icon-denim icon-denim-active" href="./prolist.php" target="_self"><i class="fa-solid fa-box-archive"></i> Tất cả sản phẩm</a>
        <a class="icon-denim" href="./addpro.php" target="_self"> <i class="fa-solid fa-cart-plus"></i> Thêm sản phẩm</a>
        <a class="icon-denim" href="./satistics.php" target="_self"> <i class="fa-solid fa-chart-column"></i> Thống kê tình hình</a>
        <a class="icon-denim" href="../index.php" target="_self"><i class="fa-solid fa-user-xmark"></i> Đăng xuất</a>
        </ul>
    </div>

    <hr>

    <main class="main" id="main">
        <div class="web-header">
            <a href=""> <img src="../img/mau-thiet-ke-logo-trai-cay-SPencil-Agency-7.png"
                    alt="mau-thiet-ke-logo-trai-cay-SPencil-Agency-7"></a>
        </div>

        <div class="order-management">
            <h1 style="font-weight: bold;">Danh Sách Sản Phẩm</h1>
        </div>
        
            <a href="./addpro.php"><button style="outline: none;" class="btn green"><i class="fa-solid fa-plus"></i> Tạo mới sản phẩm</button></a>

        <br>

        <table>
            <thead>
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Ảnh</th>
                    <th>Giá</th>
                    <th>Danh mục</th>
                    <th>Chức năng</th>
                </tr>
            </thead>
            <tbody id="productTable">
            </tbody>
        </table>
    </main>

    <nav aria-label="Page navigation " class="page-center">
        <ul class="pagination justify-content-center" id="pagination">
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Lùi" onclick="changePage(currentPage - 1)">
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(1)">1</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(2)">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(3)">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Tiếp" onclick="changePage(currentPage + 1)">
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
        </ul>
    </nav>
    

    <!--delete-->
    <div class="modal fade" id="ModalRM" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xác Nhận Xóa Sản Phẩm</h5>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Hủy Bỏ</button>
                    <button type="button" class="btn btn-success" id="confirmDelete">Đồng Ý</button>
                </div>
            </div>
        </div>
    </div>

    <!--edit-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa thông tin sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="control-label">Mã sản phẩm</label>
                        <input class="form-control" type="text" id="product-code" readonly value="">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Tên sản phẩm</label>
                        <input class="form-control" type="text" id="product-name">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Giá bán</label>
                        <input class="form-control" type="text" id="product-price">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Trạng thái</label>
                        <select class="form-control" id="product-status">
                            <option value="Hiển thị">Hiển thị</option>
                            <option value="Ẩn">Ẩn</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Danh mục</label>
                        <select class="form-control" id="product-type">
                            <option value="Trái Cây Nhập Khẩu">Trái Cây Nhập Khẩu</option>
                            <option value="Trái Cây Việt">Trái Cây Việt</option>
                            <option value="Trái Cây Ngon">Trái Cây Ngon</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Ảnh</label>
                        <img class="d-block mb-2" id="product-image" src="" alt="Hình ảnh sản phẩm" width="100%">
                        <input type="file" id="image-upload" style="display: none;">
                        <input type="button" id="edit-image" value="Chỉnh sửa">
                        <input type="button" id="delete-image" value="Xoá ảnh">
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelBtn">Hủy bỏ</button>
                    <button type="button" class="btn btn-primary" id="saveBtn">Lưu lại</button>

                </div>
            </div>
        </div>
    </div>
<script src="../js/editpic.js"></script>
<script src="../js/pagenavi.js"></script>
<script src="../js/update.js"></script>
<script src="../js/deletepic.js"></script>
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