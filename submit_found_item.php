<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $finder_name = $_POST['finder_name'];
    $finder_contact = $_POST['finder_contact'];
    $found_type = $_POST['found_type'];
    $found_description = $_POST['found_description'];
    $found_date = $_POST['found_date'];
    $found_location = $_POST['found_location'];

    // ตรวจสอบการอัปโหลดภาพ
    $image = null;
    if (isset($_FILES['found_image']) && $_FILES['found_image']['error'] === UPLOAD_ERR_OK) {
        // ตรวจสอบขนาดไฟล์และประเภทไฟล์
        $file_size = $_FILES['found_image']['size'];
        $file_type = $_FILES['found_image']['type'];

        if ($file_size > 1048576) {  // 1MB
            echo "
            <script>
                alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 1MB)');
                window.history.back();
            </script>";
            exit;
        }

        if (!in_array($file_type, ['image/jpeg', 'image/png'])) {
            echo "
            <script>
                alert('ขออภัย, ไฟล์ต้องเป็นภาพ JPEG หรือ PNG เท่านั้น');
                window.history.back();
            </script>";
            exit;
        }

        // อ่านข้อมูลไฟล์
        $image = file_get_contents($_FILES['found_image']['tmp_name']);
    } else if ($_FILES['found_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // ข้อผิดพลาดที่ไม่ใช่การไม่อัปโหลดไฟล์
        echo "
        <script>
            alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 1MB)');
                window.history.back();
        </script>";
        exit;
    }
    $status_id = 2; // 'พบ' ในตาราง statuses
    // เตรียมคำสั่ง SQL เพื่อบันทึกข้อมูล
    $stmt = $mysqli->prepare("INSERT INTO found_items (finder_name, finder_contact, found_type, found_description, found_date, found_location, found_image, status_id) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");

    if ($stmt === false) {
        die('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $mysqli->error);
    }

    // ผูกค่าตัวแปรกับคำสั่ง SQL
    $stmt->bind_param("sssssss", $finder_name, $finder_contact, $found_type, $found_description, $found_date, $found_location, $image);

    // บันทึกข้อมูลลงฐานข้อมูล
    if ($stmt->execute()) {
        echo "
    <div style='display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f5f5f5;'>
        <div style='text-align: center; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <p style='font-size: 24px; color: #4CAF50; margin-bottom: 20px;'>บันทึกข้อมูลสำเร็จ</p>
            <a href='found_items_list.php' style='display: inline-block; font-size: 18px; color: #ffffff; background-color: #007BFF; padding: 10px 20px; text-decoration: none; border-radius: 4px; transition: background-color 0.3s;'>
                ดูรายการ
            </a>
        </div>
    </div>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $mysqli->close();
}
?>
