<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $status_id = $_POST['status_id'];

    // กำหนดโฟลเดอร์สำหรับจัดเก็บภาพ
    $upload_dir = 'return_images/';
    $finder_image_path = null;

    if (isset($_FILES['finder_image']) && $_FILES['finder_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['finder_image']['tmp_name'];
        $file_size = $_FILES['finder_image']['size'];
        $file_name = $_FILES['finder_image']['name']; // ใช้ชื่อไฟล์เดิม
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // ตรวจสอบประเภทและขนาดไฟล์
        if ($file_size > 1048576) { // 1MB
            echo "<script>alert('ขออภัย, ขนาดไฟล์ใหญ่เกินไป (สูงสุด 1MB)'); window.history.back();</script>";
            exit;
        }

        if (!in_array($file_extension, ['jpeg', 'jpg', 'png'])) {
            echo "<script>alert('ขออภัย, ไฟล์ต้องเป็นภาพ JPEG หรือ PNG เท่านั้น'); window.history.back();</script>";
            exit;
        }

        // กำหนดชื่อไฟล์ใหม่ (ถ้าต้องการ) หรือใช้ชื่อไฟล์เดิม
        $new_file_name = $file_name;  // ถ้าไม่ต้องการเปลี่ยนชื่อ ให้ใช้ชื่อเดิม

        // กำหนด path สำหรับจัดเก็บ (ใช้ชื่อไฟล์ใหม่)
        $finder_image_path = $upload_dir . $new_file_name;

        // ย้ายไฟล์ไปยังโฟลเดอร์เป้าหมาย
        if (!move_uploaded_file($file_tmp, $finder_image_path)) {
            $error_code = $_FILES['finder_image']['error'];
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์ : $error_code'); window.history.back();</script>";
            exit;
        }

        // เก็บแค่ชื่อไฟล์ในฐานข้อมูล
        $finder_image_name = $new_file_name;
    }

    // เริ่มต้นคำสั่ง SQL สำหรับการอัปเดต
    if ($finder_image_name) {
        // ถ้ามีภาพให้เก็บชื่อไฟล์ในฐานข้อมูล
        $query = "
            UPDATE lost_items 
            SET status_id = ?, finder_image = ? 
            WHERE item_id = ?
        ";
        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            die('Error preparing statement: ' . $mysqli->error);
        }

        // ส่งชื่อไฟล์แทน path
        $stmt->bind_param('ssi', $status_id, $finder_image_name, $item_id);
    } else {
        // ถ้าไม่มีภาพ ก็อัปเดตเฉพาะสถานะ
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
