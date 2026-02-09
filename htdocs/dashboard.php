<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect("login.php");
}

// Lấy thông tin user
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <nav>
                <a href="index.php">Trang chủ</a>
                <a href="profile.php">Hồ sơ</a>
                <?php if (isAdmin()): ?>
                    <a href="admin.php">Quản lý</a>
                <?php endif; ?>
                <a href="logout.php">Đăng xuất</a>
            </nav>
        </header>

        <main>
            <div class="welcome-section">
                <h2>Xin chào, <?php echo $user['full_name']; ?>!</h2>
                <p>Chào mừng bạn đến với hệ thống quản lý thành viên</p>
            </div>

            <div class="dashboard-cards">
                <div class="card">
                    <h3><i class="fas fa-user"></i> Thông tin cá nhân</h3>
                    <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                    <p><strong>Vai trò:</strong> <?php echo $user['role'] == 'admin' ? 'Quản trị viên' : 'Thành viên'; ?></p>
                    <a href="profile.php" class="btn btn-primary">Cập nhật thông tin</a>
                </div>

                <div class="card">
                    <h3><i class="fas fa-calendar"></i> Thống kê</h3>
                    <p><strong>Ngày tham gia:</strong> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Cập nhật lần cuối:</strong> <?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?></p>
                </div>

                <?php if (isAdmin()): ?>
                <div class="card">
                    <h3><i class="fas fa-users-cog"></i> Quản trị</h3>
                    <p>Truy cập trang quản lý để xem và quản lý tất cả thành viên</p>
                    <a href="admin.php" class="btn btn-primary">Truy cập quản lý</a>
                </div>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
        </footer>
    </div>
</body>
</html>