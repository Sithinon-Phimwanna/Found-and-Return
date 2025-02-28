<?php
include 'config.php'; // รวมไฟล์ config.php ที่เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($mysqli->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $mysqli->connect_error);
}
$found_id = isset($_GET['found_id']) ? $_GET['found_id'] : null;
if (!$found_id) {
    die("ไม่พบรหัสข้อมูล (found_id) ใน URL");
}

// ใช้ $mysqli แทน $conn ในการ query
$query = "SELECT fi.found_id, fi.finder_name, fi.finder_contact, fi.found_name, 
                 fi.found_description, fi.found_location, fi.found_date, fi.found_image, 
                 fi.consignee, s.status_name 
          FROM found_items fi 
          LEFT JOIN statuses s ON fi.status_id = s.status_id 
          WHERE fi.found_id = ?";

$stmt = $mysqli->prepare($query); // ใช้ $mysqli แทน
if (!$stmt) {
    die("Query Error: " . $mysqli->error);
}
$stmt->bind_param("i", $found_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// ตรวจสอบข้อมูล
if (!$row) {
    die("ไม่พบข้อมูลของ found_id: " . htmlspecialchars($found_id));
}

// ดึงชื่อผู้รับแจ้งจาก users
$consignee_id = $row['consignee'];
$UserAdminName = "ไม่พบข้อมูล";

$userQuery = $mysqli->prepare("SELECT UserAdminName FROM users WHERE id = ?");
if ($userQuery) {
    $userQuery->bind_param("i", $consignee_id);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userRow = $userResult->fetch_assoc();
    
    if ($userRow) {
        $UserAdminName = htmlspecialchars($userRow['UserAdminName']);
    }
}

// แสดงข้อมูล
echo "<p><strong>รหัส:</strong> " . htmlspecialchars($row['found_id']) . "</p>";
echo "<p><strong>ชื่อผู้แจ้ง:</strong> " . htmlspecialchars($row['finder_name']) . "</p>";
echo "<p><strong>ช่องทางติดต่อ:</strong> " . htmlspecialchars($row['finder_contact']) . "</p>";
echo "<p><strong>ทรัพย์สิน:</strong> " . htmlspecialchars($row['found_name']) . "</p>";
echo "<p><strong>รายละเอียด:</strong> " . htmlspecialchars($row['found_description']) . "</p>";
echo "<p><strong>สถานที่เก็บได้:</strong> " . htmlspecialchars($row['found_location']) . "</p>";
echo "<p><strong>ผู้รับแจ้ง:</strong> " . $UserAdminName . "</p>";
echo "<p><strong>วันที่เก็บได้:</strong> " . date('d/m/Y H:i น.', strtotime($row['found_date'])) . "</p>";

if (!empty($row['found_image'])) {
    $images = explode(',', $row['found_image']);
    foreach ($images as $image) {
        echo '<img src="../found_images/' . htmlspecialchars(trim($image)) . '" style="max-width: 100px; margin-right: 10px;">';
    }
} else {
    echo "<p>ไม่มีภาพ</p>";
}

echo "<p><strong>สถานะ:</strong> " . htmlspecialchars($row['status_name']) . "</p>";

// ปิดการเชื่อมต่อ
$stmt->close();
$userQuery->close();
$mysqli->close();
?>
