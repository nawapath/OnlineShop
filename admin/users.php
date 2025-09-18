<?php
    
    require_once '../config.php';
    require_once 'auth_admin.php';

    // ลบสมาชิก
    if (isset($_GET['delete'])) {
        $user_id = $_GET['delete'];

        // ป้องกันลบตัวเอง
        if ($user_id != $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
            $stmt->execute([$user_id]);
        }
        header("Location: users.php");
        exit;
    }

    // ดึงข้อมูลสมาชิก
    $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสมาชิก</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CDN SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body style="background:#f8f9fa; font-family:'Segoe UI',sans-serif;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Admin Panel</a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">← กลับหน้าหลัก</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0">👥 จัดการสมาชิก</h2>
            <span class="badge bg-primary">จำนวนสมาชิก: <?= count($users) ?></span>
        </div>

        <?php if (count($users) === 0): ?>
            <div class="alert alert-warning text-center shadow-sm">ยังไม่มีสมาชิกในระบบ</div>
        <?php else: ?>
        <div class="card shadow-sm border-0" style="border-radius:12px;">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ชื่อผู้ใช้</th> 
                                <th>ชื่อ - นามสกุล</th>
                                <th>อีเมล</th>
                                <th>วันที่สมัคร</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['created_at'] ?></td>
                            <td class="text-center">
                                <a href="edit_user.php?id=<?= $user['user_id'] ?>" 
                                    class="btn btn-sm btn-warning">✏️ แก้ไข</a>
                                <!-- <a href="users.php?delete=<?= $user['user_id'] ?>" 
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?')">🗑️ ลบ</a> -->
                                    <form action="delUser_Sweet.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="u_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="button" class="delete-button btn btn-danger btn-sm " data-user-id="<?php echo
                                        $user['user_id']; ?>">ลบ</button>
                                    </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center text-muted py-3 mt-5 shadow-sm">
        &copy; 2025 ระบบผู้ดูแล | Nawapath
    </footer>


    <script>
        // ฟังกช์ นั ส ำหรับแสดงกลอ่ งยนื ยัน SweetAlert2
        function showDeleteConfirmation(userId) {
                Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'คุณจะไม่สามารถเรียกคืนข้อมูลกลับได ้!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                // หำกผใู้ชย้นื ยัน ใหส้ ง่ คำ่ ฟอรม์ ไปยัง delete.php เพื่อลบข ้อมูล
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'delUser_Sweet.php';
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'u_id';
                    input.value = userId;
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        // แนบตัวตรวจจับเหตุกำรณ์คลิกกับองค์ปุ ่่มลบทั ่ ้งหมดที่มีคลำส delete-button
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach((button) => {
            button.addEventListener('click', () => {
            const userId = button.getAttribute('data-user-id');
            showDeleteConfirmation(userId);
            });
        });
    </script>




</body>
</html>
