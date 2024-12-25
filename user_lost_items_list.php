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
        statuses.status_name AS status
    FROM 
        lost_items
    JOIN 
        statuses ON lost_items.status_id = statuses.status_id
    WHERE 
        lost_items.owner_name LIKE ? 
        OR lost_items.item_type LIKE ? 
        OR lost_items.lost_location LIKE ?
";

$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die('Error preparing statement: ' . $mysqli->error);
}

$search_term = '%' . $search_query . '%';
$stmt->bind_param('sss', $search_term, $search_term, $search_term);

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
                <th>ชื่อเจ้าของ</th>
                <th>ติดต่อ</th>
                <th>ประเภท</th>
                <th>รายละเอียด</th>
                <th>วันที่</th>
                <th>สถานที่</th>
                <th>ภาพของหาย</th>
                <th>ภาพคนรับ</th>
                <th>สถานะ&emsp;</th>
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
                    <td><?= $row['lost_date'] ?></td>
                    <td><?= htmlspecialchars($row['lost_location']) ?></td>
                    <td>
                        <?php if ($row['item_image']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['item_image']) ?>" alt="ภาพของหาย" style="max-width:100px;">
                        <?php else: ?>
                            ไม่มีภาพ
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['finder_image']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['finder_image']) ?>" alt="ภาพคนรับ" style="max-width:100px;">
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
