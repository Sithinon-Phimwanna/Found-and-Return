<?php
session_start();

// ตรวจสอบว่าได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    // ป้องกันการแคชอย่างเข้มงวด
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    header('Location: login.php'); // ถ้าไม่ได้ล็อกอินให้ไปหน้าเข้าสู่ระบบ
    exit;
}

require 'config.php';

    // ถ้าไม่มีการค้นหาให้ดึงข้อมูลทั้งหมด
    $query = "
        SELECT 
            found_items.found_id,
            found_items.finder_name,
            found_items.finder_contact,
            found_items.found_name,
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
        ORDER BY 
            found_items.found_date DESC;
    ";
    


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
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- table -->
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="register.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>สมัครสมาชิก</p>
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
            <h1 class="m-0">ตารางแจ้งทรัพย์สินที่เก็บได้</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">ตารางแจ้งทรัพย์สินที่เก็บได้</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <!-- <div class="card-header">
                <h3 class="card-title">DataTable with default features</h3>
              </div> -->
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th style="font-size: 14px;">รหัส</th>
                    <th style="font-size: 14px;">ชื่อผู้แจ้ง</th>
                    <th style="font-size: 14px;">ทรัพย์สิน</th>
                    <th style="font-size: 14px;">รายละเอียด</th>
                    <th style="font-size: 14px;">สถานที่</th>
                    <th style="font-size: 14px;">วันที่แจ้ง</th>
                    <th style="font-size: 14px;">สถานะ</th>
                    <th style="font-size: 14px;">เพิ่มเติม</th>
                    <th style="font-size: 14px;">แก้ไข</th>
                    <th style="font-size: 14px;">ลบ</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                  <td style="font-size: 14px;"><?= $row['found_id'] ?></td>
                                    <td style="font-size: 14px;"><?= htmlspecialchars($row['finder_name']) ?></td>
                                    <td style="font-size: 14px;"><?= htmlspecialchars($row['found_name']) ?></td>
                                    <td style="font-size: 14px;"><?= htmlspecialchars($row['found_description']) ?></td>
                                    <td style="font-size: 14px;"><?= htmlspecialchars($row['found_location']) ?></td>
                                    <td style="font-size: 14px;"><?= date('d/m/Y H:i', strtotime($row['found_date'])) ?></td>
                                    <td style="font-size: 14px;"><?= htmlspecialchars($row['status']) ?></td>
                                    <!-- รายละเอียดเพิ่มเติม -->
                                    <td style="font-size: 14px;">
                                      <button class="btn btn-info" onclick="viewDetails(<?= $row['found_id'] ?>) " style="font-size: 14px;">ดูรายละเอียดเพิ่มเติม</button>
                                  </td>
                                  <!-- แก้ไข -->
                                    <td style="font-size: 14px;">
                                    <button class="btn btn-warning" onclick="window.location.href='found_edit.php?found_id=<?= $row['found_id'] ?>'" style="font-size: 14px;">แก้ไข</button>
                                    <?php
                                        // ตรวจสอบค่า success ใน URL
                                        if (isset($_GET['success']) && $_GET['success'] == 2) {
                                            echo '
                                            <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
                                            <script>
                                                setTimeout(function() {
                                                    swal({
                                                        title: "แก้ไขข้อมูลสำเร็จแล้ว!",
                                                        text: "ข้อมูลถูกแก้ไขเรียบร้อยแล้ว",
                                                        type: "success"
                                                    }, function() {
                                                        window.location = "found_items_list.php";  // รีไดเร็กต์ไปหน้า list หลังแสดงผล
                                                    });
                                                }, 1000);
                                            </script>';
                                            exit;
                                        }
                                        ?>
                                    </td>
                                    <td style="font-size: 14px;">
                                        <!-- ปุ่มลบข้อมูล -->
                                        <button onclick="deleteItem(<?= $row['found_id'] ?>)" class="btn btn-danger" style="font-size: 14px;">ลบ</button>
                                        <?php
                                        // ตรวจสอบค่า success ใน URL
                                        if (isset($_GET['success']) && $_GET['success'] == 1) {
                                            echo '
                                            <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                                            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
                                            <script>
                                                setTimeout(function() {
                                                    swal({
                                                        title: "ลบข้อมูลสำเร็จ!",
                                                        text: "ข้อมูลถูกลบออกจากระบบเรียบร้อยแล้ว",
                                                        type: "success"
                                                    }, function() {
                                                        window.location = "found_items_list.php";  // รีไดเร็กต์ไปหน้า list หลังแสดงผล
                                                    });
                                                }, 1000);
                                            </script>';
                                            exit;
                                        }
                                        ?>
                                    </td>
                  </tr>
                  <?php endwhile; ?>
                  </tbody>
                  <!-- <tfoot>
                  <tr>
                    <th>Rendering engine</th>
                    <th>Browser</th>
                    <th>Platform(s)</th>
                    <th>Engine version</th>
                    <th>CSS grade</th>
                  </tr>
                  </tfoot> -->
                </table>
                <style>
                .zoomed-in {
                      transform: scale(3);  /* ขยายภาพ 3 เท่า */
                      cursor: zoom-out;
                      transition: transform 0.2s ease;
                      
                      /* ทำให้ภาพแสดงกลางหน้าจอ */
                      position: fixed;  /* ใช้ position fixed เพื่อให้ภาพแสดงอยู่บนหน้าจอ */
                      top: 50%;         /* วางภาพให้ตรงกลางแนวตั้ง */
                      left: 50%;        /* วางภาพให้ตรงกลางแนวนอน */
                      transform: translate(-50%, -50%) scale(3);  /* ใช้ translate เพื่อย้ายภาพกลับมาที่กลาง */
                  }

                  .enlargeable-img {
                      max-width: 100%;
                      width: 100%;
                      height: auto;
                      transition: transform 0.2s ease;
                  }


              </style>

                  <!-- Modal สำหรับแสดงรายละเอียด -->
                <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel">รายละเอียดทรัพย์สิน</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="modalContent">
                                <!-- ข้อมูลจะแสดงที่นี่ -->
                                <img id="modal-img" src="image_url.jpg" class="enlargeable-img" alt="คำอธิบายรูปภาพ">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>


              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
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
<!-- DataTables  & Plugins -->
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../assets/dist/js/pages/dashboard.js"></script><s></s>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- ส่วน script สำหรับการลบ -->
<script>
    function deleteItem(foundId) {
        if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?")) {
            // ส่งคำขอ POST ไปยังไฟล์ลบข้อมูล
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete_found.php'; // เปลี่ยนเส้นทางไปที่ไฟล์ลบข้อมูล

            // สร้าง hidden input สำหรับ found_id
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'found_id';
            input.value = foundId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    function viewDetails(found_id) {
    $.ajax({
        url: "found_details.php",
        type: "GET",
        data: { found_id: found_id },
        success: function(response) {
            $("#modalContent").html(response);
            $('#detailModal').modal('show'); // เปิด Modal เมื่อโหลดข้อมูลเสร็จ

            // ฟังก์ชันการขยายภาพ
            $('#modalContent img').click(function() {
                $(this).toggleClass('zoomed-in'); // เพิ่ม/ลบคลาส zoomed-in เพื่อขยายหรือย่อ
            });
        }
    });
}


  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,"order": [[1, 'desc']],
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "order": [[1, 'desc']],
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
// ฟังก์ชันที่จะถูกเรียกเมื่อหน้าโหลดหรือเมื่อมีการเปลี่ยนแปลงข้อมูลในตาราง
function updateStatusColors() {
    // เลือกทุกแถวในตาราง
    const rows = document.querySelectorAll("#example1 tbody tr");

    // ลูปผ่านแต่ละแถว
    rows.forEach(row => {
        // ดึงค่าจากคอลัมน์สถานะ
        const statusCell = row.cells[6];  // คอลัมน์สถานะ (หมายเลขคอลัมน์ 6)
        const status = statusCell.textContent.trim();

        // ดึงค่าวันที่จากคอลัมน์วันที่แจ้ง
        const lostDateCell = row.cells[5];  // คอลัมน์วันที่ (หมายเลขคอลัมน์ 5)
        const lostDate = new Date(lostDateCell.textContent);

        // คำนวณความแตกต่างระหว่างวันที่ปัจจุบันกับวันที่แจ้ง
        const currentDate = new Date();
        const timeDifference = currentDate - lostDate;
        const oneWeekInMilliseconds = 7 * 24 * 60 * 60 * 1000; // 1 สัปดาห์เป็นมิลลิวินาที

        // กำหนดสีตามสถานะ
        if (status === "แจ้งพบ") {
            statusCell.style.color = "blue";  // สีแดงสำหรับสถานะหาย
        } else if (status === "ได้รับคืนแล้ว") {
            statusCell.style.color = "green";  // สีเขียวสำหรับสถานะได้รับคืนแล้ว
        } else if (status === "แจ้งพบเกิน 1 สัปดาห์") {
            statusCell.style.color = "rgba(255, 206, 86, 1)";  // สีส้มสำหรับสถานะไม่พบ
        } else if (timeDifference > oneWeekInMilliseconds) {
            statusCell.style.color = "orenge";  // สีเหลืองสำหรับกรณีเกิน 1 สัปดาห์
        }
    });
}

// เรียกใช้ฟังก์ชันเมื่อโหลดหน้าหรือเมื่อข้อมูลมีการเปลี่ยนแปลง
document.addEventListener("DOMContentLoaded", updateStatusColors);
</script>
</body>
</html>
