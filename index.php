<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เก็บได้..ให้คืน</title>
    <link rel="stylesheet" href="css/stylesindex.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="p-2 mb-2 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <a class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
                <img src="logo/Found & Return.png" alt="Logo" width="80" height="80" class="me-2">
            </a>
            <h3 class="me-auto">เก็บได้..ให้คืน</h3>
            <div class="d-flex align-items-center ms-auto">
                <!-- Search bar -->
                <form method="GET" class="search-bar d-flex align-items-center me-3">
                    <input type="text" name="search" placeholder="ค้นหา..." value="<?= htmlspecialchars($search_query) ?>" class="form-control me-2" style="flex-grow: 1;">
                    <button type="submit" class="btn btn-primary" style="background-color: #28a745; color: white; border: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-1" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                        ค้นหา
                    </button>
                </form>

                <!-- Profile icon and dropdown -->
                <div class="dropdown text-end ms-3">
                    <a class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                        </svg>
                    </a>
                    <ul class="dropdown-menu text-small">
                        <li><a class="dropdown-item" href="login.php">Log in admin</a></li>
                    </ul>
                </div>
        </div>
    </div>
</header>


<div class="container">
<?php
        require 'config.php';

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if (!$mysqli) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // ดึงค่าค้นหาจาก GET (ถ้ามี)
        $search_query = isset($_GET['search']) ? $_GET['search'] : '';

        // สร้างคำสั่ง SQL โดยใช้ JOIN
        $query = "
            SELECT 
                found_items.found_id,
                found_items.found_description,
                found_items.found_type,
                found_items.found_date,
                found_items.found_location,
                found_items.found_image,
                statuses.status_name AS status
            FROM 
                found_items
            JOIN 
                statuses ON found_items.status_id = statuses.status_id
            WHERE 
                found_items.found_date LIKE ? 
                OR found_items.found_type LIKE ? 
                OR found_items.found_location LIKE ?
        ";

        // เตรียมการ query
        $stmt = $mysqli->prepare($query);
        if (!$stmt) {
            die('Error preparing statement: ' . $mysqli->error);
        }

        $search_term = '%' . $search_query . '%';
        $stmt->bind_param('sss', $search_term, $search_term, $search_term);

        // Execute คำสั่ง SQL
        if (!$stmt->execute()) {
            die('Error executing statement: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        ?>
    <h1 class="text-center">รายการของที่พบ</h1> 

    <!-- Section สำหรับแสดงข้อมูล -->
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- รูปภาพ -->
                        <?php if ($row['found_image']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['found_image']) ?>" class="card-img-top" alt="ภาพของที่พบ" style="height: 100px; wide: 100px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top" style="height: 100px; wide: 100px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #aaa;">
                                ไม่มีภาพ
                            </div>
                        <?php endif; ?>

                        <!-- ข้อมูลของรายการ -->
                        <h5 class="card-title mt-3"><?= htmlspecialchars($row['found_type']) ?></h5>
                        <p class="card-text">
                            <strong>รายละเอียด:</strong> <?= htmlspecialchars($row['found_description']) ?><br>
                            <strong>สถานที่:</strong> <?= htmlspecialchars($row['found_location']) ?><br>
                            <strong>วันที่:</strong> <?= htmlspecialchars($row['found_date']) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-success">กลับหน้าแรก</a>
    </div>
</div>

    <footer class="text-center mt-4">
        &copy; 2024 สำนักวิทยบริการและเทคโนโลยีสารสนเทศ มหาวิทยาลัยราชภัฏพิบูลสงราม.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
