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
// นำเข้าไฟล์ config.php
require 'config.php';

// ตรวจสอบว่าได้ล็อกอินหรือยัง


// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
try {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT UserAdminName, email FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query); // ใช้ $mysqli
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
$apiUrl = "https://dummyjson.com/users";
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// ตรวจสอบว่ามีข้อมูล users หรือไม่
$users = isset($data['users']) ? $data['users'] : [];
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
                ข้อมูลทรัพย์สิน
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="found_items_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ข้อมูลแจ้งพบทรัพสิน</p>
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
            <h1 class="m-0">แจ้งพบทรัพย์สิน</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">แจ้งพบทรัพย์สิน</li>
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
        <!-- ช่องให้ผู้ใช้กรอก ID หรือชื่อ -->
        <div class="form-group row">
            <label for="user_id" class="col-sm-2">ค้นหา (ID หรือ ชื่อ):</label>
            <input type="text" class="form-control-sm-4" id="user_id" placeholder="กรอกเลข ID หรือ ชื่อ" oninput="fetchUserData()">
        </div>
        
        <div class="form-group row">
            <label for="finder_name" class="col-sm-2">ชื่อผู้แจ้ง:</label>
            <input type="text" class="form-control-sm-4" id="finder_name" name="finder_name" required>
        </div>
        <div class="form-group row">
            <label for="finder_contact" class="col-sm-2">ช่องทางการติดต่อ:</label>
            <input type="text" class="form-control-sm-4" id="finder_contact" name="finder_contact" required>
        </div>
        <div class="form-group row">
            <label for="found_name" class="col-sm-2">ทรัพย์สิน:</label>
            <input type="text" class="form-control-sm-4" id="found_name" name="found_name" required>
        </div>
        <div class="form-group row">
            <label for="found_description" class="col-sm-2">รายละเอียด:</label>
            <textarea class="form-control-sm-4" id="found_description" name="found_description" rows="3" required></textarea>
        </div>
        <div class="form-group row">
            <label for="found_date" class="col-sm-2">วันที่เก็บได้:</label>
            <input type="date" class="form-control-sm-4" id="found_date" name="found_date" required>
        </div>
        <div class="form-group row">
            <label for="found_location" class="col-sm-2">สถานที่เก็บได้:</label>
            <select class="form-control-sm-4" id="found_location" name="found_location" required>
                <option value="">เลือกสถานที่</option>
                <!-- PHP for populating locations dynamically -->
                <?php while ($location = $locations->fetch_assoc()): ?>
                    <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group row">
           <label for="consignee" class="col-sm-2">ผู้รับแจ้ง:</label> 
           <input type="text" class="form-control-sm-4" id="consignee" name="consignee" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group row">
            <label for="found_image" class="col-sm-2">อัพโหลดภาพทรัพย์สินที่เก็บได้:</label>
            <input class="form-control-sm-4" type="file" id="found_image" name="found_image[]" multiple>
        </div>
        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </div>
    </form>
