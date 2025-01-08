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

require 'config.php'; // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าเป็นการส่งข้อมูลสมัครสมาชิกหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAdminID = $_POST['UserAdminID'];
    $password = $_POST['Password'];
    $userAdminName = $_POST['UserAdminName'];
    $position_id = $_POST['position_id'];
    $group_id = $_POST['group_id'];
    $level_id = $_POST['level_id'];
    $email = $_POST['email'];

    // ตรวจสอบว่าผู้ใช้มี UserAdminID หรือ Email นี้อยู่ในฐานข้อมูลหรือไม่
    $query = "SELECT * FROM users WHERE UserAdminID = ? OR email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $userAdminID, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว!";
    } else {
        // เข้ารหัสรหัสผ่านด้วย MD5
        $hashed_password = md5($password);

        // แทรกข้อมูลผู้ใช้ใหม่
        $query = "INSERT INTO users (UserAdminID, Password, UserAdminName, position_id, group_id, level_id, email) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssiiss', $userAdminID, $hashed_password, $userAdminName, $position_id, $group_id, $level_id, $email);

        if ($stmt->execute()) {
            $message = "สมัครสมาชิกสำเร็จ! <a href='login.php'>เข้าสู่ระบบ</a>";
        } else {
            $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="css/styleregiste.css">
</head>
<body>
    <h1>สมัครสมาชิก</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="UserAdminID">ชื่อผู้ใช้:</label>
        <input type="text" name="UserAdminID" id="UserAdminID" required>

        <label for="Password">รหัสผ่าน:</label>
        <input type="password" name="Password" id="Password" required>

        <label for="UserAdminName">ชื่อ-นามสกุล:</label>
        <input type="text" name="UserAdminName" id="UserAdminName" required>

        <label for="position_id">ตำแหน่ง:</label>
        <input type="number" name="position_id" id="position_id" required>

        <label for="group_id">กลุ่ม:</label>
        <input type="number" name="group_id" id="group_id" required>

        <label for="level_id">ระดับการเข้าถึง:</label>
        <input type="number" name="level_id" id="level_id" required>

        <label for="email">อีเมล:</label>
        <input type="email" name="email" id="email" required>

        <button type="submit">สมัครสมาชิก</button>
    </form>
</body>
</html>
