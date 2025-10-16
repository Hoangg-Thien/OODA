<?php
    require_once '../config/connect.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_code = isset($_POST['product-code']) ? trim($_POST['product-code']) : '';
        $product_name = isset($_POST['product-name']) ? trim($_POST['product-name']) : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';

        // Nhận trực tiếp category_id từ form (select value)
        $category_id = 0;
        if (!empty($type) && is_numeric($type)) {
            $category_id = (int)$type;
        }

        // Xử lý hình ảnh: lưu file với timestamp, lưu vào DB chỉ tên file
        $image_path = '';
        if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
            $upload_dir = __DIR__ . '/../img/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $original_name = basename($_FILES['product-image']['name']);
            $file_name = time() . '_' . $original_name;
            $target_file = $upload_dir . $file_name;
            if (!move_uploaded_file($_FILES['product-image']['tmp_name'], $target_file)) {
                die("Không thể tải lên file.");
            }
            $image_path = $file_name; // chỉ lưu tên file
        }

        // hidden: Hiển thị => 0, còn lại => 1
        $hidden = ($status === 'Hiển thị') ? 0 : 1;

        // Lấy supplier_id từ form nếu có, mặc định 1
        $supplier_id = 1;
        if (isset($_POST['supplier']) && is_numeric($_POST['supplier'])) {
            $supplier_id = (int)$_POST['supplier'];
        }

        // Nhận các trường mô tả và liên kết
        $product_link = isset($_POST['product_link']) ? trim($_POST['product_link']) : '';
        $productnolog_link = isset($_POST['productnolog_link']) ? trim($_POST['productnolog_link']) : '';
        $product_description = isset($_POST['product_description']) ? (string)$_POST['product_description'] : '';

        // (product_id, category_id, supplier_id, product_name, product_image, product_status, product_price, product_description, product_link, productnolog_link, hidden)
        $sql = "INSERT INTO sanpham(product_id, category_id, supplier_id, product_name, product_image, product_status, product_price, product_description, product_link, productnolog_link, hidden)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Lỗi chuẩn bị SQL: " . $conn->error);
        }

        $priceFloat = is_numeric($price) ? (float)$price : 0.0;
        $stmt->bind_param("siisssdsssi",
            $product_code,
            $category_id,
            $supplier_id,
            $product_name,
            $image_path,
            $status,
            $priceFloat,
            $product_description,
            $product_link,
            $productnolog_link,
            $hidden
        );

        if($stmt->execute()){
            header("Location: ../pages/addpro.php?success");
            exit();
        } else {
            echo "SQL Error: " . $stmt->error;
            exit();
        }

        $stmt->close();
        $conn->close();
    }
?>
