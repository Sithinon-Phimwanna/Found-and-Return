<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $status_id = $_POST['status_id'];

    // ตรวจสอบและอัปโหลด finder_image (ถ้ามี)
    $finder_image = null;
    $has_image = isset($_FILES['finder_image']) && $_FILES['finder_image']['error'] === UPLOAD_ERR_OK;

    // ตรวจสอบค่าของ $_FILES เพื่อให้แน่ใจว่าไฟล์ถูกส่งมาจริง
    if (empty($_FILES['finder_image']) || $_FILES['finder_image']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 2MB)'); window.history.back();</script>";
        exit;
    }

    // ตรวจสอบขนาดไฟล์ไม่เกิน 1MB
    if ($has_image) {
        $file_size = $_FILES['finder_image']['size'];
        if ($file_size > 1048576) { // 1MB
            echo "<script>alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 1MB)'); window.history.back();</script>";
            exit;
        }

        // ตรวจสอบประเภทไฟล์เป็น JPEG หรือ PNG เท่านั้น
        $file_tmp = $_FILES['finder_image']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        if (!in_array($file_type, ['image/jpeg', 'image/png'])) {
            echo "<script>alert('ขออภัย, ไฟล์ต้องเป็นภาพ JPEG หรือ PNG เท่านั้น'); window.history.back();</script>";
            exit;
        }

        // อ่านข้อมูลไฟล์
        $finder_image = file_get_contents($file_tmp);

        // เริ่มต้นคำสั่ง SQL สำหรับการอัปเดต
        $query = "
            UPDATE lost_items 
            SET status_id = ?, finder_image = ? 
            WHERE item_id = ?
        ";
        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            die('Error preparing statement: ' . $mysqli->error);
        }

        // ส่งข้อมูลภาพและ ID
        $stmt->bind_param('sbi', $status_id, $finder_image, $item_id);
        $stmt->send_long_data(1, $finder_image);  // ส่งข้อมูลรูปภาพแบบละเอียด
    } else {
        // ถ้าไม่มีภาพ ก็แค่ปรับสถานะ
        $query = "
            UPDATE lost_items 
            SET status_id = ? 
            WHERE item_id = ?
        ";
        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            die('Error preparing statement: ' . $mysqli->error);
        }

        $stmt->bind_param('si', $status_id, $item_id);
    }

    // ดำเนินการอัปเดต
    if ($stmt->execute()) {
        header('Location: lost_items_list.php'); // กลับไปยังหน้าเดิม
        exit;
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
}
?>
