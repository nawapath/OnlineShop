<?php
session_start();
require 'config.php';
// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) { // ตรวจสอบว่ามี session ของ user
    header("Location: login.php"); // ถ้าไม่มีให้ไปหน้า login
    exit;
}
$user_id = $_SESSION['user_id']; // กำหนด user_id

// ดึงรายการสินค้าในตะกร้า
$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, cart.product_id,
                                products.product_name, products.price
                        FROM cart
                        JOIN products ON cart.product_id = products.product_id
                        WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณราคารวม
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price']; // quantity * price
}

// เมื่อผู้ใช้กดยืนยันคำสั่งซื้อ (method POST)
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address     = trim($_POST['address']);     // ช่องกรอกที่อยู่
    $city        = trim($_POST['city']);        // ช่องกรอกจังหวัด
    $postal_code = trim($_POST['postal_code']); // ช่องกรอกรหัสไปรษณีย์
    $phone       = trim($_POST['phone']);       // ช่องกรอกเบอร์โทรศัพท์

    // ตรวจสอบการกรอกข้อมูล
    if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        $errors[] = "กรุณากรอกข้อมูลให้ครบถ้วน"; // ข้อความแจ้งเตือนกรอกไม่ครบ
    }

    if (empty($errors)) {
        // เริ่ม transaction
        $conn->beginTransaction();
        try {
            // บันทึกข้อมูลการสั่งซื้อ
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total]);
            $order_id = $conn->lastInsertId();

            // บันทึกข้อมูลสินค้าใน order_items
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            }

            // บันทึกข้อมูลการจัดส่ง
            $stmt = $conn->prepare("INSERT INTO shipping (order_id, address, city, postal_code, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $address, $city, $postal_code, $phone]);

            // ลบตะกร้าสินค้า
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            // ยืนยันการบันทึก
            $conn->commit();
            header("Location: orders.php?success=1"); // หน้าสำหรับแสดงผลคำสั่งซื้อ
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สั่งซื้อสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>ยืนยันการสั่งซื้อ</h2>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- แสดงรายการสินค้าในตะกร้า -->
    <h5>รายการสินค้าในตะกร้า</h5>
    <ul class="list-group mb-4">
        <?php foreach ($items as $item): ?>
        <li class="list-group-item">
            <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> =
            <?= number_format($item['price'] * $item['quantity'], 2) ?> บาท
        </li>
        <?php endforeach; ?>
        <li class="list-group-item text-end">
            <strong>รวมทั้งหมด : <?= number_format($total, 2) ?> บาท</strong>
        </li>
    </ul>

    <!-- ฟอร์มกรอกข้อมูลการจัดส่ง -->
    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label for="address" class="form-label">ที่อยู่</label>
            <input type="text" name="address" id="address" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="city" class="form-label">จังหวัด</label>
            <input type="text" name="city" id="city" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label for="postal_code" class="form-label">รหัสไปรษณีย์</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">ยืนยันการสั่งซื้อ</button>
            <a href="cart.php" class="btn btn-secondary">← กลับตะกร้า</a>
        </div>
    </form>
</body>
</html>
