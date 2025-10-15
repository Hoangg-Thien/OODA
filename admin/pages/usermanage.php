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

require 'connect.php';

$sql = "SELECT user_name, fullname, user_address, user_email, phone, user_role, user_status, district, province FROM nguoidung";
$result = $conn->query($sql);

// Lấy trang hiện tại từ URL, mặc định là 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 4; 
$offset = ($page - 1) * $limit;

$sql = "SELECT user_name, fullname, user_address, user_email, phone, user_role, user_status, district, province FROM nguoidung LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Lấy tổng số người dùng để tính số trang
$totalSql = "SELECT COUNT(*) as total FROM nguoidung";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalUsers = $totalRow['total'];
$totalPages = ceil($totalUsers / $limit);

$where_clause = "";
if (isset($_GET['province']) && !empty($_GET['province'])) {
    $province_id = $_GET['province'];
    // Lấy tên tỉnh/thành từ ID
    $province_sql = "SELECT name FROM province WHERE province_id = '$province_id'";
    $province_result = mysqli_query($conn, $province_sql);
    if ($province_result && mysqli_num_rows($province_result) > 0) {
        $province_data = mysqli_fetch_assoc($province_result);
        $province_name = mysqli_real_escape_string($conn, trim($province_data['name']));
        $where_clause .= " AND (hd.province LIKE '%$province_name%' OR hd.address LIKE '%$province_name%')";
    }
}

if (isset($_GET['district']) && !empty($_GET['district'])){
    $district_id = $_GET['district'];
    // Lấy tên quận/huyện từ ID
    $district_sql = "SELECT name FROM district WHERE district_id = '$district_id'";
    $district_result = mysqli_query($conn, $district_sql);
    if ($district_result && mysqli_num_rows($district_result) > 0) {
        $district_data = mysqli_fetch_assoc($district_result);
        $district_name = mysqli_real_escape_string($conn, trim($district_data['name']));
        $where_clause .= " AND (hd.district LIKE '%$district_name%' OR hd.address LIKE '%$district_name%')";
    }
}

