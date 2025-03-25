<?php
// เริ่มต้นเซสชัน
session_start();

// ตัวแปรเก็บค่าหมายเลขพัสดุ
$product = null;
$error = null;
$product_id_input = "";

// ตรวจสอบการส่งฟอร์ม (GET)
if (isset($_GET['product_id'])) {
    $product_id = trim($_GET['product_id']);
    $product_id_input = $product_id; // เก็บค่าไว้สำหรับแสดงใน input

    if (!empty($product_id)) {
        // เรียก API
        $api_url = "https://script.google.com/macros/s/AKfycbwpVEIWY5JLIVTc5hNdgttXOQTunX42KVuCvQwVJWZToSZPoeXvp5MrJ4EiMeuTnG4dRA/exec?product_id=" . urlencode($product_id);

        $response = file_get_contents($api_url);
        $data = json_decode($response, true);

        // ตรวจสอบผลลัพธ์
        if ($data && isset($data['success']) && $data['success'] && !empty($data['product'])) {
            $product = $data['product'];
        } else {
            $error = "ไม่พบรายการพัสดุที่ค้นหา";
        }
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

        <form method="GET" action="">
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

                <div class="grid grid-cols-3 gap-2 text-white text-2xl">
                    <p><strong>ชื่ออากาศยาน:</strong> <?= htmlspecialchars($product['aircraft_name']) ?></p>
                    <p><strong>ซ่อมทำระดับ:</strong> <?= htmlspecialchars($product['maintenance_level']) ?></p>
                    <p><strong>ระบบ:</strong> <?= htmlspecialchars($product['system']) ?></p>
                    <p><strong>หมายเลขพัสดุ:</strong> <?= htmlspecialchars($product['product_id']) ?></p>
                    <p><strong>รายการพัสดุ:</strong> <?= htmlspecialchars($product['product_name']) ?></p>
                    <p><strong>PART NUMBER:</strong> <?= htmlspecialchars($product['part_number']) ?></p>
                    <p><strong>จำนวน:</strong> <?= htmlspecialchars($product['quantity']) ?>
                        <?= htmlspecialchars($product['unit']) ?></p>
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