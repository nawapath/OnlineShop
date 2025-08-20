<?php
    session_start();

    // ตรวจสอบว่ามีการ login แล้วหรือยัง
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body style="height:100vh; display:flex; justify-content:center; align-items:center; background:linear-gradient(135deg,#4facfe,#00f2fe);">

    <div style="background:white; padding:40px; border-radius:20px; box-shadow:0 8px 20px rgba(0,0,0,0.2); text-align:center; max-width:400px; width:100%;">
        <h1 style="color:#333; margin-bottom:20px;">👋 ยินดีต้อนรับ</h1>
    <p style="font-size:18px; margin-bottom:30px;">
        ผู้ใช้: <b><?= htmlspecialchars($_SESSION['username']) ?></b> <br>
        (<?= htmlspecialchars($_SESSION['role']) ?>)
    </p>
    <a href="logout.php" class="btn btn-danger btn-lg w-100">ออกจากระบบ</a>
    </div>

</body>
</html>