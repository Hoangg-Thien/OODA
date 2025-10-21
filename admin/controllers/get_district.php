<?php
// bật hiển thị lỗi (chỉ bật khi debug, không bật trên production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../classes/Database.php';

$db = new Database();
$conn = $db->getConnection(); // **phải** là mysqli connection

class DistrictController {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getDistrictsByProvince($provinceId) {
        // trả về mặc định nếu không có provinceId hợp lệ
        $data = [
            ['id' => null, 'name' => 'Chọn một Quận/huyện']
        ];

        if (empty($provinceId) || !is_numeric($provinceId)) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        $stmt = $this->conn->prepare("SELECT district_id, name FROM district WHERE province_id = ?");
        if (!$stmt) {
            // nếu prepare lỗi, thêm log hoặc trả về lỗi cho debug
            return json_encode(['error' => 'Prepare failed: ' . $this->conn->error], JSON_UNESCAPED_UNICODE);
        }
        $stmt->bind_param("i", $provinceId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id' => $row['district_id'],
                'name' => $row['name']
            ];
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

// --- SỬA: truyền $conn (mysqli) chứ không phải $db (object Database)
$districtController = new DistrictController($conn);

// header để JS nhận đúng kiểu dữ liệu
header('Content-Type: application/json; charset=utf-8');

$provinceId = isset($_GET['province_id']) ? $_GET['province_id'] : null;
echo $districtController->getDistrictsByProvince($provinceId);
