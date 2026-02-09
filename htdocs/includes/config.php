<?php
// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'member_management');

// Cấu hình website
define('SITE_NAME', 'Quản Lý Thành Viên');
define('SITE_URL', 'http://localhost/member-management/');

// Kết nối database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Bắt đầu session
session_start();
?>