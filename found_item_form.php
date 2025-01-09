<?php
session_start();

// เปิดการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// นำเข้าไฟล์ config.php
require 'config.php';

// ตรวจสอบว่าได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Location: login.php');
    exit;
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
try {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT UserAdminName, email FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query); // ใช้ $mysqli
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
    <title>แจ้งเก็บทรัพย์สินได้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/stylefrom.css">
</head>
<body>
    <div class="container mt-3">
        <div class="row justify-content-start ms-10">
            <div class="col-10 col-md-10 col-lg-10">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title text-center">แจ้งเก็บทรัพย์สินได้</h5>
                        <form action="submit_found_item.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-1">
                                <label for="finder_name" class="form-label">ชื่อผู้แจ้ง:</label>
                                <input type="text" class="form-control-sm" id="finder_name" name="finder_name" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                            <div class="mb-1">
                                <label for="finder_contact" class="form-label">ช่องทางการติดต่อ:</label>
                                <input type="text" class="form-control-sm" id="finder_contact" name="finder_contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                            </div>
                            <div class="mb-1">
                                <label for="found_type" class="form-label">ทรัพย์สิน:</label>
                                <input type="text" class="form-control-sm" id="found_type" name="found_type" required>
                            </div>
                            <div class="mb-1">
                                <label for="found_description" class="form-label">รายละเอียด:</label>
                                <textarea class="form-control-sm" id="found_description" name="found_description" rows="3" required></textarea>
                            </div>
                            <div class="mb-1">
                                <label for="found_date" class="form-label">วันที่เก็บได้:</label>
                                <input type="date" class="form-control-sm" id="found_date" name="found_date" required>
                            </div>
                            <div class="mb-1">
                                <label for="found_location" class="form-label">สถานที่เก็บได้:</label>
                                <input type="text" class="form-control-sm" id="found_location" name="found_location" required>
                            </div>
                            <div class="mb-1">
                                <label for="found_image" class="form-label">อัพโหลดภาพทรัพย์สินที่เก็บได้:</label>
                                <input class="form-control-sm" type="file" id="found_image" name="found_image[]" multiple>
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

    <!-- รวมสคริปต์ Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
