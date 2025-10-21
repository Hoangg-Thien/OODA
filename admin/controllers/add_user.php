<?php
header('Content-Type: application/json');
require '../classes/Database.php';
require '../classes/User.php';
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Phương thức không hợp lệ");
    }
    
      // ✅ Tạo đối tượng Database và lấy kết nối
    $db = new Database();
    $conn = $db->getConnection();

      // ✅ Tạo đối tượng User và thêm người dùng mới
    
    $user = new User($conn);
    $user->addUser($_POST);

    echo json_encode([
        "status" => "success",
        "message" => "Thêm người dùng thành công"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>
