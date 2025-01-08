<?php
session_start();

// เปิดการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// นำเข้าไฟล์ config.php
require 'config.php';

// ตรวจสอบว่าได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Location: login.php');
    exit;
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
try {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT UserAdminName, email FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query); // ใช้ $mysqli แทน $conn
    $stmt->bind_param("d", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("ไม่พบข้อมูลผู้ใช้ในระบบ");
    }

    $name = $user['UserAdminName'] ?? '';
    $contact = $user['email'] ?? '';
} catch (Exception $e) {
    die("ข้อผิดพลาด: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งเก็บทรัพย์สินได้</title>
    <link rel="stylesheet" href="css/stylefrom.css">
</head>
<body>
    <h1>แจ้งเก็บทรัพย์สินได้</h1>
    <form action="submit_found_item.php" method="POST" enctype="multipart/form-data">
        <label>ชื่อผู้แจ้ง:</label>
        <input type="text" name="finder_name" value="<?php echo htmlspecialchars($name); ?>" required><br>
        <label>ช่องทางการติดต่อ:</label>
        <input type="text" name="finder_contact" value="<?php echo htmlspecialchars($contact); ?>" required><br>
        <label>ทรัพย์สิน:</label>
        <input type="text" name="found_type" required><br>
        <label>รายละเอียด:</label>
        <textarea name="found_description" required></textarea><br>
        <label>วันที่เก็บได้:</label>
        <input type="date" name="found_date" required><br>
        <label>สถานที่เก็บได้:</label>
        <input type="text" name="found_location" required><br>
        <label>อัพโหลดภาพทรัพย์สินที่เก็บได้:</label>
        <input type="file" name="found_image[]" multiple><br>
        <button type="submit">ส่งข้อมูล</button>
    </form>
</body>
</html>
