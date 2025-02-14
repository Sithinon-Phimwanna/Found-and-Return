<?php
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// คำสั่ง SQL นับจำนวนไอเทมตาม status_id
$sql = "SELECT status_id, COUNT(*) as count FROM lost_items GROUP BY status_id";
$result = $conn->query($sql); // ใช้ query กับ mysqli

if ($result === false) {
    die("Error in SQL query: " . $conn->error);
}

$status_data = [];
while ($row = $result->fetch_assoc()) {
    $status_data[$row['status_id']] = $row['count'];
}

$conn->close(); // ปิดการเชื่อมต่อ
$data_json = json_encode($status_data); // แปลงข้อมูลเป็น JSON
?>


<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="lostItemsChart"></canvas>
    <script>
        var ctx = document.getElementById('lostItemsChart').getContext('2d');
        var data = <?php echo $data_json; ?>;

        // ตรวจสอบข้อมูลก่อนใช้
        console.log(data); // แสดงข้อมูลในคอนโซลเพื่อการดีบัก

        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['หาย', 'ได้รับคืนแล้ว', 'ค้างระบบเกิน 1 อาทิตย์'],
                datasets: [{
                    label: 'จำนวนรายการ',
                    data: [
                        data[1] || 0, // กรณีไม่พบข้อมูลให้ใช้ 0
                        data[2] || 0,
                        data[3] || 0
                    ],
                    backgroundColor: ['red', 'green', 'orange']
                }]
            }
        });
    </script>
</body>
</html>
