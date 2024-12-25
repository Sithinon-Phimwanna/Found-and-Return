<?php
session_start(); // เริ่มต้นเซสชัน
session_unset(); // ลบข้อมูลทั้งหมดในเซสชัน
session_destroy(); // ทำลายเซสชันทั้งหมด

// ป้องกันการแคช
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// เปลี่ยนเส้นทางไปหน้า login.php
header('Location: index.php');
exit;
?>
