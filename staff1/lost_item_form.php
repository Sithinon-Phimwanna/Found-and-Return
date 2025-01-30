<?php
session_start();


// เปิดการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ป้องกันการเข้าถึงโดยไม่ได้ล็อกอิน
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); // ถ้ายังไม่ได้ล็อกอินให้ไปหน้า login
  exit;
}
// เชื่อมต่อฐานข้อมูล
require 'config.php';

// ดึงข้อมูลผู้ใช้งานจากฐานข้อมูล
try {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT UserAdminName, email FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("d", $user_id); // ใช้ integer สำหรับ user_id
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("ไม่พบข้อมูลผู้ใช้ในระบบ");
    }

    $name = $user['UserAdminName'] ?? '';
    $contact = $user['email'] ?? '';
} catch (Exception $e) {
    die("ข้อผิดพลาด: " . $e->getMessage());
}
// ดึงข้อมูลสถานที่จากฐานข้อมูล
try {
    $query_locations = "SELECT location_id, location_name FROM location";
    $stmt_locations = $mysqli->prepare($query_locations);
    $stmt_locations->execute();
    $locations = $stmt_locations->get_result();
} catch (Exception $e) {
    die("ข้อผิดพลาดในการดึงข้อมูลสถานที่: " . $e->getMessage());
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
        <a href="../staff1_index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../staff1_index.php" class="brand-link">
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
            <h1 class="m-0">แจ้งทรัพย์สินหาย</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">แจ้งทรัพย์สินหาย</li>
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
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group row">
                <label for="owner_name" class="col-sm-2">ชื่อผู้แจ้ง:</label>
                <input type="text" class="form-control-sm-4" id="owner_name" name="owner_name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group row">
                <label for="owner_contact" class="col-sm-2">ช่องทางการติดต่อ:</label>
                <input type="text" class="form-control-sm-4" id="owner_contact" name="owner_contact" value="<?php echo htmlspecialchars($contact); ?>" required>
            </div>
            <div class="form-group row">
                <label for="item_type" class="col-sm-2">ทรัพย์สินที่หาย:</label>
                <input type="text" class="form-control-sm-4" id="item_type" name="item_type" required>
            </div>
            <div class="form-group row">
                <label for="item_description" class="col-sm-2">รายละเอียด:</label>
                <textarea class="form-control-sm-4" id="item_description" name="item_description" rows="3" required></textarea>
            </div>
            <div class="form-group row">
                <label for="lost_date" class="col-sm-2">วันที่หาย:</label>
                <input type="date" class="form-control-sm-4" id="lost_date" name="lost_date" required>
            </div>
            <div class="form-group row">
                <label for="lost_location" class="col-sm-2">สถานที่หาย:</label>
                <select class="form-control-sm-4" id="lost_location" name="lost_location" required>
                    <option value="">เลือกสถานที่</option>
                    <?php while ($location = $locations->fetch_assoc()): ?>
                        <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group row">
                <label for="item_image" class="col-sm-2">อัพโหลดภาพทรัพย์สินที่หาย (ถ้ามี):</label>
                <input class="form-control-sm-4" type="file" id="item_image" name="item_image[]" multiple>
            </div>
            <div class="form-group row">
                <label  class="col-sm-2"></label>
                <div class="col-sm-4">
                  <button type="submit" class="btn btn-primary">ส่งข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
</div>
                      <!--php sunmit -->
                      <?php
                      require 'config.php';

                      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                          // รับค่าจากฟอร์ม
                          $owner_name = $_POST['owner_name'];
                          $owner_contact = $_POST['owner_contact'];
                          $item_type = $_POST['item_type'];
                          $item_description = $_POST['item_description'];
                          $lost_location = $_POST['lost_location'];

                          // ใช้เวลาปัจจุบัน
                          $lost_date = date('Y-m-d H:i:s');

                          // ตรวจสอบและจัดการอัปโหลดภาพหลายไฟล์ (ทำให้ไม่บังคับ)
                          $images = [];
                          if (isset($_FILES['item_image']) && !empty($_FILES['item_image']['name'][0])) {
                              $upload_dir = '../lost_images/';

                              if (!is_dir($upload_dir)) {
                                  mkdir($upload_dir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
                              }

                              foreach ($_FILES['item_image']['name'] as $key => $filename) {
                                  $tmp_name = $_FILES['item_image']['tmp_name'][$key];
                                  $error = $_FILES['item_image']['error'][$key];
                                  $file_size = $_FILES['item_image']['size'][$key];
                                  $file_type = $_FILES['item_image']['type'][$key];

                                  if ($error === UPLOAD_ERR_OK) {
                                      // ตรวจสอบขนาดไฟล์ (1MB)
                                      if ($file_size > 1048576) {
                                          echo '
                                          <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                          <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                                          echo '<script>
                                              setTimeout(function() {
                                                  swal({
                                                      title: "ไฟล์ ' . $filename . ' มีขนาดใหญ่เกินไป (สูงสุด 1MB)",
                                                      type: "error"
                                                  }, function() {
                                                      window.location = "lost_item_form.php";
                                                  });
                                              }, 1000);
                                          </script>';
                                          exit;
                                      }

                                      // ตรวจสอบประเภทไฟล์
                                      if (!in_array($file_type, ['image/jpeg', 'image/png'])) {
                                          echo '
                                          <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                          <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                                          echo '<script>
                                              setTimeout(function() {
                                                  swal({
                                                      title: "ไฟล์ ' . $filename . ' ต้องเป็น JPEG หรือ PNG เท่านั้น",
                                                      type: "error"
                                                  }, function() {
                                                      window.location = "lost_item_form.php";
                                                  });
                                              }, 1000);
                                          </script>';
                                          exit;
                                      }

                                      // ตั้งชื่อไฟล์ใหม่
                                      $timestamp = date('Y-m-d_H-i-s');
                                      $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                      $new_filename = $timestamp . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension;
                                      $target_file = $upload_dir . $new_filename;

                                      if (move_uploaded_file($tmp_name, $target_file)) {
                                          $images[] = $new_filename;
                                      } else {
                                          echo '
                                          <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                          <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                                          echo '<script>
                                              setTimeout(function() {
                                                  swal({
                                                      title: "ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์",
                                                      type: "error"
                                                  }, function() {
                                                      window.location = "lost_item_form.php";
                                                  });
                                              }, 1000);
                                          </script>';
                                          exit;
                                      }
                                  }
                              }
                          }

                          $images_str = !empty($images) ? implode(',', $images) : NULL;
                          $status_id = 1; // สถานะ "หาย"

                          $stmt = $mysqli->prepare("INSERT INTO lost_items (owner_name, owner_contact, item_type, item_description, lost_date, lost_location, item_image, finder_image, deliverer, status_id) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, NULL, NULL, ?)");

                          if ($stmt === false) {
                              die('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $mysqli->error);
                          }

                          $stmt->bind_param("sssssssi", $owner_name, $owner_contact, $item_type, $item_description, $lost_date, $lost_location, $images_str, $status_id);

                          if ($stmt->execute()) {
                              echo '
                              <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                              <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                              echo '<script>
                                  setTimeout(function() {
                                      swal({
                                          title: "เพิ่มข้อมูลสำเร็จ",
                                          type: "success"
                                      }, function() {
                                          window.location = "lost_item_form.php";
                                      });
                                  }, 1000);
                              </script>';
                          } else {
                              echo '
                              <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                              <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                              echo '<script>
                                  setTimeout(function() {
                                      swal({
                                          title: "เกิดข้อผิดพลาด",
                                          type: "error"
                                      }, function() {
                                          window.location = "lost_item_form.php";
                                      });
                                  }, 1000);
                              </script>';
                          }

                          $stmt->close();
                          $mysqli->close();
                      }
                      ?>



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
