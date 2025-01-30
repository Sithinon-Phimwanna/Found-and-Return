<?php
session_start();
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



    // ส่งกลับไปยังหน้าเดิม
    header('Location: found_items_list.php?success=4' );
    exit();
}
?>
