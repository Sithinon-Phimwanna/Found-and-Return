<?php
session_start();
require 'config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบว่ามีการส่งค่า found_id มาหรือไม่
if (!isset($_GET['found_id'])) {
    // sweet alert 
    echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
    echo '<script>
            setTimeout(function() {
                swal({
                title: "แก้ไขเรียบร้อยแล้ว",
                type: "success"
                }, function() {
                window.location = "found_edit.php"; //หน้าที่ต้องการให้กระโดดไป
                });
            }, 1000);
          </script>';
           exit;
}

$found_id = $_GET['found_id'];

// ดึงข้อมูลจากฐานข้อมูล
$query = "SELECT * FROM found_items WHERE found_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $found_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // sweet alert 
    echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
    echo '<script>
            setTimeout(function() {
                swal({
                title: "ไม่พบข้อมูลทรัพย์สินที่ต้องการแก้ไข",
                type: "error"
                }, function() {
                window.location = "found_edit.php"; //หน้าที่ต้องการให้กระโดดไป
                });
            }, 1000);
          </script>';
          exit;
}

$item = $result->fetch_assoc();

// ดึงข้อมูลสถานที่และสถานะจากฐานข้อมูล
$locations = $mysqli->query("SELECT * FROM location");
$statuses = $mysqli->query("SELECT * FROM statuses");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $found_id = $_POST['found_id'];
    $finder_name = $_POST['finder_name'];
    $finder_contact = $_POST['finder_contact'];
    $found_type = $_POST['found_type'];
    $found_description = $_POST['found_description'];
    $found_date = $_POST['found_date'];
    $found_location = $_POST['found_location'];
    $status_id = $_POST['status_id'];

    // ตรวจสอบค่าที่ได้รับจากฟอร์ม
    echo "<pre>";
    var_dump($found_id, $finder_name, $finder_contact, $found_type, $found_description, $found_date, $found_location, $status_id);
    echo "</pre>";

    // ดึงชื่อไฟล์รูปเดิมจากฐานข้อมูล
    $query = "SELECT found_image FROM found_items WHERE found_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $found_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_image = $result->fetch_assoc()['found_image'];

    // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
    if (!empty($_FILES['found_image']['name'])) {
        $target_dir = "../found_images/";

        // ตั้งชื่อไฟล์ใหม่เป็น timestamp_ชื่อไฟล์เดิม
        $original_name = pathinfo($_FILES['found_image']['name'], PATHINFO_FILENAME);
        $imageFileType = strtolower(pathinfo($_FILES['found_image']['name'], PATHINFO_EXTENSION));

        // แยก timestamp ออกมาเป็นปี, เดือน, วัน, ชั่วโมง, นาที, วินาที
        $timestamp = time();
        $formatted_timestamp = date("d-m-Y_H-i-s", $timestamp);

        // ตั้งชื่อไฟล์ใหม่ โดยคั่นระหว่างวันและเวลา และใส่ชื่อไฟล์เดิม
        $new_image_name = $formatted_timestamp . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $original_name) . "." . $imageFileType;

        $target_file = $target_dir . $new_image_name;


        // ตรวจสอบประเภทไฟล์
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            // sweet alert 
            echo '
            <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
            echo '<script>
                    setTimeout(function() {
                        swal({
                        title: "ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น (JPG, JPEG, PNG, GIF)",
                        type: "error"
                        }, function() {
                        window.location = "found_edit.php"; //หน้าที่ต้องการให้กระโดดไป
                        });
                    }, 1000);
                  </script>';
                  exit;
        }

        // ตรวจสอบขนาดไฟล์ (ไม่เกิน 1MB)
        $max_file_size = 1 * 1024 * 1024; // 1MB
        if ($_FILES['found_image']['size'] > $max_file_size) {
            // sweet alert 
            echo '
            <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
            echo '<script>
                    setTimeout(function() {
                        swal({
                        title: "ไฟล์ $filename มีขนาดใหญ่เกินไป (สูงสุด 1MB)",
                        type: "error"
                        }, function() {
                        window.location = "found_edit.php"; //หน้าที่ต้องการให้กระโดดไป
                        });
                    }, 1000);
                  </script>';
                  exit;
        }

        // ลบรูปภาพเก่าถ้ามี
        if (!empty($old_image) && file_exists($target_dir . $old_image)) {
          unlink($target_dir . $old_image);
      }

        // ย้ายไฟล์ไปยังโฟลเดอร์
        if (!move_uploaded_file($_FILES['found_image']['tmp_name'], $target_file)) {
            // sweet alert 
        echo '
        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
        echo '<script>
                setTimeout(function() {
                    swal({
                    title: "เกิดข้อผิดพลาดในการอัปโหลดไฟล์",
                    type: "error"
                    }, function() {
                    window.location = "found_edit.php"; //หน้าที่ต้องการให้กระโดดไป
                    });
                }, 1000);
              </script>';
              exit;
        }

        // อัปเดตข้อมูลรวมถึงรูปภาพใหม่
        $query = "UPDATE found_items SET finder_name=?, finder_contact=?, found_type=?, found_description=?, found_date=?, found_location=?, found_image=?, status_id=? WHERE found_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssssisii', $finder_name, $finder_contact, $found_type, $found_description, $found_date, $found_location, $new_image_name, $status_id, $found_id);
    } else {
        // อัปเดตข้อมูลโดยไม่เปลี่ยนรูปภาพ
        $query = "UPDATE found_items SET finder_name=?, finder_contact=?, found_type=?, found_description=?, found_date=?, found_location=?, status_id=? WHERE found_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssssiis', $finder_name, $finder_contact, $found_type, $found_description, $found_date, $found_location, $status_id, $found_id);
    }

    if ($stmt->execute()) {
        header("Location: found_items_list.php?success=2");
        exit;
    } else {
        // sweet alert 
        echo '
        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
        echo '<script>
                setTimeout(function() {
                    swal({
                    title: "เกิดข้อผิดพลาดในการอัพเดทไฟล์",
                    type: "error"
                    }, function() {
                    window.location = "found_edit.php"; //หน้าที่ต้องการให้กระโดดไป
                    });
                }, 1000);
              </script>';
              exit;
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
  <!-- summernote -->
  <link rel="stylesheet" href="../assets/plugins/summernote/summernote-bs4.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
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
        <a href="../staff_index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../staff_index.php" class="brand-link">
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
            <h1 class="m-0">แก้ไขข้อมูลทรัพย์ที่เก็บได้</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">แก้ไขข้อมูลทรัพย์ที่เก็บได้</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="card shadow">
    <div class="card-body">

        <form action="#" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="found_id" value="<?= $item['found_id'] ?>">
            
            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">ชื่อผู้แจ้ง:</label>
                <input type="text" name="finder_name" class="form-control-sm-4" value="<?= htmlspecialchars($item['finder_name']) ?>" required>
            </div>

            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">ช่องทางติดต่อ</label>
                <input type="text" name="finder_contact" class="form-control-sm-4" value="<?= htmlspecialchars($item['finder_contact']) ?>" required>
            </div>
            
            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">ทรัพย์สิน</label>
                <input type="text" name="found_type" class="form-control-sm-4" value="<?= htmlspecialchars($item['found_type']) ?>" required>
            </div>
            
            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">รายละเอียด</label>
                <textarea name="found_description" class="form-control-sm-4" required><?= htmlspecialchars($item['found_description']) ?></textarea>
            </div>
            
            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">วันที่เก็บได้</label>
                <input type="datetime-local" name="found_date" class="form-control-sm-4" value="<?= date('Y-m-d\TH:i', strtotime($item['found_date'])) ?>" required>
            </div>
            
            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">สถานที่เก็บได้</label>
                <select name="found_location" class="form-control-sm-4" required>
                    <?php while ($location = $locations->fetch_assoc()): ?>
                        <option value="<?= $location['location_id'] ?>" <?= ($item['found_location'] == $location['location_id']) ? 'selected' : '' ?>>
                            <?= $location['location_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">อัปโหลดรูปภาพใหม่ (ถ้ามี)</label>
                <input type="file" name="found_image" class="form-control-sm-4">
                <p class="form-control-sm-4">รูปภาพปัจจุบัน:</p>
                <?php if ($item['found_image']): ?>
                    <img src="../found_images/<?= htmlspecialchars($item['found_image']) ?>" alt="รูปภาพทรัพย์สิน" style="max-width: 150px;">
                <?php else: ?>
                    ไม่มีรูปภาพ
                <?php endif; ?>
            </div>
            
            <div class="form-group row">
                <label for="finder_name" class="col-sm-2">สถานะ</label>
                <select name="status_id" class="form-control-sm-4" required>
                    <?php while ($status = $statuses->fetch_assoc()): ?>
                        <option value="<?= $status['status_id'] ?>" <?= ($item['status_id'] == $status['status_id']) ? 'selected' : '' ?>>
                            <?= $status['status_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group row">
                                <label class="col-sm-2"></label>
                                  <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                    <button type="submit" class="btn btn-danger"><a href="found_items_list.php" style="color:white;">ยกเลิก</a></button>
                                  </div>
                                </div>
        </form>
    </div>
</div>
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

<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
</body>
</html>
