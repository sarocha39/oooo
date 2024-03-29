<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Port Check</title>
<style> 
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f0f0;
    }
    .container {
        max-width: 600px;
        margin: 50px auto;
        background-color: #fff;
        padding: 60px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
        text-align: center;
    }
    form {
        margin-bottom: 20px;
    }
    label {
        display: block;
        margin-bottom: 5px;
    }
    input[type="text"],
    select,
    input[type="submit"] {
        width: calc(100% - 22px); /* Subtracting 22px for the border width */
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box; /* Include padding and border in the width calculation */
    }
    input[type="submit"] {
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background-color: #0056b3;
    }
    p {
        padding: 10px;
        border-radius: 5px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Check port </h1>
    <form action="" method="post">
        <label for="ip_address">IP Address</label>
        <input type="text" id="ip_address" name="ip_address" required><br><br>
        <label for="port_number">Port Number</label>
        <select id="port_number" name="port_number" required>
            <option value="22">22</option>
            <option value="80">80</option>
            <option value="8080">8080</option>
            <!-- เพิ่ม Port ตามต้องการ -->
        </select><br><br>
        <input type="submit" value="Check">
    </form>
    <?php
    // ตรวจสอบว่าวิธีการร้องขอเป็น POST หรือไม่
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ดึง IP address และเลขพอร์ตจากข้อมูลที่ส่งผ่าน POST
        $ipAddress = $_POST['ip_address'];
        $portNumber = $_POST['port_number'];

        // เชื่อมต่อ SSH โดยใช้ฟังก์ชัน ssh2_connect
        $connection = ssh2_connect($ipAddress, $portNumber);
        // ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
        if (!$connection) {
            echo "<p style='color: red;'>เชื่อมต่อไม่สำเร็จกับโฮสต์ระยะไกล</p>";
        } else {
            echo "<p style='color: green;'>เชื่อมต่อสำเร็จกับโฮสต์ระยะไกล</p>";
        }

        // การรับรองตัวตนกับโฮสต์ระยะไกลโดยใช้ฟังก์ชัน ssh2_auth_password
        if (!ssh2_auth_password($connection, 'root', 'root')) {
            // หากการรับรองตัวตนล้มเหลว การดำเนินการสคริปต์จะสิ้นสุดด้วยข้อความข้อผิดพลาด
            die("<p style='color: red;'>การรับรองตัวตนล้มเหลวกับโฮสต์ระยะไกล</p>");
        }

        // กำลังสร้างคำสั่งเพื่อตรวจสอบว่าพอร์ตเปิดหรือปิด
        $command = "echo >/dev/tcp/$ipAddress/$portNumber && echo \"พอร์ต $portNumber เปิด\" || echo \"พอร์ต $portNumber ปิด\"";
        // การดำเนินการคำสั่งบนโฮสต์ระยะไกลโดยใช้ฟังก์ชัน ssh2_exec
        $stream = ssh2_exec($connection, $command);

        // การตั้งค่าบล็อกสตรีมเป็น true
        stream_set_blocking($stream, true);
        // การอ่านผลลัพธ์ของคำสั่งจากสตรีม
        $output = stream_get_contents($stream);

        // ปิดการเชื่อมต่อ SSH
        ssh2_disconnect($connection);

        // แสดงผลลัพธ์ของคำสั่ง
        echo "<p>$output</p>";
    }
    ?>
</div>
</body>
</html>
