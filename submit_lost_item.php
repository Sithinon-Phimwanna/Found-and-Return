<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $owner_name = $_POST['owner_name'] ?? '';
    $owner_contact = $_POST['owner_contact'] ?? '';
    $item_type = $_POST['item_type'] ?? '';
    $item_description = $_POST['item_description'] ?? '';
    $lost_location = $_POST['lost_location'] ?? '';

    // ใช้เวลาปัจจุบัน
    $lost_date = date('Y-m-d H:i:s'); // วันที่และเวลาปัจจุบันในรูปแบบที่เหมาะสม

    // ตรวจสอบการกรอกข้อมูล
    if (empty($owner_name) || empty($owner_contact) || empty($item_type) || empty($item_description) || empty($lost_location)) {
        echo "<script>alert('โปรดกรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
        exit;
    }

    // ตรวจสอบการอัปโหลดภาพ
    $item_images = [];
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'][0] === UPLOAD_ERR_OK) {
        // ตรวจสอบแต่ละไฟล์
        $files = $_FILES['item_image'];
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                // ตรวจสอบขนาดไฟล์และประเภทไฟล์
                $file_size = $files['size'][$i];
                $file_type = $files['type'][$i];

                if ($file_size > 1048576) {  // 1MB
                    echo "<script>alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 1MB)'); window.history.back();</script>";
                    exit;
                }

                if (!in_array($file_type, ['image/jpeg', 'image/png'])) {
                    echo "<script>alert('ขออภัย, ไฟล์ต้องเป็นภาพ JPEG หรือ PNG เท่านั้น'); window.history.back();</script>";
                    exit;
                }

                // ใช้ชื่อไฟล์เดิม
                $image_name = $files['name'][$i];
                $target_dir = 'lost_images/'; // โฟลเดอร์ที่เก็บไฟล์
                $target_file = $target_dir . $image_name;

                // ตรวจสอบว่ามีไฟล์ที่มีชื่อเดียวกันอยู่แล้วในโฟลเดอร์หรือไม่
                if (file_exists($target_file)) {
                    echo "<script>alert('ขออภัย, ไฟล์นี้มีอยู่ในระบบแล้ว กรุณาเลือกไฟล์อื่น'); window.history.back();</script>";
                    exit;
                }

                // ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
                if (!move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                    echo "<script>alert('ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์'); window.history.back();</script>";
                    exit;
                }

                $item_images[] = $image_name; // บันทึกชื่อไฟล์ในอาเรย์
            }
        }
    } else {
        echo "<script>alert('ไม่ได้เลือกไฟล์อัปโหลด'); window.history.back();</script>";
        exit;
    }

    // รวมชื่อไฟล์ทั้งหมดเป็นสตริงที่คั่นด้วยเครื่องหมายจุลภาค
    $item_images_str = implode(',', $item_images);

    // เตรียมคำสั่ง SQL เพื่อบันทึกข้อมูล
    $stmt = $mysqli->prepare("
        INSERT INTO lost_items 
        (owner_name, owner_contact, item_type, item_description, lost_date, lost_location, item_image, status_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if ($stmt === false) {
        die('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $mysqli->error);
    }

    $status_id = 1; // 'หาย' ในตาราง statuses
    $stmt->bind_param("sssssssi", $owner_name, $owner_contact, $item_type, $item_description, $lost_date, $lost_location, $item_images_str, $status_id);

    // บันทึกข้อมูลลงฐานข้อมูล
    if ($stmt->execute()) {
        echo "
        <div style='display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f5f5f5;'>
            <div style='text-align: center; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                <p style='font-size: 24px; color: #4CAF50; margin-bottom: 20px;'>บันทึกข้อมูลสำเร็จ</p>
                <a href='lost_items_list.php' style='display: inline-block; font-size: 18px; color: #ffffff; background-color: #007BFF; padding: 10px 20px; text-decoration: none; border-radius: 4px; transition: background-color 0.3s;'>ดูรายการ</a>
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
