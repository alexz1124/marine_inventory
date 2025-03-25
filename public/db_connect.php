<?php
// ข้อมูลการเชื่อมต่อ
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}
?>
