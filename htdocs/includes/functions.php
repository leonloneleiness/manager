<?php
// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm kiểm tra quyền admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Hàm chuyển hướng
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Hàm hiển thị thông báo
function displayMessage($type, $message) {
    return '<div class="alert alert-' . $type . '">' . $message . '</div>';
}

// Hàm bảo vệ input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Hàm ghi log
function logActivity($user_id, $activity, $pdo) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql = "INSERT INTO activity_logs (user_id, activity, ip_address) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $activity, $ip]);
}
?>