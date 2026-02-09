<?php
// profile.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect("login.php");
}

// Xác định xem đang xem hồ sơ của ai
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
$is_own_profile = ($profile_id == $_SESSION['user_id']);
$can_edit = ($is_own_profile || isAdmin());

// Lấy thông tin thành viên
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$profile_id]);
$member = $stmt->fetch();

if (!$member) {
    die("Thành viên không tồn tại!");
}

// Xử lý cập nhật thông tin
if (isset($_POST['update_profile']) && $can_edit) {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $email = sanitize($_POST['email']);
    
    // Kiểm tra email trùng (trừ chính user đó)
    $stmt = $pdo->prepare("SELECT id FROM members WHERE email = ? AND id != ?");
    $stmt->execute([$email, $profile_id]);
    
    if ($stmt->rowCount() > 0) {
        $error = "Email đã được sử dụng bởi thành viên khác!";
    } else {
        // Cập nhật thông tin
        $stmt = $pdo->prepare("UPDATE members SET full_name = ?, phone = ?, address = ?, email = ?, updated_at = NOW() WHERE id = ?");
        if ($stmt->execute([$full_name, $phone, $address, $email, $profile_id])) {
            // Cập nhật session nếu là profile của chính mình
            if ($is_own_profile) {
                $_SESSION['full_name'] = $full_name;
            }
            
            logActivity($_SESSION['user_id'], "Cập nhật thông tin thành viên ID: $profile_id", $pdo);
            $success = "Cập nhật thông tin thành công!";
            
            // Lấy lại thông tin mới
            $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
            $stmt->execute([$profile_id]);
            $member = $stmt->fetch();
        } else {
            $error = "Cập nhật thông tin thất bại!";
        }
    }
}

// Xử lý đổi mật khẩu
if (isset($_POST['change_password']) && $is_own_profile) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Kiểm tra mật khẩu hiện tại
    if (!password_verify($current_password, $member['password'])) {
        $password_error = "Mật khẩu hiện tại không đúng!";
    } elseif ($new_password !== $confirm_password) {
        $password_error = "Mật khẩu mới không khớp!";
    } elseif (strlen($new_password) < 6) {
        $password_error = "Mật khẩu phải có ít nhất 6 ký tự!";
    } else {
        // Cập nhật mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE members SET password = ?, updated_at = NOW() WHERE id = ?");
        
        if ($stmt->execute([$hashed_password, $profile_id])) {
            logActivity($_SESSION['user_id'], "Đổi mật khẩu", $pdo);
            $password_success = "Đổi mật khẩu thành công!";
        } else {
            $password_error = "Đổi mật khẩu thất bại!";
        }
    }
}

