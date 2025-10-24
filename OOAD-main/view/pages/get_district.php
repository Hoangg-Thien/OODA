<?php
require_once __DIR__ . '/../../classes/Database.php';
$db = new Database();
$conn = $db->connect();

if (isset($_GET['province_id'])) {
    $provinceId = intval($_GET['province_id']);
    $query = "SELECT * FROM district WHERE province_id = $provinceId";
    $result = $conn->query($query);

    echo '<option value="">Chọn một quận/huyện</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['district_id'] . '">' . htmlspecialchars($row['name']) . '</option>';
    }
}
$conn->close();
?>
