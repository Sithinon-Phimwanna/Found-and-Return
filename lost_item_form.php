<?php
session_start(); // เริ่มต้นเซสชัน

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // ถ้ายังไม่ได้ล็อกอินให้ไปหน้า login
    exit;
}

// ป้องกันการแคช
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0"); // ให้หมดอายุทันที
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งของหาย</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>แจ้งของหาย</h1>
    <form action="submit_lost_item.php" method="POST" enctype="multipart/form-data">
        <label>ชื่อผู้แจ้ง:</label>
        <input type="text" name="owner_name" required><br>
        <label>ช่องทางการติดต่อ:</label>
        <input type="text" name="owner_contact" required><br>
        <label>ประเภทของหาย:</label>
        <input type="text" name="item_type" required><br>
        <label>รายละเอียด:</label>
        <textarea name="item_description" required></textarea><br>
        <label>วันที่ของหาย:</label>
        <input type="date" name="lost_date" required><br>
        <label>สถานที่ของหาย:</label>
        <input type="text" name="lost_location" required><br>
        <label>อัพโหลดภาพของที่หาย(ถ้ามี):</label>
        <input type="file" name="item_image"><br>
        <button type="submit">ส่งข้อมูล</button>
        <a href="index.php" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;">กลับหน้าแรก</a>
    </form>
</body>
</html>
