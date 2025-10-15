<?php
    require_once 'connect.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_code = $_POST['product-code'];
        $product_name = $_POST['product-name'];
        $category_id = isset($_POST['category-id']) ? $_POST['category-id'] : 0; // Gán giá trị mặc định là 0 nếu không có
        $type = $_POST['type'];
        $price = $_POST['price'];
        $status = $_POST['status'];


        // Xử lý hình ảnh
$image_path = '';
if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
    $upload_dir = '../img/';
    
    // Lấy tên gốc của file
    $original_name = basename($_FILES['product-image']['name']);
    
    // Tạo tên mới để lưu file trên server (tránh trùng)
    $file_name = time() . '_' . $original_name;
    $target_file = $upload_dir . $file_name;

    // Kiểm tra thư mục có quyền ghi
    if (!is_writable($upload_dir)) {
        die("Thư mục $upload_dir không có quyền ghi.");
    }

    // Di chuyển file upload
    if (move_uploaded_file($_FILES['product-image']['tmp_name'], $target_file)) {
        // Lưu vào DB: chỉ lưu tên gốc (không có timestamp)
        $image_path = $original_name;
    } else {
        die("Không thể tải lên file.");
    }
}


        // Tự động set hidden theo status
        $hidden = (strtolower($status) == 'đã bán') ? 1 : 0;

        // Tạm gán product_link và productnolo_link bằng chuỗi rỗng hoặc khoảng trắng
        $product_link = ' ';
        $productnolog_link = ' ';

        // Thêm hidden, product_link, productnolo_link vào câu lệnh INSERT
        $sql = "INSERT INTO sanpham(product_id, product_name, category_id, product_type, product_price, product_status, product_image, hidden, product_link, productnolog_link)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Lỗi chuẩn bị SQL: " . $conn->error);
        }

        // Bind đủ 11 tham số
        $stmt->bind_param("ssssssssis", 
            $product_code, 
            $product_name, 
            $category_id, 
            $type, 
            $price, 
            $status, 
            $image_path, 
            $hidden, 
            $product_link, 
            $productnolog_link
        );

        if($stmt->execute()){
            header("Location: addpro.php?success");
            exit();
        } else {
            echo "SQL Error: " . $stmt->error;
            exit();
        }

        $stmt->close();
        $conn->close();
    }
?>
