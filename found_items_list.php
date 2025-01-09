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

// ดึงค่าค้นหาจาก GET (ถ้ามี)
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// สร้างคำสั่ง SQL โดยใช้ JOIN ดึงข้อมูลจากตาราง found_items และ statuses
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

// เตรียมการ query
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die('Error preparing statement: ' . $mysqli->error);
}

$search_term = '%' . $search_query . '%';
$stmt->bind_param('ssss', $search_term, $search_term, $search_term, $search_term);

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการของที่พบ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="page-header">
        <h1>รายการของที่พบ</h1>
    </header>

    <!-- แสดงข้อความแจ้งเตือน (ถ้ามี) -->
    <?php
    if (isset($_SESSION['status_update_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['status_update_message'] . '</div>';
        unset($_SESSION['status_update_message']);
    }
    ?>

    <!-- ฟอร์มค้นหา -->
    <section class="search-section">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="ค้นหา..." class="search-input">
            <button type="submit" class="search-button">ค้นหา</button>
        </form>
    </section>

    <!-- แสดงข้อมูลในตาราง -->
    <section class="table-section">
        <table class="found-items-table">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อผู้แจ้ง</th>
                    <th>ช่องทางการติดต่อ</th>
                    <th>ทรัพย์สิน</th>
                    <th>รายละเอียด</th>
                    <th>สถานที่เก็บได้</th>
                    <th>วันที่เก็บได้</th>
                    <th>ภาพทรัพย์สิน</th>
                    <th>สถานะ</th>
                    <th>อัปเดตสถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['found_id'] ?></td>
                        <td><?= htmlspecialchars($row['finder_name']) ?></td>
                        <td><?= htmlspecialchars($row['finder_contact']) ?></td>
                        <td><?= htmlspecialchars($row['found_type']) ?></td>
                        <td><?= htmlspecialchars($row['found_description']) ?></td>
                        <td><?= htmlspecialchars($row['found_location']) ?></td>
                        <td><?= date('d/m/Y H:m:s', strtotime($row['found_date'])) ?></td>
                        <td class="image-cell">
                            <?php 
                            $images = explode(',', $row['found_image']);
                            foreach ($images as $image):
                                if (!empty($image)):
                            ?>
                                <img src="found_images/<?= htmlspecialchars($image) ?>" alt="ภาพของที่พบ" class="item-image">
                            <?php 
                                endif;
                            endforeach;
                            ?>
                        </td>

                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form method="POST" action="update_status_found.php" class="status-form">
                                <input type="hidden" name="found_id" value="<?= $row['found_id'] ?>">
                                <select name="status_id" class="status-select">
                                    <?php
                                    $status_query = "SELECT * FROM statuses";
                                    $status_result = $mysqli->query($status_query);
                                    while ($status = $status_result->fetch_assoc()):
                                    ?>
                                        <option value="<?= $status['status_id'] ?>" <?= $row['status'] === $status['status_name'] ? 'selected' : '' ?>>
                                            <?= $status['status_name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="submit" class="update-button">อัปเดต</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebarLinks = document.querySelectorAll(".sidebar .nav-link .update-button");

            sidebarLinks.forEach(link => {
                link.addEventListener("click", function (event) {
                    event.preventDefault();
                    sidebarLinks.forEach(link => link.classList.remove("active"));
                    this.classList.add("active");

                    const url = this.getAttribute("href");

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                            return response.text();
                        })
                        .then(data => {
                            document.querySelector("main").innerHTML = data;
                        })
                        .catch(error => {
                            console.error("Error fetching content:", error);
                            document.querySelector("main").innerHTML = "<p>Error loading content. Please try again later.</p>";
                        });
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <footer class="page-footer">
        <a href="admin_index.php" class="back-button">กลับหน้าแรก</a>
    </footer>
</body>
</html>