<?php
require '../config/connect.php';
session_name('ADMINSESSID');
session_start();
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: order.php');
    exit;
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);

// Lấy thông tin chi tiết đơn hàng
$order_sql = "SELECT hd.*, nd.fullname, nd.phone, nd.district, nd.province, nd.user_address
              FROM hoadon hd 
              LEFT JOIN nguoidung nd ON hd.user_name = nd.user_name
              WHERE hd.order_id = '$order_id'";

$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header('Location: order.php');
    exit;
}

// Lấy chi tiết các sản phẩm trong đơn hàng
$items_sql = "SELECT cthd.*, sp.product_name, sp.product_price
              FROM chitiethoadon cthd
              LEFT JOIN sanpham sp ON cthd.product_id = sp.product_id
              WHERE cthd.order_id = '$order_id'";
$items_result = mysqli_query($conn, $items_sql);

// Tính tổng tiền
$total_amount_sql = "SELECT SUM(cthd.quantity * sp.product_price) as total_amount
                     FROM chitiethoadon cthd 
                     JOIN sanpham sp ON cthd.product_id = sp.product_id 
                     WHERE cthd.order_id = '$order_id'";
$total_result = mysqli_query($conn, $total_amount_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_amount = $total_row['total_amount'];

$order_date = date('d/m/Y', strtotime($order['order_date']));
$order_time = date('H:i', strtotime($order['order_date']));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết hóa đơn #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="./stylescss/satistics.css">
    <link rel="stylesheet" href="./stylescss/responsivestatistics.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        .invoice-container {
            max-width: 850px;
            margin: 20px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            position: relative;
            overflow: hidden;
        }
        
        .invoice-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #4CAF50, #2E7D32);
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .invoice-logo img {
            max-width: 150px;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #47b475;
            margin-bottom: 5px;
        }
        
        .invoice-subtitle {
            color: #777;
            margin-bottom: 0;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-details-col {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin: 0 10px;
        }
        
        .invoice-details-col:first-child {
            margin-left: 0;
        }
        
        .invoice-details-col:last-child {
            margin-right: 0;
        }
        
        .invoice-details-col h4 {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 15px;
            color: #47b475;
            border-bottom: 2px solid #47b475;
            padding-bottom: 8px;
            display: inline-block;
        }
        
        .invoice-items {
            margin-bottom: 30px;
        }
        
        .invoice-items h4 {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 15px;
            color: #47b475;
            border-bottom: 2px solid #47b475;
            padding-bottom: 8px;
            display: inline-block;
        }
        
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .invoice-items th {
            background-color: #f9f9f9;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #ddd;
            color: #333;
        }
        
        .invoice-items td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .invoice-items tr:hover {
            background-color: #f9f9f9;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .invoice-total {
            text-align: right;
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        
        .invoice-total .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        
        .invoice-total .total-label {
            width: 180px;
            text-align: right;
            padding-right: 20px;
            color: #666;
        }
        
        .invoice-total .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #47b475;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #47b475;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-weight: 600;
        }
        
        .btn-back:hover {
            background-color: #47b475;
            color: white;
            text-decoration: none;
        }
        
        .actions {
            text-align: center;
            margin-top: 20px;
        }
        
        .pending {
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-size: 0.9em;
            white-space: nowrap;
            background-color: #efb11e;
            font-weight: 600;
        }

        .completed {
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-size: 0.9em;
            white-space: nowrap;
            background-color: #4CAF50;
            font-weight: 600;
        }

        .confirmed {
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-size: 0.9em;
            white-space: nowrap;
            background-color: #3c97e6;
            font-weight: 600;
        }

        .cancelled {
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-size: 0.9em;
            white-space: nowrap;
            background-color: #e54432;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .invoice-details {
                flex-direction: column;
            }
            .invoice-details-col {
                margin: 10px 0;
            }
            .invoice-container {
                padding: 15px;
            }
        }
        
        @media print {
            .btn-back, .btn-print, .sidebar, .web-header, button.toggle-sidebar {
                display: none !important;
            }
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            body {
                background-color: #fff;
            }
            .watermark {
                display: none;
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
            <a class="icon-denim" href="./usermanage.php" target="_self"><i class="fa-solid fa-user-shield"></i> Quản lí người dùng</a>
            <a class="icon-denim icon-denim-active" href="./order.php" target="_self"><i class="fa-solid fa-cart-shopping"></i> Quản lý đơn hàng</a>
            <a class="icon-denim" href="./prolist.php" target="_self"><i class="fa-solid fa-box-archive"></i> Tất cả sản phẩm</a>
            <a class="icon-denim" href="./addpro.php" target="_self"><i class="fa-solid fa-cart-plus"></i> Thêm sản phẩm</a>
            <a class="icon-denim" href="./satistics.php" target="_self"><i class="fa-solid fa-chart-column"></i> Thống kê tình hình</a>
            <a class="icon-denim" href="../index.php" target="_self"><i class="fa-solid fa-user-xmark"></i> Đăng xuất</a>
        </ul>
    </div>

    <main class="main" id="main">
        <div class="invoice-container">
            
            <div class="invoice-header">
                <div class="invoice-logo">
                    <img src="../img/mau-thiet-ke-logo-trai-cay-SPencil-Agency-7.png" alt="Logo">
                </div>
                <div class="invoice-info">
                    <h1 class="invoice-title">HÓA ĐƠN</h1>
                    <p class="invoice-subtitle">Mã đơn hàng: #<?php echo $order_id; ?></p>
                </div>
            </div>

            <div class="invoice-details">
                <div class="invoice-details-col">
                    <h4>THÔNG TIN NGƯỜI MUA HÀNG</h4>
                    <p>
                        <i class="fas fa-user" style="width: 20px; color: #47b475;"></i> <strong>Họ tên:</strong> <?php echo $order['fullname']; ?><br>
                        <i class="fas fa-phone" style="width: 20px; color: #47b475;"></i> <strong>SĐT:</strong> <?php echo isset($order['phone']) ? $order['phone'] : 'N/A'; ?><br>
                        <i class="fas fa-map-marker-alt" style="width: 20px; color: #47b475;"></i> <strong>Địa chỉ:</strong> <?php echo $order['user_address']; ?>, <?php echo $order['district']; ?>, <?php echo $order['province']; ?>
                    </p>
                </div>
                <div class="invoice-details-col">
                    <h4>THÔNG TIN ĐƠN HÀNG</h4>
                    <p>
                        <i class="fas fa-credit-card" style="width: 20px; color: #47b475;"></i> <strong>Phương thức thanh toán:</strong> <?php echo $order['PaymentMethod']; ?><br>
                        <i class="fas fa-calendar-alt" style="width: 20px; color: #47b475;"></i> <strong>Ngày đặt:</strong> <?php echo $order_date; ?><br>
                        <i class="fas fa-clock" style="width: 20px; color: #47b475;"></i> <strong>Giờ đặt:</strong> <?php echo $order_time; ?><br>
                        <i class="fas fa-info-circle" style="width: 20px; color: #47b475;"></i> <strong>Trạng thái:</strong> 
                        <?php 
                            $status = $order['order_status'];
                            $status_class = '';
                            
                            switch($status) {
                                case 'Đã xác nhận':
                                    $status_class = 'confirmed';
                                    break;
                                case 'Chưa xác nhận':
                                    $status_class = 'pending';
                                    break;
                                case 'Giao thành công':
                                    $status_class = 'completed';
                                    break;
                                case 'Đã hủy':
                                    $status_class = 'cancelled';
                                    break;
                                default:
                                    $status_class = '';
                            }
                        ?>
                        <span class="<?php echo $status_class; ?>"><?php echo $status; ?></span>
                    </p>
                </div>
            </div>

            <?php if($order['order_status'] === 'Giao thành công'): ?>
            <div class="invoice-details-col">
                    <h4>THÔNG TIN NGƯỜI NHẬN</h4>
                    <p>
                        <i class="fas fa-user" style="width: 20px; color: #47b475;"></i> <strong>Họ tên:</strong> <?php echo $order['fullname']; ?><br>
                        <i class="fas fa-phone" style="width: 20px; color: #47b475;"></i> <strong>SĐT:</strong> <?php echo isset($order['phone']) ? $order['phone'] : 'N/A'; ?><br>
                        <i class="fas fa-map-marker-alt" style="width: 20px; color: #47b475;"></i> <strong>Địa chỉ:</strong> <?php echo $order['user_address']; ?>, <?php echo $order['district']; ?>, <?php echo $order['province']; ?>
                    </p>
            </div>
            <?php ?>
                <?php else: ?>
                    <div class="invoice-details-col">
                    <h4>THÔNG TIN NGƯỜI NHẬN</h4>
                    <p>
                        <i class="fas fa-user" style="width: 20px; color: #47b475;"></i> <strong>Họ tên:</strong> <?php echo $order['fullname']; ?><br>
                        <i class="fas fa-phone" style="width: 20px; color: #47b475;"></i> <strong>SĐT:</strong> <?php echo isset($order['phone']) ? $order['phone'] : 'N/A'; ?><br>
                        <i class="fas fa-map-marker-alt" style="width: 20px; color: #47b475;"></i> <strong>Địa chỉ:</strong> <?php echo $order['user_address']; ?>, <?php echo $order['district']; ?>, <?php echo $order['province']; ?>
                    </p>
                    </div>
                <?php endif; ?>

            <div class="invoice-items">
                <h4>CHI TIẾT SẢN PHẨM</h4>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($items_result && mysqli_num_rows($items_result) > 0) {
                            while ($item = mysqli_fetch_assoc($items_result)) {
                                $product_id = $item['product_id'];
                                $image_sql = "SELECT product_image FROM sanpham WHERE product_id = '$product_id'";
                                $image_result = mysqli_query($conn, $image_sql);
                                $image_row = mysqli_fetch_assoc($image_result);
                                $image_file = isset($image_row['product_image']) ? $image_row['product_image'] : 'default.jpg';
                                $image_path = "../img/" . $image_file;
                                
                                $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                                $price = $item['product_price'];
                                $subtotal = $quantity * $price;
                        ?>
                        <tr>
                            <td><img src="<?php echo $image_path; ?>" alt="<?php echo $item['product_name']; ?>" class="item-image"></td>
                            <td><strong><?php echo $item['product_name']; ?></strong></td>
                            <td><?php echo number_format($price, 0, ',', '.'); ?>đ</td>
                            <td><?php echo $quantity; ?>kg</td>
                            <td style="text-align: right;"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="5">Không có sản phẩm nào</td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="invoice-total">
                <div class="total-row">
                    <div class="total-label"><i class="fas fa-shopping-basket" style="margin-right: 8px;"></i>Tổng tiền hàng:</div>
                    <div class="total-value"><?php echo number_format($total_amount, 0, ',', '.'); ?>đ</div>
                </div>
                <div class="total-row">
                    <div class="total-label"><i class="fas fa-truck" style="margin-right: 8px;"></i>Phí vận chuyển:</div>
                    <div class="total-value">30.000đ</div>
                </div>
                <?php if ($total_amount > 0): ?>
                <div class="total-row">
                    <div class="total-label"><i class="fas fa-tags" style="margin-right: 8px;"></i>Giảm giá: </div>
                    <div class="total-value">-30.000đ</div>
                </div>
                <?php endif; ?>
                <div class="total-row grand-total">
                    <div class="total-label"><i class="fas fa-money-bill-wave" style="margin-right: 8px;"></i>TỔNG THANH TOÁN:</div>
                    <div class="total-value">
                        <?php 
                        $shipping_fee = 30000;
                        $discount = ($total_amount > 0) ? 30000 : 0;
                        $final_total = $total_amount + $shipping_fee - $discount;
                        echo number_format($final_total, 0, ',', '.'); 
                        ?>đ
                    </div>
                </div>
            </div>

        <div class="actions">
            <button class="btn-back" onclick="goBack()"><i class="fas fa-arrow-left"></i> Quay lại</button>
        </div>
    </main>

    <script>
        $(document).ready(function () {
            $("#toggleSidebar").click(function () {
                $("#sidebar").toggleClass("active");
            });

            $(document).click(function (event) {
                if (!$(event.target).closest('#sidebar, #toggleSidebar').length && $('#sidebar').hasClass('active')) {
                    $("#sidebar").removeClass("active");
                }
            });
        });
        function goBack() {
            window.history.back();
            }
    </script>
</body>
</html>