<?php
include 'config.php';

$item_id = $_GET['item_id'];

// ใช้ JOIN เพื่อดึงชื่อสถานะจากตาราง statuses
$query = "SELECT li.item_id, li.owner_name, li.owner_contact, li.item_type, 
                 li.item_description, li.lost_location, li.lost_date, li.item_image, 
                 li.finder_image,li.status_id, s.status_name 
          FROM lost_items li 
          LEFT JOIN statuses s ON li.status_id = s.status_id 
          WHERE li.item_id = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// แสดงข้อมูลที่ดึงมา
echo "<p><strong>รหัส:</strong> " . $row['item_id'] . "</p>";
echo "<p><strong>ชื่อผู้แจ้ง:</strong> " . htmlspecialchars($row['owner_name']) . "</p>";
echo "<p><strong>ช่องทางติดต่อ:</strong> " . htmlspecialchars($row['owner_contact']) . "</p>";
echo "<p><strong>ประเภททรัพย์สิน:</strong> " . htmlspecialchars($row['item_type']) . "</p>";
echo "<p><strong>รายละเอียด:</strong> " . htmlspecialchars($row['item_description']) . "</p>";
echo "<p><strong>สถานที่:</strong> " . htmlspecialchars($row['lost_location']) . "</p>";
echo "<p><strong>วันที่แจ้ง:</strong> " . date('d/m/Y H:i', strtotime($row['lost_date'])) . "</p>";

// แสดงสถานะ
echo "<p><strong>สถานะ:</strong> " . htmlspecialchars($row['status_name']) . "</p>";

// แสดงภาพทรัพย์สิน
echo "<p><strong>ภาพทรัพย์สิน:</strong></p>";
if ($row['item_image']) {
    $images = explode(',', $row['item_image']);
    foreach ($images as $image) {
        echo '<img src="../lost_images/' . htmlspecialchars(trim($image)) . '" style="max-width: 100px; margin-right: 10px;">';
    }
} else {
    echo "<p>ไม่มีภาพ</p>";
}

// แสดงภาพผู้รับคืน
echo "<p><strong>ภาพผู้รับคืน:</strong></p>";
if ($row['finder_image']) {
    $finder_images = explode(',', $row['finder_image']);
    foreach ($finder_images as $image) {
        $image_path = "../return_images/" . htmlspecialchars(trim($image));
        
        // ตรวจสอบเส้นทางไฟล์ภาพ
        if (file_exists($image_path)) {
            echo '<img src="' . $image_path . '" style="max-width: 100px; margin-right: 10px;">';
        } else {
            echo "<p>ไม่พบไฟล์ภาพ: " . htmlspecialchars($image) . "</p>";
        }
    }
} else {
    echo "<p>ไม่มีภาพ</p>";
}


?>
