<?php
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// เปิดการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
require 'config.php';

// ดึงข้อมูลผู้ใช้งานจากฐานข้อมูล
try {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT UserAdminName, email FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("d", $user_id); // ใช้ integer สำหรับ user_id
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("ไม่พบข้อมูลผู้ใช้ในระบบ");
    }

    $name = $user['UserAdminName'] ?? '';
    $contact = $user['email'] ?? '';
} catch (Exception $e) {
    die("ข้อผิดพลาด: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งทรัพย์สินหาย</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/stylefrom.css">
</head>
<body>
    <div class="container mt-3">
        <div class="row justify-content-start ms-10">
            <div class="col-10 col-md-10 col-lg-10">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title text-center">แจ้งทรัพย์สินหาย</h5>
                        <form action="submit_lost_item.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-1">
                                <label for="owner_name" class="form-label">ชื่อผู้แจ้ง:</label>
                                <input type="text" class="form-control-sm" id="owner_name" name="owner_name" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                            <div class="mb-1">
                                <label for="owner_contact" class="form-label">ช่องทางการติดต่อ:</label>
                                <input type="text" class="form-control-sm" id="owner_contact" name="owner_contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                            </div>
                            <div class="mb-1">
                                <label for="item_type" class="form-label">ทรัพย์สิน:</label>
                                <input type="text" class="form-control-sm" id="item_type" name="item_type" required>
                            </div>
                            <div class="mb-1">
                                <label for="item_description" class="form-label">รายละเอียด:</label>
                                <textarea class="form-control-sm" id="item_description" name="item_description" rows="3" required></textarea>
                            </div>
                            <div class="mb-1">
                                <label for="lost_date" class="form-label">วันที่หาย:</label>
                                <input type="date" class="form-control-sm" id="lost_date" name="lost_date" required>
                            </div>
                            <div class="mb-1">
                                <label for="lost_location" class="form-label">สถานที่หาย:</label>
                                <input type="text" class="form-control-sm" id="lost_location" name="lost_location" required>
                            </div>
                            <div class="mb-1">
                                <label for="item_image" class="form-label">อัพโหลดภาพทรัพย์สิน (ถ้ามี):</label>
                                <input class="form-control-sm" type="file" id="item_image" name="item_image[]" multiple>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">ส่งข้อมูล</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