</div>
                    <!--php submit -->
                    <?php
                      require 'config.php';

                      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                          // รับค่าจากฟอร์ม
                          $finder_name = $_POST['finder_name'];
                          $finder_contact = $_POST['finder_contact'];
                          $found_name = $_POST['found_name'];
                          $found_description = $_POST['found_description'];
                          $found_location = $_POST['found_location'];
                          $consignee =$_POST['consignee'];

                          // ใช้เวลาปัจจุบัน
                          $found_date = date('Y-m-d H:i:s');

                          // ตรวจสอบและจัดการอัปโหลดภาพหลายไฟล์ (ทำให้ไม่บังคับ)
                          $images = [];
                          if (isset($_FILES['found_image']) && !empty($_FILES['found_image']['name'][0])) {
                              $upload_dir = '../found_images/';

                              if (!is_dir($upload_dir)) {
                                  mkdir($upload_dir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
                              }

                              foreach ($_FILES['found_image']['name'] as $key => $filename) {
                                $tmp_name = $_FILES['found_image']['tmp_name'][$key];
                                $error = $_FILES['found_image']['error'][$key];
                                $file_size = $_FILES['found_image']['size'][$key];
                                $file_type = $_FILES['found_image']['type'][$key];
                            
                                // ตรวจสอบข้อผิดพลาดจากการอัปโหลดไฟล์
                                if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                                    // ข้อผิดพลาดเกิดจากขนาดไฟล์เกินที่ตั้งไว้
                                    echo '
                                    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
                                    <script>
                                        setTimeout(function() {
                                            swal({
                                            title: "ไฟล์ ' . $filename . ' มีขนาดใหญ่เกินไป (สูงสุด 1MB)",
                                            type: "error"
                                            }, function() {
                                            window.location = "found_item_form.php"; //หน้าที่ต้องการให้กระโดดไป
                                            });
                                        }, 1000);
                                    </script>';
                                    die(); // หยุดการทำงานเมื่อเกิดข้อผิดพลาดขนาดไฟล์
                                }
                            
                                // ตรวจสอบประเภทไฟล์
                                if (!in_array($file_type, ['image/jpeg', 'image/png'])) {
                                    echo '
                                    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
                                    <script>
                                        setTimeout(function() {
                                            swal({
                                            title: "ไฟล์ ' . $filename . ' ต้องเป็น JPEG หรือ PNG เท่านั้น",
                                            type: "error"
                                            }, function() {
                                            window.location = "found_item_form.php"; //หน้าที่ต้องการให้กระโดดไป
                                            });
                                        }, 1000);
                                    </script>';
                                    die(); // หยุดการทำงานเมื่อเกิดข้อผิดพลาดประเภทไฟล์
                                }
                            
                                // ตรวจสอบขนาดไฟล์ที่เกิน 1MB
                                $max_file_size = 1048576; // 1MB
                                if ($file_size > $max_file_size) {
                                    echo '
                                    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
                                    <script>
                                        setTimeout(function() {
                                            swal({
                                            title: "ไฟล์ ' . $filename . ' มีขนาดใหญ่เกินไป (สูงสุด 1MB)",
                                            type: "error"
                                            }, function() {
                                            window.location = "found_item_form.php"; //หน้าที่ต้องการให้กระโดดไป
                                            });
                                        }, 1000);
                                    </script>';
                                    die(); // หยุดการทำงานเมื่อขนาดไฟล์เกินขีดจำกัด
                                }
                            
                                // ตั้งชื่อไฟล์ใหม่และอัปโหลดไฟล์
                                $timestamp = date('Y-m-d_H-i-s');
                                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                $new_filename = $timestamp . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension;
                                $target_file = $upload_dir . $new_filename;
                            
                                if (move_uploaded_file($tmp_name, $target_file)) {
                                    $images[] = basename($target_file);
                                } else {
                                    echo '
                                    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
                                    <script>
                                        setTimeout(function() {
                                            swal({
                                            title: "ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์",
                                            type: "error"
                                            }, function() {
                                            window.location = "found_item_form.php"; //หน้าที่ต้องการให้กระโดดไป
                                            });
                                        }, 1000);
                                    </script>';
                                    exit; // หยุดการทำงานเมื่อเกิดข้อผิดพลาดในการอัปโหลดไฟล์
                                }
                            }
                            
                            
                            
                          }      

                          // แปลงอาร์เรย์ชื่อไฟล์เป็นสตริง (ใช้ , แยก) หรือใช้ NULL ถ้าไม่อัปโหลดไฟล์
                          $images_str = !empty($images) ? implode(',', $images) : NULL;

                          $status_id = 1; // 'พบ' ในข้อมูล statuses

                          // เตรียมคำสั่ง SQL เพื่อบันทึกข้อมูล
                          $stmt = $mysqli->prepare("INSERT INTO found_items (finder_name, finder_contact, found_name, found_description, found_date, found_location, consignee, found_image, status_id) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                          if ($stmt === false) {
                              die('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $mysqli->error);
                          }

                          // ผูกค่าตัวแปรกับคำสั่ง SQL
                          $stmt->bind_param("ssssssssi", $finder_name, $finder_contact, $found_name, $found_description, $found_date, $found_location, $consignee, $images_str, $status_id);

                          // บันทึกข้อมูลลงฐานข้อมูล
                          if ($stmt->execute()) {
                              // Sweet alert แจ้งเตือน
                              echo '
                              <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                              <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                              echo '<script>
                                  setTimeout(function() {
                                      swal({
                                          title: "บันทึกข้อมูลสำเร็จ",
                                          type: "success"
                                      }, function() {
                                          window.location = "found_item_form.php"; //หน้าที่ต้องการให้กระโดดไป
                                      });
                                  }, 1000);
                              </script>';
                          } else {
                              // Sweet alert แจ้งเตือน
                              echo '<script>
                                  setTimeout(function() {
                                      swal({
                                          title: "เกิดข้อผิดพลาด",
                                          type: "error"
                                      }, function() {
                                          window.location = "found_item_form.php"; //หน้าที่ต้องการให้กระโดดไป
                                      });
                                  }, 1000);
                              </script>';
                          }

                          // ปิดการเชื่อมต่อฐานข้อมูล
                          $stmt->close();
                          $mysqli->close();
                      }
                      ?> 
            </div>
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
<script>
        function fetchUserData() {
    const userInput = document.getElementById("user_id").value;
    
    if (userInput.length === 0) return; // ถ้าไม่มีข้อมูลให้หยุดทำงาน

    fetch("https://dummyjson.com/users")
        .then(response => response.json())
        .then(data => {
            const users = data.users;
            let foundUser = null;

            // ค้นหาผู้ใช้จาก ID หรือ ชื่อ
            foundUser = users.find(user => user.id.toString() === userInput || user.firstName.toLowerCase().includes(userInput.toLowerCase()));

            if (foundUser) {
                document.getElementById("finder_name").value = `${foundUser.firstName} ${foundUser.lastName}`;
                document.getElementById("finder_contact").value = foundUser.phone;
            }
        })
        .catch(error => console.error("Error fetching user data:", error));
}

    </script>
</body>
</html>
