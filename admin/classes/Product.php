<?php
class Product {
    private $conn;
    private $product_id;
    private $status;
    private $uploadDir;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->uploadDir = __DIR__ . '/../img/';
    }

    // Gán dữ liệu từ request
    public function setData($product_id, $status) {
        $this->product_id = trim($product_id);
        $this->status = trim($status);
    }

    // Hàm xử lý chính
    public function process() {
        if (empty($this->product_id) || $this->status === null) {
            return $this->response(false, "Thiếu thông tin sản phẩm");
        }

        error_log("Status received: [{$this->status}]");

        if ($this->status === 'Ẩn') {
            return $this->hide();
        } else {
            return $this->delete();
        }
    }

    private function hide() {
        error_log("Setting hidden=1 for product: {$this->product_id}");
        $stmt = $this->conn->prepare("UPDATE sanpham SET hidden = 1 WHERE product_id = ?");
        $stmt->bind_param("s", $this->product_id);

        if ($stmt->execute()) {
            $stmt->close();
            return $this->response(true, "Đã ẩn sản phẩm thành công");
        } else {
            $stmt->close();
            return $this->response(false, "Không thể cập nhật sản phẩm: " . $this->conn->error);
        }
    }

    private function delete() {
        error_log("Deleting product: {$this->product_id}");
        $stmt = $this->conn->prepare("DELETE FROM sanpham WHERE product_id = ?");
        $stmt->bind_param("s", $this->product_id);

        if ($stmt->execute()) {
            $stmt->close();
            return $this->response(true, "Đã xóa sản phẩm thành công");
        } else {
            $stmt->close();
            return $this->response(false, "Không thể xóa sản phẩm: " . $this->conn->error);
        }
    }

    private function response($success, $message = "") {
        return json_encode([
            "success" => $success,
            "message" => $message
        ]);
    }
    public function getAll() {
        $sql = "
            SELECT 
                sp.product_id,
                sp.product_name,
                sp.product_image,
                sp.product_status,
                sp.product_price,
                sp.hidden,
                sp.category_id,
                sp.supplier_id,
                lsp.name_type AS category_name,
                ncc.supplier_name AS supplier_name
            FROM sanpham sp
            LEFT JOIN loaisanpham lsp ON lsp.category_id = sp.category_id
            LEFT JOIN nhacungcap ncc ON ncc.supplier_id = sp.supplier_id
            ORDER BY sp.product_id
        ";

        $result = $this->conn->query($sql);
        $products = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }

        return $products;
    }
    
     private function uploadImage($file) {
        if (!isset($file) || $file['error'] !== 0) {
            return ''; // Không có file
        }

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $originalName = basename($file['name']);
        $fileName = time() . '_' . $originalName;
        $targetFile = $this->uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $fileName;
        } else {
            throw new Exception("Không thể tải lên file.");
        }
    }

    public function addProduct($data, $file) {
        $product_code = isset($data['product-code']) ? trim($data['product-code']) : '';
        $product_name = isset($data['product-name']) ? trim($data['product-name']) : '';
        $type = isset($data['type']) ? (int)$data['type'] : 0;
        $price = isset($data['price']) && is_numeric($data['price']) ? (float)$data['price'] : 0.0;
        $status = isset($data['status']) ? $data['status'] : '';
        $hidden = ($status === 'Hiển thị') ? 0 : 1;

        $supplier_id = isset($data['supplier']) && is_numeric($data['supplier']) ? (int)$data['supplier'] : 1;
        $product_link = isset($data['product_link']) ? trim($data['product_link']) : '';
        $productnolog_link = isset($data['productnolog_link']) ? trim($data['productnolog_link']) : '';
        $product_description = isset($data['product_description']) ? (string)$data['product_description'] : '';

        // Upload hình ảnh
        $image_path = $this->uploadImage($file);

        $sql = "INSERT INTO sanpham(product_id, category_id, supplier_id, product_name, product_image, product_status, 
                product_price, product_description, product_link, productnolog_link, hidden)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị SQL: " . $this->conn->error);
        }

        $stmt->bind_param("siisssdsssi",
            $product_code,
            $type,
            $supplier_id,
            $product_name,
            $image_path,
            $status,
            $price,
            $product_description,
            $product_link,
            $productnolog_link,
            $hidden
        );

        if (!$stmt->execute()) {
            throw new Exception("SQL Error: " . $stmt->error);
        }

        $stmt->close();
        return true;
    }


     

    private function deleteImage($imageName) {
        if (empty($imageName)) return;
        $filePath = realpath($this->uploadDir . $imageName);
        if ($filePath && is_file($filePath)) {
            @unlink($filePath);
        }
    }

    
    

    // ---- CẬP NHẬT ----
    public function updateProduct($data, $file) {
        $product_id = $data['product_id'] ?? '';
        $product_name = $data['product_name'] ?? '';
        $product_price = (float)($data['product_price'] ?? 0);
        $product_status = $data['product_status'] ?? '';
        $product_type_name = $data['product_type'] ?? '';
        $delete_image = isset($data['delete_image']) && $data['delete_image'] === 'true';

        // Map tên loại -> category_id
        $mapped_category_id = null;
        if ($product_type_name !== '') {
            $stmt = $this->conn->prepare("SELECT category_id FROM loaisanpham WHERE name_type=? LIMIT 1");
            $stmt->bind_param("s", $product_type_name);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $mapped_category_id = (int)$row['category_id'];
            }
            $stmt->close();
        }

        // Lấy thông tin hiện tại
        $stmt = $this->conn->prepare("SELECT hidden, product_image FROM sanpham WHERE product_id=?");
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $current_hidden = $res['hidden'] ?? 1;
        $current_image = $res['product_image'] ?? '';
        $stmt->close();

        // Quy tắc hidden
        $hidden_value = ($product_status === 'Hiển thị') ? 0 : $current_hidden;

        // Xử lý ảnh
        $new_image = '';

        if ($delete_image) {
            $this->deleteImage($current_image);
            $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, hidden=?, product_image=''"
                 . ($mapped_category_id !== null ? ", category_id=?" : "") . " WHERE product_id=?";
            $stmt = $this->conn->prepare($sql);
            if ($mapped_category_id !== null)
                $stmt->bind_param("sdsiss", $product_name, $product_price, $product_status, $hidden_value, $mapped_category_id, $product_id);
            else
                $stmt->bind_param("sdsss", $product_name, $product_price, $product_status, $hidden_value, $product_id);
        }
        elseif (isset($file['product_image']) && $file['product_image']['error'] === 0) {
            $new_image = $this->uploadImage($file['product_image']);
            $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, hidden=?, product_image=?"
                 . ($mapped_category_id !== null ? ", category_id=?" : "") . " WHERE product_id=?";
            $stmt = $this->conn->prepare($sql);
            if ($mapped_category_id !== null)
                $stmt->bind_param("sdsssis", $product_name, $product_price, $product_status, $hidden_value, $new_image, $mapped_category_id, $product_id);
            else
                $stmt->bind_param("sdsssis", $product_name, $product_price, $product_status, $hidden_value, $new_image, $product_id);
        } 
        else {
            $sql = "UPDATE sanpham SET product_name=?, product_price=?, product_status=?, hidden=?"
                 . ($mapped_category_id !== null ? ", category_id=?" : "") . " WHERE product_id=?";
            $stmt = $this->conn->prepare($sql);
            if ($mapped_category_id !== null)
                $stmt->bind_param("sdsiis", $product_name, $product_price, $product_status, $hidden_value, $mapped_category_id, $product_id);
            else
                $stmt->bind_param("sdsis", $product_name, $product_price, $product_status, $hidden_value, $product_id);
        }

        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi cập nhật: " . $stmt->error);
        }

        $stmt->close();
        return true;
    }
    public function getProductById($product_id) {
    if (empty($product_id)) {
        throw new Exception("Thiếu product_id");
    }

    $stmt = $this->conn->prepare("SELECT * FROM sanpham WHERE product_id = ?");
    if (!$stmt) {
        throw new Exception("Lỗi SQL: " . $this->conn->error);
    }

    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    } else {
        $stmt->close();
        throw new Exception("Không tìm thấy sản phẩm với ID: $product_id");
    }
}

}
?>
