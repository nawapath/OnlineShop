<?php
    require_once 'config.php';

    $error = []; //Array to hold error messages

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //รับค่าจากฟอร์ม
        $username = trim($_POST['username']);
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        //ตรวจสอบว่กรอกข้อมูลมาครบหรือไม่ (empty)
        if(empty($username)|| empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
            $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
            //ตรวจสอบว่าอีเมลถูกต้องหรือไม่ (filter-ver)
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            $error[] = "กรุณากรอกอีเมลให้ถูกต้อง";
            }//ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
        elseif($password !== $confirm_password) {
            $error[] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            //ตรวจสอบว่ามีชื่อผู้ใช้หรืออีเมลนี้ในฐานข้อมูลหรือไม่
            $sql="SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = $conn -> prepare($sql);
            $stmt -> execute([$username, $email]);

        if($stmt->rowCount() > 0) {
            $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานไปแล้ว";
        } 

}

        if(empty($error)){ //ถ้าไม่ข้อผิดพลาดใดๆ
            //นำข้อมูลบันทึกลงฐานข้อมูล
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users(username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'member')";
                $stmt = $conn-> prepare($sql);
                $stmt -> execute([$username, $fullname, $email, $hashedPassword]) ;


            // ถ้าบันทึกสำเร็จ ให้เปลีี่ยนเส้นทางไปที่หน้า login
                header("Location: login.php?registeer=success");
                exit(); //หยุดการทำงานของสคิปต์หลังจากเปลี่ยนเส้นทาง
        
        }
    }






?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>
<body>


    <div class="container mt-5 d-flex justify-content-center align-items-center ">
        <div class="card w-50 p-5 bg-light shadow ">
            <h2 class="text-center text-primary">สมัครสมาชิก</h2>

            <?php if (!empty($error)): // ถ้ามีข้อผิดพลาด ให้แสดงข้อความ ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($error as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                            <!-- ใช ้ htmlspecialchars เพื่อป้องกัน XSS  -->
                            <!-- < ? = คือ short echo tag ?> -->
                            <!-- ถ้าเขียนเต็ม จะได ้แบบด้านนล่าง -->
                            <?php // echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>



            <form action="" method="post">
                <div>
                    <label for="username" class="from-label ">ชื่อผู้ใช้</label>
                    <input type="text" name="username"  id="username" class="form-control " placeholder="ชื่อผู้ใช้" 
                    value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" require>
                </div>
                <div>
                    <label for="fullname" class="from-label">ชื่อ-สกุล</label>
                    <input type="text" name="fullname" value="" id="fullname" class="form-control " placeholder="ชื่อ-สกุล"
                    value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>" require>
                </div>
                <div>
                    <label for="email" class="from-label">อีเมล</label>
                    <input type="email" name="email" id="email" class="form-control " placeholder="อีเมล" 
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" require>
                </div>
                <div>
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control " placeholder="รหัสผ่าน" require>
                </div>
                <div>
                    <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control " placeholder="ยืนยันรหัสผ่าน" >
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
                    <a href="login.php" class="btn btn-link">เข้าสู่ระบบ</a>
                </div>
            </form>
        </div>
    </div>


    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>