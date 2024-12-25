<?php
session_start();

// ตรวจสอบว่าได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    
        // ป้องกันการแคชอย่างเข้มงวด
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

    header('Location: login.php'); // ถ้าไม่ได้ล็อกอินให้ไปหน้าเข้าสู่ระบบ
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งของที่พบ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>แจ้งของที่พบ</h1>
    <form action="submit_found_item.php" method="POST" enctype="multipart/form-data">
        <label>ชื่อผู้พบ:</label>
        <input type="text" name="finder_name" required><br>
        <label>ช่องทางการติดต่อ:</label>
        <input type="text" name="finder_contact" required><br>
        <label>ประเภทของที่พบ:</label>
        <input type="text" name="found_type" required><br>
        <label>รายละเอียดเพิ่มเติม:</label>
        <textarea name="found_description" required></textarea><br>
        <label>วันที่พบ:</label>
        <input type="date" name="found_date" required><br>
        <label>สถานที่พบ:</label>
        <input type="text" name="found_location" required><br>
        <label>อัพโหลดภาพของที่พบ:</label>
        <input type="file" name="found_image"><br>
        <button type="submit">ส่งข้อมูล</button>
        <a href="index.php" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;">กลับหน้าแรก</a>
    </form>
</body>
</html>
