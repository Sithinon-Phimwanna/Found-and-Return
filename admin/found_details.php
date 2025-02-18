<?php
include 'config.php';

$found_id = $_GET['found_id'];

// ใช้ JOIN เพื่อดึงชื่อสถานะจากตาราง statuses ตั้งแต่แรก
$query = "SELECT fi.found_id, fi.finder_name, fi.finder_contact, fi.found_name, 
                 fi.found_description, fi.found_location, fi.found_date, fi.found_image, 
                 s.status_name 
          FROM found_items fi 
          LEFT JOIN statuses s ON fi.status_id = s.status_id 
          WHERE fi.found_id = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $found_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// แสดงข้อมูลที่ดึงมา
echo "<p><strong>รหัส:</strong> " . $row['found_id'] . "</p>";
echo "<p><strong>ชื่อผู้แจ้ง:</strong> " . htmlspecialchars($row['finder_name']) . "</p>";
echo "<p><strong>ช่องทางติดต่อ:</strong> " . htmlspecialchars($row['finder_contact']) . "</p>";
echo "<p><strong>ทรัพย์สิน:</strong> " . htmlspecialchars($row['found_name']) . "</p>";
echo "<p><strong>รายละเอียด:</strong> " . htmlspecialchars($row['found_description']) . "</p>";
echo "<p><strong>สถานที่เก็บได้:</strong> " . htmlspecialchars($row['found_location']) . "</p>";
echo "<p><strong>วันที่เก็บได้:</strong> " . date('d/m/Y H:i', strtotime($row['found_date'])) . "</p>";

// แสดงภาพ
if ($row['found_image']) {
    $images = explode(',', $row['found_image']);
    foreach ($images as $image) {
        echo '<img src="../found_images/' . htmlspecialchars(trim($image)) . '" style="max-width: 100px; margin-right: 10px;">';
    }
} else {
    echo "<p>ไม่มีภาพ</p>";
}

// แสดงสถานะ
echo "<p><strong>สถานะ:</strong> " . htmlspecialchars($row['status_name']) . "</p>";

?>
