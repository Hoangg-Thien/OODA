<?php
define('BASE_URL', '/OOAD-main/'); 
session_start();

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_id']); 
require_once __DIR__ . '/controllers/ProductController.php';


$controller = new ProductController();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$page = $_GET['page'] ?? 1;

if ($action === 'detail' && $id) {
    $controller->showProductDetail($id);    
} else {
    $controller->showProducts($page);
}
?>