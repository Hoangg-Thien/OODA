<?php
$host = 'localhost';
$username = 'c07u'; 
$password = 'v73vNzf5lnnuDs02'; 
$database = 'c07db';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>