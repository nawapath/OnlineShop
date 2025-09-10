<?php
    require_once 'config.php';
    session_start();

    $isLoggedIN = isset($_SESSION['user_id']);

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit(); 
    }

    $product_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT p.*, c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f8f9fa; font-family:'Segoe UI',sans-serif;">

    <main class="container py-4">
        <a href="index.php" class="btn btn-outline-secondary mb-3">← กลับหน้ารายการสินค้า</a>

        <section class="card shadow-sm border-0" style="border-radius:12px;">
            <div class="row g-0">
                <!-- ส่วนแสดงภาพ (ถ้าอนาคตมีรูป) -->
                <div class="col-md-5 d-flex align-items-center justify-content-center p-3 bg-light">
                    <span class="text-muted">[ไม่มีภาพสินค้า]</span>
                </div>

                <!-- ส่วนรายละเอียดสินค้า -->
                <div class="col-md-7">
                    <div class="card-body">
                        <h3 class="card-title fw-bold text-primary"><?= htmlspecialchars($product['product_name']) ?></h3>
                        <h6 class="text-muted mb-3">หมวดหมู่: <?= htmlspecialchars($product['category_name']) ?></h6>
                        
                        <p class="card-text text-secondary"><?= nl2br(htmlspecialchars($product['description'] ?? "ไม่มีรายละเอียดสินค้า")) ?></p>
                        
                        <p class="mt-3"><strong>ราคา:</strong> <span class="text-danger h5"><?= number_format($product['price'], 2) ?> บาท</span></p>
                        <p><strong>คงเหลือ:</strong> <?= htmlspecialchars($product['stock']) ?> ชิ้น</p>

                        <?php if ($isLoggedIN): ?>
                            <form action="cart.php" method="post" class="d-flex align-items-center mt-4">
                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                <label for="quantity" class="me-2">จำนวน:</label>
                                <input type="number" name="quantity" id="quantity" value="1" 
                                        min="1" max="<?= $product['stock'] ?>" required
                                        class="form-control w-25 me-3">
                                <button type="submit" class="btn btn-success">เพิ่มในตะกร้า</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info mt-3">กรุณาเข้าสู่ระบบเพื่อสั่งซื้อสินค้า</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

</body>
</html>
