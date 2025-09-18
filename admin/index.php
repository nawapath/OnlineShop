<?php

    require_once '../config.php';
    require_once 'auth_admin.php';
    
    // ป้องกัน error ถ้าไม่มีค่าใน session
    $userName = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'ผู้ดูแลระบบ';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แผงควบคุมผู้ดูแลระบบ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f8f9fa; font-family:'Segoe UI',sans-serif;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Admin Panel</a>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">👤 <?= $userName ?></span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold">ระบบผู้ดูแลระบบ</h2>
            <p class="text-muted">เลือกเมนูที่ต้องการจัดการ</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <a href="products.php" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center p-4 h-100" style="border-radius:12px;">
                        <div class="h1 text-primary mb-2">📦</div>
                        <h6 class="fw-semibold">จัดการสินค้า</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="category.php" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center p-4 h-100" style="border-radius:12px;">
                        <div class="h1 text-success mb-2">🛒</div>
                        <h6 class="fw-semibold">จัดการคำสั่งซื้อ</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="users.php" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center p-4 h-100" style="border-radius:12px;">
                        <div class="h1 text-warning mb-2">👥</div>
                        <h6 class="fw-semibold">จัดการสมาชิก</h6>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="categories.php" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center p-4 h-100" style="border-radius:12px;">
                        <div class="h1 text-dark mb-2">📂</div>
                        <h6 class="fw-semibold">จัดการหมวดหมู่</h6>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center text-muted py-3 mt-5 shadow-sm">
            &copy; 2025 ระบบผู้ดูแล | พัฒนาโดย Nawapath
    </footer>

</body>
</html>
