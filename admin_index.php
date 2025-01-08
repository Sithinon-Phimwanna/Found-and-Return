<?php
session_start(); // เริ่มเซสชัน

// ป้องกันการเข้าถึงโดยไม่ได้ล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // ถ้ายังไม่ได้ล็อกอินให้ไปหน้า login
    exit;
}

// ป้องกันการแคชของเบราว์เซอร์
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ป้องกันการใช้งานเซสชันซ้ำ
session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found&Return</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 90px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        @media (max-width: 767.98px) {
            .sidebar {
                top: 11.5rem;
                padding: 0;
            }
        }
            
        .navbar {
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .1);
        }

        @media (min-width: 767.98px) {
            .navbar {
                top: 0;
                position: sticky;
                z-index: 999;
            }
        }

        .sidebar .nav-link {
            color: #333;
        }

        .sidebar .nav-link.active {
            color: #0d6efd;
        }
        
        main {
            margin-left: 250px; /* ชดเชยสำหรับความกว้างของ sidebar */
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light p-3">
        <div class="d-flex col-12 col-md-3 col-lg-2 mb-2 mb-lg-0 flex-wrap flex-md-nowrap justify-content-between">
            <a class="navbar-brand" href="#">Found&Return</a>
            <button class="navbar-toggler d-md-none collapsed mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="dropdown">
            <i data-feather="user"></i>
            <script>feather.replace();</script>
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-expanded="false">
              <span class="me-3"><?= htmlspecialchars($_SESSION['UserAdminName']); ?></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                          <a class="nav-link active" id="homeTab" data-bs-toggle="pill" aria-current="page" href="dashboard_content.php">
                            <i data-feather="home"></i>
                            <script>feather.replace();</script>
                            <span class="ml-2">หน้าหลัก</span>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="foundTab" data-bs-toggle="pill" href="found_item_form.php">
                            <i data-feather="folder-plus"></i>
                            <script>feather.replace();</script>
                            <span class="ml-2">แจ้งเก็บทรัพย์สินได้</span>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="lostTab" data-bs-toggle="pill" href="lost_item_form.php">
                            <i data-feather="folder-minus"></i>
                            <script>feather.replace();</script>
                            <span class="ml-2">แจ้งทรัพย์สินหาย</span>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="foundTabTwo" data-bs-toggle="pill" href="found_items_list.php">
                              <i data-feather="align-justify"></i>
                              <script>feather.replace();</script>
                            <span class="ml-2">รายการแจ้งทรัพย์สินที่เก็บได้</span>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="lostTabTow" data-bs-toggle="pill" href="lost_items_list.php">
                            <i data-feather="align-left"></i>
                              <script>feather.replace();</script>
                            <span class="ml-2">รายการแจ้งทรัพย์สินสูญหาย</span>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="register" data-bs-toggle="pill" href="register.php">
                            <i data-feather="users"></i>
                              <script>feather.replace();</script>
                            <span class="ml-2">สมัครสมาชิก</span>
                          </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <!-- ข้อมูลที่จะแสดงในพื้นที่นี้ -->
                <h2>ยินดีต้อนรับสู่ Found&Return</h2>
                <p>จัดการการแจ้งทรัพย์สินที่หายและที่เก็บได้ง่ายดาย</p>

                <!-- เพิ่มเนื้อหาเพิ่มเติม เช่น ฟอร์ม รายการ ตาราง เป็นต้น -->
            </main>
            
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <script>
        new Chartist.Line('#traffic-chart', {
            labels: ['January', 'Februrary', 'March', 'April', 'May', 'June'],
            series: [
                [23000, 25000, 19000, 34000, 56000, 64000]
            ]
        }, {
            low: 0,
            showArea: true
>>>>>>> 098dcc7 (fourth commit)
        });
    </script>
</body>
</html>
