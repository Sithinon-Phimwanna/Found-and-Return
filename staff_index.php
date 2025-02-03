<?php
session_start(); // เริ่มเซสชัน

// ตรวจสอบการล็อกอินและบทบาทผู้ใช้
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); // ถ้ายังไม่ได้ล็อกอิน ให้เปลี่ยนเส้นทางไปหน้า login
  exit;
}
require 'config.php';

// ดึงวันที่ปัจจุบัน
$current_date = date('Y-m-d');

// ตรวจสอบว่ามี Cookie "visited_today" หรือไม่
if (!isset($_COOKIE['visited_today'])) {
    // ถ้ายังไม่มี Cookie ให้เพิ่มการนับในฐานข้อมูล
    $result = $mysqli->query("SELECT visit_count FROM visitor_counter WHERE visit_date = '$current_date'");

    if ($result->num_rows > 0) {
        // อัปเดตจำนวนผู้เข้าชมสำหรับวันนี้
        $mysqli->query("UPDATE visitor_counter SET visit_count = visit_count + 1 WHERE visit_date = '$current_date'");
    } else {
        // เพิ่มแถวใหม่สำหรับวันที่ปัจจุบัน
        $mysqli->query("INSERT INTO visitor_counter (visit_date, visit_count) VALUES ('$current_date', 1)");
    }

    // ตั้ง Cookie "visited_today" หมดอายุในเวลา 23:59:59
    $expiry_time = strtotime('tomorrow') - 1; // เวลาเที่ยงคืนวันนี้
    setcookie('visited_today', '1', $expiry_time);
}

// ดึงจำนวนผู้เข้าชมทั้งหมด
$result_total = $mysqli->query("SELECT SUM(visit_count) AS total_visits FROM visitor_counter");
$total_visits = $result_total->fetch_assoc()['total_visits'];

// ดึงจำนวนผู้เข้าชมวันนี้
$result_today = $mysqli->query("SELECT visit_count FROM visitor_counter WHERE visit_date = '$current_date'");
$visits_today = $result_today->fetch_assoc()['visit_count'];

// นับจำนวนแจ้งทรัพย์สินหาย
$result_lost = $mysqli->query("SELECT COUNT(*) AS lost_count FROM lost_items WHERE item_id");
$lost_count = $result_lost->fetch_assoc()['lost_count'];

// นับจำนวนแจ้งทรัพย์สินที่เก็บได้
$result_found = $mysqli->query("SELECT COUNT(*) AS found_count FROM found_items WHERE found_id");
$found_count = $result_found->fetch_assoc()['found_count'];

// นับจำนวนแจ้งทรัพย์สินหาย (เฉพาะวันปัจจุบัน)
$result_lost_today = $mysqli->query("SELECT COUNT(*) AS lost_count_today FROM lost_items WHERE item_id AND DATE(lost_date) = '$current_date'");
$lost_count_today = $result_lost_today->fetch_assoc()['lost_count_today'];

// นับจำนวนแจ้งทรัพย์สินที่เก็บได้ (เฉพาะวันปัจจุบัน)
$result_found_today = $mysqli->query("SELECT COUNT(*) AS found_count_today FROM found_items WHERE found_id AND DATE(found_date) = '$current_date'");
$found_count_today = $result_found_today->fetch_assoc()['found_count_today'];

// ตรวจสอบว่าผู้ใช้ล็อกอินและบทบาทเป็นแอดมินหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['level_id'] !== 2) {
    header('Location: login.php'); // ถ้าไม่ได้ล็อกอินหรือไม่ใช่แอดมิน ให้เปลี่ยนเส้นทางไปหน้า login
    exit;
}

// ป้องกันการแคชของเบราว์เซอร์
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ป้องกันการใช้งานเซสชันซ้ำ
session_regenerate_id(true);

// ดึงข้อมูลชื่อแอดมิน
$adminName = $_SESSION['UserAdminName'];
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
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">
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
        <a href="staff_index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="staff_index.php" class="brand-link">
      <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Found & Return</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="assets/dist/img/user-gear.png" class="img-circle elevation-2" alt="User Image">
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
                <a href="staff/found_item_form.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>แจ้งเก็บทรัพย์สินได้</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="staff/lost_item_form.php" class="nav-link">
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
                <a href="staff/found_items_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ตารางแจ้งทรัพย์สินที่เก็บได้</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="staff/lost_items_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ตารางแจ้งทรัพย์สินหาย</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-header">การจัดการ</li>
          <a href="#" class="nav-link">
              <i class="nav-icon far fa-user"></i>
              <p>
                จัดการ แอดมิน
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
          <li class="nav-item">
            <li class="nav-item">
                    <a href="logout.php" class="nav-link">
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
            <h1 class="m-0">หน้าหลัก</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">หน้าหลัก</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $lost_count ?></h3>

                <p>จำนวนทรัพย์สินหาย</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">แจ้งทรัพย์สินหาย (วันนี้): <?= $lost_count_today ?></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $found_count ?></h3>

                <p>จำนวนทรัพย์สินที่เก็บได้</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">แจ้งทรัพย์สินที่เก็บได้(วันนี้): <?= $found_count_today ?></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $total_visits ?></h3>

                <p>จำนวนผู้เข้าชม</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">จำนวนผู้เข้าชมวันนี้: <?= $visits_today ?></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>แผนภูมิ</h3>

                <p>แสดงจำนวนทรัพย์สิน</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="staff/chart.php" class="small-box-footer">คลิกเพื่อดูข้อมูล<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Summernote -->
<script src="assets/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="assets/dist/js/pages/dashboard.js"></script>
</body>
</html>
