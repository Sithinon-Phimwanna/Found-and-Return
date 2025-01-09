<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = "localhost";
$username = "6412231023";
$password = "P@ss1023";
$database = "6412231023_Lostitem";

// สร้างการเชื่อมต่อฐานข้อมูล
$mysqli = new mysqli($host, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($mysqli->connect_errno) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $mysqli->connect_error);
}

// ตั้งค่าการเข้ารหัสตัวอักษรให้เป็น UTF-8
if (!$mysqli->set_charset("utf8")) {
    die("การตั้งค่าชุดอักขระ UTF-8 ล้มเหลว: " . $mysqli->error);
}

// แสดงข้อความเมื่อเชื่อมต่อสำเร็จ (สำหรับการตรวจสอบในช่วงพัฒนา)
// echo "เชื่อมต่อฐานข้อมูลสำเร็จ";
?>
