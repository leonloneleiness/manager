<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-users"></i> <?php echo SITE_NAME; ?></h1>
            <nav>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="profile.php">Hồ sơ</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Quản lý</a>
                    <?php endif; ?>
                    <a href="logout.php">Đăng xuất</a>
                <?php else: ?>
                    <a href="index.php">Trang chủ</a>
                    <a href="login.php">Đăng nhập</a>
                    <a href="register.php">Đăng ký</a>
                <?php endif; ?>
            </nav>
        </header>

        <main>
            <div class="hero">
                <h2>Chào mừng đến với hệ thống quản lý thành viên</h2>
                <p>Quản lý thông tin thành viên một cách dễ dàng và hiệu quả</p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-primary">Đăng nhập</a>
                        <a href="register.php" class="btn btn-secondary">Đăng ký</a>
                    </div>
                <?php else: ?>
                    <div class="welcome">
                        <p>Xin chào, <strong><?php echo $_SESSION['full_name']; ?></strong>!</p>
                        <a href="dashboard.php" class="btn btn-primary">Truy cập Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="features">
                <div class="feature">
                    <i class="fas fa-user-plus"></i>
                    <h3>Đăng ký dễ dàng</h3>
                    <p>Tạo tài khoản nhanh chóng với thông tin cơ bản</p>
                </div>
                <div class="feature">
                    <i class="fas fa-cogs"></i>
                    <h3>Quản lý thông tin</h3>
                    <p>Cập nhật và quản lý thông tin cá nhân</p>
                </div>
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Bảo mật</h3>
                    <p>Hệ thống bảo mật an toàn cho thông tin cá nhân</p>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>