// Lấy lịch sử hoạt động (cho admin hoặc chính mình)
$activity_logs = [];
if ($can_edit) {
    $stmt = $pdo->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$profile_id]);
    $activity_logs = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-user-circle"></i> Hồ sơ thành viên</h1>
            <nav>
                <a href="index.php">Trang chủ</a>
                <a href="dashboard.php">Dashboard</a>
                <?php if (isAdmin()): ?>
                    <a href="admin.php">Quản lý</a>
                <?php endif; ?>
                <a href="logout.php">Đăng xuất</a>
            </nav>
        </header>

        <main>
            <?php if (isset($error)) echo displayMessage('danger', $error); ?>
            <?php if (isset($success)) echo displayMessage('success', $success); ?>
            
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle fa-5x"></i>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($member['full_name']); ?></h2>
                        <p class="profile-username">@<?php echo htmlspecialchars($member['username']); ?></p>
                        <span class="badge <?php echo $member['role'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                            <?php echo $member['role'] == 'admin' ? 'Quản trị viên' : 'Thành viên'; ?>
                        </span>
                    </div>
                </div>

                <div class="profile-tabs">
                    <button class="tab-button active" data-tab="info">Thông tin cá nhân</button>
                    <?php if ($is_own_profile): ?>
                        <button class="tab-button" data-tab="password">Đổi mật khẩu</button>
                    <?php endif; ?>
                    <?php if ($can_edit): ?>
                        <button class="tab-button" data-tab="activity">Lịch sử hoạt động</button>
                    <?php endif; ?>
                </div>

                <!-- Tab 1: Thông tin cá nhân -->
                <div class="tab-content active" id="info-tab">
                    <div class="profile-card">
                        <h3><i class="fas fa-info-circle"></i> Thông tin chi tiết</h3>
                        
                        <?php if ($can_edit): ?>
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" id="username" value="<?php echo htmlspecialchars($member['username']); ?>" disabled>
                                    <small>Username không thể thay đổi</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="full_name">Họ và tên:</label>
                                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($member['full_name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Điện thoại:</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Địa chỉ:</label>
                                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($member['address']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Ngày tham gia:</label>
                                <input type="text" value="<?php echo date('d/m/Y H:i', strtotime($member['created_at'])); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Cập nhật lần cuối:</label>
                                <input type="text" value="<?php echo date('d/m/Y H:i', strtotime($member['updated_at'])); ?>" disabled>
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </form>
                        <?php else: ?>
                        <!-- Chế độ xem (không có quyền chỉnh sửa) -->
                        <div class="profile-view">
                            <div class="info-item">
                                <strong>Email:</strong>
                                <span><?php echo htmlspecialchars($member['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Họ và tên:</strong>
                                <span><?php echo htmlspecialchars($member['full_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Điện thoại:</strong>
                                <span><?php echo htmlspecialchars($member['phone'] ?: 'Chưa cập nhật'); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Địa chỉ:</strong>
                                <span><?php echo htmlspecialchars($member['address'] ?: 'Chưa cập nhật'); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Ngày tham gia:</strong>
                                <span><?php echo date('d/m/Y H:i', strtotime($member['created_at'])); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab 2: Đổi mật khẩu -->
                <?php if ($is_own_profile): ?>
                <div class="tab-content" id="password-tab">
                    <div class="profile-card">
                        <h3><i class="fas fa-key"></i> Đổi mật khẩu</h3>
                        
                        <?php if (isset($password_error)) echo displayMessage('danger', $password_error); ?>
                        <?php if (isset($password_success)) echo displayMessage('success', $password_success); ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="current_password">Mật khẩu hiện tại:</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="new_password">Mật khẩu mới:</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                    <small>Ít nhất 6 ký tự</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="fas fa-exchange-alt"></i> Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tab 3: Lịch sử hoạt động -->
                <?php if ($can_edit): ?>
                <div class="tab-content" id="activity-tab">
                    <div class="profile-card">
                        <h3><i class="fas fa-history"></i> Lịch sử hoạt động gần đây</h3>
                        
                        <?php if (empty($activity_logs)): ?>
                            <p class="text-center">Chưa có hoạt động nào được ghi nhận.</p>
                        <?php else: ?>
                            <div class="activity-list">
                                <?php foreach ($activity_logs as $log): ?>
                                <div class="activity-item">
                                    <div class="activity-content">
                                        <p><?php echo htmlspecialchars($log['activity']); ?></p>
                                        <small>
                                            <i class="fas fa-clock"></i> 
                                            <?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?>
                                            <?php if ($log['ip_address']): ?>
                                                • <i class="fas fa-globe"></i> IP: <?php echo $log['ip_address']; ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
        </footer>
    </div>

    <script>
        // Xử lý chuyển tab
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Ẩn tất cả nội dung tab
                    tabContents.forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Xóa active tất cả button
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Hiển thị tab được chọn
                    document.getElementById(tabId + '-tab').classList.add('active');
                    this.classList.add('active');
                });
            });
            
            // Xử lý hiển thị thông báo mật khẩu
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('password_changed')) {
                const passwordTab = document.querySelector('[data-tab="password"]');
                if (passwordTab) {
                    passwordTab.click();
                }
            }
        });
    </script>
</body>
</html>