<?php
    require '../config.php'; // ✅ เชื่อมต่อฐานข้อมูลผ่านไฟล์ config ที่มี PDO
    require 'auth_admin.php';

    // ✅ ตรวจสอบสิทธิ์ (Admin Guard)
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit;
    }

    // ✅ เพิ่มหมวดหมู่
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
        $category_name = trim($_POST['category_name']);
        if ($category_name) {
            $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->execute([$category_name]);
            $_SESSION['success'] = "เพิ่มหมวดหมู่เรียบร้อยแล้ว";
            header("Location: category.php");
            exit;

        } 
    }

    // ✅ ลบหมวดหมู่
    // if (isset($_GET['delete'])) {
    //     $category_id = $_GET['delete'];
    //     $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    //     $stmt->execute([$category_id]);
    //     $_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
    //     header("Location: category.php");
    //     exit;
    // }


    // ✅ ลบหมวดหมู่
    if (isset($_GET['delete'])) {
        $category_id = $_GET['delete'];

        // ตรวจสอบว่าหมวดหมู่ยังถูกใช้อยู่หรือไม่ (มีสินค้าที่ใช้หมวดหมู่นี้หรือเปล่า)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $productCount = $stmt->fetchColumn();

        if ($productCount > 0) {
            // ถ้ามีสินค้าใช้อยู่
            $_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีสินค้าอยู่ในหมวดหมู่";
        } else {
            // ถ้าไม่มีสินค้า -> ลบได้
            $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
            $stmt->execute([$category_id]);
            $_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
        }

        header("Location: category.php");
        exit;
    }


    // ✅ แก้ไขหมวดหมู่
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = trim($_POST['new_name']);
        if ($category_name) {
            $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
            $stmt->execute([$category_name, $category_id]);
            $_SESSION['success'] = "แก้ไขหมวดหมู่เรียบร้อยแล้ว";
            header("Location: category.php");
            exit;
        } else {
            $_SESSION['error'] = "กรุณากรอกชื่อใหม่";
            header("Location: category.php");
            exit;
        }
    }

    // ✅ ดึงหมวดหมู่ทั้งหมด
    $categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการหมวดหมู่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container mt-4" style="background:#f8f9fa;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#2c3e50;">
            <i class="bi bi-tags-fill me-2" style="color:#6c63ff;"></i> จัดการหมวดหมู่สินค้า
        </h2>
        <a href="index.php" class="btn btn-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-left-circle me-1"></i> กลับหน้าผู้ดูแล
        </a>
    </div>

    <!-- แสดงข้อความ error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger shadow-sm" style="border-radius:8px;"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- แสดงข้อความ success -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success shadow-sm" style="border-radius:8px;"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- ฟอร์มเพิ่มหมวดหมู่ -->
    <form method="post" class="row g-3 mb-4 shadow-sm p-3" style="border-radius:8px;background:#fff;">
        <div class="col-md-6">
            <input type="text" name="category_name" class="form-control" placeholder="ชื่อหมวดหมู่ใหม่" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add_category" class="btn btn-primary w-100" style="border-radius:8px;">
                <i class="bi bi-plus-circle me-1"></i> เพิ่มหมวดหมู่
            </button>
        </div>
    </form>

    <h5 class="mb-3" style="color:#2c3e50;">รายการหมวดหมู่</h5>
    <div class="table-responsive shadow-sm" style="border-radius:8px; overflow:hidden;">
        <table class="table table-bordered table-hover m-0 text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ชื่อหมวดหมู่</th>
                    <th>แก้ไขชื่อ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['category_name']) ?></td>
                    <td>
                        <form method="post" class="d-flex justify-content-center align-items-center">
                            <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                            <input type="text" name="new_name" class="form-control me-2" placeholder="ชื่อใหม่" required>
                            <button type="submit" name="update_category" class="btn btn-warning btn-sm" style="border-radius:8px;">
                                <i class="bi bi-pencil-square"></i> แก้ไข
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="category.php?delete=<?= $cat['category_id'] ?>" 
                            class="btn btn-danger btn-sm" style="border-radius:8px;"
                            onclick="return confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่?')">
                            <i class="bi bi-trash"></i> ลบ
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer class="text-center mt-4 mb-2" style="color:gray; font-size:14px;">
        © 2025 ระบบผู้ดูแล | Nawapath
    </footer>

</body>
</html>
