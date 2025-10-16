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

require '../config/connect.php';

$sql = "SELECT * FROM province";
$result = mysqli_query($conn, $sql);

if (isset($_POST['add_sale'])) {
    echo "<pre>";
    print_r($_POST);
    die();
}

// lọc theo tỉnh/thành và quận/huyện
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

// Lọc theo trạng thái
if (isset($_GET['status']) && !empty($_GET['status']) && $_GET['status'] !== 'all') {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $where_clause .= " AND hd.order_status = '$status'";
}

// Lọc theo ngày
if (isset($_GET['datein']) && !empty($_GET['datein'])) {
    $date_in = mysqli_real_escape_string($conn, $_GET['datein']);
    $where_clause .= " AND DATE(hd.order_date) >= '$date_in'";
}

if (isset($_GET['dateout']) && !empty($_GET['dateout'])) {
    $date_out = mysqli_real_escape_string($conn, $_GET['dateout']);
    $where_clause .= " AND DATE(hd.order_date) <= '$date_out'";
}

$order_sql = "SELECT hd.*, nd.fullname, nd.district, nd.province, nd.user_address 
              FROM hoadon hd 
              LEFT JOIN nguoidung nd ON hd.creator = nd.user_name
              WHERE 1=1 $where_clause
              ORDER BY hd.order_date DESC";

$order_result = mysqli_query($conn, $order_sql);

$status_map_to_code = [
    'Chưa xác nhận' => 'pending',
    'Đã xác nhận' => 'confirmed',
    'Giao thành công' => 'completed',
    'Đã hủy' => 'cancelled'
];

$status_map_to_text = [
    'pending' => 'Chưa xác nhận',
    'confirmed' => 'Đã xác nhận',
    'completed' => 'Giao thành công',
    'cancelled' => 'Đã hủy'
];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit; 

$order_sql = "SELECT hd.*, nd.fullname, nd.district, nd.province, nd.user_address 
              FROM hoadon hd 
              LEFT JOIN nguoidung nd ON hd.creator = nd.user_name
              WHERE 1=1 $where_clause
              ORDER BY hd.order_date DESC
              LIMIT $limit OFFSET $offset"; 

$order_result = mysqli_query($conn, $order_sql);

