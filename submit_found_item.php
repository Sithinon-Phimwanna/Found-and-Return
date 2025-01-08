<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $finder_name = $_POST['finder_name'];
    $finder_contact = $_POST['finder_contact'];
    $found_type = $_POST['found_type'];
    $found_description = $_POST['found_description'];
    $found_location = $_POST['found_location'];

    // ใช้เวลาปัจจุบัน
    $found_date = date('Y-m-d H:i:s');

    // ตรวจสอบและจัดการอัปโหลดภาพหลายไฟล์
    $images = [];
    if (isset($_FILES['found_image']) && !empty($_FILES['found_image']['name'][0])) {
        $upload_dir = 'found_images/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
        }

        foreach ($_FILES['found_image']['name'] as $key => $filename) {
            $tmp_name = $_FILES['found_image']['tmp_name'][$key];
            $error = $_FILES['found_image']['error'][$key];
            $file_size = $_FILES['found_image']['size'][$key];
            $file_type = $_FILES['found_image']['type'][$key];

            if ($error === UPLOAD_ERR_OK) {
                if ($file_size > 1048576) { // ตรวจสอบขนาดไฟล์ (1MB)
                    echo "<script>alert('ไฟล์ $filename มีขนาดใหญ่เกินไป (สูงสุด 1MB)'); window.history.back();</script>";
                    exit;
                }

                if (!in_array($file_type, ['image/jpeg', 'image/png'])) { // ตรวจสอบประเภทไฟล์
                    echo "<script>alert('ไฟล์ $filename ต้องเป็น JPEG หรือ PNG เท่านั้น'); window.history.back();</script>";
                    exit;
                }

                // ใช้ชื่อไฟล์เดิม
                $target_file = $upload_dir . basename($filename);

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $images[] = basename($target_file); // เก็บชื่อไฟล์ที่อัปโหลดสำเร็จ
                } else {
                    echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์ $filename'); window.history.back();</script>";
                    exit;
                }
            }
        }
    }

    // ตรวจสอบว่ามีไฟล์ที่อัปโหลดไหม
    if (empty($images)) {
        echo "<script>alert('กรุณาอัปโหลดไฟล์'); window.history.back();</script>";
        exit;
    }

    // แปลงอาร์เรย์ชื่อไฟล์เป็นสตริง (ใช้ , แยก)
    $images_str = implode(',', $images);

    $status_id = 2; // 'พบ' ในตาราง statuses

    // เตรียมคำสั่ง SQL เพื่อบันทึกข้อมูล
    $stmt = $mysqli->prepare("INSERT INTO found_items (finder_name, finder_contact, found_type, found_description, found_date, found_location, found_image, status_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $mysqli->error);
    }

    // ผูกค่าตัวแปรกับคำสั่ง SQL
    $stmt->bind_param("sssssssi", $finder_name, $finder_contact, $found_type, $found_description, $found_date, $found_location, $images_str, $status_id);

    // บันทึกข้อมูลลงฐานข้อมูล
    if ($stmt->execute()) {
        echo "
        <div style='display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f5f5f5;' >
            <div style='text-align: center; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                <p style='font-size: 24px; color: #4CAF50; margin-bottom: 20px;'>บันทึกข้อมูลสำเร็จ</p>
                <a href='found_items_list.php' style='display: inline-block; font-size: 18px; color: #ffffff; background-color: #007BFF; padding: 10px 20px; text-decoration: none; border-radius: 4px; transition: background-color 0.3s;'>ดูรายการ</a>
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
