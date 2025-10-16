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
 
$where_clause = "";

// Lọc theo ngày
if (isset($_GET['datein']) && !empty($_GET['datein'])) {
    $date_in = mysqli_real_escape_string($conn, $_GET['datein']);
    $where_clause .= " AND DATE(hd.order_date) >= '$date_in'";
}

if (isset($_GET['dateout']) && !empty($_GET['dateout'])) {
    $date_out = mysqli_real_escape_string($conn, $_GET['dateout']);
    $where_clause .= " AND DATE(hd.order_date) <= '$date_out'";
}

// Truy vấn để lấy tất cả đơn hàng
$orders_sql = "SELECT hd.order_id, hd.order_date, nd.fullname, 
              (SELECT SUM(cthd.quantity * sp.product_price) 
               FROM chitiethoadon cthd 
               JOIN sanpham sp ON cthd.product_id = sp.product_id 
               WHERE cthd.order_id = hd.order_id) as total_amount
              FROM hoadon hd 
              LEFT JOIN nguoidung nd ON hd.name = nd.user_name
              WHERE 1=1 AND hd.order_status = 'Giao thành công'
              ORDER BY hd.order_date DESC";

$orders_result = mysqli_query($conn, $orders_sql);

// Mảng để lưu trữ khách hàng đã gộp
$merged_customers = [];

// Gộp khách hàng
if ($orders_result && mysqli_num_rows($orders_result) > 0) {
    while ($order = mysqli_fetch_assoc($orders_result)) {
        $fullname = $order['fullname'];

        // Nếu chưa có khách hàng này trong mảng gộp
        if (!isset($merged_customers[$fullname])) {
            $merged_customers[$fullname] = [
                'fullname' => $fullname,
                'orders' => [],
                'total_amount' => 0,
                'date_range' => []
            ];
        }

        // Thêm đơn hàng vào khách hàng
        $merged_customers[$fullname]['orders'][] = [
            'order_id' => $order['order_id'],
            'order_date' => $order['order_date'],
            'total_amount' => $order['total_amount']
        ];

        // Cộng dồn tổng tiền
        $merged_customers[$fullname]['total_amount'] += $order['total_amount'];
    }
}

// Sắp xếp khách hàng theo tổng tiền giảm dần
uasort($merged_customers, function($a, $b) {
    return $b['total_amount'] <=> $a['total_amount'];
});

// Lấy 5 khách hàng có mức mua cao nhất
$top_customers = array_slice($merged_customers, 0, 5);

// Sắp xếp khách hàng theo tổng tiền giảm dần
uasort($merged_customers, function($a, $b) {
    return $b['total_amount'] <=> $a['total_amount'];
});

// Lấy 5 khách hàng có mức mua cao nhất
$top_customers = array_slice($merged_customers, 0, 5);

// Truy vấn để lấy 5 sản phẩm bán chạy nhất
$top_products_sql = "SELECT sp.product_id, sp.product_name, sp.product_price, sp.product_image, 
                    SUM(cthd.quantity) as total_sold,
                    SUM(cthd.quantity * sp.product_price) as total_revenue
                    FROM sanpham sp
                    JOIN chitiethoadon cthd ON sp.product_id = cthd.product_id
                    JOIN hoadon hd ON cthd.order_id = hd.order_id
                    WHERE 1=1 AND hd.order_status = 'Giao thành công' $where_clause
                    GROUP BY sp.product_id, sp.product_name, sp.product_price, sp.product_image
                    ORDER BY total_sold DESC
                    LIMIT 5";

$top_products_result = mysqli_query($conn, $top_products_sql);
$top_products = [];

if ($top_products_result && mysqli_num_rows($top_products_result) > 0) {
    while ($product = mysqli_fetch_assoc($top_products_result)) {
        // Truy vấn đơn hàng liên quan đến sản phẩm
        $product_orders_sql = "SELECT DISTINCT hd.order_id, hd.order_date, nd.fullname
                      FROM hoadon hd
                      JOIN chitiethoadon cthd ON hd.order_id = cthd.order_id
                      LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                      WHERE cthd.product_id = '{$product['product_id']}'
                      AND hd.order_status = 'Giao thành công' $where_clause
                      ORDER BY hd.order_date DESC";

        $product_orders_result = mysqli_query($conn, $product_orders_sql);
        $product['orders'] = [];
        $product['order_count'] = 0;
        
        if ($product_orders_result && mysqli_num_rows($product_orders_result) > 0) {
            while ($order = mysqli_fetch_assoc($product_orders_result)) {
                $product['orders'][] = $order;
                $product['order_count']++;
            }
        }
        
        $top_products[] = $product;
    }
}

