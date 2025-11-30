CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL DEFAULT '' UNIQUE,
  phone VARCHAR(20) DEFAULT NULL,
  name VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  gender ENUM('male','female','other') NOT NULL DEFAULT 'other',
  role ENUM('admin','moderator','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maSuKien VARCHAR(20) NOT NULL UNIQUE,
  tenSuKien VARCHAR(255) NOT NULL,
  thoiGianBatDau DATETIME DEFAULT NULL,
  thoiGianKetThuc DATETIME DEFAULT NULL,
  trangThai ENUM('upcoming','ongoing','completed') NOT NULL DEFAULT 'upcoming',
  soNguoiThamGia INT UNSIGNED NOT NULL DEFAULT 0,
  soKhaoSat INT UNSIGNED NOT NULL DEFAULT 0,
  diaDiem VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  maNguoiTao INT UNSIGNED NOT NULL,
  FOREIGN KEY (maNguoiTao) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  isQuickPoll BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (maNguoiTao) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (maSuKien) REFERENCES events(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS daily_rewards (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  current_streak INT UNSIGNED NOT NULL DEFAULT 0,
  last_claimed_date DATE DEFAULT NULL,
  total_points INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_user_daily_reward (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma varchar(20) NOT NULL UNIQUE,
  hoTen VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  soDienThoai VARCHAR(20) DEFAULT NULL,
  chuDe VARCHAR(255) NOT NULL,
  tinNhan TEXT NOT NULL,
  idNguoiDung INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  phanHoi TEXT DEFAULT NULL,
  FOREIGN KEY (idNguoiDung) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS feedbacks (
  id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma varchar(20) NOT NULL UNIQUE,
  idKhaoSat INT UNSIGNED NOT NULL,
  idNguoiDung INT UNSIGNED ,
  tenNguoiDung VARCHAR(255),
  danhGia int UNSIGNED NOT NULL,
  binhLuan TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (idKhaoSat) REFERENCES surveys(id) ON DELETE CASCADE,
  FOREIGN KEY (idNguoiDung) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO users (id, code, name, avatar, email, phone, password, gender, role, created_at, updated_at) VALUES
  (1, 'US001','Nguyễn Văn AB', NULL, 'user1@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (2, 'US002' ,'Trần Thị B', NULL, 'user2@example.com', NULL, '$2y$10$92IXUNpkjO0rOO5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (3, 'US003', 'Phạm Văn C', NULL, 'admin1@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'admin', NOW(), NOW()),
  (4, 'US004', 'Phạm Minh H', NULL, 'user4@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (5, 'US005', 'Nguyễn Văn J', NULL, 'user5@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (6, 'US006', 'Đặng Thị K', NULL,'user6@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (7, 'US007', 'Bùi Văn L', NULL,'user7@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (8, 'US008', 'Vũ Thị M', NULL,'user8@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (9, 'US009', 'Hồ Thị P', NULL,'user9@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (10, 'US010', 'Lý Văn Q', NULL,'user10@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (11, 'US011', 'Mai Thị R', NULL,'user11@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (12, 'US012', 'Đỗ Văn S', NULL,'user12@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (13, 'US013', 'Trịnh Thị T', NULL,'user13@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (14, 'US014', 'Cao Văn U', NULL,'user14@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW()),
  (15, 'US015', 'Phan Thị V', NULL,'user15@example.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 'user', NOW(), NOW());

  
INSERT IGNORE INTO events (id, maSuKien, tenSuKien, thoiGianBatDau, thoiGianKetThuc, trangThai, soNguoiThamGia, soKhaoSat, diaDiem, maNguoiTao, created_at, updated_at) VALUES
  (1, 'SK001', 'Sự kiện Khởi động Năm Mới', '2024-01-01 09:00:00', '2024-01-01 17:00:00', 'completed', 150, 3, 'Hội trường A', 1, NOW(), NOW()),
  (2, 'SK002', 'Hội thảo Sức khỏe Cộng đồng', '2024-02-15 10:00:00', '2024-02-15 16:00:00', 'completed', 120, 2, 'Trung tâm Hội nghị', 2, NOW(), NOW()),
  (3, 'SK003', 'Workshop về Digital Marketing 2024', '2024-03-10 09:00:00', '2024-03-10 16:00:00', 'ongoing', 85, 1, 'Khách sạn 5 sao', 1, NOW(), NOW()),
  (4, 'SK004', 'Ngày hội Công nghệ', '2024-04-20 09:00:00', '2024-04-20 18:00:00', 'upcoming', 0, 0, 'Online', 3, NOW(), NOW());

INSERT IGNORE INTO surveys (
  id, maKhaoSat, tieuDe, moTa, loaiKhaoSat, maNguoiTao, trangThai, diemThuong, danhMuc, 
  maSuKien, trangThaiKiemDuyet, thoiLuongDuTinh, isQuickPoll, created_at, updated_at
) VALUES
  (1, 'KS001', 'Khảo sát về thói quen đọc sách', 'Khảo sát nhằm tìm hiểu thói quen đọc sách của người Việt Nam.', 'Thói quen', 1, 'pending', 10, 1, NULL, 'approved', 15, FALSE, NOW(), NOW()),
  (2, 'KS002', 'Khảo sát về sức khỏe cộng đồng', 'Khảo sát nhằm đánh giá tình trạng sức khỏe cộng đồng.', 'Sức khỏe', 2, 'draft', 15, 2, NULL, 'pending', 20, FALSE, NOW(), NOW()),
  (3, 'KS003', 'Khảo sát về trang web thương mại điện tử', 'Đánh giá trải nghiệm mua sắm trực tuyến của bạn.', 'Thương mại', 1, 'pending', 12, 1, NULL, 'approved', 12, FALSE, NOW(), NOW()),
  (4, 'KS004', 'Khảo sát về ứng dụng di động', 'Cho biết ý kiến của bạn về các ứng dụng di động yêu thích.', 'Công nghệ', 2, 'pending', 10, 2, NULL, 'approved', 10, FALSE, NOW(), NOW()),
  (5, 'KS005', 'Khảo sát về dịch vụ khách hàng', 'Đánh giá chất lượng dịch vụ khách hàng.', 'Dịch vụ', 1, 'pending', 20, 1, NULL, 'approved', 18, FALSE, NOW(), NOW()),
  (6, 'KS006', 'Khảo sát về nhu cầu giáo dục trực tuyến', 'Tìm hiểu nhu cầu về các khóa học trực tuyến.', 'Giáo dục', 2, 'pending', 15, 2, NULL, 'approved', 16, FALSE, NOW(), NOW()),
  (7,'KS007','Mức độ hài lòng tổng quan','Khảo sát nhanh về mức độ hài lòng.','QuickPoll',1,'pending',5,1,NULL,'approved',1,TRUE,NOW(),NOW()),
  (8,'KS008','Bạn hay uống cà phê không?','Khảo sát nhanh về thói quen uống cà phê.','QuickPoll',1,'pending',5,1,NULL,'approved',1,TRUE,NOW(),NOW()),
  (9,'KS009','Thời gian dùng mạng xã hội','Khảo sát thời lượng dùng MXH.','QuickPoll',2,'pending',5,1,NULL,'approved',1,TRUE,NOW(),NOW()),
  (10,'KS010','Bạn tập thể dục bao lâu?','Khảo sát về mức độ vận động.','QuickPoll',1,'pending',5,1,NULL,'approved',1,TRUE,NOW(),NOW()),
  (11,'KS011','Bạn ngủ bao nhiêu giờ?','Khảo sát thói quen ngủ.','QuickPoll',1,'pending',5,1,NULL,'approved',1,TRUE,NOW(),NOW()),
  (12,'KS012','Bạn xem phim thể loại gì?','Khảo sát thể loại phim yêu thích.','QuickPoll',1,'pending',5,1,NULL,'approved',1,TRUE,NOW(),NOW()),
  (13,'QP013','Ý tưởng Year End Party','Thu thập ý kiến nhanh cho tiệc cuối năm.','QuickPoll',1,'pending',5,3,NULL,'approved',1,TRUE,NOW(),NOW()),
  (14,'QP014','Phản hồi tính năng Dark Mode','Đánh giá nhanh giao diện tối mới cập nhật.','QuickPoll',2,'pending',5,2,NULL,'approved',1,TRUE,NOW(),NOW()),
  (15,'QP015','Bữa trưa nay ăn gì?','Bình chọn món ăn cho team building trưa nay.','QuickPoll',1,'pending',0,3,NULL,'approved',1,TRUE,NOW(),NOW()),
  (16,'QP016','Góp ý ẩn danh','Hòm thư góp ý nhanh ẩn danh hàng tuần.','QuickPoll',2,'pending',0,3,NULL,'approved',1,TRUE,NOW(),NOW()),
  (17, 'KS017', 'Khảo sát mức độ hài lòng công việc', 'Thu thập đánh giá về môi trường và mức độ hài lòng trong công việc.', 'Nhân sự', 1, 'pending', 20, 2, NULL, 'approved', 10, FALSE, NOW(), NOW()),
  (18, 'KS018', 'Khảo sát thói quen sử dụng Internet', 'Tìm hiểu thời gian và mục tiêu sử dụng Internet mỗi ngày.', 'Công nghệ', 2, 'pending', 15, 1, NULL, 'approved', 12, FALSE, NOW(), NOW()),
  (19, 'KS019', 'Khảo sát hành vi mua sắm', 'Phân tích hành vi mua sắm online và offline.', 'Thương mại', 1, 'draft', 25, 3, NULL, 'pending', 18, FALSE, NOW(), NOW()),
  (20, 'KS020', 'Khảo sát môi trường làm việc', 'Đánh giá chất lượng môi trường làm việc của nhân viên.', 'Nhân sự', 2, 'pending', 18, 2, NULL, 'approved', 15, FALSE, NOW(), NOW()),
  (21, 'KS021', 'Khảo sát mức độ hài lòng sản phẩm', 'Đánh giá trải nghiệm sử dụng sản phẩm gần đây.', 'Dịch vụ', 1, 'pending', 10, 1, NULL, 'approved', 14, FALSE, NOW(), NOW()),
  (22, 'KS022', 'Khảo sát về nhu cầu học kỹ năng mềm', 'Khảo sát nhu cầu tham gia khóa học kỹ năng mềm.', 'Giáo dục', 2, 'draft', 12, 2, NULL, 'pending', 20, FALSE, NOW(), NOW()),
  (23, 'KS023', 'Khảo sát bảo vệ môi trường', 'Tìm hiểu nhận thức cộng đồng về bảo vệ môi trường.', 'Môi trường', 1, 'pending', 20, 3, NULL, 'approved', 12, FALSE, NOW(), NOW()),
  (24, 'KS024', 'Khảo sát văn hóa doanh nghiệp', 'Đánh giá mức độ hiểu biết và hòa nhập văn hóa doanh nghiệp.', 'Nhân sự', 2, 'pending', 15, 2, NULL, 'approved', 10, FALSE, NOW(), NOW()),
  (25, 'KS025', 'Khảo sát thói quen đọc báo', 'Tìm hiểu thói quen đọc báo mạng và báo giấy.', 'Thói quen', 1, 'pending', 10, 1, NULL, 'approved', 15, FALSE, NOW(), NOW()),
  (26, 'KS026', 'Khảo sát xu hướng công nghệ 2025', 'Thu thập ý kiến về các xu hướng công nghệ trong tương lai.', 'Công nghệ', 2, 'pending', 22, 1, NULL, 'approved', 16, FALSE, NOW(), NOW());
  
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
  (12, 'CH012', 6, 'multiple_choice', 'Hình thức học tập nào bạn ưa thích?', TRUE, 2, FALSE, NOW(), NOW()),
  (13,'CH013',7,'single_choice','Bạn cảm thấy mức độ hài lòng hiện tại như thế nào?',TRUE,1,TRUE,NOW(),NOW()),
  (14,'CH014',8,'single_choice','Bạn uống cà phê bao nhiêu lần/tuần?',TRUE,1,TRUE,NOW(),NOW()),
  (15,'CH015',9,'single_choice','Bạn dùng mạng xã hội bao nhiêu giờ mỗi ngày?',TRUE,1,TRUE,NOW(),NOW()),
  (16,'CH016',10,'single_choice','Bạn tập thể dục bao lâu mỗi tuần?',TRUE,1,TRUE,NOW(),NOW()),
  (17,'CH017',11,'single_choice','Bạn ngủ trung bình bao nhiêu giờ mỗi ngày?',TRUE,1,TRUE,NOW(),NOW()),
  (18,'CH018',12,'single_choice','Bạn thích thể loại phim nào nhất?',TRUE,1,TRUE,NOW(),NOW()),
  (19, 'QPQ001', 13, 'single_choice', 'Bạn thích tổ chức Year End Party trong nhà hay ngoài trời?', TRUE, 1, TRUE, NOW(), NOW()),
  (20, 'QPQ002', 14, 'single_choice', 'Bạn đánh giá giao diện Dark Mode mới bao nhiêu điểm?', TRUE, 1, TRUE, NOW(), NOW()),
  (21, 'QPQ003', 15, 'multiple_choice', 'Trưa nay bạn muốn ăn món gì? (Chọn tối đa 2)', TRUE, 1, TRUE, NOW(), NOW()),
  (22, 'QPQ004', 16, 'text', 'Điều gì làm bạn cảm thấy không thoải mái nhất trong tuần qua tại công ty?', TRUE, 1, TRUE, NOW(), NOW());


INSERT IGNORE INTO answers (id, maCauHoi, noiDungCauTraLoi, laDung, creator_id, created_at, updated_at) VALUES
  (1, 1, 'Buổi sáng', TRUE, 1, NOW(), NOW()),
  (2, 1, 'Buổi chiều', FALSE, 1, NOW(), NOW()),
  (3, 1, 'Buổi tối', FALSE, 1, NOW(), NOW()),
  (4, 1, 'Trước khi ngủ', FALSE, 1, NOW(), NOW()),
  (5, 2, 'Tiểu thuyết', FALSE, 1, NOW(), NOW()),
  (6, 2, 'Tự truyện', TRUE, 1, NOW(), NOW()),
  (7, 2, 'Sách khoa học', FALSE, 1, NOW(), NOW()),
  (8, 2, 'Sách triết học', FALSE, 1, NOW(), NOW()),
  (9, 4, 'Có, tôi tập rất đều đặn', TRUE, 2, NOW(), NOW()),
  (10, 4, 'Thỉnh thoảng (2-3 lần/tuần)', FALSE, 2, NOW(), NOW()),
  (11, 4, 'Hiếm khi tập', FALSE, 2, NOW(), NOW()),
  (12, 4, 'Không bao giờ', FALSE, 2, NOW(), NOW()),
  (13, 5, 'Hàng ngày', TRUE, 1, NOW(), NOW()),
  (14, 5, 'Hàng tuần', FALSE, 1, NOW(), NOW()),
  (15, 5, 'Hàng tháng', FALSE, 1, NOW(), NOW()),
  (16, 5, 'Ít hơn hàng tháng', FALSE, 1, NOW(), NOW()),
  (17, 6, 'Quần áo và giày dép', TRUE, 1, NOW(), NOW()),
  (18, 6, 'Điện tử', TRUE, 1, NOW(), NOW()),
  (19, 6, 'Sách và tạp chí', FALSE, 1, NOW(), NOW()),
  (20, 6, 'Mỹ phẩm', FALSE, 1, NOW(), NOW()),
  (21, 8, 'Rất tốt', TRUE, 2, NOW(), NOW()),
  (22, 8, 'Tốt', FALSE, 2, NOW(), NOW()),
  (23, 8, 'Trung bình', FALSE, 2, NOW(), NOW()),
  (24, 8, 'Tệ', FALSE, 2, NOW(), NOW()),
  (25, 10, 'Chat/Zalo', TRUE, 1, NOW(), NOW()),
  (26, 10, 'Email', TRUE, 1, NOW(), NOW()),
  (27, 10, 'Điện thoại', FALSE, 1, NOW(), NOW()),
  (28, 10, 'Mạng xã hội', FALSE, 1, NOW(), NOW()),
  (29, 11, 'Lập trình', TRUE, 2, NOW(), NOW()),
  (30, 11, 'Thiết kế', FALSE, 2, NOW(), NOW()),
  (31, 11, 'Tiếp thị số', FALSE, 2, NOW(), NOW()),
  (32, 11, 'Tiếng Anh', FALSE, 2, NOW(), NOW()),
  (33, 12, 'Video học tập', TRUE, 2, NOW(), NOW()),
  (34, 12, 'Bài giảng trực tiếp', TRUE, 2, NOW(), NOW()),
  (35, 12, 'Tài liệu chữ', FALSE, 2, NOW(), NOW()),
  (36, 12, 'Dự án thực tế', FALSE, 2, NOW(), NOW()),
  (37,13,'Rất hài lòng',TRUE,1,NOW(),NOW()),
  (38,13,'Hài lòng',FALSE,1,NOW(),NOW()),
  (39,13,'Bình thường',FALSE,1,NOW(),NOW()),
  (40,13,'Không hài lòng',FALSE,1,NOW(),NOW()),
  (41,14,'Mỗi ngày',TRUE,1,NOW(),NOW()),
  (42,14,'2–3 lần/tuần',FALSE,1,NOW(),NOW()),
  (43,14,'1 lần/tuần',FALSE,1,NOW(),NOW()),
  (44,14,'Không uống',FALSE,1,NOW(),NOW()),
  (45,15,'Dưới 1 giờ',TRUE,1,NOW(),NOW()),
  (46,15,'1–2 giờ',FALSE,1,NOW(),NOW()),
  (47,15,'3–4 giờ',FALSE,1,NOW(),NOW()),
  (48,15,'Trên 4 giờ',FALSE,1,NOW(),NOW()),
  (49,16,'Dưới 1 giờ',TRUE,1,NOW(),NOW()),
  (50,16,'1–2 giờ',FALSE,1,NOW(),NOW()),
  (51,16,'3–5 giờ',FALSE,1,NOW(),NOW()),
  (52,16,'Hầu như không tập',FALSE,1,NOW(),NOW()),
  (53,17,'Dưới 5 giờ',TRUE,1,NOW(),NOW()),
  (54,17,'5–6 giờ',FALSE,1,NOW(),NOW()),
  (55,17,'7–8 giờ',FALSE,1,NOW(),NOW()),
  (56,17,'Trên 8 giờ',FALSE,1,NOW(),NOW()),
  (57,18,'Hành động',TRUE,1,NOW(),NOW()),
  (58,18,'Hài',FALSE,1,NOW(),NOW()),
  (59,18,'Kinh dị',FALSE,1,NOW(),NOW()),
  (60,18,'Tình cảm',FALSE,1,NOW(),NOW()),
  (61, 19, 'Trong nhà (Sang trọng, ấm cúng)', FALSE, 1, NOW(), NOW()),
  (62, 19, 'Ngoài trời (Thoáng mát, BBQ)', FALSE, 1, NOW(), NOW()),
  (63, 19, 'Resort ngoại ô', FALSE, 1, NOW(), NOW()),
  (64, 20, 'Rất đẹp (5/5)', TRUE, 2, NOW(), NOW()),
  (65, 20, 'Ổn (4/5)', FALSE, 2, NOW(), NOW()),
  (66, 20, 'Bình thường (3/5)', FALSE, 2, NOW(), NOW()),
  (67, 20, 'Chưa tốt, khó nhìn (1-2/5)', FALSE, 2, NOW(), NOW()),
  (68, 21, 'Cơm tấm', FALSE, 1, NOW(), NOW()),
  (69, 21, 'Bún bò Huế', FALSE, 1, NOW(), NOW()),
  (70, 21, 'Pizza', FALSE, 1, NOW(), NOW()),
  (71, 21, 'Gà rán', FALSE, 1, NOW(), NOW()),
  (72, 21, 'Cơm văn phòng healthy', TRUE, 1, NOW(), NOW());


-- dữ liệu cho bảng contact mesages
INSERT IGNORE INTO contact_messages (id, ma, hoTen, email, soDienThoai, chuDe, tinNhan, idNguoiDung, created_at, updated_at, phanHoi) VALUES
  (1, 'CM001', 'Lê Văn D', 'levand@example.com', '0909123456', 'Hỗ trợ khảo sát', 'Tôi không thể gửi kết quả khảo sát, báo lỗi khi submit.', 1, NOW(), NOW(), NULL),
  (2, 'CM002', 'Ngô Thị E', 'ngothe@example.com', NULL, 'Yêu cầu hợp tác', 'Công ty chúng tôi muốn hợp tác tổ chức sự kiện cùng bạn.', NULL, NOW(), NOW(), 'Cám ơn, đã nhận yêu cầu. Chúng tôi sẽ liên hệ.'),
  (3, 'CM003', 'Hoàng Văn F', 'hoangvf@example.com', '0912345678', 'Báo lỗi', 'Gặp lỗi khi xem kết quả khảo sát KS003.', 2, NOW(), NOW(), NULL),
  (4, 'CM004', 'Trương Thị G', 'truongtg@example.com', '0987654321', 'Góp ý giao diện', 'Giao diện dark mode vẫn còn một số chỗ chưa tối.', 3, NOW(), NOW(), 'Cảm ơn góp ý, chúng tôi sẽ xem xét.'),
  (5, 'CM005', 'Khách Ẩn Danh', 'anonymous@example.com', NULL, 'Góp ý chung', 'Mong có thêm tính năng xuất báo cáo PDF.', NULL, NOW(), NOW(), NULL),
  (6, 'CM006', 'Phạm Minh H', 'phamminhh@example.com', '0933111222', 'Lỗi đăng nhập', 'Tôi bị quên mật khẩu và không nhận được email khôi phục.', 4, NOW(), NOW(), NULL),
  (7, 'CM007', 'Trần Thu I', 'tranthui@example.com', NULL, 'Hỏi về giá', 'Cho tôi hỏi chi phí nâng cấp lên tài khoản Premium.', NULL, NOW(), NOW(), 'Đã gửi thông tin báo giá qua email.'),
  (8, 'CM008', 'Nguyễn Văn J', 'nguyenvanj@example.com', '0905556667', 'Lỗi hiển thị', 'Ảnh trong bài khảo sát không load được trên điện thoại.', 5, NOW(), NOW(), NULL),
  (9, 'CM009', 'Đặng Thị K', 'dangthik@example.com', '0918889999', 'Khiếu nại', 'Tôi đã thanh toán nhưng tài khoản chưa được kích hoạt.', 6, NOW(), NOW(), 'Đã kiểm tra và kích hoạt thủ công. Xin lỗi vì sự bất tiện.'),
  (10, 'CM010', 'Bùi Văn L', 'buivanl@example.com', NULL, 'Hỏi tính năng', 'Hệ thống có hỗ trợ xuất dữ liệu ra Excel không?', 7, NOW(), NOW(), NULL),
  (11, 'CM011', 'Vũ Thị M', 'vuthim@example.com', '0966777888', 'Spam khảo sát', 'Tôi nhận được quá nhiều email thông báo khảo sát mới.', 8, NOW(), NOW(), NULL),
  (12, 'CM012', 'Đỗ Văn N', 'dovann@example.com', '0944333222', 'Tuyển dụng', 'Bên mình còn tuyển vị trí Content Marketing không?', NULL, NOW(), NOW(), 'Hiện tại chưa có đợt tuyển dụng mới.'),
  (13, 'CM013', 'Hồ Thị P', 'hothip@example.com', NULL, 'Góp ý câu hỏi', 'Nên thêm tùy chọn câu hỏi dạng lưới (matrix).', 9, NOW(), NOW(), NULL),
  (14, 'CM014', 'Lý Văn Q', 'lyvanq@example.com', '0388999111', 'Lỗi kết nối', 'Trang web thường xuyên bị timeout vào buổi tối.', 10, NOW(), NOW(), 'Bộ phận kỹ thuật đang kiểm tra server.'),
  (15, 'CM015', 'Mai Thị R', 'maithir@example.com', NULL, 'Xóa tài khoản', 'Vui lòng hướng dẫn tôi cách xóa vĩnh viễn tài khoản.', 11, NOW(), NOW(), NULL);

-- dữ liệu cho bảng feedbacks
INSERT IGNORE INTO feedbacks (id,ma, idKhaoSat, idNguoiDung, tenNguoiDung , danhGia, binhLuan, created_at, updated_at) VALUES
  (1, 'FB001', 1, 1 , 'Nguyễn Văn A', 5, 'Khảo sát hữu ích, câu hỏi rõ ràng.', NOW(), NOW()),
  (2,'FB002', 2, 2,'Trần Thị B', 4, 'Nội dung tốt nhưng thời gian khảo sát hơi dài.', NOW(), NOW()),
  (3,'FB003', 3, 1, 'Nguyễn Văn',  5, 'Rất phù hợp với mục tiêu nghiên cứu.', NOW(), NOW()),
  (4, 'FB004',13, 2, 'Trần Thị B',3, 'Quick poll hơi nhiều lựa chọn không cần thiết.', NOW(), NOW()),
  (5,'FB005', 7, NULL, 'Khách Ẩn Danh', 4, 'Khảo sát nhanh, dễ trả lời.', NOW(), NOW()),
  (6, 'FB006', 3, NULL, 'Khách Ẩn Danh', 5, 'Khảo sát nhanh, thông tin rõ ràng.', NOW(), NOW()),
  (7, 'FB007', 4, NULL, 'Khách Ẩn Danh', 4, 'Câu hỏi hợp lý và dễ hiểu.', NOW(), NOW()),
  (8, 'FB008', 5, NULL, 'Khách Ẩn Danh', 5, 'Rất hữu ích, trả lời nhanh gọn.', NOW(), NOW()),
  (9, 'FB009', 2, NULL, 'Khách Ẩn Danh', 3, 'Khảo sát hơi dài một chút.', NOW(), NOW()),
  (10, 'FB010', 1, NULL, 'Khách Ẩn Danh', 4, 'Tạm ổn, thông tin đầy đủ.', NOW(), NOW()),
  (11, 'FB011', 6, NULL, 'Khách Ẩn Danh', 5, 'Tôi thấy khảo sát chất lượng.', NOW(), NOW()),
  (12, 'FB012', 2, NULL, 'Khách Ẩn Danh', 3, 'Cũng được, câu hỏi bình thường.', NOW(), NOW()),
  (13, 'FB013', 8, NULL, 'Khách Ẩn Danh', 4, 'Khảo sát gọn và dễ thao tác.', NOW(), NOW()),
  (14, 'FB014', 9, NULL, 'Khách Ẩn Danh', 5, 'Rất tốt, tôi thích khảo sát dạng này.', NOW(), NOW()),
  (15, 'FB015', 7, NULL, 'Khách Ẩn Danh', 4, 'Nội dung hữu ích, nên giữ.', NOW(), NOW());
