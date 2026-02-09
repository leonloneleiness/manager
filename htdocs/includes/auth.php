<?php
require_once 'config.php';
require_once 'functions.php';

// Xử lý đăng ký
if (isset($_POST['register'])) {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    
    // Kiểm tra mật khẩu
    if ($password !== $confirm_password) {
        $error = "Mật khẩu không khớp!";
    } else {
        // Kiểm tra username tồn tại
        $stmt = $pdo->prepare("SELECT id FROM members WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Username hoặc email đã tồn tại!";
        } else {
            // Hash mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Thêm thành viên mới
            $stmt = $pdo->prepare("INSERT INTO members (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password, $full_name])) {
                logActivity($pdo->lastInsertId(), "Đăng ký tài khoản mới", $pdo);
                $success = "Đăng ký thành công! Vui lòng đăng nhập.";
            } else {
                $error = "Đăng ký thất bại!";
            }
        }
    }
}

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Tìm user
    $stmt = $pdo->prepare("SELECT * FROM members WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        logActivity($user['id'], "Đăng nhập hệ thống", $pdo);
        redirect("dashboard.php");
    } else {
        $error = "Sai username/email hoặc mật khẩu!";
    }
}
?>