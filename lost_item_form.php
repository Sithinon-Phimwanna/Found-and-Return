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
    <title>แจ้งทรัพย์สินหาย</title>
    <link rel="stylesheet" href="css/stylefrom.css">
</head>
<body>
    <h1>แจ้งทรัพย์สินหาย</h1>
    <form action="submit_lost_item.php" method="POST" enctype="multipart/form-data">
        <label>ชื่อผู้แจ้ง:</label>
        <input type="text" name="owner_name" required><br>
        <label>ช่องทางการติดต่อ:</label>
        <input type="text" name="owner_contact" required><br>
        <label>ทรัพย์สิน:</label>
        <input type="text" name="item_type" required><br>
        <label>รายละเอียด:</label>
        <textarea name="item_description" required></textarea><br>
        <label>วันที่หาย:</label>
        <input type="date" name="lost_date" required><br>
        <label>สถานที่หาย:</label>
        <input type="text" name="lost_location" required><br>
        <label>อัพโหลดภาพทรัพย์สิน(ถ้ามี):</label>
        <input type="file" name="item_image[]"multiple><br>
        <button type="submit">ส่งข้อมูล</button>
    </form>
</body>
</html>
