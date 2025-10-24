<?php
require_once __DIR__ . '/../classes/ProductDAO.php';

class ProductController {
    private $dao;

    public function __construct() {
        $this->dao = new ProductDAO();
    }

    public function showProducts($page = 1, $limit = 6) {
        $total = $this->dao->getTotalProducts();
        $totalPages = ceil($total / $limit);
        $start = ($page - 1) * $limit;
        $products = $this->dao->getProducts($start, $limit);

        include __DIR__ . '/../view/product/ProductList.php';
    }

    public function showProductDetail($id) {
        $product = $this->dao->getProductById($id);
        include __DIR__ . '/../view/product/ProductDetails.php';
    }
}
?>
