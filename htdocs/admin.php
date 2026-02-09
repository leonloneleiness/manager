<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect("index.php");
}

// Xử lý xóa thành viên
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$id]);
        logActivity($_SESSION['user_id'], "Xóa thành viên ID: $id", $pdo);
    }
}

// Lấy danh sách thành viên
$stmt = $pdo->query("SELECT * FROM members ORDER BY created_at DESC");
$members = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thành viên - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-users-cog"></i> Quản lý thành viên</h1>
            <nav>
                <a href="index.php">Trang chủ</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Đăng xuất</a>
            </nav>
        </header>

        <main>
            <div class="admin-header">
                <h2>Danh sách thành viên</h2>
                <p>Tổng số thành viên: <?php echo count($members); ?></p>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Vai trò</th>
                            <th>Ngày đăng ký</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo $member['id']; ?></td>
                            <td><?php echo $member['username']; ?></td>
                            <td><?php echo $member['full_name']; ?></td>
                            <td><?php echo $member['email']; ?></td>
                            <td><?php echo $member['phone'] ?: 'N/A'; ?></td>
                            <td>
                                <span class="badge <?php echo $member['role'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo $member['role'] == 'admin' ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($member['created_at'])); ?></td>
                            <td>
                                <a href="profile.php?id=<?php echo $member['id']; ?>" class="btn btn-sm btn-info">Xem</a>
                                <?php if ($member['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?php echo $member['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
        </footer>
    </div>
</body>
</html>