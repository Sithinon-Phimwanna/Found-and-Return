<?php
session_start();
require 'config.php';


// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบว่าในเซสชันมีการตั้งค่า UserAdminName หรือไม่
$current_user_name = isset($_SESSION['UserAdminName']) ? $_SESSION['UserAdminName'] : 'ไม่ทราบชื่อ';


// ตรวจสอบว่ามีการส่งค่า item_id มาหรือไม่
if (!isset($_GET['item_id'])) {
    echo '<script>
            setTimeout(function() {
                swal({
                title: "ไม่พบข้อมูลทรัพย์สิน",
                type: "error"
                }, function() {
                window.location = "lost_edit.php";
                });
            }, 1000);
          </script>';
    exit;
}

$item_id = $_GET['item_id'];

// ดึงข้อมูลจากฐานข้อมูล
$query = "SELECT * FROM lost_items WHERE item_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<script>
            setTimeout(function() {
                swal({
                title: "ไม่พบข้อมูลทรัพย์สิน",
                type: "error"
                }, function() {
                window.location = "lost_edit.php";
                });
            }, 1000);
          </script>';
    exit;
}

$item = $result->fetch_assoc();

// ดึงข้อมูลสถานที่และสถานะจากฐานข้อมูล
$locations = $mysqli->query("SELECT * FROM location");
$statuses = $mysqli->query("SELECT * FROM statuses");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // รับค่าจากฟอร์ม
  $owner_name = $_POST['owner_name'];
  $owner_contact = $_POST['owner_contact'];
  $item_type = $_POST['item_type'];
  $item_description = $_POST['item_description'];
  $lost_date = $_POST['lost_date'];
  $lost_location = $_POST['lost_location'];
  $deliverer = $_POST['deliverer'];
  $status_id = $_POST['status_id'];
  $item_id = $_POST['item_id'];

  // ตรวจสอบค่าที่ได้รับจากฟอร์ม
  if (empty($owner_name) || empty($item_type) || empty($item_description) || empty($lost_date) || empty($lost_location) || empty($status_id)) {
      echo '<script>
              Swal.fire({ title: "กรุณากรอกข้อมูลให้ครบถ้วน", icon: "error" }).then(() => { window.location = "lost_edit.php"; });
            </script>';
      exit;
  }

  // ดึงชื่อไฟล์รูปภาพเก่าจากฐานข้อมูล
  $query = "SELECT item_image, finder_image FROM lost_items WHERE item_id = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('i', $item_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $old_item_image = $row['item_image'];
  $old_finder_image = $row['finder_image'];

  // ฟังก์ชันอัปโหลดไฟล์
  function uploadImages($files, $target_dir, $old_images) {
      $uploaded_images = [];
      foreach ($files['name'] as $key => $file_name) {
          if (!empty($file_name)) {
              $original_name = pathinfo($file_name, PATHINFO_FILENAME);
              $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
              $formatted_timestamp = date("d-m-Y_H-i-s");
              $new_image_name = $formatted_timestamp . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $original_name) . "." . $imageFileType;
              $target_file = $target_dir . $new_image_name;

              $allowed_types = ["jpg", "jpeg", "png", "gif"];
              if (!in_array($imageFileType, $allowed_types) || $files['size'][$key] > 1048576) {
                  echo '<script>
                          Swal.fire({ title: "ไฟล์ต้องเป็น JPG, JPEG, PNG หรือ GIF และไม่เกิน 1MB", icon: "error" }).then(() => { window.location = "lost_edit.php"; });
                        </script>';
                  exit;
              }

              if (!move_uploaded_file($files['tmp_name'][$key], $target_file)) {
                  echo '<script>
                          Swal.fire({ title: "เกิดข้อผิดพลาดในการอัปโหลดไฟล์", icon: "error" }).then(() => { window.location = "lost_edit.php"; });
                        </script>';
                  exit;
              }

              // Only delete old image if a new one is uploaded
              if (!empty($old_images) && file_exists($target_dir . $old_images)) {
                  unlink($target_dir . $old_images);
              }
              $uploaded_images[] = $new_image_name;
          } else {
              $uploaded_images[] = $old_images; // Keep old image if no new file is uploaded
          }
      }
      return implode(',', $uploaded_images);
  }

  // อัปโหลดรูปภาพ
  $new_item_image = uploadImages($_FILES['item_image'], "../lost_images/", $old_item_image);
  $new_finder_image = uploadImages($_FILES['finder_image'], "../return_images/", $old_finder_image);

  // อัปเดตข้อมูล
  $query = "UPDATE lost_items SET owner_name=?, owner_contact=?, item_type=?, item_description=?, lost_date=?, lost_location=?, item_image=?, finder_image=?, deliverer=?, status_id=? WHERE item_id=?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('sssssissssi', $owner_name, $owner_contact, $item_type, $item_description, $lost_date, $lost_location, $new_item_image, $new_finder_image, $deliverer, $status_id, $item_id);

  if ($stmt->execute()) {
      header("Location: lost_items_list.php?success=3");
      exit;
  } else {
      echo '<script>
              Swal.fire({ title: "เกิดข้อผิดพลาดในการอัปเดตข้อมูล", icon: "error" }).then(() => { window.location = "lost_edit.php"; });
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
  <title>lost & Return</title>

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
      <span class="brand-text font-weight-light">lost & Return</span>
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
            <h1 class="m-0">แก้ไขข้อมูลทรัพย์ที่หาย</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">แก้ไขข้อมูลทรัพย์หาย</li>
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
            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
            
            <div class="form-group row">
                <label for="owner_name" class="col-sm-2">ชื่อผู้แจ้ง:</label>
                <input type="text" name="owner_name" class="form-control-sm-4" value="<?= htmlspecialchars($item['owner_name']) ?>" required>
            </div>

            <div class="form-group row">
                <label for="owner_contact" class="col-sm-2">ช่องทางติดต่อ</label>
                <input type="text" name="owner_contact" class="form-control-sm-4" value="<?= htmlspecialchars($item['owner_contact']) ?>" required>
            </div>
            
            <div class="form-group row">
                <label for="item_type" class="col-sm-2">ทรัพย์สิน</label>
                <input type="text" name="item_type" class="form-control-sm-4" value="<?= htmlspecialchars($item['item_type']) ?>" required>
            </div>
            
            <div class="form-group row">
                <label for="item_description" class="col-sm-2">รายละเอียด</label>
                <textarea name="item_description" class="form-control-sm-4" required><?= htmlspecialchars($item['item_description']) ?></textarea>
            </div>
            
            <div class="form-group row">
                <label for="lost_date" class="col-sm-2">วันที่เก็บได้</label>
                <input type="datetime-local" name="lost_date" class="form-control-sm-4" value="<?= date('Y-m-d\TH:i', strtotime($item['lost_date'])) ?>" required>
            </div>
            
            <div class="form-group row">
                <label for="lost_location" class="col-sm-2">สถานที่เก็บได้</label>
                <select name="lost_location" class="form-control-sm-4" required>
                    <?php while ($location = $locations->fetch_assoc()): ?>
                        <option value="<?= $location['location_id'] ?>" <?= ($item['lost_location'] == $location['location_id']) ? 'selected' : '' ?>>
                            <?= $location['location_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group row">
                <label for="item_image" class="col-sm-2">อัปโหลดรูปภาพใหม่ (ถ้ามี)</label>
                <input type="file" name="item_image[]" class="form-control-sm-4" multiple>
                <p class="form-control-sm-4">รูปภาพปัจจุบัน:</p>
                <?php
                  if ($item['item_image']) {
                      $images = explode(',', $item['item_image']); // แยกรูปภาพหลายๆ ไฟล์
                      foreach ($images as $image) {
                          if (file_exists("../lost_images/" . $image)) {
                              echo '<img src="../lost_images/' . htmlspecialchars($image) . '" alt="รูปภาพทรัพย์สิน" style="max-width: 150px; margin-right: 10px;">';
                          }
                      }
                  } else {
                      echo 'ไม่มีรูปภาพ';
                  }
                  ?>
                
            </div>

            <!-- รูปภาพผู้รับคืน -->
            <div class="form-group row">
                <label for="finder_image" class="col-sm-2">อัปโหลดรูปภาพผู้รับคืน (ถ้ามี)</label>
                <input type="file" name="finder_image[]" class="form-control-sm-4" multiple>
                <p class="form-control-sm-4">รูปภาพปัจจุบัน:</p>
                <?php
                  if ($item['finder_image']) {
                      $finder_images = explode(',', $item['finder_image']); // แยกรูปภาพผู้รับคืนหลายๆ ไฟล์
                      foreach ($finder_images as $finder_image) {
                          if (file_exists("../return_images/" . $finder_image)) {
                              echo '<img src="../return_images/' . htmlspecialchars($finder_image) . '" alt="รูปภาพผู้รับคืน" style="max-width: 150px; margin-right: 10px;">';
                          }
                      }
                  } else {
                      echo 'ไม่มีรูปภาพ';
                  }
                  ?>
            </div>

            <!-- ผู้ส่งมอบทรัพย์สิน -->
            <div class="form-group row">
    <label for="deliverer" class="col-sm-2">ผู้ส่งมอบทรัพย์สิน</label>
    <?php if (!empty($item['deliverer'])): ?>
        <!-- แสดงชื่อผู้ส่งมอบทรัพย์สินหากมีค่า -->
        <input type="text" name="deliverer" class="form-control-sm-4" value="<?= htmlspecialchars($item['deliverer']) ?>">
    <?php else: ?>
        <!-- ถ้าไม่มีผู้ส่งมอบทรัพย์สิน แสดงชื่อผู้ใช้ที่ล็อกอิน -->
        <input type="text" name="deliverer" class="form-control-sm-4" value="<?= htmlspecialchars($current_user_name) ?>" readonly>
    <?php endif; ?>
</div>



            
            <div class="form-group row">
                <label for="status_id" class="col-sm-2">สถานะ</label>
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
                                    <button type="submit" class="btn btn-primary">บันทึก</button>
                                    <button type="submit" class="btn btn-danger"><a href="lost_items_list.php" style="color:white;">ยกเลิก</a></button>
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
