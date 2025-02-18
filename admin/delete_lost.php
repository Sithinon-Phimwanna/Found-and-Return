<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่าได้รับค่า item_id หรือไม่
    if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
        $item_id = intval($_POST['item_id']);

        // ดึงข้อมูลภาพที่เกี่ยวข้องกับ item_id
        $query = "SELECT item_image, finder_image FROM lost_items WHERE item_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // ลบภาพในโฟลเดอร์
            $item_images = explode(',', $row['item_image']);
            $finder_images = explode(',', $row['finder_image']);

            foreach ($item_images as $image) {
                $file_path = '../lost_images/' . trim($image);
                if (file_exists($file_path)) {
                    unlink($file_path); // ลบไฟล์ภาพ
                }
            }

            foreach ($finder_images as $image) {
                $file_path = '../return_images/' . trim($image);
                if (file_exists($file_path)) {
                    unlink($file_path); // ลบไฟล์ภาพ
                }
            }

            // ลบข้อมูลในฐานข้อมูล
            $delete_query = "DELETE FROM lost_items WHERE item_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param("i", $item_id);
            $delete_stmt->execute();

            if ($delete_stmt->affected_rows > 0) {
                header("Location: lost_items_list.php?success=1");
            } else {
                echo "ไม่สามารถลบข้อมูลได้";
            }
        } else {
            echo "ไม่พบข้อมูลที่ต้องการลบ";
        }

        $stmt->close();
        $delete_stmt->close();
    } else {
        echo "ไม่ได้รับค่า item_id";
    }
} else {
    echo "ไม่ได้รับคำขอ POST";
}

$mysqli->close();
?>
