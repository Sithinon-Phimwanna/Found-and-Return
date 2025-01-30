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
    $finder_image = ""; // เก็บชื่อไฟล์ทั้งหมดเป็นสตริง

    // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
    if (isset($_FILES['finder_image']) && !empty($_FILES['finder_image']['name'][0])) {
        $upload_dir = '../return_images/'; // โฟลเดอร์เก็บรูป
        $file_names = [];

        foreach ($_FILES['finder_image']['name'] as $key => $value) {
            $file_tmp = $_FILES['finder_image']['tmp_name'][$key];

            // ดึงชื่อไฟล์ต้นฉบับและนามสกุลไฟล์
            $original_name = pathinfo($_FILES['finder_image']['name'][$key], PATHINFO_FILENAME);
            $imageFileType = strtolower(pathinfo($_FILES['finder_image']['name'][$key], PATHINFO_EXTENSION));

            // สร้างชื่อไฟล์ใหม่ตามรูปแบบที่ต้องการ
            $formatted_timestamp = date("d-m-Y_H-i-s");
            $new_image_name = $formatted_timestamp . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $original_name) . "." . $imageFileType;
            $file_path = $upload_dir . $new_image_name;

            // ตรวจสอบประเภทไฟล์ที่อนุญาต
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowed_types)) {
                echo "ไฟล์ต้องเป็น JPG, JPEG, PNG หรือ GIF เท่านั้น: $new_image_name";
                continue;
            }

            // ตรวจสอบว่าการอัปโหลดสำเร็จหรือไม่
            if ($_FILES['finder_image']['error'][$key] === UPLOAD_ERR_OK && move_uploaded_file($file_tmp, $file_path)) {
                $file_names[] = $new_image_name;
            } else {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: $new_image_name";
            }
        }

        // รวมชื่อไฟล์เป็นสตริงเพื่อบันทึกลงฐานข้อมูล
        if (!empty($file_names)) {
            $finder_image = implode(',', $file_names);
        }
    }

    // ตรวจสอบและกำหนดค่าที่จะอัปเดต
    $deliverer = $_POST['deliverer'];
    $status_id = $_POST['status_id'];
    $item_id = $_POST['item_id'];

    if (!empty($finder_image)) {
        // หากมีการอัปโหลดไฟล์ ให้รวมข้อมูลรูป
        $query = "UPDATE lost_items SET finder_image = ?, deliverer = ?, status_id = ? WHERE item_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssi', $finder_image, $deliverer, $status_id, $item_id);
    } else {
        // ไม่มีรูปที่อัปโหลด อัปเดตเฉพาะ deliverer และ status_id
        $query = "UPDATE lost_items SET deliverer = ?, status_id = ? WHERE item_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssi', $deliverer, $status_id, $item_id);
    }

    // ดำเนินการอัปเดต
    if ($stmt->execute()) {
        header("Location: lost_items_list.php?success=4");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error;
    }
}
?>
