<?php
session_start(); // เริ่มต้นเซสชัน

// เปิดการแสดงข้อผิดพลาดสำหรับการดีบัก
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบการล็อกอิน
if (isset($_SESSION['user_id'])) {
    // ตรวจสอบ level_id และเปลี่ยนเส้นทางไปตามบทบาท
    switch ($_SESSION['level_id']) {
        case 1:
            header('Location: staff1_index.php');
            exit;
        case 2:
            header('Location: ../staff2/staff2_index.php');
            exit;
        case 3:
            header('Location: ../admin/admin_index.php');
            exit;
        default:
            header('Location: login.php');
            exit;
    }
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
            $_SESSION['level_id'] = $user['level_id'];

            // เปลี่ยนเส้นทางไปยังหน้าแดชบอร์ดตาม level_id
            switch ($user['level_id']) {
                case 1:
                    header('Location: staff1_index.php');
                    exit;
                case 2:
                    header('Location: ../staff2/staff2_index.php');
                    exit;
                case 3:
                    header('Location: ../admin/admin_index.php');
                    exit;
                default:
                    $error = "ไม่พบสิทธิ์ที่เหมาะสม!";
            }
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
  <title>Found & Return| Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Found</b> & <b>Retrun</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">ลงชื่อเข้าใช้เพื่อเริ่มใช้เว็บสำหรับแอดมิน</p>

      <form method="post" action="">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" name="UserAdminID" id="UserAdminID" required>
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
          <div class="col-sm-4 offset-sm-4 d-flex justify-content-center">
            <button type="submit" class="btn btn-primary btn-block">Signin</button>
          </div>
        </div>
      </form>

      <?php if (isset($error)) { ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
      <?php } ?>

    </div>
  </div>
</div>

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
</body>
</html>
