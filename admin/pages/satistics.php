<?php
session_name('ADMINSESSID');
session_start();

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_name']) || empty($_SESSION['user_name'])) {
    header("Location: /admin/index.php");
    exit();
}

// Kiểm tra xem người dùng có quyền admin không
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Nhân viên') {
    header("Location: /admin/index.php");
    exit();
}

require_once '../classes/Database.php';
require_once '../classes/Statistics.php';

$db = new Database();
$statistics = new Statistics($db);

// Áp dụng bộ lọc ngày nếu có
if (isset($_GET['datein']) || isset($_GET['dateout'])) {
    $statistics->setDateFilter(
        $_GET['datein'] ?? null,
        $_GET['dateout'] ?? null
    );
}

// Lấy dữ liệu thống kê
$top_customers = $statistics->getTopCustomers();
$top_products = $statistics->getTopProducts();
$best_seller = $statistics->getBestSeller();
$worst_seller = $statistics->getWorstSeller();

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
                            $orderCount = count($customer['orders']);
                            $orderDisplay = $orderCount > 1 ? "{$orderCount} đơn hàng" : "1 đơn hàng";
                            
                            if ($orderCount == 1) {
                                $order = reset($customer['orders']);
                                $button = sprintf(
                                    '<a href="satictics_detail.php?id=%s" class="btn btn-info btn-sm" style="background-color: #17ab1d; color: white; border: none; padding: 6px 12px; border-radius: 4px; text-decoration: none;">
                                        <i class="fa fa-eye"></i> Xem đơn hàng
                                    </a>',
                                    $order['order_id']
                                );
                            } else {
                                $button = '<div class="dropdown">
                                    <button class="btn btn-info1 dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i></i>▼ Xem đơn hàng
                                    </button>
                                    <div class="dropdown-content">';

                                usort($customer['orders'], function($a, $b) {
                                    return strtotime($a['order_date']) - strtotime($b['order_date']);
                                });

                                foreach ($customer['orders'] as $index => $order) {
                                    $orderDate = date('d/m/Y', strtotime($order['order_date']));
                                    $button .= sprintf(
                                        '<a href="satictics_detail.php?id=%s">Đơn %d: %s</a>',
                                        $order['order_id'],
                                        $index + 1,
                                        $orderDate
                                    );
                                }
                                
                                $button .= '</div></div>';
                            }
                    ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo htmlspecialchars($customer['fullname']); ?></td>
                        <td><?php echo $orderDisplay; ?></td>
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