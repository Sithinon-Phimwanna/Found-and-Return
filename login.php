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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Found</b> & <b>Retrun</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form method="post" action="">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" name="UserAdminID" id="UserAdminID" required >
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="Password" id="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
