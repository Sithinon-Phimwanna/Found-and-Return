<?php
session_start();
require 'config.php';

// เปิดการแสดงผลข้อผิดพลาดเพื่อช่วยในการดีบัก
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่
if ($mysqli->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $mysqli->connect_error);
}

// รับค่าปี, เดือน และสถานะจาก GET (ถ้ามี)
$year = isset($_GET['year']) ? $_GET['year'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// คำสั่ง SQL เพื่อดึงข้อมูลนับตามปี, เดือน
$query = "
    SELECT 
        YEAR(found_date) AS year,
        MONTH(found_date) AS month,
        COUNT(found_id) AS count
    FROM 
        found_items
    WHERE 
        1=1
";

// กรองข้อมูลตามปี, เดือน และสถานะ (ถ้ามี)
if (!empty($year)) {
    $query .= " AND YEAR(found_date) = ?";
}
if (!empty($month)) {
    $query .= " AND MONTH(found_date) = ?";
}
if (!empty($status)) {
    $query .= " AND status_id = ?";
}

$query .= "
    GROUP BY 
        YEAR(found_date), MONTH(found_date)
    ORDER BY 
        YEAR(found_date) DESC, MONTH(found_date) DESC
";

// เตรียมคำสั่ง SQL
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    die('ข้อผิดพลาดในคำสั่ง SQL: ' . $mysqli->error);
}

// เตรียมค่าตัวกรอง
$params = [];

// เพิ่มค่าปี, เดือน และสถานะ (ถ้ามี)
if (!empty($year)) {
    $params[] = $year;
}
if (!empty($month)) {
    $params[] = $month;
}
if (!empty($status)) {
    $params[] = $status;
}

// ผูกค่าพารามิเตอร์กับคำสั่ง SQL
if (count($params) > 0) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}

// รันคำสั่ง SQL
$stmt->execute();

// ดึงผลลัพธ์จากฐานข้อมูล
$result = $stmt->get_result();

// จัดการข้อมูลผลลัพธ์
$months = [];
$counts = [];
while ($row = $result->fetch_assoc()) {
    $months[] = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
    $counts[] = $row['count'];  // จำนวนทั้งหมดในเดือนนั้น ๆ
}

// ตรวจสอบว่าไม่มีข้อมูลแสดงผล
if (empty($months)) {
    // กรณีที่ไม่มีการกรองข้อมูล (แสดงผลทั้งหมด)
    $query_all = "
        SELECT 
            YEAR(found_date) AS year,
            MONTH(found_date) AS month,
            COUNT(found_id) AS count
        FROM 
            found_items
        GROUP BY 
            YEAR(found_date), MONTH(found_date)
        ORDER BY 
            YEAR(found_date) DESC, MONTH(found_date) DESC
    ";
    $result_all = $mysqli->query($query_all);
    
    // รีเซ็ตค่าหากไม่มีข้อมูล
    $months = [];
    $counts = [];
    
    while ($row = $result_all->fetch_assoc()) {
        $months[] = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        $counts[] = $row['count'];  // จำนวนทั้งหมดในเดือนนั้น ๆ
    }
}

// กรณีไม่มีข้อมูลในบางเดือน
if (empty($counts)) {
    $counts = array_fill(0, count($months), 0);
}

$monthsFormatted = [];
$thaiMonths = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
];

foreach ($months as $month) {
    // แยกปีและเดือน
    $timestamp = strtotime($month . '-01');  // ใช้วันที่ 1 เพื่อให้ได้เดือนและปี
    $monthNum = date("n", $timestamp) - 1; // รับเดือนเป็นตัวเลขแล้วลด 1 เพื่อหาตำแหน่งใน array
    $formattedMonth = $thaiMonths[$monthNum] . ' ' . date("Y", $timestamp);  // แสดงเดือนและปี
    $monthsFormatted[] = $formattedMonth;
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
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="staff2_index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

   <!-- Main Sidebar Container -->
   <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="staff2_index.php" class="brand-link">
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
              <li class="nav-item">
                <a href="resize.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ลดขนาดไฟล์รูปภาพ</p>
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
            <h1 class="m-0">รายงานข้อมูล</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">รายงานข้อมูล</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>แผนภูมิ</h3>

                <p>แสดงรายงานแจ้งพบ</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="show_result_found.php" class="small-box-footer">คลิกเพื่อดูข้อมูล<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>แผนภูมิ</h3>

                <p>แสดงรายงานแจ้งหาย</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="show_result_lost.php" class="small-box-footer">คลิกเพื่อดูข้อมูล<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
       <!-- Main content -->
        <section class="content"></section>

        </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer text-center">
    <strong>สำนักวิทยบริการและเทคโนโลยีสารสนเทศ มหาวิทยาลัยราชภัฏพิบูลสงราม. &copy; 2024 <a href="https://library.psru.ac.th/">LIBRARY.PSRU</a>.</strong>
  </footer>
</div>

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>
<!-- โหลด jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- โหลด jQuery UI สำหรับ Datepicker -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // ดึงปีปัจจุบัน
    var currentYear = new Date().getFullYear();
    
    // กำหนดปีเริ่มต้นและปีสิ้นสุด (ใช้ปีปัจจุบันเป็นปีเริ่มต้น)
    var startYear = currentYear;
    var endYear = currentYear + 10; // เพิ่มปีในอนาคต 10 ปี

    // สร้างตัวเลือกปีใน <select>
    for (var year = startYear; year <= endYear; year++) {
      $('#year').append('<option value="' + year + '">' + year + '</option>');
    }
  </script>
</body>
</html>
