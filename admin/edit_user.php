<?php
    
    require '../config.php'; // TODO-1: เชื่อมต่อฐานข้อมูลด้วย PDO
    require 'auth_admin.php'; // TODO-2: การ์ดสิทธิ์ (Admin Guard)


    // TODO-3: ตรวจว่ามีพารามิเตอร์ id จริงไหม (ผ่าน GET)
    if (!isset($_GET['id'])) {
        header("Location: users.php");
        exit;
    }

    // TODO-4: ดึงค่า id และ "แคสต์เป็น int" เพื่อความปลอดภัย
    $user_id = (int)$_GET['id'];

    // TODO-5: ดึงข้อมูลสมาชิกที่จะถูกแก้ไข (เฉพาะ role = member)
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // TODO-6: ถ้าไม่พบข้อมูล -> แสดงข้อความและ exit
    if (!$user) {
        echo "<h3>ไม่พบสมาชิก</h3>";
        exit;
    }

    // ========== เมื่อผู้ใช้กด Submit ฟอร์ม ==========
    $error = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // TODO-7: รับค่า POST + trim
        $username   = trim($_POST['username']);
        $full_name  = trim($_POST['full_name']);
        $email      = trim($_POST['email']);

        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        // TODO-8: ตรวจความครบถ้วน และตรวจรูปแบบ email
        if ($username === '' || $email === '') {
            $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "รูปแบบอีเมลไม่ถูกต้อง";
        }

        // TODO-9: ตรวจสอบซ้ำ (username/email ซ้ำกับคนอื่นที่ไม่ใช่ตัวเอง)
        if (!$error) {
            $chk = $conn->prepare("SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
            $chk->execute([$username, $email, $user_id]);

            if ($chk->fetch()) {
                $error = "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้วในระบบ";
            }
        }

    // ตรวจรหัสผ่าน (กรณีต้องการเปลี่ยน)
    // เงื่อนไข: อนุญาตให้ปล่อยว่างได้ (คือไม่เปลี่ยนรหัสผ่าน)
    $updatePassword = false;
    $hashed = null;

    if (!$error && ($password !== '' || $confirm !== '')) {
        // TODO: ตรวจเงื่อนไข เช่น ยาว >= 6 และรหัสผ่านตรงกัน
        if (strlen($password) < 6) {
            $error = "รหัสผ่านต้องยาวอย่างน้อย 6 อักขระ";
        } elseif ($password !== $confirm) {
            $error = "รหัสผ่านใหม่กับยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            // แฮชรหัสผ่าน
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updatePassword = true;
        }
    }

    // สร้าง SQL UPDATE แบบยืดหยุ่น (ถ้าไม่เปลี่ยนรหัสผ่านจะไม่แตะ field password)
    if (!$error) {
        if ($updatePassword) {
            // อัปเดตรวมรหัสผ่าน
            $sql = "UPDATE users
                    SET username = ?, full_name = ?, email = ?, password = ?
                    WHERE user_id = ?";
            $args = [$username, $full_name, $email, $hashed, $user_id];
        } else {
            // อัปเดตเฉพาะข้อมูลทั่วไป
            $sql = "UPDATE users
                    SET username = ?, full_name = ?, email = ?
                    WHERE user_id = ?";
            $args = [$username, $full_name, $email, $user_id];
        }

        $upd = $conn->prepare($sql);
        $upd->execute($args);

        header("Location: users.php");
        exit;
    }


        // TODO-10: ถ้าไม่ซ้ำ -> ทำ UPDATE
        // if (!$error) {
        //     $upd = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ? WHERE user_id = ?");
        //     $upd->execute([$username, $full_name, $email, $user_id]);

        //     // TODO-11: redirect กลับหน้ารายชื่อสมาชิกหลังอัปเดตสำเร็จ
        //     header("Location: users.php");
        //     exit;
        // }

        // OPTIONAL: อัปเดตค่า $user เพื่อสะท้อนค่าที่ช่องฟอร์ม (หากมี error)
        $user['username']  = $username;
        $user['full_name'] = $full_name;
        $user['email']     = $email;
    }
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f8f9fa;font-family:'Segoe UI',sans-serif;">

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand fw-bold" href="#">Admin Panel</a>
            <div>
                <a href="users.php" class="btn btn-outline-light btn-sm me-2">← กลับหน้าสมาชิก</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;border-radius:12px;">
            <div class="card-body p-4">
                <h2 class="fw-bold mb-4 text-center text-dark">✏️ แก้ไขข้อมูลสมาชิก</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <!-- ชื่อผู้ใช้ -->
                    <div class="col-md-6">
                        <label class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" name="username" class="form-control" required
                                value="<?= htmlspecialchars($user['username']) ?>">
                    </div>

                    <!-- ชื่อ - นามสกุล -->
                    <div class="col-md-6">
                        <label class="form-label">ชื่อ - นามสกุล</label>
                        <input type="text" name="full_name" class="form-control"
                                value="<?= htmlspecialchars($user['full_name']) ?>">
                    </div>

                    <!-- อีเมล -->
                    <div class="col-md-6">
                        <label class="form-label">อีเมล</label>
                        <input type="email" name="email" class="form-control" required
                                value="<?= htmlspecialchars($user['email']) ?>">
                    </div>

                    <!-- รหัสผ่านใหม่ -->
                    <div class="col-md-6">
                        <label class="form-label">
                            รหัสผ่านใหม่
                            <small class="text-muted">(ถ้าไม่ต้องการเปลี่ยน ให้เว้นว่าง)</small>
                        </label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <!-- ยืนยันรหัสผ่านใหม่ -->
                    <div class="col-md-6">
                        <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>

                    <!-- ปุ่มบันทึก -->
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">💾 บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-white text-center text-muted py-3 mt-5 shadow-sm">
        &copy; 2025 ระบบผู้ดูแล | Nawapath
    </footer>

</body>
</html>
