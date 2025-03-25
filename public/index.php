<?php
// เริ่มต้นเซสชัน
session_start();

// เชื่อมต่อฐานข้อมูล
require 'db_connect.php';

// ตัวแปรเก็บค่าหมายเลขพัสดุ
$product = null;
$error = null;
$product_id_input = "";

// ตรวจสอบการส่งฟอร์ม (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = trim($_POST['product_id']);
    $product_id_input = $product_id; // เก็บค่าไว้สำหรับแสดงใน input

    // ✅ ตรวจสอบรูปแบบ Input (รับได้เฉพาะ A-Z, 0-9, ขีดกลาง)
    if (!empty($product_id) && preg_match("/^[A-Za-z0-9\-]+$/", $product_id)) {

        // ✅ ใช้ Prepared Statement ป้องกัน SQL Injection
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("s", $product_id); // "s" = string
        $stmt->execute();
        $result = $stmt->get_result();

        // ตรวจสอบผลลัพธ์
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc(); // ดึงข้อมูลจากฐานข้อมูล
        } else {
            $error = "ไม่พบรายการพัสดุที่ค้นหา"; // ไม่เจอข้อมูล
        }

        $stmt->close(); // ปิด statement
    } else {
        $error = "รูปแบบหมายเลขพัสดุไม่ถูกต้อง"; // รูปแบบไม่ถูกต้อง
    }

    $conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล

    // ✅ รีเซ็ตค่า Session ให้ปลอดภัย
    session_regenerate_id(true);
    $_SESSION['product'] = $product ?? null;
    $_SESSION['error'] = $error;
    $_SESSION['product_id_input'] = $product_id_input;

    // Redirect เพื่อรีเซ็ต POST
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// ตรวจสอบการรีเฟรชหน้า (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['product']) || isset($_SESSION['error'])) {
        $product = $_SESSION['product'] ?? null;
        $error = $_SESSION['error'] ?? null;
        $product_id_input = $_SESSION['product_id_input'] ?? "";

        // ล้างค่าในเซสชันเมื่อรีเฟรชหน้า
        session_unset();
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบค้นหาพัสดุ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=TH+Sarabun+New&display=swap');

        body {
            font-family: 'TH Sarabun New', sans-serif;
            background-image: url('assets/images/bgmarine.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="flex justify-center items-center min-h-screen bg-gray-900 bg-opacity-50">
    <div class="bg-white bg-opacity-30 backdrop-blur-md p-8 rounded-lg shadow-lg w-full max-w-4xl">
        <h2 class="text-4xl font-bold text-center text-white mb-6">ระบบค้นหาพัสดุ</h2>

        <form method="POST" action="">
            <div class="mb-6">
                <label class="block text-white mb-2 text-2xl font-semibold">กรอกหมายเลขพัสดุ:</label>
                <input type="text" name="product_id" required value="<?= htmlspecialchars($product_id_input) ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-2xl">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 text-2xl">
                ค้นหา
            </button>
        </form>

        <!-- ส่วนแสดงข้อมูลพัสดุ -->
        <?php if ($product): ?>
            <div class="mt-4 p-6 bg-white-500 bg-opacity-30 shadow-lg rounded-lg backdrop-blur-md">
                <h3 class="text-2xl font-bold text-white mb-6">รายละเอียดพัสดุ</h3>

                <!-- เพิ่มจำนวนคอลัมน์ -->
                <div class="grid grid-cols-3 gap-2 text-white text-2xl">
                    <p><strong>ชื่ออากาศยาน:</strong> <?= htmlspecialchars($product['aircraft_name']) ?></p>
                    <p><strong>ซ่อมทำระดับ:</strong> <?= htmlspecialchars($product['maintenance_level']) ?></p>
                    <p><strong>ระบบ:</strong> <?= htmlspecialchars($product['system']) ?></p>
                    <p><strong>หมายเลขพัสดุ:</strong> <?= htmlspecialchars($product['product_id']) ?></p>
                    <p><strong>รายการพัสดุ:</strong> <?= htmlspecialchars($product['product_name']) ?></p>
                    <p><strong>PART NUMBER:</strong> <?= htmlspecialchars($product['part_number']) ?></p>
                    <p><strong>จำนวน:</strong> <?= htmlspecialchars($product['quantity']) ?>
                        <?= htmlspecialchars($product['unit']) ?>
                    </p>
                    <p><strong>ราคากลาง:</strong> <?= number_format($product['standard_price'], 2) ?> บาท</p>
                    <p><strong>หน่วยละ:</strong> <?= number_format($product['unit_price'], 2) ?> บาท</p>
                    <p><strong>รวมเป็นเงิน:</strong> <?= number_format($product['total_price'], 2) ?> บาท</p>
                    <p><strong>ปีงบประมาณ:</strong> <?= htmlspecialchars($product['fiscal_year']) ?></p>
                </div>
            </div>
        <?php elseif ($error): ?>
            <div class="mt-8 p-6 bg-red-100 rounded-lg text-red-700 text-xl">
                <?= $error ?>
            </div>
        <?php endif; ?>

    </div>
</body>


</html>