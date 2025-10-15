<?php

include 'connect.php';
session_start();



if (!isset($_SESSION['payment_info'])) {
    echo "Không có dữ liệu đơn hàng.";
    exit();
}

$payment_info = $_SESSION['payment_info']; // THIẾT YẾU




// Xử lý khi người dùng xác nhận đơn hàng
// Xử lý khi người dùng xác nhận đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $pdo = new PDO("mysql:host=localhost;dbname=c07db", "c07u", "v73vNzf5lnnuDs02");

    try {
        $pdo->beginTransaction();

        // Thêm vào bảng đơn hàng
        $stmt = $pdo->prepare("INSERT INTO hoadon (order_status, order_date, user_name, district, province, PaymentMethod, phone, address, customerName) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Dữ liệu từ payment_info được thêm vào cột trong DB
        $stmt->execute([
            'Chưa xác nhận', // order_status
            $payment_info['order_date'], // order_date
            $_SESSION['user_name'], // user_name
            $payment_info['district'], // district
            $payment_info['province'], // city
            $payment_info['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng':'Chuyển khoản', // PaymentMethod
            $payment_info['phone'], // phone
            $payment_info['address'], // address
            $payment_info['full_name'], // customerName
    
        ]);

        $order_id = $pdo->lastInsertId(); // Lấy id của đơn hàng mới được tạo

        // Thêm chi tiết đơn hàng vào bảng order_details
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $stmt_detail = $pdo->prepare("INSERT INTO chitiethoadon (order_id, product_id, total_amount, quantity, unit_price)
                                          VALUES (?, ?, ?, ?, ?)");
            $stmt_detail->execute([
                $order_id,
                $product_id,
                $item['price'] * $item['quantity'],
                $item['quantity'],
                $item['price']
            ]);
        }
        
        

        

        // Commit transaction
        $pdo->commit();

        unset($_SESSION['cart']);
        unset($_SESSION['payment_info']);

        // Chạy SweetAlert thông báo và chuyển trang
        echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'Đặt hàng thành công!',
                        text: 'Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ xử lý đơn hàng sớm!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = 'bill-summary.php'; // Chuyển đến trang giỏ hàng
                    });
                }, 1000);
              </script>";
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $pdo->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }

}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xem trước hóa đơn</title>
    <style>
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .order-details, .customer-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        /* Thiết lập màu sắc cơ bản */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4; /* Màu nền nhẹ */
    color: #333; /* Màu chữ cơ bản */
    margin: 0;
    padding: 0;
}

/* Cài đặt viền cho form */
form {
    width: 80%;
    max-width: 800px;
    margin: 30px auto;
    padding: 20px;
    background-color: white;
    border: 2px solid #000000; /* Viền đen */
    border-radius: 10px; /* Bo tròn viền */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ */
}

/* Thiết lập màu xanh lá cho các tiêu đề và phần quan trọng */
h1, h2, h3, h4 {
    color: #28a745; /* Xanh lá */
    font-weight: bold;
}

/* Cải thiện style cho các trường nhập */
input[type="text"], input[type="number"], select, textarea {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 16px;
}

input[type="text"]:focus, input[type="number"]:focus, select:focus, textarea:focus {
    border-color: #28a745; /* Viền xanh lá khi focus */
    outline: none;
}

/* Thiết lập nút bấm đẹp mắt */
button {
    background-color: #28a745; /* Nút xanh lá */
    color: white;
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    margin-top: 15px;
}

button:hover {
    background-color: #218838; /* Đổi màu khi hover */
}

/* Style cho các bảng thông tin hóa đơn */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #28a745;
    color: white;
}

td {
    background-color: #f9f9f9;
}

/* Style cho thông tin tổng hợp */
.total-info {
    text-align: right;
    margin-top: 20px;
    font-weight: bold;
    font-size: 18px;
}

/* Đảm bảo giao diện đẹp trên mọi thiết bị */
@media screen and (max-width: 768px) {
    form {
        width: 90%;
    }

    button {
        width: 100%;
    }

    .total-info {
        font-size: 16px;
    }
}

/* Container chứa các nút */
.button-container {
    display: flex;
    justify-content: space-between; /* Đảm bảo khoảng cách đều giữa các nút */
    margin-top: 20px; /* Căn chỉnh khoảng cách trên nếu cần */
}

/* CSS cho các nút */
.button-container button {
    width: 48%; /* Đặt độ rộng của mỗi nút là 48% */
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
}

/* Nút xác nhận (màu xanh lá) */
.confirm-button {
    background-color: #4CAF50; /* Màu nền xanh lá cho nút xác nhận */
    color: white;
    border: none;
    text-align: center;
}

/* Nút quay lại (màu đỏ) */
.go-back-button {
    background-color: #4CAF50; /* Màu nền đỏ cho nút quay lại */
    color: white;
    border: none;
    text-align: center;
}

    </style>
</head>
<body>
<form method="POST">
    <div class="preview-container">
        <h1>Xem trước hóa đơn</h1>
        
        <div class="customer-info">
            <h3>Thông tin khách hàng</h3>
            <p>Họ tên: <?= htmlspecialchars($payment_info['full_name']) ?></p>
            <p>Địa chỉ: <?= htmlspecialchars($payment_info['address']) ?></p>
            <p>Số điện thoại: <?= htmlspecialchars($payment_info['phone']) ?></p>
            <p>Email: <?= htmlspecialchars($payment_info['email']) ?></p>
            <?php
$methods = [
  'cod' => 'Thanh toán khi nhận hàng',
  'bank_transfer' => 'Chuyển khoản'
];
$method_display = $methods[$payment_info['payment_method']] ?? 'Không xác định';
?>
<p>Phương thức thanh toán: <?= $method_display ?></p>
        </div>
        
        <div class="order-details">
            <h3>Chi tiết đơn hàng</h3>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payment_info['cart'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</td>
                        <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> VNĐ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Tổng cộng:</strong></td>
                        <td><strong><?= number_format($payment_info['total'], 0, ',', '.') ?> VNĐ</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
       
            <div class="button-container">
                <button type="submit" name="confirm_order" class="btn btn-success">Xác nhận đơn hàng</button>
                <button type="button" onclick="window.location.href='cart-user.php';" class="btn btn-secondary">Quay lại giỏ hàng</button>

            </div>
        </form>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert JS -->

<!-- Thêm SweetAlert vào trong <head> của trang -->
