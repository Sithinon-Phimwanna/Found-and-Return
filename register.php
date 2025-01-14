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
            echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ.'); window.location.href = 'login.php';</script>";
            exit;
        } else {
            $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Found & Return</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="#"><b>Found</b> & <b>Return</b></a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register a new membership</p>

      <form action="#" method="POST">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="ชื่อผู้ใช้" name="UserAdminID" id="UserAdminID" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="อีเมล" name="email" id="email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="รหัสผ่าน" name="Password" id="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="ชื่อ-นามสกุล" name="UserAdminName" id="UserAdminName" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-address-card"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="ตำแหน่ง" name="position_id" id="position_id" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-star"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="กลุ่ม" name="group_id" id="group_id" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-users"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="ระดับการเข้าถึง" name="level_id" id="level_id" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-wrench"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">สมัครสมาชิก</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>
<!-- /.register-box -->

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
