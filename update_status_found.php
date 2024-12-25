<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $found_id = $_POST['found_id'];
    $status_id = $_POST['status_id'];

    // แค่ทำการอัปเดตสถานะ
    $update_query = "UPDATE found_items SET status_id = ? WHERE found_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('ii', $status_id, $found_id);
    $update_stmt->execute();

    // ปิดการเชื่อมต่อฐานข้อมูล
    $update_stmt->close();
    $mysqli->close();

    // รีไดเร็กต์ไปยังหน้ารายการของที่พบ
    header("Location: found_items_list.php");  // เปลี่ยน "found_items_list.php" เป็นชื่อหน้าเดิมที่คุณต้องการ
    exit();
}
?>
