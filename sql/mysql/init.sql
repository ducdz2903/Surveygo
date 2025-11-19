-- User
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','moderator','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; -- cấu hình cách lưu trữ và xử lí dữ liệu trong cơ sở dữ liệu
-- Event table
CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maSuKien VARCHAR(10) NOT NULL UNIQUE,
  tenSuKien VARCHAR(255) NOT NULL,
  thoiGianBatDau DATETIME DEFAULT NULL,
  thoiGianKetThuc DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  maNguoiTao INT UNSIGNED NOT NULL,
  FOREIGN KEY (maNguoiTao) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Surveys table
CREATE TABLE IF NOT EXISTS surveys (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maKhaoSat VARCHAR(10) NOT NULL UNIQUE,
  tieuDe VARCHAR(255) NOT NULL,
  moTa TEXT DEFAULT NULL,
  loaiKhaoSat VARCHAR(50) DEFAULT NULL,
  maNguoiTao INT UNSIGNED NOT NULL,
  trangThai ENUM('draft','pending') NOT NULL DEFAULT 'draft',
  diemThuong INT DEFAULT 10,
  danhMuc INT DEFAULT NULL,
  maSuKien INT UNSIGNED DEFAULT NULL,
  trangThaiKiemDuyet ENUM('pending','approved','rejected') DEFAULT 'pending',
  thoiGianBatDau DATETIME DEFAULT NULL,
  thoiGianKetThuc DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (maNguoiTao) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (maSuKien) REFERENCES events(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Questions table
CREATE TABLE IF NOT EXISTS questions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maCauHoi VARCHAR(10) NOT NULL UNIQUE,
  maKhaoSat INT UNSIGNED NOT NULL,
  loaiCauHoi VARCHAR(50) NOT NULL,
  noiDungCauHoi TEXT NOT NULL,
  batBuocTraLoi BOOLEAN NOT NULL DEFAULT FALSE,
  thuTu INT DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  quick_poll BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (maKhaoSat) REFERENCES surveys(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data (password = "password")
INSERT IGNORE INTO users (id, name, email, password, role, created_at, updated_at) VALUES
  (1, 'Nguyễn Văn A', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW(), NOW()),
  (2, 'Trần Thị B', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW(), NOW());
  
-- Seed data for events (fields match schema)
INSERT IGNORE INTO events (id, maSuKien, tenSuKien, thoiGianBatDau, thoiGianKetThuc, maNguoiTao, created_at, updated_at) VALUES
  (1, 'SK001', 'Sự kiện Khởi động Năm Mới', '2024-01-01 09:00:00', '2024-01-01 17:00:00', 1, NOW(), NOW()),
  (2, 'SK002', 'Hội thảo Sức khỏe Cộng đồng', '2024-02-15 10:00:00', '2024-02-15 16:00:00', 2, NOW(), NOW());

-- Seed data for surveys (fields match schema)
INSERT IGNORE INTO surveys (
  id, maKhaoSat, tieuDe, moTa, loaiKhaoSat, maNguoiTao, trangThai, diemThuong, danhMuc, maSuKien, trangThaiKiemDuyet, thoiGianBatDau, thoiGianKetThuc, created_at, updated_at
) VALUES
  (1, 'KS001', 'Khảo sát về thói quen đọc sách', 'Khảo sát nhằm tìm hiểu thói quen đọc sách của người Việt Nam.', 'Thói quen', 1, 'pending', 10, 1, NULL, 'approved', '2024-01-01 00:00:00', '2024-01-31 23:59:59', NOW(), NOW()),
  (2, 'KS002', 'Khảo sát về sức khỏe cộng đồng', 'Khảo sát nhằm đánh giá tình trạng sức khỏe cộng đồng.', 'Sức khỏe', 2, 'draft', 15, 2, NULL, 'pending', '2024-02-01 00:00:00', '2024-02-28 23:59:59', NOW(), NOW());

-- Seed data for questions (fields match schema)
INSERT IGNORE INTO questions (id, maCauHoi, maKhaoSat, loaiCauHoi, noiDungCauHoi, batBuocTraLoi, thuTu, quick_poll, created_at, updated_at) VALUES
  (1, 'CH001', 1, 'multiple_choice', 'Bạn thường đọc sách vào thời gian nào trong ngày?', TRUE, 1, FALSE, NOW(), NOW()),
  (2, 'CH002', 1, 'single_choice', 'Bạn thích thể loại sách nào nhất?', TRUE, 2, FALSE, NOW(), NOW()),
  (3, 'CH003', 2, 'text', 'Bạn có thường xuyên kiểm tra sức khỏe không?', FALSE, 1, FALSE, NOW(), NOW()),
  (4, 'CH004', 2, 'multiple_choice', 'Bạn có thói quen tập thể dục hàng ngày không?', TRUE, 2, FALSE, NOW(), NOW());
-- End of file
