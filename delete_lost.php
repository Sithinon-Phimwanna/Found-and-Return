<?php
// เริ่มต้น session ถ้าต้องการ
session_start();

// เชื่อมต่อฐานข้อมูล
include 'config.php'; // เปลี่ยนชื่อไฟล์ตามที่คุณตั้ง

// ตรวจสอบว่ามีข้อมูลที่ส่งมาจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    // ตรวจสอบว่า item_id ถูกต้อง
    if ($item_id > 0) {
        // ลบข้อมูลในฐานข้อมูล
        $query = "DELETE FROM lost_items WHERE item_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $item_id);

        if ($stmt->execute()) {
            // ลบสำเร็จ
            $_SESSION['message'] = "ลบข้อมูลสำเร็จ!";
        } else {
            // เกิดข้อผิดพลาด
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "ข้อมูลไม่ถูกต้อง!";
    }
} else {
    $_SESSION['error'] = "ไม่มีข้อมูลที่ส่งมา!";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$mysqli->close();

// กลับไปหน้าหลัก
header("Location: lost_items_list.php"); // เปลี่ยนไปหน้าที่คุณต้องการ
exit;
?>