// Truy vấn để lấy sản phẩm bán chạy nhất
$best_seller_sql = "SELECT sp.product_id, sp.product_name, sp.product_price, sp.product_image, 
                    SUM(cthd.quantity) as total_sold,
                    SUM(cthd.quantity * sp.product_price) as total_revenue
                    FROM sanpham sp
                    JOIN chitiethoadon cthd ON sp.product_id = cthd.product_id
                    JOIN hoadon hd ON cthd.order_id = hd.order_id
                    WHERE 1=1 AND hd.order_status = 'Giao thành công' $where_clause
                    GROUP BY sp.product_id, sp.product_name, sp.product_price, sp.product_image
                    ORDER BY total_sold DESC
                    LIMIT 1";

$best_seller_result = mysqli_query($conn, $best_seller_sql);
$best_seller = null;

// Truy vấn để lấy sản phẩm bán ế nhất
$worst_seller_sql = "SELECT sp.product_id, sp.product_name, sp.product_price, sp.product_image, 
                    COALESCE(SUM(cthd.quantity), 0) as total_sold,
                    COALESCE(SUM(cthd.quantity * sp.product_price), 0) as total_revenue
                    FROM sanpham sp
                    LEFT JOIN chitiethoadon cthd ON sp.product_id = cthd.product_id
                    LEFT JOIN hoadon hd ON cthd.order_id = hd.order_id AND (1=1 AND hd.order_status = 'Giao thành công' $where_clause)
                    GROUP BY sp.product_id, sp.product_name, sp.product_price, sp.product_image
                    HAVING total_sold > 0
                    ORDER BY total_sold ASC
                    LIMIT 1";

$worst_seller_result = mysqli_query($conn, $worst_seller_sql);
$worst_seller = null;

// Xử lý sản phẩm bán chạy nhất
if ($best_seller_result && mysqli_num_rows($best_seller_result) > 0) {
    $best_seller = mysqli_fetch_assoc($best_seller_result);
    
    // Truy vấn đơn hàng liên quan đến sản phẩm bán chạy
    $product_orders_sql = "SELECT DISTINCT hd.order_id, hd.order_date, nd.fullname
                      FROM hoadon hd
                      JOIN chitiethoadon cthd ON hd.order_id = cthd.order_id
                      LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                      WHERE cthd.product_id = '{$best_seller['product_id']}'
                      AND hd.order_status = 'Giao thành công' $where_clause
                      ORDER BY hd.order_date DESC";

    $product_orders_result = mysqli_query($conn, $product_orders_sql);
    $best_seller['orders'] = [];
    $best_seller['order_count'] = 0;
    
    if ($product_orders_result && mysqli_num_rows($product_orders_result) > 0) {
        while ($order = mysqli_fetch_assoc($product_orders_result)) {
            $best_seller['orders'][] = $order;
            $best_seller['order_count']++;
        }
    }
}

