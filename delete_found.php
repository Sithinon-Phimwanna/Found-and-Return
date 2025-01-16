<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['found_id']) && !empty($_POST['found_id'])) {
        $found_id = intval($_POST['found_id']);

        // ดึงข้อมูลที่ต้องการลบ
        $query = "SELECT found_image FROM found_items WHERE found_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $found_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // ลบไฟล์ภาพ
            $images = explode(',', $row['found_image']);
            foreach ($images as $image) {
                $file_path = 'found_images/' . trim($image);
                
                // ตรวจสอบว่าไฟล์มีอยู่จริง
                if (file_exists($file_path)) {
                    // ลบไฟล์
                    if (unlink($file_path)) {
                        echo "ไฟล์ $file_path ถูกลบสำเร็จ<br>";
                    } else {
                        echo "ไม่สามารถลบไฟล์ $file_path ได้<br>";
                    }
                } else {
                    echo "ไม่พบไฟล์ $file_path<br>";
                }
            }

            // ลบข้อมูลในฐานข้อมูล
            $delete_query = "DELETE FROM found_items WHERE found_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param("i", $found_id);
            $delete_stmt->execute();

            if ($delete_stmt->affected_rows > 0) {
                // ลบสำเร็จแล้ว รีไดเร็กต์ไปหน้าก่อนหน้า
                header("Location: found_items_list.php?success=1");
                exit(); // ปิดการทำงานของ PHP script
            } else {
                echo "ไม่สามารถลบข้อมูลได้";
            }
        } else {
            echo "ไม่พบข้อมูลที่ต้องการลบ";
        }

        $stmt->close();
        $delete_stmt->close();
    } else {
        echo "ไม่ได้รับค่า found_id";
    }
} else {
    echo "ไม่ได้รับคำขอ POST";
}

$mysqli->close();
?>