// Lấy danh sách tỉnh/thành phố cho form thêm người dùng
$province_sql = "SELECT province_id, name FROM province ORDER BY name";
$province_result = $conn->query($province_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lí người dùng</title>
    <link rel="stylesheet" href="./stylescss/usermanage.css">
    <link rel="stylesheet" href="./stylescss/responsiveuser.css">
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
    <script>
        var currentUsername = "<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>";
        console.log("User session hiện tại:", currentUsername);
        
        // Hiển thị giá trị session cho debugging
        <?php if (isset($_SESSION['user_name'])): ?>
        console.log("PHP session user_name: <?php echo htmlspecialchars($_SESSION['user_name']); ?>");
        <?php else: ?>
        console.log("Không có session user_name");
        <?php endif; ?>
    </script>
</head>

<body>
    <button class="toggle-sidebar" id="toggleSidebar"><i class="fas fa-bars"></i></button>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
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
        </div>

        <hr>

        <ul class="sidebar-menu">
            <a class="icon-denim icon-denim-active" href="./usermanage.php" target="_self"> <i
                    class="fa-solid fa-user-shield"></i> <span>Quản lí người dùng</span></a>
            <a class="icon-denim" href="./order.php" target="_self"> <i class="fa-solid fa-cart-shopping"></i> Quản lý
                đơn hàng</a>
            <a class="icon-denim" href="./prolist.php" target="_self"><i class="fa-solid fa-box-archive"></i> Tất cả
                sản phẩm</a>
            <a class="icon-denim" href="./addpro.php" target="_self"> <i class="fa-solid fa-cart-plus"></i> Thêm sản
                phẩm</a>
            <a class="icon-denim" href="./satistics.php" target="_self"> <i class="fa-solid fa-chart-column"></i> Thống
                kê tình hình</a>
            <a class="icon-denim" href="../index.php" target="_self"><i class="fa-solid fa-user-xmark"></i> Đăng
                xuất</a>
        </ul>
    </div>

    <hr>

    <main class="main" id="main">

        <div class="web-header">
            <a href=""> <img src="../img/mau-thiet-ke-logo-trai-cay-SPencil-Agency-7.png"
                    alt="mau-thiet-ke-logo-trai-cay-SPencil-Agency-7"></a>
        </div>

        <div class="order-management">
            <h1 style="font-weight: bold;">Danh Sách Người Dùng</h1>
        </div>
            <button style="outline: none; margin-bottom: 24px;" class="btn green1" onclick=""><i class="fa-solid fa-plus"></i> Thêm
                mới</button>

            <div class="table-responsive" style="overflow-x: auto; width: 100%;">
            <table>
                <thead>
                    <tr>
                        <th>Tên người dùng</th>
                        <th>Họ và tên</th>
                        <th>Địa chỉ</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row["user_name"]; ?></td>
                        <td><?php echo $row["fullname"]; ?></td>
                        <td><?php echo $row["user_address"] . ", " . $row["district"] . ", " . $row["province"]; ?></td>
                        <td><?php echo $row["user_email"]; ?> </td>
                        <td><?php echo $row["phone"]; ?></td>
                        <td><?php echo $row["user_role"]; ?></td>
                        <td><?php echo $row["user_status"]; ?></td>
                        <td>
                            <button style="outline: none;" class="btn delete" onclick=""><i
                                class="fa-solid fa-lock-open"></i></button>
                            <button style="outline: none;" class="btn gear" onclick=""><i class="fa fa-edit"></i></button>
                            <button style="outline: none;" class="btn lock" onclick=""><i
                                class="fa-solid fa-lock"></i></button>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align:center'>Không có dữ liệu người dùng</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
                </table>
            </div>
    </main>

    <<nav aria-label="Page navigation " class="page-center">
    <ul class="pagination justify-content-center" id="pagination">
        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>" style="<?= $page == 1 ? 'display:none;' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Lùi">
                <span aria-hidden="true">&lt</span>
            </a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>" style="<?= $page == $totalPages ? 'display:none;' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Tiếp">
                <span aria-hidden="true">&gt</span>
            </a>
        </li>
    </ul>
</nav>

    <!--mo-->
    <div class="modal fade" id="ModalRM" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xác Nhận Mở Khóa Người Dùng</h5>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button style="outline: none; border: none;" type="button" class="btn btn-danger"
                        data-dismiss="modal">Hủy Bỏ</button>
                    <button style="outline: none; border: none;" type="button" class="btn btn-success"
                        data-dismiss="modal" id="confirmDelete">Đồng Ý</button>
                </div>
            </div>
        </div>
    </div>

    <!--sua-->
    <div class="modal fade" id="ModalUP" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa thông tin người dùng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="control-label">Tên người dùng</label>
                            <input class="form-control" type="text" id="edit_username" readonly value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Họ và tên</label>
                            <input class="form-control" type="text" id="edit_fullname">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Địa chỉ</label>
                            <input class="form-control" type="text" id="edit_address">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Email</label>
                            <input class="form-control" type="text" id="edit_email">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Số điện thoại</label>
                            <input class="form-control" type="text" id="edit_phone">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Thành phố/ Tỉnh</label>
                            <input class="form-control" type="text" id="edit_province">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Quận/ Huyện</label>
                            <input class="form-control" type="text" id="edit_district">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Vai trò</label>
                            <input class="form-control" type="text" id="edit_role">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Trạng thái</label>
                            <select class="form-control" id = "edit_status">
                              <option value="Hoạt động">Hoạt động</option>
                              <option value="Đã khóa">Đã khóa</option>
                          </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelBtn">Hủy
                            bỏ</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="saveBtn">Đồng
                            ý</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--khoa-->
    <div class="modal fade" id="ModalRL" tabindex="-1" role="dialog" aria-labelledby="lockModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lockModalLabel">Xác Nhận Khóa Người Dùng</h5>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button style="outline: none; border: none;" type="button" class="btn btn-danger"
                        data-dismiss="modal" id="cancelBtn">Hủy Bỏ</button>
                    <button style="outline: none; border: none;" type="button" class="btn btn-success"
                        data-dismiss="modal" id="saveBtn">Đồng Ý</button>
                </div>
            </div>
        </div>
    </div>

    <!--them-->
    <div class="modal fade" id="ModalKP" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thông tin người dùng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Tên người dùng</label>
                            <input class="form-control" type="text" id="username" placeholder="Nhập tên người dùng">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Mật khẩu</label>
                            <input class="form-control" type="text" id="password" placeholder="Nhập mật khẩu">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Họ và tên</label>
                            <input class="form-control" type="text" id="fullname" placeholder="Nhập họ và tên">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Số điện thoại</label>
                            <input class="form-control" type="text" id="phone" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Địa chỉ</label>
                            <input class="form-control" type="text" id="address" placeholder="Nhập địa chỉ">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label for="province">Thành Phố/ Tỉnh</label>
                            <select id="province" name="province" class="form-control">
                            <option value="">Chọn một tỉnh/thành phố</option>
                            <?php
                                if ($province_result && $province_result->num_rows > 0) {
                                    while ($row = $province_result->fetch_assoc()) {
                                    ?>
                                        <option value="<?php echo $row['province_id']; ?>"><?php echo $row['name']; ?></option>
                                    <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label for="district">Quận/ Huyện</label>
                            <select id="district" name="district" class="form-control">
                                <option value="">Chọn một quận/huyện</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Email</label>
                            <input class="form-control" type="text" id="email" placeholder="Nhập email">
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label class="control-label">Vai trò</label>
                            <select class="form-control" id="role">
                            <option value="Khách hàng">Khách hàng</option>
                            <option value="Quản lý">Quản lý</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="addUserCancelBtn">Hủy
                        bỏ</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="addUserSaveBtn">Lưu
                        lại</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/adjust_user.js"></script>
    <script src = "../js/nextpage.js"></script>

    <script>
    $(document).ready(function(){
        $('#province').change(function(){
            var province_id = $(this).val();
            if(province_id != ''){
                $.ajax({
                    url: 'get_district.php',
                    type: 'GET',
                    dataType: 'json',  
                    data: {province_id: province_id},
                    success: function(response){
                        try {
                            var data = typeof response === 'string' ? JSON.parse(response) : response;
                            var options = '<option value="">Chọn một quận/huyện</option>';
                            if(Array.isArray(data)) {
                                data.forEach(function(item) {
                                    if(item.id && item.name) {
                                        options += '<option value="' + item.id + '">' + item.name + '</option>';
                                    }
                                });
                            }
                            $('#district').html(options);
                        } catch(e) {
                            console.error('Lỗi xử lý dữ liệu:', e);
                            $('#district').html('<option value="">Chọn một quận/huyện</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Lỗi AJAX:', error);
                        console.log('Response:', xhr.responseText);
                        $('#district').html('<option value="">Chọn một quận/huyện</option>');
                    }
                });
            } else {
                $('#district').html('<option value="">Chọn một quận/huyện</option>');
            }
        });
    });
    </script>
</body>
</html>