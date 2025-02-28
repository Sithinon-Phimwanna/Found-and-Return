<?php
session_start();
require 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($mysqli->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $mysqli->connect_error);
}

// ตรวจสอบว่ามีการเลือกเดือนและปีจากฟอร์มหรือไม่
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// ถ้ายังไม่ได้เลือกเดือนและปี ไม่ต้องดึงข้อมูลจากฐานข้อมูล
$dataPoints = [];
if (!empty($year) && !empty($month)) {
    // ดึงข้อมูลจากฐานข้อมูล
    $query = "
        SELECT 
            MONTH(lost_date) AS month,
            status_id,
            COUNT(item_id) AS count
        FROM 
            lost_items
        WHERE 
            1=1
    ";

    if (!empty($year)) {
        $query .= " AND YEAR(lost_date) = ?";
    }
    if (!empty($month)) {
        $query .= " AND MONTH(lost_date) = ?";
    }

    $query .= "
        GROUP BY 
            MONTH(lost_date), status_id
        ORDER BY 
            MONTH(lost_date) ASC
    ";

    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        die('ข้อผิดพลาดในคำสั่ง SQL: ' . $mysqli->error);
    }

    $params = [];
    if (!empty($year)) $params[] = $year;
    if (!empty($month)) $params[] = $month;

    if (count($params) > 0) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['status_id']] = $row['count'];
    }

    // แปลงข้อมูลสำหรับ CanvasJS
    $statusLabels = [
        1 => "แจ้งหาย",
        2 => "คืนแล้ว",
        3 => "ค้างในระบบเกิน 1 สัปดาห์"
    ];

    $colors = [
        1 => "#007bff", // สีน้ำเงิน สำหรับ "แจ้งหาย"
        2 => "#28a745", // สีเขียว สำหรับ "คืนแล้ว"
        3 => "#FFCC66"  // สีแดง สำหรับ "ค้างในระบบเกิน 1 สัปดาห์"
    ];


    foreach ($statusLabels as $statusId => $label) {
        $count = isset($data[$statusId]) ? $data[$statusId] : 0;
        $dataPoints[] = [
            'label' => $label,
            'y' => $count,
            'color' => $colors[$statusId]  // เพิ่มสีที่กำหนดลงใน dataPoint
        ];
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
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="chart_admin.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="chart_admin.php" class="brand-link">
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
                  <p>แจ้งพบทรัพย์สิน</p>
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
                ข้อมูลทรัพย์สิน
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="found_items_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ข้อมูลแจ้งพบทรัพย์สิน</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="lost_items_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ข้อมูลแจ้งทรัพย์สินหาย</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
            <i class="nav-icon fas far fa-regular fa-hand-holding-heart"></i>
              <p>
                ส่งคืนทรัพย์สิน
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="return_item.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ส่งคืนทรัพย์สิน</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>
                รายงาน
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="show_result_found_m.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>รายงานแจ้งพบทรัพสิน รายเดือน</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="show_result_found.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>รายงานแจ้งพบทรัพสิน รายปี</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="show_resoult_lost_m.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>รายงานแจ้งทรัพย์สินหาย เดือน</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="show_resoult_lost.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>รายงายแจ้งทรัพย์สินหาย รายปี</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-header">ล็อกเอาท์</li>
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
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">รายงานข้อมูลแจ้งหาย รายเดือน</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">รายงานข้อมูลแจ้งหาย รายเดือน</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <form method="GET" action="">
              <div class="form-group">
                <label for="year">รายงานปี:</label>
                <select name="year" id="year" class="form-control">
                  <option value="">-- เลือกปี --</option>
                  <?php
                  // สร้างตัวเลือกปีในฟอร์ม
                  for ($i = 2020; $i <= date("Y"); $i++) {
                      echo "<option value=\"$i\" " . ($year == $i ? "selected" : "") . ">$i</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label for="month">รายงานเดือน:</label>
                <select name="month" id="month" class="form-control">
                  <option value="">-- เลือกเดือน --</option>
                  <?php
                  $thaiMonths = [
                    1 => "มกราคม",
                    2 => "กุมภาพันธ์",
                    3 => "มีนาคม",
                    4 => "เมษายน",
                    5 => "พฤษภาคม",
                    6 => "มิถุนายน",
                    7 => "กรกฎาคม",
                    8 => "สิงหาคม",
                    9 => "กันยายน",
                    10 => "ตุลาคม",
                    11 => "พฤศจิกายน",
                    12 => "ธันวาคม"
                ];
                  // สร้างตัวเลือกเดือนในฟอร์ม
                  foreach ($thaiMonths as $i => $monthName) {
                    echo "<option value=\"$i\" " . ($month == $i ? "selected" : "") . ">$monthName</option>";
                }
                  ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">แสดงรายงาน</button>
            </form>
          </div>
        </div>
        
        <div class="card-body">
          <?php if (!empty($year) && !empty($month)): ?>
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <script>
            window.onload = function() {
                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    title: {
                        text: "รายงานข้อมูลแจ้งหาย รายเดือน"
                    },
                    axisY: {
                        title: "จำนวน",
                        valueFormatString: "#0",
                        includeZero: true
                    },
                    data: [{
                        type: "bar",
                        yValueFormatString: "#,##0 ชิ้น",
                        indexLabel: "{y}",
                        indexLabelPlacement: "inside",
                        indexLabelFontWeight: "bolder",
                        indexLabelFontColor: "white",
                        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                    }]
                });
                chart.render();
            }
            </script>
          <?php else: ?>
            <p>กรุณาเลือกปีและเดือนเพื่อดูรายงาน</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
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
<!-- โหลด jQuery UI สำหรับ Datepicker -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Chart.js Datalabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>
</html>
