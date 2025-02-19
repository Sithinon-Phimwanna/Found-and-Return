<?php
session_start();
require 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($mysqli->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $mysqli->connect_error);
}

$year = isset($_GET['year']) ? $_GET['year'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$query = "
    SELECT 
        YEAR(lost_date) AS year,
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
        YEAR(lost_date), MONTH(lost_date), status_id
    ORDER BY 
        YEAR(lost_date) DESC, MONTH(lost_date) DESC
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
    $monthKey = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
    $data[$monthKey][$row['status_id']] = $row['count'];
}

// แปลงข้อมูลสำหรับ Chart.js
$months = array_keys($data);
$statusLabels = [
    1 => "แจ้งหาย",
    2 => "คืนแล้ว",
    3 => "ไม่พบทรัพย์สิน"
];

$datasets = [];
foreach ($statusLabels as $statusId => $label) {
    $dataset = [
        'label' => $label,
        'data' => [],
        'backgroundColor' => ($statusId == 2 ? 'rgba(28, 245, 136, 0.5)' : 
                              ($statusId == 3 ? 'rgba(255, 206, 86, 0.55)' : 
                              'rgba(255, 99, 132, 0.5)')),
        'borderColor' => ($statusId == 2 ? 'rgba(52, 250, 151, 1)' : 
                          ($statusId == 3 ? 'rgba(255, 206, 86, 1)' : 
                          'rgba(255, 99, 132, 1)')),
        'borderWidth' => 1
    ];
    
    foreach ($months as $month) {
        $dataset['data'][] = $data[$month][$statusId] ?? 0;
    }
    
    $datasets[] = $dataset;
}

// แปลงชื่อเดือนเป็นภาษาไทย
$thaiMonths = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
];

$monthsFormatted = [];
foreach ($months as $month) {
    $timestamp = strtotime($month . '-01');
    $monthNum = date("n", $timestamp) - 1;
    $monthsFormatted[] = $thaiMonths[$monthNum] . ' ' . date("Y", $timestamp);
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
              <li class="nav-item">
                <a href="resize.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ลดขนาดไฟล์รูปภาพ</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-header">การจัดการ</li>
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
            <h1 class="m-0">แผนภูมิข้อมูลแจ้งหาย</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">แผนภูมิข้อมูลแจ้งหาย</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container">
      <form method="GET" action="">
      <div class="container mt-5">
      <div class="card-body">
        <div class="form-group">
            <label for="year">เลือกปี : </label>
            <select id="year" class="form-control-sm-4"></select>
            </div>

          <div class="form-group">
            <label for="month">เลือกเดือน : </label>
            <select name="month" id="month" class="form-control-sm-4">
              <option value="">ทุกเดือน</option>
              <option value="1">มกราคม</option>
              <option value="2">กุมภาพันธ์</option>
              <option value="3">มีนาคม</option>
              <option value="4">เมษายน</option>
              <option value="5">พฤษภาคม</option>
              <option value="6">มิถุนายน</option>
              <option value="7">กรกฎาคม</option>
              <option value="8">สิงหาคม</option>
              <option value="9">กันยายน</option>
              <option value="10">ตุลาคม</option>
              <option value="11">พฤศจิกายน</option>
              <option value="12">ธันวาคม</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">ค้นหาข้อมูล</button>
        </form>

        <div class="row">
          <div class="col-sm-12">
            <canvas id="myChart" width="400" height="200"></canvas>
            <canvas id="myChart" width="400" height="200"></canvas>
<script>
  var ctx = document.getElementById("myChart").getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($monthsFormatted); ?>,
      datasets: <?php echo json_encode($datasets); ?>
    },
    options: {
      responsive: true,
      scales: {
        x: {
          stacked: false, // ให้ Bar แต่ละอันแยกกัน (ถ้า stacked: true จะเป็นแถบรวม)
          title: {
            display: true,
            text: 'เดือนและปี'
          }
        },
        y: {
          ticks: {
            beginAtZero: true,
            stepSize: 5  // กำหนดขั้นของแกน Y เป็นจำนวนเต็มที่เป็นขั้น
          },
          title: {
            display: true,
            text: 'จำนวน'
          }
        }
      }
    }
  });
</script>

            </div>
          </div>
        </div>
      </div>
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
