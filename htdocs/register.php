<?php
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h2>
            
            <?php if (isset($error)) echo displayMessage('danger', $error); ?>
            <?php if (isset($success)) echo displayMessage('success', $success); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Họ và tên:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Xác nhận mật khẩu:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="register" class="btn btn-primary">Đăng ký</button>
            </form>
            
            <p class="text-center">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
        </div>
    </div>
</body>
</html>