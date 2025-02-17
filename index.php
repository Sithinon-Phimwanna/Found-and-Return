<?php
session_start();

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



// ดึงค่าค้นหาจาก GET (ถ้ามี)
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// ตรวจสอบว่า $search_query ไม่ว่าง
if (!empty($search_query)) {
    $query = "
        SELECT 
            found_items.found_id,
            found_items.finder_name,
            found_items.finder_contact,
            found_items.found_type,
            found_items.found_description,
            found_items.found_date,
            found_items.found_location,
            found_items.found_image,
            statuses.status_name AS status
        FROM 
            found_items
        JOIN 
            statuses ON found_items.status_id = statuses.status_id
        WHERE 
            found_items.finder_name LIKE ? 
            OR found_items.found_type LIKE ? 
            OR found_items.found_location LIKE ? 
            OR found_items.found_date LIKE ?
    ";
} else {
    // ถ้าไม่มีการค้นหาให้ดึงข้อมูลทั้งหมด
    $query = "
        SELECT 
            found_items.found_id,
            found_items.finder_name,
            found_items.finder_contact,
            found_items.found_type,
            found_items.found_description,
            found_items.found_date,
            location.location_name AS found_location, -- ใช้ location_name แทน location_id
            found_items.found_image,
            statuses.status_name AS status
        FROM 
            found_items
        JOIN 
            location ON found_items.found_location = location.location_id -- เชื่อมกับตาราง location
        JOIN 
            statuses ON found_items.status_id = statuses.status_id
    ";
}

// เตรียมการ query
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die('Error preparing statement: ' . $mysqli->error);
}

$search_term = '%' . $search_query . '%';
if (!empty($search_query)) {
    $stmt->bind_param('ssss', $search_term, $search_term, $search_term, $search_term);
}

if (!$stmt->execute()) {
    die('Error executing statement: ' . $stmt->error);
}

$result = $stmt->get_result();
if (!$result) {
    die('Error fetching result: ' . $mysqli->error);
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
        <a href="index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.php" class="brand-link">
      <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Found & Return</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="assets/dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
        <P class="mr-2 user-none" style="color: white;">ผู้ใช้งานทั่วไป</P>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
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
                    <a href="login.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ล็อกอิน</p>
                    </a>
                  </li>          
            </ul>
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

                <p>แจ้งทรัพย์สินหายทั้งหมด</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">แจ้งทรัพย์สินหาย(วันนี้): <?= $lost_count_today ?></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $found_count ?></h3>

                <p>จำนวนพบทรัพย์ทั้งหมด</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">แจ้งพบทรัพย์สิน(วันนี้): <?= $found_count_today ?></a>
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
              <a href="#" class="small-box-footer">จำนวนผู้เข้าชม(วันนี้): <?= $visits_today ?></a>
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
              <a href="chart_user.php" class="small-box-footer">คลิกเพื่อดูข้อมูล<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->

      <!-- table found -->
      <div class="card direct-tabel-found direct-tabel-found-primary">
          <?php
            // Define the function to convert month number to Thai month name
            function getThaiMonth($month) {
                $thaiMonths = [
                    '01' => 'มกราคม',
                    '02' => 'กุมภาพันธ์',
                    '03' => 'มีนาคม',
                    '04' => 'เมษายน',
                    '05' => 'พฤษภาคม',
                    '06' => 'มิถุนายน',
                    '07' => 'กรกฎาคม',
                    '08' => 'สิงหาคม',
                    '09' => 'กันยายน',
                    '10' => 'ตุลาคม',
                    '11' => 'พฤศจิกายน',
                    '12' => 'ธันวาคม'
                ];
                return $thaiMonths[$month] ?? '';
              }
          ?>
          <style>
                      .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 100%;
          }

          .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            padding: 10px;
          }

          .card-img-top {
            width: 100%;
            max-width: 250px; /* ปรับให้ขนาดไม่ใหญ่เกินไป */
            height: auto;
            display: block;
            margin: 0 auto;
          }

          .card-title,
          .card-text {
            margin: 10px 0;
          }

          .card-footer {
            text-align: center;
            padding: 10px;
          }
          </style>

          <div class="container py-4">
            <div class="row justify-content-start">
              <?php while ($row = $result->fetch_assoc()): ?>
                
                <?php if (trim($row['status']) == 'ได้รับคืนแล้ว') continue; ?>

                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                  <div class="card shadow h-100 border-0">
                    <!-- รูปภาพ -->
                    <?php
                      $images = explode(',', $row['found_image']); // Split images into an array
                      if (count($images) > 1): // If there are multiple images
                    ?>
                      <div id="carousel-<?= $row['found_id'] ?>" class="carousel slide" data-ride="carousel" >
                        <div class="carousel-inner">
                          <?php foreach ($images as $index => $image): ?>
                            <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                              <img src="found_images/<?= htmlspecialchars($image) ?>" 
                                  class="d-block w-100 img-fluid rounded-top" 
                                  alt="ไม่มีรูปภาพ" 
                                  style="object-fit:cover; height: auto; display: block; margin-left: auto; margin-right: auto; width:100%;">
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <a class="carousel-control-prev" href="#carousel-<?= $row['found_id'] ?>" role="button" data-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carousel-<?= $row['found_id'] ?>" role="button" data-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="sr-only">Next</span>
                        </a>
                      </div>
                    <?php else: ?>
                      <img src="found_images/<?= htmlspecialchars($images[0]) ?>" 
                          class="card-img-top img-fluid rounded-top" 
                          alt="ไม่มีรูปภาพ" 
                          style="object-fit:cover; height: auto; display: block; margin-left: auto; margin-right: auto; width:100%;">
                    <?php endif; ?>
                    <!-- เนื้อหา -->
                    <div class="card-body"  style="display: flex;  flex-direction: column; align-items: center;  justify-content: center;">
                      <p class="card-title text-primary text-center">
                        <strong><?= htmlspecialchars($row['found_type']) ?></strong>
                      </p>
                      <!-- สถานที่เก็บได้ -->
                      <p class="card-text"><strong>สถานที่เก็บได้:</strong> <?= htmlspecialchars($row['found_location']) ?></p>
                      <!-- รายละเอียด -->
                      <p class="card-text"><strong>รายละเอียด:</strong> <?= htmlspecialchars($row['found_description']) ?></p>
                      <!-- สถานะ -->
                      <p class="card-text"><strong>สถานะ:</strong> <?= htmlspecialchars($row['status']) ?></p>
                    </div>
                    <!-- วันที่ -->
                    <div class="card-footer text-muted text-center">
                      <?php 
                        // แปลง ค.ศ. เป็น พ.ศ.
                        $timestamp = strtotime($row['found_date']); 
                        $yearBE = date('Y', $timestamp) + 543; // เพิ่ม 543 เพื่อแปลงเป็น พ.ศ.
                        
                        echo date('d ', $timestamp) . 
                            getThaiMonth(date('m', $timestamp)) . 
                            " " . $yearBE . 
                            date(', H:i', $timestamp);
                      ?>
                    </div>

                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>



        </div>
              <!-- /.card-footer-->
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
