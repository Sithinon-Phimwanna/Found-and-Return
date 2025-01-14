<?php
session_start();
require 'config.php';

// ตรวจสอบการอัปเดต
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['finder_image']) && !empty($_FILES['finder_image']['name'][0])) {
        $upload_dir = 'return_images/';
        $file_names = [];

        foreach ($_FILES['finder_image']['name'] as $key => $value) {
            $file_tmp = $_FILES['finder_image']['tmp_name'][$key];
            $file_name = basename($_FILES['finder_image']['name'][$key]);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $file_names[] = $file_name;
            } else {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: $file_name";
            }
        }

        $finder_image = implode(',', $file_names);

        $stmt = $mysqli->prepare("UPDATE lost_items SET finder_image = ? WHERE item_id = ?");
        $stmt->bind_param('si', $finder_image, $_POST['item_id']);
        if ($stmt->execute()) {
            // ส่งกลับไปยังหน้าเดิมและแสดงข้อความ
            header("Location: lost_items_list.php?update_success=true");
            exit();
        } else {
            echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error;
        }
    } else {
        echo "กรุณาเลือกไฟล์เพื่ออัปโหลด";
    }
}
?>
