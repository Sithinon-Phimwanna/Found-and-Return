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
    <!-- ช่องให้ผู้ใช้กรอก ID หรือชื่อ -->
    <div class="form-group row">
        <label for="user_id" class="col-sm-2">ค้นหา (ID หรือ ชื่อ):</label>
        <input type="text" class="form-control-sm-4" id="user_id" placeholder="กรอกเลข ID หรือ ชื่อ" oninput="fetchUserData()">
    </div>
    
    <div class="form-group row">
        <label for="owner_name" class="col-sm-2">ชื่อผู้แจ้ง:</label>
        <input type="text" class="form-control-sm-4" id="owner_name" name="owner_name" required>
    </div>
    <div class="form-group row">
        <label for="owner_contact" class="col-sm-2">ช่องทางการติดต่อ:</label>
        <input type="text" class="form-control-sm-4" id="owner_contact" name="owner_contact" required>
    </div>
    
    <div class="form-group row">
        <label for="item_name" class="col-sm-2">ทรัพย์สินที่หาย:</label>
        <input type="text" class="form-control-sm-4" id="item_name" name="item_name" required>
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
        <label for="item_image" class="col-sm-2">อัพโหลดภาพ:</label>
        <input class="form-control-sm-4" type="file" id="item_image" name="item_image[]" multiple onchange="resizeImages(event)"required>
    </div>
    <div class="form-group row">
        <label class="col-sm-2"></label>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
    </div>
</form>

    </div>
</div>
                      <!--php sunmit -->
                      <?php
                      require 'config.php';

                      function resizeImage($source, $destination, $max_width, $max_height) {
                          list($orig_width, $orig_height, $image_type) = getimagesize($source);
                          
                          $allowed_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
                          if (!in_array($image_type, $allowed_types)) {
                              return false;
                          }

                          $ratio = min($max_width / $orig_width, $max_height / $orig_height);
                          $new_width = round($orig_width * $ratio);
                          $new_height = round($orig_height * $ratio);

                          $image_p = imagecreatetruecolor($new_width, $new_height);
                          
                          if ($image_type == IMAGETYPE_JPEG) {
                              $image = imagecreatefromjpeg($source);
                              imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
                              imagejpeg($image_p, $destination, 90);
                          } elseif ($image_type == IMAGETYPE_PNG) {
                              $image = imagecreatefrompng($source);
                              imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
                              imagepng($image_p, $destination, 9);
                          }
                          
                          return true;
                      }

                      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                          $owner_name = $_POST['owner_name'];
                          $owner_contact = $_POST['owner_contact'];
                          $item_name = $_POST['item_name'];
                          $item_description = $_POST['item_description'];
                          $lost_location = $_POST['lost_location'];
                          $lost_date = date('Y-m-d H:i:s');

                          $images = [];
                          if (isset($_FILES['item_image']) && !empty($_FILES['item_image']['name'][0])) {
                              $upload_dir = '../lost_images/';
                              if (!is_dir($upload_dir)) {
                                  mkdir($upload_dir, 0777, true);
                              }

                              foreach ($_FILES['item_image']['name'] as $key => $filename) {
                                  $tmp_name = $_FILES['item_image']['tmp_name'][$key];
                                  $error = $_FILES['item_image']['error'][$key];
                                  $file_size = $_FILES['item_image']['size'][$key];
                                  $file_type = $_FILES['item_image']['type'][$key];
                                  
                                  // ตรวจสอบข้อผิดพลาดในการอัปโหลด
                                  if ($error !== UPLOAD_ERR_OK) continue;
                                  if (!in_array($file_type, ['image/jpeg', 'image/png'])) continue;
                                  if ($file_size > 1048576) continue;
                                  
                                  $timestamp = date('Y-m-d_H-i-s');
                                  $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                  $new_filename = $timestamp . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension;
                                  $target_file = $upload_dir . $new_filename;

                                  if (move_uploaded_file($tmp_name, $target_file)) {
                                    // สร้างไฟล์รีไซส์
                                    $resized_file = $upload_dir . 'resized_' . $new_filename;
                                    if (resizeImage($target_file, $resized_file, 1024, 1024)) {
                                        unlink($target_file); // ลบไฟล์ต้นฉบับถ้าสำเร็จ
                                        $images[] = basename($resized_file); // เพิ่มชื่อไฟล์รีไซส์ลงในอาร์เรย์
                                        echo "Image resized successfully: " . $resized_file; // แสดงข้อความว่าการรีไซส์สำเร็จ
                                    } else {
                                        // ถ้ารีไซส์ไม่สำเร็จ
                                        echo "Error: Image resize failed for: " . $target_file; // แสดงข้อผิดพลาด
                                        echo "<br>Error details: Unable to resize the image. Please check the file format or size."; // ข้อความเพิ่มเติม
                                        unlink($target_file); // ลบไฟล์ต้นฉบับที่ไม่สามารถรีไซส์ได้
                                        exit; // หยุดการดำเนินการเพื่อไม่ให้ส่งไฟล์ไป
                                    }
                                }
                                
                                
                              }
                          }

                          $images_str = !empty($images) ? implode(',', $images) : NULL;
                          $status_id = 1;

                          // เปลี่ยนคำสั่ง SQL สำหรับตาราง lost_items
                          $stmt = $mysqli->prepare("INSERT INTO lost_items (owner_name, owner_contact, item_name, item_description, lost_date, lost_location, item_image, status_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                          if ($stmt) {
                              $stmt->bind_param("sssssssi", $owner_name, $owner_contact, $item_name, $item_description, $lost_date, $lost_location, $images_str, $status_id);
                              if ($stmt->execute()) {
                                  echo '<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>';
                                  echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>';
                                  echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                                  echo '<script>setTimeout(function() { swal({ title: "บันทึกข้อมูลสำเร็จ", type: "success" }, function() { window.location = "lost_item_form.php"; }); }, 1000);</script>';
                              }
                              $stmt->close();
                          }
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
                document.getElementById("owner_name").value = `${foundUser.firstName} ${foundUser.lastName}`;
                document.getElementById("owner_contact").value = foundUser.phone;
            }
        })
        .catch(error => console.error("Error fetching user data:", error));
}

function resizeImages(event) {
    const files = event.target.files;
    const maxSize = 1024; // กำหนดขนาดสูงสุด 1024px

    let resizedFiles = [];

    Array.from(files).forEach(file => {
        if (!file.type.startsWith("image/")) return; // ตรวจสอบว่าเป็นรูปภาพเท่านั้น

        const reader = new FileReader();
        reader.readAsDataURL(file);

        reader.onload = function (e) {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function () {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > maxSize) {
                        height *= maxSize / width;
                        width = maxSize;
                    }
                } else {
                    if (height > maxSize) {
                        width *= maxSize / height;
                        height = maxSize;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(blob => {
                    const newFile = new File([blob], file.name, { type: "image/jpeg", lastModified: Date.now() });

                    resizedFiles.push(newFile);

                    if (resizedFiles.length === files.length) {
                        const dataTransfer = new DataTransfer();
                        resizedFiles.forEach(resizedFile => dataTransfer.items.add(resizedFile));

                        document.getElementById("item_image").files = dataTransfer.files;
                    }
                }, "image/jpeg", 0.8);
            };
        };
    });
}

    </script>

<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
</body>
</html>
