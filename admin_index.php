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
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styleadmin.css">
    <title>Found & Return</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-4">
            <h4 class="mb-4 ">Found & Return</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link active" data-target="dashboard_content.php">หน้าหลัก</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link" data-target="lost_item_form.php">แจ้งของหาย</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link" data-target="found_item_form.php">แจ้งของที่พบ</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link" data-target="lost_items_list.php">รายการของหาย</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link" data-target="found_items_list.php">รายการของที่พบ</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link" data-target="register.php">สมัครสมาชิก</a>
                </li>
            </ul>
        </div>

        <!-- Content -->
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2></h2>
                <div>

                    <span class="me-3"><?= htmlspecialchars($_SESSION['UserAdminName']); ?></span>
                    <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
                </div>
            </div>

            <!-- Cards -->
            <div class="row" id="cards">
                <!-- เนื้อหาจะแสดงผลที่นี่ -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 สำนักวิทยบริการและเทคโนโลยีสารสนเทศ มหาวิทยาลัยราชภัฏพิบูลสงราม.
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AJAX Script -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const links = document.querySelectorAll('.nav-link');
            const cards = document.getElementById('cards');

            // เริ่มโหลดเนื้อหาจาก dashboard_content.php
            loadContent('dashboard_content.php');

            // ฟังการคลิกเมนู
            links.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const url = link.getAttribute('data-target');
                    loadContent(url);

                    // เปลี่ยนสถานะ active ของเมนู
                    links.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                });
            });

            // ฟังก์ชันสำหรับโหลดเนื้อหาจากไฟล์
            function loadContent(url) {
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        cards.innerHTML = html;
                    })
                    .catch(error => {
                        cards.innerHTML = `<div class="alert alert-danger">เกิดข้อผิดพลาด: ${error.message}</div>`;
                    });
            }
        });
    </script>
</body>
</html>
