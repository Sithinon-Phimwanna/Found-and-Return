<?php
session_start(); // เริ่มต้นเซสชัน

// ตรวจสอบการล็อกอิน
if (isset($_SESSION['user_id'])) {
    header('Location: admin_index.php'); // ถ้าเคยล็อกอินแล้วจะไปที่หน้าแดชบอร์ด
    exit;
}

// ป้องกันการแคชของเบราว์เซอร์
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'config.php'; // เชื่อมต่อกับฐานข้อมูล

    $userAdminID = $_POST['UserAdminID'];
    $password = $_POST['Password'];

    // ค้นหาผู้ใช้ในฐานข้อมูล
    $query = "SELECT * FROM users WHERE UserAdminID = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $userAdminID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่านที่ถูกเข้ารหัส MD5
        if (md5($password) === $user['Password']) {
            // เริ่มต้นเซสชันหากล็อกอินสำเร็จ
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['UserAdminID'] = $user['UserAdminID'];
            $_SESSION['UserAdminName'] = $user['UserAdminName'];

            header('Location: admin_index.php'); // เปลี่ยนเส้นทางไปหน้าแดชบอร์ด
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        $error = "ชื่อผู้ใช้ไม่ถูกต้อง!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="Thu, 19 Nov 1981 08:52:00 GMT">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body>
    <h1>เข้าสู่ระบบ</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="UserAdminID">ชื่อผู้ใช้:</label>
        <input type="text" name="UserAdminID" id="UserAdminID" required>

        <label for="Password">รหัสผ่าน:</label>
        <input type="password" name="Password" id="Password" required>

        <button type="submit">เข้าสู่ระบบ</button>
    </form>
</body>
</html>
