-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS member_management;
USE member_management;

-- Bảng thành viên
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng logs
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity VARCHAR(255),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Thêm tài khoản admin mặc định (mật khẩu: admin123)
INSERT INTO members (username, email, password, full_name, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$YourHashedPasswordHere', 'Quản trị viên', 'admin');

-- Thêm một số thành viên mẫu
INSERT INTO members (username, email, password, full_name, phone, address) 
VALUES 
('user1', 'user1@example.com', '$2y$10$YourHashedPasswordHere', 'admin', '0912345678', 'Hà Nội'),
('user2', 'user2@example.com', '$2y$10$YourHashedPasswordHere', 'user', '0923456789', 'TP HCM');