<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Product.php';

class ProductDAO {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getProducts($start = 0, $limit = 10) {
        $sql = "SELECT * FROM sanpham LIMIT ?, ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                $row['product_id'],
                $row['product_name'],
                $row['product_price'],
                $row['product_image'],
                $row['category_id']
            );
        }
        return $products;
    }

    public function getProductById($id) {
        $sql = "SELECT * FROM sanpham WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Product(
                $row['product_id'],
                $row['product_name'],
                $row['product_price'],
                $row['product_image'],
                $row['category_id']
            );
        }
        return null;
    }

    public function getTotalProducts() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM sanpham");
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function close() {
        $this->conn->close();
    }
}
