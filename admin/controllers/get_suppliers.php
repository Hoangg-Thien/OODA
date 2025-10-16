<?php
header('Content-Type: application/json');
require '../config/connect.php';

$sql = "SELECT supplier_id, supplier_name FROM supplier ORDER BY supplier_name";
$result = $conn->query($sql);
$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
$conn->close();
?>


