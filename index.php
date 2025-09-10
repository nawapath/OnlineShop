<?php
    session_start();
    //เชื่อมต่อฐานข้อมูล
    require_once 'config.php';

    // ตรวจสอบว่ามีการ login แล้วหรือยัง
    $isLoggedIN = isset($_SESSION['user_id']);

    $stmt = $conn->query("SELECT p.*, c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body style="background:#f8f9fa; font-family:'Segoe UI',sans-serif;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">My Shop</a>
            <div class="d-flex">
                <?php if ($isLoggedIN): ?>
                    <span class="navbar-text text-white me-3">
                        👋 ยินดีต้อนรับ <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)
                    </span>
                    <a href="profile.php" class="btn btn-sm btn-info me-2">ข้อมูลส่วนตัว</a>
                    <a href="cart.php" class="btn btn-sm btn-warning me-2">ตะกร้าสินค้า</a>
                    <a href="logout.php" class="btn btn-sm btn-danger">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-success me-2">เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-sm btn-primary">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="container text-center my-5">
        <h1 class="fw-bold" style="color:#333;">🛍️ รายการสินค้า</h1>
        <p class="text-muted">เลือกซื้อสินค้าที่คุณชื่นชอบได้เลย</p>
    </header>
    
    <!-- รายการสินค้า -->
    <div class="container">
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm border-0" style="border-radius:12px;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-semibold"><?= htmlspecialchars($product['product_name']) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name']) ?></h6>
                            <p class="card-text text-truncate" style="max-height:60px;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            <p class="mt-auto"><strong>ราคา:</strong> <?= number_format($product['price'], 2) ?> บาท</p>
                            
                            <div class="d-flex justify-content-between">
                                <?php if ($isLoggedIN): ?>
                                    <form action="cart.php" method="post" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-sm btn-success">เพิ่มตะกร้า</button>
                                    </form>
                                <?php else: ?>
                                    <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                                <?php endif; ?>
                                <a href="product_detail.php?id=<?= $product['product_id'] ?>" 
                                    class="btn btn-sm btn-outline-primary">ดูรายละเอียด</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (count($products) === 0): ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center shadow-sm">ยังไม่มีสินค้าในระบบ</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center text-muted py-3 mt-5 shadow-sm">
        &copy; <?= date("Y") ?> My Shop | Nawapath
    </footer>

</body>
</html>
