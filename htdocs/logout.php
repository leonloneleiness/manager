<?php
// logout.php
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Xóa cookie nếu có
setcookie(session_name(), '', time() - 3600, '/');

// Chuyển hướng về trang chủ
header('Location: index.php');
exit();
?>