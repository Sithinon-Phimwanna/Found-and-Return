<?php
session_start(); // Start session

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

require 'config.php'; // Include the database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAdminID = $_POST['UserAdminID'];
    $password = $_POST['Password'];
    $userAdminName = $_POST['UserAdminName'];
    $position_id = $_POST['position_id'];
    $group_id = $_POST['group_id'];
    $level_id = $_POST['level_id'];
    $email = $_POST['email'];

    // Check if the username or email already exists
    $query = "SELECT * FROM users WHERE UserAdminID = ? OR email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $userAdminID, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว!";
    } else {
        // Hash the password
        $hashed_password = md5($password);

        // Insert the new user into the database
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
    <link rel="stylesheet" href="css/stylefrom.css">
</head>
<body>
    <div class="container mt-3">
        <div class="row justify-content-start ms-10">
            <div class="col-10 col-md-10 col-lg-10">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title text-center">สมัครสมาชิก</h5>

                        <!-- Error message -->
                        <?php if (isset($error)): ?>
                            <p style="color: red;"><?= $error ?></p>
                        <?php endif; ?>

                        <!-- Success message -->
                        <?php if (isset($message)): ?>
                            <p style="color: green;"><?= $message ?></p>
                        <?php endif; ?>

                        <!-- Registration form -->
                        <form method="POST">
                            <div class="mb-1">
                                <label for="UserAdminID" class="form-label">ชื่อผู้ใช้:</label>
                                <input type="text" class="form-control-sm" name="UserAdminID" id="UserAdminID" required>
                            </div>
                            <div class="mb-1">
                                <label for="Password" class="form-label">รหัสผ่าน:</label>
                                <input type="password" class="form-control-sm" name="Password" id="Password" required>
                            </div>
                            <div class="mb-1">
                                <label for="UserAdminName" class="form-label">ชื่อ-นามสกุล:</label>
                                <input type="text" class="form-control-sm" name="UserAdminName" id="UserAdminName" required>
                            </div>
                            <div class="mb-1">
                                <label for="position_id" class="form-label">ตำแหน่ง:</label>
                                <input type="number" class="form-control-sm" name="position_id" id="position_id" required>
                            </div>
                            <div class="mb-1">
                                <label for="group_id" class="form-label">กลุ่ม:</label>
                                <input type="number" class="form-control-sm" name="group_id" id="group_id" required>
                            </div>
                            <div class="mb-1">
                                <label for="level_id" class="form-label">ระดับการเข้าถึง:</label>
                                <input type="number" class="form-control-sm" name="level_id" id="level_id" required>
                            </div>
                            <div class="mb-1">
                                <label for="email" class="form-label">อีเมล:</label>
                                <input type="email" class="form-control-sm" name="email" id="email" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