// Xử lý sản phẩm bán ế nhất
if ($worst_seller_result && mysqli_num_rows($worst_seller_result) > 0) {
    $worst_seller = mysqli_fetch_assoc($worst_seller_result);
    
    // Truy vấn đơn hàng liên quan đến sản phẩm bán ế
    $product_orders_sql = "SELECT DISTINCT hd.order_id, hd.order_date, nd.fullname
                      FROM hoadon hd
                      JOIN chitiethoadon cthd ON hd.order_id = cthd.order_id
                      LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                      WHERE cthd.product_id = '{$worst_seller['product_id']}'
                      AND hd.order_status = 'Giao thành công' $where_clause
                      ORDER BY hd.order_date DESC";

    $product_orders_result = mysqli_query($conn, $product_orders_sql);
    $worst_seller['orders'] = [];
    $worst_seller['order_count'] = 0;
    
    if ($product_orders_result && mysqli_num_rows($product_orders_result) > 0) {
        while ($order = mysqli_fetch_assoc($product_orders_result)) {
            $worst_seller['orders'][] = $order;
            $worst_seller['order_count']++;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê tình hình kinh doanh </title>
    <link rel="stylesheet" href="./stylescss/satistics.css">
    <link rel="stylesheet" href="./stylescss/responsivestatistics.css">
    <link rel="stylesheet" href="./stylescss/turnover.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 250px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            right: 0;
            max-height: 130px;
            overflow-y: auto; 
        }
        
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            white-space: nowrap;
        }
        
        .dropdown-content a:hover {background-color: #f1f1f1}
        
        .btn-info1.dropdown-toggle {
            background-color: #17ab1d; 
            color: white; 
            border: none; 
            padding: 6px 12px; 
            border-radius: 4px; 
            text-decoration: none;
        }
        .btn-info1.dropdown-toggle:active {
           outline: none;
        }
        .multi-orders {
            font-size: 0.8em;
            color: #666;
            margin-top: 3px;
        }
        .product-comparison-section {
        margin-bottom: 30px;
    }
    
    .section-title {
        color: #333;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #17ab1d;
    }
    
    .product-comparison-container {
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .product-comparison-wrapper {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .single-product {
        justify-content: center;
    }
    
    .product-card {
        position: relative;
        width: 250px;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    
    .best-seller-card {
        background-color: #f0f9f0;
        border: 2px solid #17ab1d;
    }
    
    .worst-seller-card {
        background-color: #fff9f9;
        border: 2px solid #ff6b6b;
    }
    
    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #17ab1d;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }
    
    .product-badge.worst {
        background-color: #ff6b6b;
    }
    
    .product-image-container {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        overflow: hidden;
        border-radius: 10px;
    }
    
    .product-image-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }
    
    .product-card:hover .product-image-container img {
        transform: scale(1.1);
    }
    
    .product-name {
        margin-top: 10px;
    }
    
    .product-name h4 {
        color: #333;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .product-sold {
        color: #666;
        font-size: 14px;
    }
    
    .no-data {
        text-align: center;
        padding: 30px;
        font-style: italic;
        color: #999;
    }
    
    @media (max-width: 768px) {
        .product-comparison-wrapper {
            flex-direction: column;
            align-items: center;
        }
        
        .product-card {
            width: 90%;
            max-width: 250px;
            margin-bottom: 20px;
        }
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
            <a class="icon-denim" href="./usermanage.php" target="_self"><i class="fa-solid fa-user-shield"></i> Quản
                lí người dùng</a>
            <a class="icon-denim" href="./order.php" target="_self"><i class="fa-solid fa-cart-shopping"></i> Quản lý
                đơn hàng</a>
            <a class="icon-denim" href="./prolist.php" target="_self"><i class="fa-solid fa-box-archive"></i> Tất cả
                sản phẩm</a>
            <a class="icon-denim" href="./addpro.php" target="_self"><i class="fa-solid fa-cart-plus"></i> Thêm sản
                phẩm</a>
            <a class="icon-denim icon-denim-active" href="./satistics.php" target="_self"><i
                    class="fa-solid fa-chart-column"></i> Thống kê tình hình</a>
            <a class="icon-denim" href="../index.php" target="_self"><i class="fa-solid fa-user-xmark"></i> Đăng
                xuất</a>
        </ul>
    </div>

    <header>
        <div class="web-header">
            <a href=""> <img src="../img/mau-thiet-ke-logo-trai-cay-SPencil-Agency-7.png"
                    alt="mau-thiet-ke-logo-trai-cay-SPencil-Agency-7"></a>
        </div>
    </header>

    <main class="main" id="main">
        <div class="order-management">
            <h1 style="font-weight: bold;">Thống Kê Tình Hình Kinh Doanh</h1>
        </div>

        <form action="" id="dateFilterForm">
            <label for="datein">Từ ngày: </label>
            <input type="date" name="datein" id="datein" value="<?php echo isset($_GET['datein']) ? htmlspecialchars($_GET['datein']) : ''; ?>">
            <label for="dateout">đến ngày: </label>
            <input type="date" name="dateout" id="dateout" value="<?php echo isset($_GET['dateout']) ? htmlspecialchars($_GET['dateout']) : ''; ?>">
        </form>
        <button style="outline: none; margin-top: 10px;" id="applyLocationFilter" class="btn btn-filter">Lọc</button>
        <button style="outline: none; margin-top: 10px;" id="resetLocationFilter" class="btn btn-reset">Đặt lại</button>

        <div>
            <h3 class="tile-title">CÁC KHÁCH HÀNG CÓ MỨC MUA CAO NHẤT</h3>
        </div>
        <div class="tile-body">
            <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên Khách hàng</th>
                        <th>Đơn hàng</th>
                        <th>Tổng tiền</th>
                        <th>Hóa đơn</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $rank = 1;
                    
                    if (!empty($top_customers)) {
                        foreach ($top_customers as $customer) {
                            // Hiển thị thông tin đơn hàng
                            $order_display = count($customer['orders']) > 1 ? 
                                            count($customer['orders']) . " đơn hàng" : 
                                            "1 đơn hàng";
                            
                            // Tạo button xem chi tiết
                            if (count($customer['orders']) == 1) {
                                // Nếu chỉ có 1 đơn hàng thì tạo button đơn giản
                                $order = reset($customer['orders']);
                                $button = '<a href="satictics_detail.php?id=' . $order['order_id'] . '" class="btn btn-info btn-sm" style="background-color: #17ab1d; color: white; border: none; padding: 6px 12px; border-radius: 4px; text-decoration: none;">
                                            <i class="fa fa-eye"></i> Xem đơn hàng
                                           </a>';
                            } else {
                                // Nếu có nhiều đơn hàng thì tạo dropdown
                                $button = '<div class="dropdown">
                                            <button class="btn btn-info1 dropdown-toggle" type="button" id="dropdownContentButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i></i>▼ Xem đơn hàng
                                            </button>
                                            <div class="dropdown-content">';

                                            usort($customer['orders'], function($a, $b) {
                                                return strtotime($a['order_date']) - strtotime($b['order_date']);
                                            });
                                
                                // Thêm link cho từng đơn hàng
                                foreach ($customer['orders'] as $index => $order) {
                                    $order_date = date('d/m/Y', strtotime($order['order_date']));
                                    $order_time = date('H:i', strtotime($order['order_date']));
                                    $button .= '<a href="satictics_detail.php?id=' . $order['order_id'] . '">
                                                Đơn ' . ($index + 1) . ': '  . '  ' . $order_date . '
                                               </a>';
                                }
                                
                                $button .= '</div></div>';
                            }
                    ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo $customer['fullname']; ?></td>
                        <td><?php echo $order_display; ?></td>
                        <td><?php echo number_format($customer['total_amount'], 0, ',', '.') . 'đ'; ?></td>
                        <td><?php echo $button; ?></td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">Không có đơn hàng nào</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

</div>
    </main>

    <script>
        $(document).ready(function () {
            $("#toggleSidebar").click(function () { 
                $("#sidebar").toggleClass("active");
            });

            $(document).click(function (event) {
                if (!$(event.target).closest('#sidebar, #toggleSidebar, .dropdown').length && $('#sidebar').hasClass('active')) {
                    $("#sidebar").removeClass("active");
                }
            });

            // lọc theo ngày
            $('#applyLocationFilter').on('click', function(e) {
                e.preventDefault();
                
                var datein = $('#datein').val();
                var dateout = $('#dateout').val();
                
                var url = 'satistics.php?';
                var params = [];
                
                if (datein) {
                    params.push('datein=' + datein);
                }
                
                if (dateout) {
                    params.push('dateout=' + dateout);
                }
                
                window.location.href = url + params.join('&');
            });
            
            $('#resetLocationFilter').on('click', function(e) {
                e.preventDefault();
                window.location.href = 'satistics.php';
            });
            
            $('#dateFilterForm').on('submit', function(e) {
                e.preventDefault();
                $('#applyLocationFilter').click();
            });
        });
            $(document).on('click', '.dropdown-toggle', function() {
                var $dropdownMenu = $(this).next('.dropdown-content');
                $dropdownMenu.toggle(); 
            });

            $(document).on('click', function(event) {
                if (!$(event.target).closest('.dropdown').length) {
                    $('.dropdown-content').hide(); 
                }
            });
            
            $(document).ready(function () {
                $(document).on('click', '.dropdown-toggle', function() {
                    var $dropdownContent = $(this).next('.dropdown-menu');
                    $dropdownContent.toggle();
                });

                $(document).on('click', function(event) {
                    if (!$(event.target).closest('.dropdown').length) {
                        $('.dropdown-menu').hide();
                    }
                });
            });
    </script>
</body>
</html>