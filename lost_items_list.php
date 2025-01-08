<?php
session_start(); // เริ่มต้นเซสชัน

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // ถ้ายังไม่ได้ล็อกอินให้ไปหน้า login
    exit;
}

// ป้องกันการแคช
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0"); // ให้หมดอายุทันที
?>

<?php
require 'config.php';

// ดึงค่าค้นหาจาก GET (ถ้ามี)
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// สร้างคำสั่ง SQL โดยใช้ JOIN ดึงข้อมูลจากตาราง lost_items และ statuses
$query = "
    SELECT 
        lost_items.item_id,
        lost_items.owner_name,
        lost_items.owner_contact,
        lost_items.item_type,
        lost_items.item_description,
        lost_items.lost_date,
        lost_items.lost_location,
        lost_items.item_image,
        lost_items.finder_image,
        lost_items.deliverer,  -- เพิ่มคอลัมน์นี้
        statuses.status_name AS status
    FROM 
        lost_items
    JOIN 
        statuses ON lost_items.status_id = statuses.status_id
    WHERE 
        lost_items.owner_name LIKE ? 
        OR lost_items.item_type LIKE ? 
        OR lost_items.lost_location LIKE ? 
        OR lost_items.lost_date LIKE ?
";

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

// ตรวจสอบว่าในเซสชันมีการตั้งค่า UserAdminName หรือไม่
$current_user_name = isset($_SESSION['UserAdminName']) ? $_SESSION['UserAdminName'] : 'ไม่ทราบชื่อ';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการของที่หาย</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>รายการของที่หาย</h1>

    <!-- ฟอร์มค้นหา -->
    <form method="GET">
        <input type="text" name="search" placeholder="ค้นหา..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit">ค้นหา</button>
    </form>

    <!-- แสดงข้อมูลในตาราง -->
    <table>
        <thead>
            <tr>
                <th>รหัส</th>
                <th>ชื่อผู้แจ้ง</th>
                <th>ช่องทางการติดต่อ</th>
                <th>ทรัพย์สิน</th>
                <th>รายละเอียด</th>
                <th>วันที่แจ้ง</th>
                <th>สถานที่</th>
                <th>ภาพทรัพย์สิน</th>
                <th>ภาพผู้รับคืน</th>
                <th>สถานะ&emsp;</th>
                <th>ผู้ส่งมอบทรัพย์สิน</th> <!-- เพิ่มคอลัมน์นี้ -->
                <th>อัปเดตข้อมูล</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['item_id'] ?></td>
                    <td><?= htmlspecialchars($row['owner_name']) ?></td>
                    <td><?= htmlspecialchars($row['owner_contact']) ?></td>
                    <td><?= htmlspecialchars($row['item_type']) ?></td>
                    <td><?= htmlspecialchars($row['item_description']) ?></td>
                    <td>
                        <!-- เปลี่ยนรูปแบบวันที่เป็น วัน/เดือน/ปี -->
                        <?= date('d/m/Y H:i:s', strtotime($row['lost_date'])) ?>
                    </td>
                    <td><?= htmlspecialchars($row['lost_location']) ?></td>
                    <td>
                        <?php 
                            // แสดงภาพทรัพย์สินหายหลายภาพ
                            if ($row['item_image']) {
                                $item_images = explode(',', $row['item_image']);
                                foreach ($item_images as $image) {
                                    echo '<img src="lost_images/' . htmlspecialchars($image) . '" alt="ภาพทรัพย์สินหาย" style="max-width:100px; margin-right: 10px;">';
                                }
                            } else {
                                echo 'ไม่มีภาพ';
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                            // แสดงภาพผู้รับคืนหลายภาพ
                            if ($row['finder_image']) {
                                $finder_images = explode(',', $row['finder_image']);
                                foreach ($finder_images as $image) {
                                    echo '<img src="return_images/' . htmlspecialchars($image) . '" alt="ภาพผู้รับคืน" style="max-width:100px; margin-right: 10px;">';
                                }
                            } else {
                                echo 'ไม่มีภาพ';
                            }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['deliverer']) ?></td> <!-- แสดงชื่อผู้ส่งมอบ -->
                    <td>
                        <!-- ฟอร์มอัปเดตข้อมูล -->
                        <form method="POST" action="update_lost.php" enctype="multipart/form-data">
                            <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                            <label for="deliverer">ชื่อผู้ส่งมอบ: </label>
                            <input type="text" name="deliverer" value="<?= htmlspecialchars($current_user_name) ?>" readonly>
                            <br>
                            <select name="status_id">
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
                            <a>เลือกภาพผู้ติดต่อรับคืน </a>
                            <input type="file" name="finder_image[]" multiple>
                            <button type="submit">อัปเดต</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
<div style="display: flex; justify-content: center; align-items: center; height: 10vh;">
    <a href="admin_index.php" style="background-color: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;">
        กลับหน้าแรก
    </a>
</div>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