// Lấy tổng số đơn hàng để tính số trang
$total_sql = "SELECT COUNT(*) as total FROM hoadon hd WHERE 1=1 $where_clause";
$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_orders = $total_row['total'];
$total_pages = ceil($total_orders / $limit);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lí đơn hàng</title>
    <link rel="stylesheet" href="./stylescss/order.css">
    <link rel="stylesheet" href="./stylescss/responsiveorder.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
    <button class="toggle-sidebar" id="toggleSidebar"><i class="fas fa-bars"></i></button>
    <div class="sidebar">
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

        <ul class="sidebar-menu"></ul>
        <a class="icon-denim" href="./usermanage.php" target="_self"> <i class="fa-solid fa-user-shield"></i></i> Quản
            lí người dùng</a>
        <a class="icon-denim icon-denim-active" href="./order.php" target="_self"> <i
                class="fa-solid fa-cart-shopping"></i> Quản lý đơn hàng</a>
        <a class="icon-denim" href="./prolist.php" target="_self"><i class="fa-solid fa-box-archive"></i> Tất cả sản
            phẩm</a>
        <a class="icon-denim" href="./addpro.php" target="_self"> <i class="fa-solid fa-cart-plus"></i> Thêm sản
            phẩm</a>
        <a class="icon-denim" href="./satistics.php" target="_self"> <i class="fa-solid fa-chart-column"></i> Thống kê
            tình hình</a>
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
            <h1 style="font-weight: bold;">Danh Sách Đơn Hàng</h1>
        </div>

        <br>

        <div class="row element-filter mx-0 mb-3">
            <div class="col-6 p-0" style="margin-bottom: 10px;">
                <label for="statusFilter">Lọc theo trạng thái:</label>
                <select id="statusFilter" class="form-select px-3">
                <option value="all">Tất cả</option>
                <option value="Chưa xác nhận" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Chưa xác nhận') ? 'selected' : ''; ?>>Chưa xác nhận</option>
                <option value="Đã xác nhận" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Đã xác nhận') ? 'selected' : ''; ?>>Đã xác nhận</option>
                <option value="Giao thành công" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Giao thành công') ? 'selected' : ''; ?>>Giao thành công</option>
                <option value="Đã hủy" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Đã hủy') ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>

            <div class="col-6 p-0" style="margin-bottom: 10px;">
                <label for="locationFilter">Thành Phố/ Tỉnh</label>
                <select id="province" name="province" class="form-control">
                <option value="">Chọn một tỉnh/thành phố</option>
                <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <option value="<?php echo $row['province_id'] ?>"><?php echo $row['name'] ?></option>
                            <?php
                            }
                            ?>
                </select>
            </div>
                <div class="form-group">
                          <label for="district">Quận/ Huyện</label>
                          <select id="district" name="district" class="form-control">
                              <option value="">Chọn một quận/huyện</option>
                          </select>
                  </div>

                  <form action="" id="dateFilterForm">
                    <label for="datein">Từ ngày: </label>
                    <input type="date" name="datein" id="datein" value="<?php echo isset($_GET['datein']) ? htmlspecialchars($_GET['datein']) : ''; ?>">
                    <label for="dateout">đến ngày: </label>
                    <input type="date" name="dateout" id="dateout" value="<?php echo isset($_GET['dateout']) ? htmlspecialchars($_GET['dateout']) : ''; ?>">
                </form>

            <div class="col-12 p-0" style="margin-top: 20px;">
                <button style="outline: none; margin-bottom: 10px;" id="applyLocationFilter" class="btn btn-filter">Lọc</button>
                <button style="outline: none; margin-bottom: 10px;" id="resetLocationFilter" class="btn btn-reset">Đặt lại</button>
            </div>
        </div>

        <br>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Tên Khách Hàng</th>
                        <th>Địa Chỉ</th>
                        <th>Người nhận</th>
                        <th>Trạng Thái</th>
                        <th>Ngày</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($order_result && mysqli_num_rows($order_result) > 0) {
                        while ($order = mysqli_fetch_assoc($order_result)) {
                            $order_id = $order['order_id'];
                            
                            $status_class = '';
                            $status_text = '';
                            
                            if (isset($status_map_to_text[$order['order_status']])) {
                                $status_class = $order['order_status'];
                                $status_text = $status_map_to_text[$order['order_status']];
                            } else if (isset($status_map_to_code[$order['order_status']])) {
                                $status_class = $status_map_to_code[$order['order_status']];
                                $status_text = $order['order_status'];
                            } else {
                                $status_class = 'pending';
                                $status_text = 'Chưa xác nhận';
                            }
                            
                            $date = date('d/m/Y', strtotime($order['order_date']));
                            $time = date('H:i', strtotime($order['order_date']));
                    ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['fullname']; ?></td>
                        <td>
                            <?php 
                            $address_parts = [];
                            
                            if (!empty($order['address'])) {
                                $address_parts[] = $order['address'];
                            } else if (!empty($order['user_address'])) {
                                $address_parts[] = $order['user_address'];
                            }
                            
                            if (!empty($order['district'])) {
                                $address_parts[] = $order['district'];
                            }
                            
                            if (!empty($order['province'])) {
                                $address_parts[] = $order['province'];
                            }
                            
                            if (!empty($address_parts)) {
                                echo implode(', ', $address_parts);
                            } else {
                                echo "Không có địa chỉ";
                            }
                            ?>
                        </td>
                        <td>
                        <?php
                            if ($status_text === 'Giao thành công') {
                                $recipient_name = $order['fullname']; 
                                echo htmlspecialchars($recipient_name);
                            } else{
                                echo $order['fullname'];
                            }
                            ?>
                        </td>
                        <td><span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                        <td><?php echo $date; ?><br><?php echo $time; ?></td>
                        <td class="text-align-center">
                        <a href="order_detail.php?id=<?php echo $order_id; ?>" class="btn btn-info btn-sm" style="background-color: #17ab1d; color: white; border: none; padding: 6px 12px; border-radius: 20px; text-decoration: none;">
                            <i class="fa-solid fa-circle-info"></i>
                        </a>
                            <button style="outline: none;" class="btn btn-outline-warning btn-sm edit m-1" type="button"
                                title="Sửa">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="9" class="text-center">Không có đơn hàng nào</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php
    if($total_orders > 0){
    ?>
        <nav aria-label="Page navigation " class="page-center">
            <ul class="pagination justify-content-center" id="pagination">
            <?php
                $filter_params = [];
                if (isset($_GET['province']) && !empty($_GET['province'])) {
                    $filter_params[] = "province=" . urlencode($_GET['province']);
                }
                if (isset($_GET['district']) && !empty($_GET['district'])) {
                    $filter_params[] = "district=" . urlencode($_GET['district']);
                }
                if (isset($_GET['status']) && !empty($_GET['status']) && $_GET['status'] !== 'all') {
                    $filter_params[] = "status=" . urlencode($_GET['status']);
                }
                if (isset($_GET['datein']) && !empty($_GET['datein'])) {
                    $filter_params[] = "datein=" . urlencode($_GET['datein']);
                }
                if (isset($_GET['dateout']) && !empty($_GET['dateout'])) {
                    $filter_params[] = "dateout=" . urlencode($_GET['dateout']);
                }
                
                $query_string = implode('&', $filter_params);
                if (!empty($query_string)) {
                    $query_string = '&' . $query_string;
                }
                ?>
                <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>" style="<?= $page == 1 ? 'display:none;' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?><?= $query_string ?>" aria-label="Lùi">
                        <span aria-hidden="true">&lt</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= $query_string ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>" style="<?= $page == $total_pages ? 'display:none;' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?><?= $query_string ?>" aria-label="Tiếp">
                        <span aria-hidden="true">&gt</span>
                    </a>
                </li>
            </ul>
        </nav>
        <?php
    }else{
    }
    ?>

    <!--edit-->
    <form action="getOrderDetail.php" method="POST" enctype="multipart/form-data">
        <div class="modal fade" id="ModalUP" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa thông tin người mua</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label">Mã đơn</label>
                                <input name="bill_id" class="form-control" type="text" readonly value="">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Tên khách hàng</label>
                                <input name="customer_name" class="form-control" type="text" readonly value="">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Địa chỉ</label>
                                <input name="address" class="form-control" type="text" readonly value="">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Quận/Huyện</label>
                                <input name="district" class="form-control" type="text" readonly value="">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Tỉnh/Thành phố</label>
                                <input name="province" class="form-control" type="text" readonly value="">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Trạng thái</label>
                                <select name="status" class="form-control" id="statusSelect">
                                    <option value="Chưa xác nhận">Chưa xác nhận</option>
                                    <option value="Đã xác nhận">Đã xác nhận</option>
                                    <option value="Giao thành công">Giao thành công</option>
                                    <option value="Đã hủy">Đã hủy</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelBtn">Hủy
                            bỏ</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="saveBtn">Lưu lại</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="../js/filterProDis.js"></script>
    <script src = "../js/nextpage.js"></script>
    <script>
        $(document).ready(function () {
            $("#toggleSidebar").click(function () {
                $(".sidebar").toggleClass("active");
            });

            $(document).click(function (event) {
                if (!$(event.target).closest('.sidebar, #toggleSidebar').length){
                    $(".sidebar").removeClass("active");
                }
            });

            // lọc theo ngày, trạng thái, tỉnh, huyện ...
            $('#applyLocationFilter').click(function() {
                var province = $('#province').val();
                var district = $('#district').val();
                var status = $('#statusFilter').val(); 
                var datein = $('#datein').val();
                var dateout = $('#dateout').val();

                console.log("Giá trị tỉnh:", province);
                console.log("Giá trị quận:", district);
                console.log("Giá trị trạng thái:", status);

                var url = 'order.php?';
                var params = [];

                
                if (province) {
                    params.push('province=' + province);
                }

                if (district) {
                    params.push('district=' + district);
                }

                if (status && status !== 'all') {
                    params.push('status=' + status); 
                }

                if (datein) {
                    params.push('datein=' + datein);
                }
                
                if (dateout) {
                    params.push('dateout=' + dateout);
                }

                window.location.href = url + params.join('&');
            });
            
            $('#resetLocationFilter').click(function() {
                window.location.href = 'order.php';
            });

            <?php if(isset($_GET['province']) && !empty($_GET['province'])): ?>
            $('#province').val('<?php echo $_GET['province']; ?>');
            $.ajax({
                url: '../pages/get_district.php',
                method: 'GET',
                dataType: "json",
                data: {
                    province_id: '<?php echo $_GET['province']; ?>'
                },
                success: function(data) {
                    $('#district').empty();
                    $.each(data, function(i, district) {
                        $('#district').append($('<option>', {
                            value: district.id,
                            text: district.name
                        }));
                    });
                    <?php if(isset($_GET['district']) && !empty($_GET['district'])): ?>
                    $('#district').val('<?php echo $_GET['district']; ?>');
                    <?php endif; ?>
                }
            });
            <?php endif; ?>

            $('.btn-outline-warning').click(function() {
                var order_id = $(this).closest('tr').find('td:first-child').text();
                
                $.ajax({
                    url: 'getOrderDetail.php',
                    type: 'GET',
                    data: { order_id: order_id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            var order = response.data;
                            $('input[name="bill_id"]').val(order.order_id);
                            $('input[name="customer_name"]').val(order.fullname);
                            $('input[name="address"]').val(order.address || '');
                            $('input[name="district"]').val(order.district || '');
                            $('input[name="province"]').val(order.province || '');
                            
                            $('#statusSelect').val(order.order_status);
                            
                            $('#statusSelect').data('current-status', order.order_status);
                            
                            disableInvalidOptions(order.order_status);
                            
                            $('#ModalUP').modal('show');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi lấy thông tin đơn hàng');
                    }
                });
            });

            function disableInvalidOptions(currentStatus) {
                $('#statusSelect option').prop('disabled', false);
                
                var statusOrder = {
                    'Chưa xác nhận': 1,
                    'Đã xác nhận': 2,
                    'Giao thành công': 3,
                    'Đã hủy': 4
                };
                
                $('#statusSelect option').each(function() {
                    var optionValue = $(this).val();
                    
                    if (currentStatus === 'Giao thành công' && optionValue === 'Đã hủy') {
                        $(this).prop('disabled', true);
                    }
                    else if (optionValue !== 'Đã hủy') {
                        if (statusOrder[optionValue] < statusOrder[currentStatus]) {
                            $(this).prop('disabled', true);
                        }
                    }
                });
            }
            
            $('#saveBtn').click(function() {
                var order_id = $('input[name="bill_id"]').val();
                var status = $('#statusSelect').val();
                var currentStatus = $('#statusSelect').data('current-status');
                
                console.log('Cập nhật đơn hàng: ' + order_id + ', từ: ' + currentStatus + ', sang: ' + status);
                
                var statusOrder = {
                    'Chưa xác nhận': 1,
                    'Đã xác nhận': 2,
                    'Giao thành công': 3,
                    'Đã hủy': 4
                };
                
                if (status === currentStatus) {
                    alert('Không có thay đổi trạng thái');
                    return;
                }
                
                if (currentStatus === 'Giao thành công' && status === 'Đã hủy'){
                    alert('Đơn hàng đã giao thành công không thể hủy');
                    return;
                }
                
                if (statusOrder[status] < statusOrder[currentStatus] && status !== 'Đã hủy') {
                    alert('Không thể cập nhật trạng thái đơn hàng ngược lại trạng thái trước đó');
                    return;
                }
                
                $.ajax({
                    url: 'updateOrderStatus.php',
                    type: 'POST',
                    data: { 
                        order_id: order_id,
                        status: status
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Cập nhật trạng thái đơn hàng thành công');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi cập nhật đơn hàng');
                    }
                });
            });
        });
    </script>

</body>
</html>