<?php
require '../config/connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    if (empty($order_id) || empty($status)) {
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu thông tin cần thiết'
        ]);
        exit;
    }
    
    $check_sql = "SELECT * FROM hoadon WHERE order_id = '$order_id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (!$check_result || mysqli_num_rows($check_result) == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy đơn hàng cần cập nhật'
        ]);
        exit;
    }
    
    $order_data = mysqli_fetch_assoc($check_result);
    $current_status = $order_data['order_status'];
    
    $status_order = [
        'Chưa xác nhận' => 1,   
        'Đã xác nhận' => 2,   
        'Giao thành công' => 3,   
        'Đã hủy' => 4    
    ];
    
    if ($current_status === 'Giao thành công' && $status === 'Đã hủy') {
        echo json_encode([
            'success' => false,
            'message' => 'Đơn hàng đã giao thành công không thể hủy'
        ]);
        exit;
    }
    
    if (isset($status_order[$current_status]) && isset($status_order[$status])) {
        if ($status_order[$status] < $status_order[$current_status] && $status !== 'Đã hủy') {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái đơn hàng ngược lại trạng thái trước đó'
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Trạng thái không hợp lệ: Current=' . $current_status . ', New=' . $status
        ]);
        exit;
    }
    
    $sql = "UPDATE hoadon SET order_status = '$status' WHERE order_id = '$order_id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi khi cập nhật: ' . mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không được hỗ trợ'
    ]);
} 