<?php
session_start(); // เริ่มเซสชัน

// ป้องกันการเข้าถึงโดยไม่ได้ล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // ถ้ายังไม่ได้ล็อกอินให้ไปหน้า login
    exit;
}

// ป้องกันการแคชของเบราว์เซอร์
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ป้องกันการใช้งานเซสชันซ้ำ
session_regenerate_id(true);
?>

<div class="col-12">
    <div class="card card-custom">
        <div class="card-body">
            <h5 class="card-title">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['UserAdminName']); ?>!</h5>
            <p class="card-text">คุณได้เข้าสู่ระบบเรียบร้อยแล้ว</p>
        </div>
    </div>
</div>

