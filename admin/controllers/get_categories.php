<?php
header('Content-Type: application/json');
require '../config/connect.php';

$sql = "SELECT category_id, category_name FROM category ORDER BY category_name";
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


