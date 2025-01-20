<?php
session_start();
require 'config.php';

// เปิดการแสดงข้อผิดพลาดสำหรับการดีบัก
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$mysqli) {
    die('การเชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . mysqli_connect_error());
}

// ตรวจสอบการอัปเดต
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบการอัปโหลดไฟล์
    if (isset($_FILES['finder_image']) && !empty($_FILES['finder_image']['name'][0])) {
        $upload_dir = 'return_images/';
        $file_names = [];

        foreach ($_FILES['finder_image']['name'] as $key => $value) {
            $file_tmp = $_FILES['finder_image']['tmp_name'][$key];
            $file_name = basename($_FILES['finder_image']['name'][$key]);
            $file_path = $upload_dir . $file_name;

            // ตรวจสอบข้อผิดพลาดในการอัปโหลดไฟล์
            if ($_FILES['finder_image']['error'][$key] !== UPLOAD_ERR_OK) {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: $file_name";
                continue; // ข้ามไฟล์นี้และไปที่ไฟล์ถัดไป
            }

            if (move_uploaded_file($file_tmp, $file_path)) {
                $file_names[] = $file_name;
            } else {
                echo "ไม่สามารถย้ายไฟล์: $file_name";
            }
        }

        $finder_image = implode(',', $file_names);
        // เตรียมคำสั่งอัปเดตพร้อมกับภาพ, ผู้ส่งมอบ, และสถานะ
        $query = "UPDATE lost_items SET finder_image = ?, deliverer = ?, status_id = ? WHERE item_id = ?";
        $stmt = $mysqli->prepare($query);

        // ตรวจสอบว่า prepare() สำเร็จหรือไม่
        if ($stmt === false) {
            die('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $mysqli->error);
        }

        $stmt->bind_param('sssi', $finder_image, $_POST['deliverer'], $_POST['status_id'], $_POST['item_id']);
    } else {
        // หากไม่มีการอัปโหลดไฟล์, อัปเดตแค่ผู้ส่งมอบและสถานะ
        $query = "UPDATE lost_items SET deliverer = ?, status_id = ? WHERE item_id = ?";
        $stmt = $mysqli->prepare($query);

        // ตรวจสอบว่า prepare() สำเร็จหรือไม่
        if ($stmt === false) {
            die('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $mysqli->error);
        }

        $stmt->bind_param('ssi', $_POST['deliverer'], $_POST['status_id'], $_POST['item_id']);
    }

    if ($stmt->execute()) {
        // ส่งกลับไปยังหน้าเดิมและแสดงข้อความ
        header("Location: lost_items_list.php?update_success=true");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error;
    }
}
?>
