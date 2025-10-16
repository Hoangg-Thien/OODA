<?php
require '../config/connect.php';
header('Content-Type: application/json');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Lấy thông tin đơn hàng và thông tin người dùng
    $sql = "SELECT hd.*, nd.fullname, nd.user_name, nd.user_address, nd.district, nd.province
            FROM hoadon hd
            LEFT JOIN nguoidung nd ON hd.user_name = nd.user_name
            WHERE hd.order_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $order_id);
        
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $order = mysqli_fetch_assoc($result);
            
            // Lấy thông tin sản phẩm từ bảng chitiethoadon
            $detail_sql = "SELECT cthd.*, sp.product_name, sp.product_price 
                        FROM chitiethoadon cthd
                        LEFT JOIN sanpham sp ON cthd.product_id = sp.product_id
                        WHERE cthd.order_id = ?";
                        
            $detail_stmt = mysqli_prepare($conn, $detail_sql);
            
            if ($detail_stmt) {
                mysqli_stmt_bind_param($detail_stmt, "s", $order_id);
                mysqli_stmt_execute($detail_stmt);
                $detail_result = mysqli_stmt_get_result($detail_stmt);
                
                $products = [];
                $total_amount = 0;
                
                if ($detail_result && mysqli_num_rows($detail_result) > 0) {
                    while ($detail = mysqli_fetch_assoc($detail_result)) {
                        $products[] = $detail;
                        $quantity = isset($detail['quantity']) ? $detail['quantity'] : 1;
                        $total_amount += $detail['product_price'] * $quantity;
                    }
                }
                
                $order['products'] = $products;
                $order['total_amount'] = $total_amount;
                
                mysqli_stmt_close($detail_stmt);
            }
            
            if (empty($order['address']) && !empty($order['user_address'])) {
                $order['address'] = $order['user_address'];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $order
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đơn hàng'
            ]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi truy vấn: ' . mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Không có mã đơn hàng được cung cấp'
    ]);
} 