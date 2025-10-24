<?php
define('BASE_URL', '/OOAD-main/');
session_start();

if (!isset($_SESSION['user_name'])) {
    // Nếu chưa đăng nhập → quay lại trang login
    header("Location: " . BASE_URL . "view/pages/login.php");
    exit();
}

require_once __DIR__ . '/controllers/ProductController.php';

$controller = new ProductController();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$page = $_GET['page'] ?? 1;

// Gọi hàm hiển thị sản phẩm hoặc chi tiết
if ($action === 'detail' && $id) {
    $controller->showProductDetail($id);
} else {
    $controller->showProducts($page);
}
?>
