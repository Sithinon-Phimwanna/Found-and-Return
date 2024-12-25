<?php
require 'config.php';

// ดึงค่าค้นหาจาก GET (ถ้ามี)
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// สร้างคำสั่ง SQL โดยใช้ JOIN ดึงข้อมูลจากตาราง found_items และ statuses
$query = "
    SELECT 
        found_items.found_id,
        found_items.finder_name,
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
        found_items.finder_name LIKE ? 
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการของที่พบ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>รายการของที่พบ</h1>

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
                <th>ชื่อผู้พบ</th>
                <th>ประเภท</th>
                <th>สถานที่</th>
                <th>วันที่</th>
                <th>ภาพ</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['found_id'] ?></td>
                    <td><?= htmlspecialchars($row['finder_name']) ?></td>
                    <td><?= htmlspecialchars($row['found_type']) ?></td>
                    <td><?= htmlspecialchars($row['found_location']) ?></td>
                    <td><?= $row['found_date'] ?></td>
                    <td>
                        <?php if ($row['found_image']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['found_image']) ?>" alt="ภาพของที่พบ" style="max-width:100px;">
                        <?php else: ?>
                            ไม่มีภาพ
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
<div style="display: flex; justify-content: center; align-items: center; height: 10vh;">
    <a href="index.php" style="background-color: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;">
        กลับหน้าแรก
    </a>
</div>

</html>

<?php
$stmt->close();
$mysqli->close();
?>
