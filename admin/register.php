<?php
session_start(); // เริ่มต้นเซสชัน

// ป้องกันการแคช
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
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
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../assets/plugins/summernote/summernote-bs4.min.css">
    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="../admin_index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../admin_index.php" class="brand-link">
      <img src="../assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Found & Return</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../assets/dist/img/user-gear.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
        <span class="me-3" style="color: white;"><?= htmlspecialchars($_SESSION['UserAdminName']); ?></span>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-edit"></i>
              <p>
              รายการแจ้ง
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="found_item_form.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>แจ้งเก็บทรัพย์สินได้</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="lost_item_form.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>แจ้งทรัพย์สินหาย</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                ตารางทรัพย์สิน
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="found_items_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ตารางแจ้งทรัพย์สินที่เก็บได้</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="lost_items_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ตารางแจ้งทรัพย์สินหาย</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-header">การจัดการ</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-user"></i>
              <p>
                จัดการ แอดมิน
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="register.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>สมัครสมากชิก</p>
                    </a>
                  </li>
            </ul>
            <li class="nav-item">
                    <a href="../logout.php" class="nav-link">
                      <i class="far fa-sign-out nav-icon"></i>
                      <p>ลงชื่อออก</p>
                    </a>
            </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">สมัครสมาชิก</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">สมัครสมากชิก</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
<div class="register-box1">
  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box">สมัครสมากชิกใหม่</p>

      <form action="" method="POST">
        <div class="input-group mb-3 col-sm-4">
          <input type="text" class="form-control" placeholder="ชื่อผู้ใช้" name="UserAdminID" id="UserAdminID" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3 col-sm-4">
          <input type="email" class="form-control" placeholder="อีเมล" name="email" id="email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3 col-sm-4">
          <input type="password" class="form-control" placeholder="รหัสผ่าน" name="Password" id="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3 col-sm-4">
          <input type="text" class="form-control" placeholder="ชื่อ-นามสกุล" name="UserAdminName" id="UserAdminName" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-address-card"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3 col-sm-4">
          <input type="text" class="form-control" placeholder="ตำแหน่ง" name="position_id" id="position_id" required list="position">
          <datalist id="position">
          <option value="1">
          <option value="2">
          <option value="3">
          <option value="4">
          <option value="5">
          <option value="6">
          <option value="7">
          <option value="8">
          <option value="9">
        </datalist>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-star"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3 col-sm-4">
          <input type="text" class="form-control" placeholder="กลุ่ม" name="group_id" id="group_id" required list="group">
          <datalist id="group">
          <option value="1">
          <option value="2">
          <option value="3">
          <option value="4">
          <option value="5">
        </datalist>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-users"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3 col-sm-4">
          <input type="text" class="form-control" placeholder="ระดับการเข้าถึง" name="level_id" id="level_id" required list="level">
          <datalist id="level">
          <option value="1"> สตาฟ1
          <option value="2"> สตาฟ2
          <option value="3"> แอดมิน
        </datalist>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-wrench"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3 col-sm-4">
          <!-- /.col -->
          <div class="col-sm-4 offset-sm-4 d-flex justify-content-center">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <?php
    require 'config.php'; // เรียกไฟล์เชื่อมต่อฐานข้อมูล

if (!isset($mysqli)) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้ โปรดตรวจสอบไฟล์ config");
}

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // กรองข้อมูลที่รับเข้ามา
    $userAdminID = htmlspecialchars(trim($_POST['UserAdminID']));
    $password = htmlspecialchars(trim($_POST['Password']));
    $userAdminName = htmlspecialchars(trim($_POST['UserAdminName']));
    $position_id = intval($_POST['position_id']);
    $group_id = intval($_POST['group_id']);
    $level_id = intval($_POST['level_id']);
    $email = htmlspecialchars(trim($_POST['email']));

    // ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลซ้ำหรือไม่
    $query = "SELECT * FROM users WHERE UserAdminID = ? OR email = ?";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("เกิดข้อผิดพลาดในคำสั่งเตรียมฐานข้อมูล: " . $mysqli->error);
    }
    $stmt->bind_param('ss', $userAdminID, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // SweetAlert เมื่อข้อมูลซ้ำ
        echo '
        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
        echo '<script>
                setTimeout(function() {
                    swal({
                    title: "ข้อมูลซ้ำ!",
                    text: "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว กรุณาลองใหม่อีกครั้ง",
                    type: "error"
                    }, function() {
                    window.location = "register.php"; // กลับไปหน้าสมัครสมาชิก
                    });
                }, 1000);
              </script>';
        $stmt->close();
        exit;
    }
    $stmt->close();

    // เข้ารหัสรหัสผ่าน
    $hashed_password = md5($password);

    // เพิ่มผู้ใช้ใหม่ในฐานข้อมูล
    $query = "INSERT INTO users (UserAdminID, Password, UserAdminName, position_id, group_id, level_id, email) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("เกิดข้อผิดพลาดในคำสั่งเตรียมฐานข้อมูล: " . $mysqli->error);
    }
    $stmt->bind_param('sssiiss', $userAdminID, $hashed_password, $userAdminName, $position_id, $group_id, $level_id, $email);

    if ($stmt->execute()) {
        // SweetAlert เมื่อสมัครสำเร็จ
        echo '
        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
        echo '<script>
                setTimeout(function() {
                    swal({
                    title: "สมัครสมาชิกสำเร็จ!",
                    text: "คุณได้สมัครสมาชิกเรียบร้อยแล้ว",
                    type: "success"
                    }, function() {
                    window.location = "register.php"; // กลับไปหน้าสมัครสมาชิก
                    });
                }, 1000);
              </script>';
    } else {
        // SweetAlert เมื่อสมัครไม่สำเร็จ
        echo '
        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
        echo '<script>
                setTimeout(function() {
                    swal({
                    title: "เกิดข้อผิดพลาด!",
                    text: "ไม่สามารถสมัครสมาชิกได้ กรุณาลองใหม่อีกครั้ง",
                    type: "error"
                    }, function() {
                    window.location = "register.php"; // กลับไปหน้าสมัครสมาชิก
                    });
                }, 1000);
              </script>';
    }
    $stmt->close();
    exit;
}
?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer text-center">
    <strong>สำนักวิทยบริการและเทคโนโลยีสารสนเทศ มหาวิทยาลัยราชภัฏพิบูลสงราม. &copy; 2024 <a href="https://library.psru.ac.th/">LIBRARY.PSRU</a>.</strong>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Summernote -->
<script src="../assets/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../assets/dist/js/pages/dashboard.js"></script>
</body>
</html>
