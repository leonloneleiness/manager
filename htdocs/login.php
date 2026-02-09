<?php
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect("dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2><i class="fas fa-sign-in-alt"></i> Đăng nhập</h2>
            
            <?php if (isset($error)) echo displayMessage('danger', $error); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username hoặc Email:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn btn-primary">Đăng nhập</button>
            </form>
            
            <p class="text-center">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
        </div>
    </div>
</body>
</html>