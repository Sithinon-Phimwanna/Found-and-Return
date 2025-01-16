<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
require 'config.php';

// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// คำสั่ง SQL
$sql = "SELECT lost_location, COUNT(*) AS item_count FROM lost_items WHERE lost_location IS NOT NULL GROUP BY lost_location ORDER BY item_count DESC";
$result = $mysqli->query($sql);

// ตรวจสอบว่า SQL query สำเร็จหรือไม่
if ($result === false) {
    die("Error in SQL query: " . $mysqli->error);
}



// สร้างอาร์เรย์เพื่อเก็บข้อมูล
$locations = [];
$item_counts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // แปลงค่าจากเลขชั้นเป็นชื่อชั้น
        switch ($row['lost_location']) {
            case '1':
                $location_name = 'ชั้น 1 โซนสำนักงาน 24 ชม.';
                break;
            case '2':
                $location_name = 'ชั้น 1 โซนธนาคาร';
                break;
            case '3':
                $location_name = 'ชั้น 2 โซน A';
                break;
            case '4':
                $location_name = 'ชั้น 2 โซน B';
                break;
            case '5':
                $location_name = 'ชั้น 3 โซน A';
                break;
            case '6':
                $location_name = 'ชั้น 3 โซน B';
                break;
            case '7':
                $location_name = 'ชั้น 4 โซน A';
                break;
            case '8':
                $location_name = 'ชั้น 4 โซน B';
                break;
            default:
                $location_name = $row['lost_location']; // หากไม่ตรงกับเงื่อนไข
        }

        $locations[] = $location_name;
        $item_counts[] = $row['item_count'];
    }
} else {
    echo "No results lost.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Example</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>จำนวนทรัพย์สินที่หายตามชั้น</h1>
    <!-- แสดงกราฟ -->
    <canvas id="my_lost_Chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 653px;" width="653" height="250" class="chartjs-render-monitor"></canvas>

    <script>
    // รับข้อมูลจาก PHP และแปลงเป็น JavaScript
    var locations = <?php echo json_encode($locations); ?>;
    var itemCounts = <?php echo json_encode($item_counts); ?>;

    // กำหนดสีสำหรับกราฟ
    var colors = [
    'rgba(255, 0, 0, 0.8)', // สี 1 - แดงสดใสจาง
    'rgba(255, 123, 0, 0.8)', // สี 2 - ส้มสดใสจาง
    'rgba(255, 251, 0, 0.8)', // สี 3 - เหลืองสดใสจาง
    'rgba(72, 253, 0, 0.8)', // สี 4 - เขียวสดใสจาง
    'rgba(38, 240, 48, 0.8)', // สี 5 - เขียวเข้มสดใสจาง
    'rgba(0, 255, 255, 0.8)', // สี 6 - ฟ้าอ่อนสดใสจาง
    'rgba(0, 59, 253, 0.8)', // สี 7 - ฟ้าเข้มสดใสจาง
    'rgba(153, 102, 255, 0.8)', // สี 8 - ม่วงสดใสจาง
    'rgba(255, 99, 132, 0.8)'  // สี 9 - ชมพูสดใสจาง
];




    // สร้างกราฟด้วย Chart.js
    var ctx = document.getElementById('my_lost_Chart').getContext('2d');
    var my_lost_Chart = new Chart(ctx, {
        type: 'pie', // ชนิดของกราฟ
        data: {
            labels: locations, // เลเบลของกราฟ (สถานที่พบ)
            datasets: [{
                label: 'จำนวนทรัพย์สินที่พบ',
                data: itemCounts, // ข้อมูลจำนวนของที่พบ
                backgroundColor: colors.slice(0, locations.length), // สีสำหรับแต่ละแท่ง
                borderColor: 'white',  // สีขอบ
                borderWidth: 2
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

</body>
</html>
