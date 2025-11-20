-- =============================================
-- 1. CẤU TRÚC BẢNG (SCHEMA)
-- =============================================

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','moderator','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events table
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
  thoiLuongDuTinh INT DEFAULT NULL,
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

-- Answers table
CREATE TABLE IF NOT EXISTS answers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maCauHoi INT UNSIGNED NOT NULL,
  noiDungCauTraLoi TEXT NOT NULL,
  laDung BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  creator_id INT UNSIGNED NOT NULL,
  FOREIGN KEY (maCauHoi) REFERENCES questions(id) ON DELETE CASCADE,
  FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Responses table
CREATE TABLE IF NOT EXISTS user_responses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maCauHoi INT UNSIGNED NOT NULL,
  maNguoiDung INT UNSIGNED NOT NULL,
  maKhaoSat INT UNSIGNED NOT NULL,
  noiDungTraLoi LONGTEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (maCauHoi) REFERENCES questions(id) ON DELETE CASCADE,
  FOREIGN KEY (maNguoiDung) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (maKhaoSat) REFERENCES surveys(id) ON DELETE CASCADE,
  INDEX idx_user_survey (maNguoiDung, maKhaoSat),
  INDEX idx_question (maCauHoi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Survey Submissions table
CREATE TABLE IF NOT EXISTS survey_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maKhaoSat INT UNSIGNED NOT NULL,
  maNguoiDung INT UNSIGNED NOT NULL,
  trangThai ENUM('draft','submitted','completed') NOT NULL DEFAULT 'submitted',
  diemDat INT DEFAULT NULL,
  ghiChu TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (maKhaoSat) REFERENCES surveys(id) ON DELETE CASCADE,
  FOREIGN KEY (maNguoiDung) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_survey (maNguoiDung, maKhaoSat),
  UNIQUE KEY unique_user_survey (maNguoiDung, maKhaoSat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. DỮ LIỆU MẪU (SEED DATA)
-- =============================================

-- Users
INSERT IGNORE INTO users (id, name, email, password, role, created_at, updated_at) VALUES
  (1, 'Nguyễn Văn A', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW(), NOW()),
  (2, 'Trần Thị B', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW(), NOW());
  
-- Events
INSERT IGNORE INTO events (id, maSuKien, tenSuKien, thoiGianBatDau, thoiGianKetThuc, maNguoiTao, created_at, updated_at) VALUES
  (1, 'SK001', 'Sự kiện Khởi động Năm Mới', '2024-01-01 09:00:00', '2024-01-01 17:00:00', 1, NOW(), NOW()),
  (2, 'SK002', 'Hội thảo Sức khỏe Cộng đồng', '2024-02-15 10:00:00', '2024-02-15 16:00:00', 2, NOW(), NOW());

-- Surveys
INSERT IGNORE INTO surveys (
  id, maKhaoSat, tieuDe, moTa, loaiKhaoSat, maNguoiTao, trangThai, diemThuong, danhMuc, maSuKien, trangThaiKiemDuyet, thoiLuongDuTinh, created_at, updated_at
) VALUES
  (1, 'KS001', 'Khảo sát về thói quen đọc sách', 'Khảo sát nhằm tìm hiểu thói quen đọc sách của người Việt Nam.', 'Thói quen', 1, 'pending', 10, 1, NULL, 'approved', 15, NOW(), NOW()),
  (2, 'KS002', 'Khảo sát về sức khỏe cộng đồng', 'Khảo sát nhằm đánh giá tình trạng sức khỏe cộng đồng.', 'Sức khỏe', 2, 'draft', 15, 2, NULL, 'pending', 20, NOW(), NOW()),
  (3, 'KS003', 'Khảo sát về trang web thương mại điện tử', 'Đánh giá trải nghiệm mua sắm trực tuyến của bạn.', 'Thương mại', 1, 'pending', 12, 1, NULL, 'approved', 12, NOW(), NOW()),
  (4, 'KS004', 'Khảo sát về ứng dụng di động', 'Cho biết ý kiến của bạn về các ứng dụng di động yêu thích.', 'Công nghệ', 2, 'pending', 10, 2, NULL, 'approved', 10, NOW(), NOW()),
  (5, 'KS005', 'Khảo sát về dịch vụ khách hàng', 'Đánh giá chất lượng dịch vụ khách hàng.', 'Dịch vụ', 1, 'pending', 20, 1, NULL, 'approved', 18, NOW(), NOW()),
  (6, 'KS006', 'Khảo sát về nhu cầu giáo dục trực tuyến', 'Tìm hiểu nhu cầu về các khóa học trực tuyến.', 'Giáo dục', 2, 'pending', 15, 2, NULL, 'approved', 16, NOW(), NOW());

-- Questions
INSERT IGNORE INTO questions (id, maCauHoi, maKhaoSat, loaiCauHoi, noiDungCauHoi, batBuocTraLoi, thuTu, quick_poll, created_at, updated_at) VALUES
  (1, 'CH001', 1, 'multiple_choice', 'Bạn thường đọc sách vào thời gian nào trong ngày?', TRUE, 1, FALSE, NOW(), NOW()),
  (2, 'CH002', 1, 'single_choice', 'Bạn thích thể loại sách nào nhất?', TRUE, 2, FALSE, NOW(), NOW()),
  (3, 'CH003', 2, 'text', 'Bạn có thường xuyên kiểm tra sức khỏe không?', FALSE, 1, FALSE, NOW(), NOW()),
  (4, 'CH004', 2, 'multiple_choice', 'Bạn có thói quen tập thể dục hàng ngày không?', TRUE, 2, FALSE, NOW(), NOW()),
  (5, 'CH005', 3, 'single_choice', 'Bạn mua sắm trực tuyến bao lâu một lần?', TRUE, 1, FALSE, NOW(), NOW()),
  (6, 'CH006', 3, 'multiple_choice', 'Nhóm sản phẩm nào bạn thường mua trực tuyến?', TRUE, 2, FALSE, NOW(), NOW()),
  (7, 'CH007', 4, 'text', 'Ứng dụng di động nào bạn sử dụng nhiều nhất?', FALSE, 1, FALSE, NOW(), NOW()),
  (8, 'CH008', 4, 'single_choice', 'Bạn đánh giá như thế nào về chất lượng ứng dụng?', TRUE, 2, FALSE, NOW(), NOW()),
  (9, 'CH009', 5, 'text', 'Dịch vụ khách hàng nào cải thiện cần thiết nhất?', FALSE, 1, FALSE, NOW(), NOW()),
  (10, 'CH010', 5, 'multiple_choice', 'Thông qua kênh nào bạn muốn liên hệ hỗ trợ?', TRUE, 2, FALSE, NOW(), NOW()),
  (11, 'CH011', 6, 'single_choice', 'Bạn quan tâm đến học các khóa nào?', TRUE, 1, FALSE, NOW(), NOW()),
  (12, 'CH012', 6, 'multiple_choice', 'Hình thức học tập nào bạn ưa thích?', TRUE, 2, FALSE, NOW(), NOW());

-- Answers (Gộp dữ liệu cũ cho Q1, Q2 và dữ liệu mới cho Q4)
INSERT IGNORE INTO answers (id, maCauHoi, noiDungCauTraLoi, laDung, creator_id, created_at, updated_at) VALUES
  -- Câu hỏi 1
  (1, 1, 'Buổi sáng', TRUE, 1, NOW(), NOW()),
  (2, 1, 'Buổi chiều', FALSE, 1, NOW(), NOW()),
  (3, 1, 'Buổi tối', FALSE, 1, NOW(), NOW()),
  (4, 1, 'Trước khi ngủ', FALSE, 1, NOW(), NOW()),
  -- Câu hỏi 2
  (5, 2, 'Tiểu thuyết', FALSE, 1, NOW(), NOW()),
  (6, 2, 'Tự truyện', TRUE, 1, NOW(), NOW()),
  (7, 2, 'Sách khoa học', FALSE, 1, NOW(), NOW()),
  (8, 2, 'Sách triết học', FALSE, 1, NOW(), NOW()),
  -- Câu hỏi 4
  (9, 4, 'Có, tôi tập rất đều đặn', TRUE, 2, NOW(), NOW()),
  (10, 4, 'Thỉnh thoảng (2-3 lần/tuần)', FALSE, 2, NOW(), NOW()),
  (11, 4, 'Hiếm khi tập', FALSE, 2, NOW(), NOW()),
  (12, 4, 'Không bao giờ', FALSE, 2, NOW(), NOW()),
  -- Câu hỏi 5
  (13, 5, 'Hàng ngày', TRUE, 1, NOW(), NOW()),
  (14, 5, 'Hàng tuần', FALSE, 1, NOW(), NOW()),
  (15, 5, 'Hàng tháng', FALSE, 1, NOW(), NOW()),
  (16, 5, 'Ít hơn hàng tháng', FALSE, 1, NOW(), NOW()),
  -- Câu hỏi 6
  (17, 6, 'Quần áo và giày dép', TRUE, 1, NOW(), NOW()),
  (18, 6, 'Điện tử', TRUE, 1, NOW(), NOW()),
  (19, 6, 'Sách và tạp chí', FALSE, 1, NOW(), NOW()),
  (20, 6, 'Mỹ phẩm', FALSE, 1, NOW(), NOW()),
  -- Câu hỏi 8
  (21, 8, 'Rất tốt', TRUE, 2, NOW(), NOW()),
  (22, 8, 'Tốt', FALSE, 2, NOW(), NOW()),
  (23, 8, 'Trung bình', FALSE, 2, NOW(), NOW()),
  (24, 8, 'Tệ', FALSE, 2, NOW(), NOW()),
  -- Câu hỏi 10
  (25, 10, 'Chat/Zalo', TRUE, 1, NOW(), NOW()),
  (26, 10, 'Email', TRUE, 1, NOW(), NOW()),
  (27, 10, 'Điện thoại', FALSE, 1, NOW(), NOW()),
  (28, 10, 'Mạng xã hội', FALSE, 1, NOW(), NOW()),
  -- Câu hỏi 11
  (29, 11, 'Lập trình', TRUE, 2, NOW(), NOW()),
  (30, 11, 'Thiết kế', FALSE, 2, NOW(), NOW()),
  (31, 11, 'Tiếp thị số', FALSE, 2, NOW(), NOW()),
  (32, 11, 'Tiếng Anh', FALSE, 2, NOW(), NOW()),
  -- Câu hỏi 12
  (33, 12, 'Video học tập', TRUE, 2, NOW(), NOW()),
  (34, 12, 'Bài giảng trực tiếp', TRUE, 2, NOW(), NOW()),
  (35, 12, 'Tài liệu chữ', FALSE, 2, NOW(), NOW()),
  (36, 12, 'Dự án thực tế', FALSE, 2, NOW(), NOW());