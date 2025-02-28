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
    echo "ไม่พบรหัสรายการ";
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
    echo "ไม่พบข้อมูลที่เกี่ยวข้องกับรหัสรายการนี้";
    exit;
}

$item = $result->fetch_assoc();

// ดึงข้อมูลสถานที่และสถานะจากฐานข้อมูล
$locations = $mysqli->query("SELECT * FROM location");
$status_lost = $mysqli->query("SELECT * FROM status_lost");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // รับค่าจากฟอร์ม
  $owner_name = $_POST['owner_name'];
  $owner_contact = $_POST['owner_contact'];
  $item_name = $_POST['item_name'];
  $item_description = $_POST['item_description'];
  $lost_date = $_POST['lost_date'];
  $lost_location = $_POST['lost_location'];
  $deliverer = $_POST['deliverer'];
  $status_id = $_POST['status_id'];
  $item_id = $_POST['item_id'];

  // ตรวจสอบค่าที่ได้รับจากฟอร์ม
  if (empty($owner_name) || empty($item_name) || empty($item_description) || empty($lost_date) || empty($lost_location) || empty($status_id)) {
      echo "กรุณากรอกข้อมูลให้ครบถ้วน";
      exit;
  }

  // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
  if (!isset($_FILES['item_image']) || !isset($_FILES['finder_image'])) {
      echo "ไม่มีไฟล์อัปโหลด";
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

  // ฟังก์ชันบีบอัดและปรับขนาดภาพ
  function resizeImage($sourcePath, $targetPath, $imageType, $maxWidth = 800, $maxHeight = 800) {
      switch ($imageType) {
          case IMAGETYPE_JPEG:
              $sourceImage = imagecreatefromjpeg($sourcePath);
              break;
          case IMAGETYPE_PNG:
              $sourceImage = imagecreatefrompng($sourcePath);
              break;
          case IMAGETYPE_GIF:
              $sourceImage = imagecreatefromgif($sourcePath);
              break;
          default:
              return false;
      }

      list($origWidth, $origHeight) = getimagesize($sourcePath);
      $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
      $newWidth = (int)($origWidth * $ratio);
      $newHeight = (int)($origHeight * $ratio);

      $newImage = imagecreatetruecolor($newWidth, $newHeight);
      imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

      switch ($imageType) {
          case IMAGETYPE_JPEG:
              imagejpeg($newImage, $targetPath, 90);
              break;
          case IMAGETYPE_PNG:
              imagepng($newImage, $targetPath, 8);
              break;
          case IMAGETYPE_GIF:
              imagegif($newImage, $targetPath);
              break;
      }

      imagedestroy($sourceImage);
      imagedestroy($newImage);

      return true;
  }

  // ฟังก์ชันอัปโหลดและบีบอัดไฟล์
  function uploadImages($files, $target_dir, $old_images, $maxWidth = 800, $maxHeight = 800) {
      $uploaded_images = [];
      foreach ($files['name'] as $key => $file_name) {
          if (!empty($file_name)) {
              $original_name = pathinfo($file_name, PATHINFO_FILENAME);
              $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
              $formatted_timestamp = date("d-m-Y_H-i-s");
              $new_image_name = $formatted_timestamp . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $original_name) . "." . $imageFileType;
              $target_file = $target_dir . $new_image_name;

              $allowed_types = ["jpg", "jpeg", "png"];

              // ตรวจสอบประเภทไฟล์
              if (!in_array($imageFileType, $allowed_types)) {
                  echo "ไฟล์ที่อัปโหลดไม่ถูกต้อง (รองรับเฉพาะ JPG, JPEG, PNG)";
                  exit;
              }

              // ตรวจสอบขนาดไฟล์ก่อนบีบอัด
              if ($files['size'][$key] > 1048576) { // 1MB
                  $imageType = exif_imagetype($files['tmp_name'][$key]);
                  if (!resizeImage($files['tmp_name'][$key], $target_file, $imageType, $maxWidth, $maxHeight)) {
                      echo "ไม่สามารถบีบอัดภาพได้";
                      exit;
                  }
              } else {
                // ตรวจสอบข้อผิดพลาดของการอัปโหลดไฟล์
if ($_FILES['item_image']['error'][0] !== UPLOAD_ERR_OK) {
  $upload_errors = [
      UPLOAD_ERR_INI_SIZE   => 'ไฟล์มีขนาดเกินกว่าที่กำหนดใน php.ini',
      UPLOAD_ERR_FORM_SIZE  => 'ไฟล์มีขนาดเกินขีดจำกัดที่กำหนดในฟอร์ม',
      UPLOAD_ERR_PARTIAL    => 'ไฟล์ถูกอัปโหลดไม่สมบูรณ์',
      UPLOAD_ERR_NO_FILE    => 'ไม่มีไฟล์ถูกเลือก',
      UPLOAD_ERR_NO_TMP_DIR => 'ไม่มีไดเรกทอรีชั่วคราว',
      UPLOAD_ERR_CANT_WRITE => 'ไม่สามารถเขียนไฟล์ไปยังดิสก์ได้',
      UPLOAD_ERR_EXTENSION  => 'การอัปโหลดถูกยกเลิกเนื่องจากส่วนขยายของ PHP'
  ];

  echo "เกิดข้อผิดพลาด: " . $upload_errors[$_FILES['item_image']['error'][0]] ?? 'ข้อผิดพลาดที่ไม่ทราบสาเหตุ';
  exit;
}

// ถ้าขนาดไม่เกิน 1MB ให้ย้ายไฟล์โดยตรง
if (!move_uploaded_file($files['tmp_name'][$key], $target_file)) {
  $error = error_get_last(); // ดึงข้อมูลข้อผิดพลาดล่าสุด
  echo "ไม่สามารถย้ายไฟล์ได้: " . $error['message']; // แสดงข้อความข้อผิดพลาด
  exit;
}

              }
              

              // ลบไฟล์เก่า (ถ้ามี)
              if (!empty($old_images) && file_exists($target_dir . $old_images)) {
                  unlink($target_dir . $old_images);
              }

              $uploaded_images[] = $new_image_name;
          } else {
              $uploaded_images[] = $old_images; // ใช้รูปเก่าถ้าไม่มีการอัปโหลดใหม่
          }
      }
      return implode(',', $uploaded_images);
  }

  // อัปโหลดรูปภาพและบีบอัดถ้าจำเป็น
  $new_item_image = uploadImages($_FILES['item_image'], "../lost_images/", $old_item_image);
  $new_finder_image = uploadImages($_FILES['finder_image'], "../return_images/", $old_finder_image);

  // อัปเดตข้อมูลในฐานข้อมูล
  $query = "UPDATE lost_items SET owner_name=?, owner_contact=?, item_name=?, item_description=?, lost_date=?, lost_location=?, item_image=?, finder_image=?, deliverer=?, status_id=? WHERE item_id=?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('sssssissssi', $owner_name, $owner_contact, $item_name, $item_description, $lost_date, $lost_location, $new_item_image, $new_finder_image, $deliverer, $status_id, $item_id);

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
        <a href="admin_index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="admin_index.php" class="brand-link">
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
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">แก้ไขข้อมูลทรัพย์สินที่หาย</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">แก้ไขข้อมูลทรัพย์สินหาย</li>
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
                <label for="item_name" class="col-sm-2">ทรัพย์สิน</label>
                <input type="text" name="item_name" class="form-control-sm-4" value="<?= htmlspecialchars($item['item_name']) ?>" required>
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
                <input type="file" id="item_image" name="item_image[]" class="form-control-sm-4" multiple>
                <p class="form-control-sm-4">รูปภาพปัจจุบัน:</p>
                <?php
                if ($item['item_image']) {
                    $images = explode(',', $item['item_image']);
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
                <input type="file" id="finder_image" name="finder_image[]" class="form-control-sm-4" multiple>
                <p class="form-control-sm-4">รูปภาพปัจจุบัน:</p>
                <?php
                if ($item['finder_image']) {
                    $finder_images = explode(',', $item['finder_image']);
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
        <input type="text" name="deliverer" class="form-control-sm-4" value="<?= htmlspecialchars($current_user_name) ?>">
    <?php endif; ?>
</div>



            
            <div class="form-group row">
                <label for="status_id" class="col-sm-2">สถานะ</label>
                <select name="status_id" class="form-control-sm-4" required>
                    <?php while ($status = $status_lost->fetch_assoc()): ?>
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
<!-- เพิ่ม SweetAlert CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css" rel="stylesheet">

<!-- เพิ่ม SweetAlert JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.js"></script>
<script>
    // แสดงตัวอย่างภาพที่ผู้ใช้เลือก
    document.getElementById('item_image').addEventListener('change', function(event) {
        let files = event.target.files;
        let previewContainer = document.getElementById('itemPreviewContainer');
        previewContainer.innerHTML = ''; // ลบตัวอย่างภาพเดิม
        for (let i = 0; i < files.length; i++) {
            let file = files[i];
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '150px';
                img.style.marginRight = '10px';
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('finder_image').addEventListener('change', function(event) {
        let files = event.target.files;
        let previewContainer = document.getElementById('finderPreviewContainer');
        previewContainer.innerHTML = ''; // ลบตัวอย่างภาพเดิม
        for (let i = 0; i < files.length; i++) {
            let file = files[i];
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '150px';
                img.style.marginRight = '10px';
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<script>
   // ฟังก์ชันบีบอัดภาพและย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
function resizeImageAndUpload($file, $target_dir, $maxWidth = 800, $maxHeight = 800) {
    // ตรวจสอบว่าไฟล์ถูกเลือกหรือไม่
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
        return false;
    }

    // ตรวจสอบประเภทไฟล์
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_types = ["jpg", "jpeg", "png"];
    if (!in_array($imageFileType, $allowed_types)) {
        echo "ไฟล์ที่อัปโหลดไม่ถูกต้อง (รองรับเฉพาะ JPG, JPEG, PNG)";
        return false;
    }

    // ฟังก์ชันบีบอัดภาพ
    $imageType = exif_imagetype($file['tmp_name']);
    $new_image_name = date("d-m-Y_H-i-s") . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $imageFileType;
    $target_file = $target_dir . $new_image_name;

    // บีบอัดและปรับขนาดภาพ
    if (!resizeImage($file['tmp_name'], $target_file, $imageType, $maxWidth, $maxHeight)) {
        echo "ไม่สามารถบีบอัดภาพได้";
        return false;
    }

    return $new_image_name; // คืนชื่อไฟล์ใหม่
}

// ฟังก์ชันบีบอัดภาพ
function resizeImage($sourcePath, $targetPath, $imageType, $maxWidth = 800, $maxHeight = 800) {
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    list($origWidth, $origHeight) = getimagesize($sourcePath);
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = (int)($origWidth * $ratio);
    $newHeight = (int)($origHeight * $ratio);

    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $targetPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $targetPath, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $targetPath);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($newImage);

    return true;
}

// ฟังก์ชันอัปโหลดและบีบอัดไฟล์
function uploadImages($files, $target_dir, $old_images, $maxWidth = 800, $maxHeight = 800) {
    $uploaded_images = [];
    foreach ($files['name'] as $key => $file_name) {
        if (!empty($file_name)) {
            $original_name = pathinfo($file_name, PATHINFO_FILENAME);
            $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $formatted_timestamp = date("d-m-Y_H-i-s");
            $new_image_name = $formatted_timestamp . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $original_name) . "." . $imageFileType;
            $target_file = $target_dir . $new_image_name;

            $allowed_types = ["jpg", "jpeg", "png"];

            // ตรวจสอบประเภทไฟล์
            if (!in_array($imageFileType, $allowed_types)) {
                echo "ไฟล์ที่อัปโหลดไม่ถูกต้อง (รองรับเฉพาะ JPG, JPEG, PNG)";
                exit;
            }

            // ตรวจสอบขนาดไฟล์ก่อนบีบอัด
            if ($files['size'][$key] > 1048576) { // 1MB
                $imageType = exif_imagetype($files['tmp_name'][$key]);
                if (!resizeImage($files['tmp_name'][$key], $target_file, $imageType, $maxWidth, $maxHeight)) {
                    echo "ไม่สามารถบีบอัดภาพได้";
                    exit;
                }
            } else {
                // ถ้าขนาดไม่เกิน 1MB ให้ย้ายไฟล์โดยตรง
                if (!move_uploaded_file($files['tmp_name'][$key], $target_file)) {
                    echo "ไม่สามารถย้ายไฟล์ได้";
                    exit;
                }
            }

            // ลบไฟล์เก่า (ถ้ามี)
            if (!empty($old_images) && file_exists($target_dir . $old_images)) {
                unlink($target_dir . $old_images);
            }

            $uploaded_images[] = $new_image_name;
        } else {
            $uploaded_images[] = $old_images; // ใช้รูปเก่าถ้าไม่มีการอัปโหลดใหม่
        }
    }
    return implode(',', $uploaded_images);
}

// ตัวอย่างการเรียกใช้ฟังก์ชัน
$new_item_image = uploadImages($_FILES['item_image'], "../lost_images/", $old_item_image);
$new_finder_image = uploadImages($_FILES['finder_image'], "../return_images/", $old_finder_image);

    </script>
</body>
</html>
