<?php
session_start();

require 'config.php';


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
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../assets/plugins/summernote/summernote-bs4.min.css">
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
            <h1 class="m-0">แจ้งเก็บทรัพย์สินได้</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">แจ้งเก็บทรัพย์สินได้</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <?php
        // ตั้งค่าการเชื่อมต่อฐานข้อมูล
        require 'config.php';

        // สร้างการเชื่อมต่อฐานข้อมูล
        $conn = new mysqli($servername, $username, $password, $dbname);

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // คำสั่ง SQL
        $sql = "SELECT found_location, COUNT(*) AS item_count FROM found_items WHERE found_location IS NOT NULL GROUP BY found_location ORDER BY item_count DESC";
        $result = $mysqli->query($sql);

        // ตรวจสอบว่า SQL query สำเร็จหรือไม่
        if ($result === false) {
            die("Error in SQL query: " . $mysqli->error);
        }

        // สร้างอาร์เรย์เพื่อเก็บข้อมูล
        $locations = [];
        $item_counts = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // แปลงค่าจากเลขชั้นเป็นชื่อชั้น
                switch ($row['found_location']) {
                    case '1':
                        $location_name = 'ชั้น 1 โซนสำนักงาน 24 ชม.';
                        break;
                    case '2':
                        $location_name = 'ชั้น 1 โซนธนาคาร';
                        break;
                    case '3':
                        $location_name = 'ชั้น 2 โซน A';
                        break;
                    case '4':
                        $location_name = 'ชั้น 2 โซน B';
                        break;
                    case '5':
                        $location_name = 'ชั้น 3 โซน A';
                        break;
                    case '6':
                        $location_name = 'ชั้น 3 โซน B';
                        break;
                    case '7':
                        $location_name = 'ชั้น 4 โซน A';
                        break;
                    case '8':
                        $location_name = 'ชั้น 4 โซน B';
                        break;
                    default:
                        $location_name = $row['found_location']; // หากไม่ตรงกับเงื่อนไข
                }

                $locations[] = $location_name;
                $item_counts[] = $row['item_count'];
            }
        } else {
            echo "No results found.";
        }

        $conn->close();
        ?>
        <div class="row">
            <!-- Card สำหรับกราฟ Found -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="card-header ui-sortable-handle" style="cursor: move;">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    จำนวนชั้นที่พบทรัพย์สินมากที่สุด
                                </h3>
                            </div>
                            <!-- กราฟ Found -->
                            <div class="chart tab-pane active" id="found-chart" style="position: relative; height: 400px;">
                                <canvas id="revenue-chart-canvas" height="320" style="height: 350px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card สำหรับกราฟ Lost -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="card-header ui-sortable-handle" style="cursor: move;">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    จำนวนชั้นทรัพย์สินหายมากที่สุด
                                </h3>
                            </div>
                            <!-- กราฟ Lost -->
                            <div class="chart tab-pane active" id="lost-chart" style="position: relative; height: 400px;">
                                <canvas id="my_lost_Chart" height="320" style="height: 350px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // รับข้อมูลจาก PHP และแปลงเป็น JavaScript
            var locations = <?php echo json_encode($locations); ?>;
            var itemCounts = <?php echo json_encode($item_counts); ?>;

            // กำหนดสีสำหรับกราฟ
            var colors = [
                'rgba(255, 0, 0, 0.8)',
                'rgba(255, 123, 0, 0.8)',
                'rgba(255, 251, 0, 0.8)',
                'rgba(72, 253, 0, 0.8)',
                'rgba(38, 240, 48, 0.8)',
                'rgba(0, 255, 255, 0.8)',
                'rgba(0, 59, 253, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ];

            // สร้างกราฟด้วย Chart.js
            var ctx = document.getElementById('revenue-chart-canvas').getContext('2d');
            var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: locations,
                datasets: [{
                    label: 'จำนวนทรัพย์สินที่พบ',
                    data: itemCounts,
                    backgroundColor: colors.slice(0, locations.length),
                    borderColor: 'white',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right', // เปลี่ยนตำแหน่งเป็นด้านล่าง
                        labels: {
                            boxWidth: 20,
                            padding: 15, // เพิ่มพื้นที่
                            font: {
                                size: 12 // ลดขนาดข้อความใน legend
                            }
                        }
                    }
                }
            }
        });
        </script>
        <!--chart lost -->
        <?php
                // ตั้งค่าการเชื่อมต่อฐานข้อมูล
                require 'config.php';

                // สร้างการเชื่อมต่อฐานข้อมูล
                $conn = new mysqli($servername, $username, $password, $dbname);

                // ตรวจสอบการเชื่อมต่อ
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // คำสั่ง SQL
                $sql = "SELECT lost_location, COUNT(*) AS item_count FROM lost_items WHERE lost_location IS NOT NULL GROUP BY lost_location ORDER BY item_count DESC";
                $result = $mysqli->query($sql);

                // ตรวจสอบว่า SQL query สำเร็จหรือไม่
                if ($result === false) {
                    die("Error in SQL query: " . $mysqli->error);
                }



                // สร้างอาร์เรย์เพื่อเก็บข้อมูล
                $locations = [];
                $item_counts = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // แปลงค่าจากเลขชั้นเป็นชื่อชั้น
                        switch ($row['lost_location']) {
                            case '1':
                                $location_name = 'ชั้น 1 โซนสำนักงาน 24 ชม.';
                                break;
                            case '2':
                                $location_name = 'ชั้น 1 โซนธนาคาร';
                                break;
                            case '3':
                                $location_name = 'ชั้น 2 โซน A';
                                break;
                            case '4':
                                $location_name = 'ชั้น 2 โซน B';
                                break;
                            case '5':
                                $location_name = 'ชั้น 3 โซน A';
                                break;
                            case '6':
                                $location_name = 'ชั้น 3 โซน B';
                                break;
                            case '7':
                                $location_name = 'ชั้น 4 โซน A';
                                break;
                            case '8':
                                $location_name = 'ชั้น 4 โซน B';
                                break;
                            default:
                                $location_name = $row['lost_location']; // หากไม่ตรงกับเงื่อนไข
                        }

                        $locations[] = $location_name;
                        $item_counts[] = $row['item_count'];
                    }
                } else {
                    echo "No results lost.";
                }

                $conn->close();
                ?>

                <script>
                // รับข้อมูลจาก PHP และแปลงเป็น JavaScript
                var locations = <?php echo json_encode($locations); ?>;
                var itemCounts = <?php echo json_encode($item_counts); ?>;

                // กำหนดสีสำหรับกราฟ
                var colors = [
                'rgba(255, 0, 0, 0.8)', // สี 1 - แดงสดใสจาง
                'rgba(255, 123, 0, 0.8)', // สี 2 - ส้มสดใสจาง
                'rgba(255, 251, 0, 0.8)', // สี 3 - เหลืองสดใสจาง
                'rgba(72, 253, 0, 0.8)', // สี 4 - เขียวสดใสจาง
                'rgba(38, 240, 48, 0.8)', // สี 5 - เขียวเข้มสดใสจาง
                'rgba(0, 255, 255, 0.8)', // สี 6 - ฟ้าอ่อนสดใสจาง
                'rgba(0, 59, 253, 0.8)', // สี 7 - ฟ้าเข้มสดใสจาง
                'rgba(153, 102, 255, 0.8)', // สี 8 - ม่วงสดใสจาง
                'rgba(255, 99, 132, 0.8)'  // สี 9 - ชมพูสดใสจาง
                ];




                // สร้างกราฟด้วย Chart.js
                var ctx = document.getElementById('my_lost_Chart').getContext('2d');
                var my_lost_Chart = new Chart(ctx, {
                    type: 'pie', // ชนิดของกราฟ
                    data: {
                        labels: locations, // เลเบลของกราฟ (สถานที่พบ)
                        datasets: [{
                            label: 'จำนวนทรัพย์สินที่หาย',
                            data: itemCounts, // ข้อมูลจำนวนของที่พบ
                            backgroundColor: colors.slice(0, locations.length), // สีสำหรับแต่ละแท่ง
                            borderColor: 'white',  // สีขอบ
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right', // เปลี่ยนตำแหน่งเป็นด้านล่าง
                                labels: {
                                    boxWidth: 20,
                                    padding: 15, // เพิ่มพื้นที่
                                    font: {
                                        size: 12 // ลดขนาดข้อความใน legend
                                    }
                                }
                            }
                        }
                    }
                });
                </script>

                      
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
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>

