<?php
    session_start(); //stsrt the session
    require_once 'config.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //รับค่าจากฟอร์ม
        $usernameOremail = trim($_POST['username_or_email']);
        $password = $_POST['password'];



        //เอาค่าที่รับมาจากฟอร์ม ไปตรวจสอบว่ามีข้อมูลตรงกับใน dbหรือไม่
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute([$usernameOremail, $usernameOremail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if($user ['role'] === 'admin'){
                header("Location: admin/index.php");

            }else{
                header("Location: index.php");
            }
            exit();
        }else{
            $error = "ชื่อผู่ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }

        
    }


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body style="height:100vh; display:flex; justify-content:center; align-items:center; background:#764ba2;">

    <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 8px 20px rgba(0,0,0,0.2); width:100%; max-width:450px;">
    
        <!-- สมัครสมาชิกสำเร็จ -->
        <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
            <div class="alert alert-success text-center" style="border-radius:10px;">✅ สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ</div>
        <?php endif; ?>

        <!-- error -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center" style="border-radius:10px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- หัวข้อ -->
        <h2 class="text-center mb-3" style="color:#333;">🔐 เข้าสู่ระบบ</h2>
        <p class="text-center text-muted mb-4">กรอกชื่อผู้ใช้หรืออีเมล และรหัสผ่าน</p>

        <!-- ฟอร์ม -->
        <form method="post">
            <div class="mb-3">
                <label for="username_or_email" class="form-label">ชื่อผู้ใช้ หรืออีเมล</label>
                <input type="text" name="username_or_email" id="username_or_email" class="form-control" placeholder="กรอกชื่อผู้ใช้หรืออีเมล" required style="border-radius:10px;">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="********" required style="border-radius:10px;">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" style="border-radius:10px;">เข้าสู่ระบบ</button>
                <a href="register.php" class="btn btn-outline-secondary" style="border-radius:10px;">สมัครสมาชิก</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>