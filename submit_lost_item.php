<?php
// แสดงข้อผิดพลาดสำหรับการดีบัก
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $owner_name = $_POST['owner_name'] ?? '';
    $owner_contact = $_POST['owner_contact'] ?? '';
    $item_type = $_POST['item_type'] ?? '';
    $item_description = $_POST['item_description'] ?? '';
    $lost_date = $_POST['lost_date'] ?? '';
    $lost_location = $_POST['lost_location'] ?? '';

    // ตรวจสอบการกรอกข้อมูล
    if (empty($owner_name) || empty($owner_contact) || empty($item_type) || empty($item_description) || empty($lost_date) || empty($lost_location)) {
        echo "<script>alert('โปรดกรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
        exit;
    }

    // ตรวจสอบและจัดการภาพที่อัปโหลด
    $item_image = null;
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $file_size = $_FILES['item_image']['size'];
        $file_tmp = $_FILES['item_image']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        // ตรวจสอบขนาดไฟล์ไม่เกิน 1MB
        if ($file_size > 1048576) { // 1MB
            echo "<script>alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 1MB)'); window.history.back();</script>";
            exit;
        }

        // ตรวจสอบประเภทไฟล์
        if (!in_array($file_type, ['image/jpeg', 'image/png'])) {
            echo "<script>alert('ขออภัย, ไฟล์ต้องเป็นภาพ JPEG หรือ PNG เท่านั้น'); window.history.back();</script>";
            exit;
        }

        // อ่านข้อมูลไฟล์
        $item_image = file_get_contents($file_tmp);
    } elseif ($_FILES['item_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // ตรวจสอบหากเกิดข้อผิดพลาดในการอัปโหลดไฟล์
        echo "<script>alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 1MB)'); window.history.back();</script>";
        exit;
    }

    // เตรียมและบันทึกข้อมูลในฐานข้อมูล
    $stmt = $mysqli->prepare("
        INSERT INTO lost_items 
        (owner_name, owner_contact, item_type, item_description, lost_date, lost_location, item_image, status_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $status_id = 1; // 'หาย' ในตาราง statuses
    $stmt->bind_param("sssssssi", $owner_name, $owner_contact, $item_type, $item_description, $lost_date, $lost_location, $item_image, $status_id);

    // ตรวจสอบการทำงานของการเพิ่มข้อมูล
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
