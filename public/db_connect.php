<?php
// เรียกใช้ autoload
require_once DIR . '/../vendor/autoload.php';

// ระบุโฟลเดอร์ที่มี .env (ไม่จำเป็นหากใช้ Railway Variables)
if (file_exists(DIR . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(DIR . '/../');
    $dotenv->load();
}

// ข้อมูลการเชื่อมต่อจาก Environment Variables
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'inventory';

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}
?>