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
  trangThai ENUM('draft','pending','published','rejected') NOT NULL DEFAULT 'draft',
  diemThuong INT DEFAULT 10,
  danhMuc INT DEFAULT NULL,
  maSuKien INT UNSIGNED DEFAULT NULL,
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
  loaiCauHoi VARCHAR(50) NOT NULL,
  noiDungCauHoi TEXT NOT NULL,
  batBuocTraLoi BOOLEAN NOT NULL DEFAULT FALSE,
  quick_poll BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng phụ liên kết nhiều-nhiều giữa khảo sát và câu hỏi (để tái sử dụng câu hỏi)
CREATE TABLE IF NOT EXISTS survey_question_map (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  idKhaoSat INT UNSIGNED NOT NULL,
  idCauHoi INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_sqm_survey FOREIGN KEY (idKhaoSat)
    REFERENCES surveys(id) ON DELETE CASCADE,

  CONSTRAINT fk_sqm_question FOREIGN KEY (idCauHoi)
    REFERENCES questions(id) ON DELETE CASCADE,

  UNIQUE KEY uniq_sqm (idKhaoSat, idCauHoi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS answers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  idCauHoi INT UNSIGNED NOT NULL,
  noiDungCauTraLoi TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  creator_id INT UNSIGNED NOT NULL,
  FOREIGN KEY (idCauHoi) REFERENCES questions(id) ON DELETE CASCADE,
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
  INDEX idx_question (maCauHoi),
  UNIQUE KEY uniq_user_question_survey (maNguoiDung, maKhaoSat, maCauHoi) -- Đảm bảo mỗi người dùng chỉ trả lời một câu hỏi trong một khảo sát một lần
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

CREATE TABLE IF NOT EXISTS user_points ( -- Quản lí tổng số điểm của người dùng
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  balance INT UNSIGNED NOT NULL DEFAULT 0, -- Số điểm còn trong ví
  total_earned INT UNSIGNED NOT NULL DEFAULT 0, -- Tổng số điểm đã kiếm được
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_points (user_id),
  CONSTRAINT fk_user_points_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS point_transactions ( -- Log lịch sử cộng và rút điểm của người dùng
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  source ENUM('daily_reward','survey','manual_adjustment') NOT NULL,
  ref_id INT UNSIGNED DEFAULT NULL,
  amount INT UNSIGNED NOT NULL,
  balance_after INT UNSIGNED NOT NULL,
  note VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_point_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_user_source_ref (user_id, source, ref_id),
  INDEX idx_pt_user (user_id),
  INDEX idx_pt_source (source)
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

-- bảng quà thưởng
CREATE TABLE IF NOT EXISTS rewards (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  
  code VARCHAR(20) NOT NULL UNIQUE, -- mã phần thưởng
  name VARCHAR(255) NOT NULL,       -- tên ví dụ: "Rút tiền Momo", "Thẻ Steam 100K"

  type ENUM('cash','e_wallet','giftcard','physical') NOT NULL, 
  -- cash = banking
  -- e_wallet = momo, zalopay
  -- giftcard = apple, steam
  -- physical = tai nghe, chuột

  provider VARCHAR(100) DEFAULT NULL, 
  -- momo, zalopay, apple, steam, ngân hàng..., hoặc null nếu quà vật lý

  point_cost INT UNSIGNED NOT NULL, -- số điểm cần để đổi

  value VARCHAR(255) DEFAULT NULL,
  -- ví dụ thẻ 100K → 100000
  -- quà vật lý có thể null

  stock INT UNSIGNED DEFAULT NULL,
  -- quà vật lý cần stock
  -- ví điện tử thì stock = null

  image VARCHAR(255) DEFAULT NULL,   -- hình ảnh minh họa

  description TEXT DEFAULT NULL,

  -- Gift card specific columns
  giftcard_code VARCHAR(100) DEFAULT NULL,    -- mã quà tặng
  giftcard_serial VARCHAR(100) DEFAULT NULL,  -- serial number
  giftcard_expiry_date DATE DEFAULT NULL,     -- ngày hết hạn

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- bảng lịch sử đổi quà
CREATE TABLE IF NOT EXISTS reward_redemptions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  user_id INT UNSIGNED NOT NULL,
  reward_id INT UNSIGNED NOT NULL,

  status ENUM('pending','processing','completed','rejected')
    NOT NULL DEFAULT 'pending',

  note VARCHAR(255) DEFAULT NULL,        -- admin ghi chú
  receiver_info TEXT DEFAULT NULL,       -- thông tin nhận: tên, SĐT, địa chỉ hoặc tài khoản ví
  
  bank_name VARCHAR(255) DEFAULT NULL,   -- tên ngân hàng (cho cash redemptions)
  account_number VARCHAR(50) DEFAULT NULL,-- số tài khoản / số điện thoại

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE CASCADE
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
  maSuKien, thoiLuongDuTinh, isQuickPoll, created_at, updated_at
) VALUES
  (1, 'KS001', 'Khảo sát về thói quen đọc sách', 'Khảo sát nhằm tìm hiểu thói quen đọc sách của người Việt Nam.', 'Thói quen', 1, 'published', 10, 1, NULL, 15, FALSE, NOW(), NOW()),
  (2, 'KS002', 'Khảo sát về sức khỏe cộng đồng', 'Khảo sát nhằm đánh giá tình trạng sức khỏe cộng đồng.', 'Sức khỏe', 2, 'draft', 15, 2, NULL, 20, FALSE, NOW(), NOW()),
  (3, 'KS003', 'Khảo sát về trang web thương mại điện tử', 'Đánh giá trải nghiệm mua sắm trực tuyến của bạn.', 'Thương mại', 1, 'pending', 12, 1, NULL, 12, FALSE, NOW(), NOW()),
  (4, 'KS004', 'Khảo sát về ứng dụng di động', 'Cho biết ý kiến của bạn về các ứng dụng di động yêu thích.', 'Công nghệ', 2, 'pending', 10, 2, NULL, 10, FALSE, NOW(), NOW()),
  (5, 'KS005', 'Khảo sát về dịch vụ khách hàng', 'Đánh giá chất lượng dịch vụ khách hàng.', 'Dịch vụ', 1, 'pending', 20, 1, NULL, 18, FALSE, NOW(), NOW()),
  (6, 'KS006', 'Khảo sát về nhu cầu giáo dục trực tuyến', 'Tìm hiểu nhu cầu về các khóa học trực tuyến.', 'Giáo dục', 2, 'pending', 15, 2, NULL, 16, FALSE, NOW(), NOW()),
  (7,'KS007','Mức độ hài lòng tổng quan','Khảo sát nhanh về mức độ hài lòng.','QuickPoll',1,'pending',5,1,NULL,1,TRUE,NOW(),NOW()),
  (8,'KS008','Bạn hay uống cà phê không?','Khảo sát nhanh về thói quen uống cà phê.','QuickPoll',1,'pending',5,1,NULL,1,TRUE,NOW(),NOW()),
  (9,'KS009','Thời gian dùng mạng xã hội','Khảo sát thời lượng dùng MXH.','QuickPoll',2,'pending',5,1,NULL,1,TRUE,NOW(),NOW()),
  (10,'KS010','Bạn tập thể dục bao lâu?','Khảo sát về mức độ vận động.','QuickPoll',1,'pending',5,1,NULL,1,TRUE,NOW(),NOW()),
  (11,'KS011','Bạn ngủ bao nhiêu giờ?','Khảo sát thói quen ngủ.','QuickPoll',1,'pending',5,1,NULL,1,TRUE,NOW(),NOW()),
  (12,'KS012','Bạn xem phim thể loại gì?','Khảo sát thể loại phim yêu thích.','QuickPoll',1,'pending',5,1,NULL,1,TRUE,NOW(),NOW()),
  (13,'QP013','Ý tưởng Year End Party','Thu thập ý kiến nhanh cho tiệc cuối năm.','QuickPoll',1,'pending',5,3,NULL,1,TRUE,NOW(),NOW()),
  (14,'QP014','Phản hồi tính năng Dark Mode','Đánh giá nhanh giao diện tối mới cập nhật.','QuickPoll',2,'pending',5,2,NULL,1,TRUE,NOW(),NOW()),
  (15,'QP015','Bữa trưa nay ăn gì?','Bình chọn món ăn cho team building trưa nay.','QuickPoll',1,'pending',0,3,NULL,1,TRUE,NOW(),NOW()),
  (16,'QP016','Góp ý ẩn danh','Hòm thư góp ý nhanh ẩn danh hàng tuần.','QuickPoll',2,'pending',0,3,NULL,1,TRUE,NOW(),NOW());

INSERT IGNORE INTO questions (id, maCauHoi, loaiCauHoi, noiDungCauHoi, batBuocTraLoi, quick_poll, created_at, updated_at) VALUES
  (1, 'CH001', 'multiple_choice', 'Bạn thường đọc sách vào thời gian nào trong ngày?', TRUE, FALSE, NOW(), NOW()),
  (2, 'CH002', 'single_choice', 'Bạn thích thể loại sách nào nhất?', TRUE, FALSE, NOW(), NOW()),
  (3, 'CH003', 'text', 'Bạn có thường xuyên kiểm tra sức khỏe không?', FALSE, FALSE, NOW(), NOW()),
  (4, 'CH004', 'multiple_choice', 'Bạn có thói quen tập thể dục hàng ngày không?', TRUE,  FALSE, NOW(), NOW()),
  (5, 'CH005', 'single_choice', 'Bạn mua sắm trực tuyến bao lâu một lần?', TRUE,  FALSE, NOW(), NOW()),
  (6, 'CH006', 'multiple_choice', 'Nhóm sản phẩm nào bạn thường mua trực tuyến?', TRUE,  FALSE, NOW(), NOW()),
  (7, 'CH007', 'text', 'Ứng dụng di động nào bạn sử dụng nhiều nhất?', FALSE, FALSE, NOW(), NOW()),
  (8, 'CH008', 'single_choice', 'Bạn đánh giá như thế nào về chất lượng ứng dụng?', TRUE,  FALSE, NOW(), NOW()),
  (9, 'CH009', 'text', 'Dịch vụ khách hàng nào cải thiện cần thiết nhất?', FALSE,  FALSE, NOW(), NOW()),
  (10, 'CH010', 'multiple_choice', 'Thông qua kênh nào bạn muốn liên hệ hỗ trợ?', TRUE,  FALSE, NOW(), NOW()),
  (11, 'CH011', 'single_choice', 'Bạn quan tâm đến học các khóa nào?', TRUE,  FALSE, NOW(), NOW()),
  (12, 'CH012', 'multiple_choice', 'Hình thức học tập nào bạn ưa thích?', TRUE,  FALSE, NOW(), NOW()),
  (13,'CH013','single_choice','Bạn cảm thấy mức độ hài lòng hiện tại như thế nào?',TRUE,TRUE,NOW(),NOW()),
  (14,'CH014','single_choice','Bạn uống cà phê bao nhiêu lần/tuần?',TRUE,TRUE,NOW(),NOW()),
  (15,'CH015','single_choice','Bạn dùng mạng xã hội bao nhiêu giờ mỗi ngày?',TRUE,TRUE,NOW(),NOW()),
  (16,'CH016','single_choice','Bạn tập thể dục bao lâu mỗi tuần?',TRUE,TRUE,NOW(),NOW()),
  (17,'CH017','single_choice','Bạn ngủ trung bình bao nhiêu giờ mỗi ngày?',TRUE,TRUE,NOW(),NOW()),
  (18,'CH018','single_choice','Bạn thích thể loại phim nào nhất?',TRUE,TRUE,NOW(),NOW()),
  (19, 'QPQ001', 'single_choice', 'Bạn thích tổ chức Year End Party trong nhà hay ngoài trời?', TRUE,  TRUE, NOW(), NOW()),
  (20, 'QPQ002', 'single_choice', 'Bạn đánh giá giao diện Dark Mode mới bao nhiêu điểm?', TRUE,  TRUE, NOW(), NOW()),
  (21, 'QPQ003', 'multiple_choice', 'Trưa nay bạn muốn ăn món gì? (Chọn tối đa 2)', TRUE,  TRUE, NOW(), NOW()),
  (22, 'QPQ004', 'text', 'Điều gì làm bạn cảm thấy không thoải mái nhất trong tuần qua tại công ty?', TRUE, TRUE, NOW(), NOW());


INSERT IGNORE INTO answers (id, idCauHoi, noiDungCauTraLoi, creator_id, created_at, updated_at) VALUES
  (1, 1, 'Buổi sáng', 1, NOW(), NOW()),
  (2, 1, 'Buổi chiều',  1, NOW(), NOW()),
  (3, 1, 'Buổi tối',  1, NOW(), NOW()),
  (4, 1, 'Trước khi ngủ',  1, NOW(), NOW()),
  (5, 2, 'Tiểu thuyết',  1, NOW(), NOW()),
  (6, 2, 'Tự truyện', 1, NOW(), NOW()),
  (7, 2, 'Sách khoa học',  1, NOW(), NOW()),
  (8, 2, 'Sách triết học',  1, NOW(), NOW()),
  (9, 4, 'Có, tôi tập rất đều đặn', 2, NOW(), NOW()),
  (10, 4, 'Thỉnh thoảng (2-3 lần/tuần)',  2, NOW(), NOW()),
  (11, 4, 'Hiếm khi tập',  2, NOW(), NOW()),
  (12, 4, 'Không bao giờ',  2, NOW(), NOW()),
  (13, 5, 'Hàng ngày', 1, NOW(), NOW()),
  (14, 5, 'Hàng tuần',  1, NOW(), NOW()),
  (15, 5, 'Hàng tháng',  1, NOW(), NOW()),
  (16, 5, 'Ít hơn hàng tháng',  1, NOW(), NOW()),
  (17, 6, 'Quần áo và giày dép', 1, NOW(), NOW()),
  (18, 6, 'Điện tử', 1, NOW(), NOW()),
  (19, 6, 'Sách và tạp chí',  1, NOW(), NOW()),
  (20, 6, 'Mỹ phẩm',  1, NOW(), NOW()),
  (21, 8, 'Rất tốt', 2, NOW(), NOW()),
  (22, 8, 'Tốt',  2, NOW(), NOW()),
  (23, 8, 'Trung bình',  2, NOW(), NOW()),
  (24, 8, 'Tệ',  2, NOW(), NOW()),
  (25, 10, 'Chat/Zalo', 1, NOW(), NOW()),
  (26, 10, 'Email', 1, NOW(), NOW()),
  (27, 10, 'Điện thoại',  1, NOW(), NOW()),
  (28, 10, 'Mạng xã hội',  1, NOW(), NOW()),
  (29, 11, 'Lập trình', 2, NOW(), NOW()),
  (30, 11, 'Thiết kế',  2, NOW(), NOW()),
  (31, 11, 'Tiếp thị số',  2, NOW(), NOW()),
  (32, 11, 'Tiếng Anh',  2, NOW(), NOW()),
  (33, 12, 'Video học tập', 2, NOW(), NOW()),
  (34, 12, 'Bài giảng trực tiếp', 2, NOW(), NOW()),
  (35, 12, 'Tài liệu chữ',  2, NOW(), NOW()),
  (36, 12, 'Dự án thực tế',  2, NOW(), NOW()),
  (37,13,'Rất hài lòng',1,NOW(),NOW()),
  (38,13,'Hài lòng',1,NOW(),NOW()),
  (39,13,'Bình thường',1,NOW(),NOW()),
  (40,13,'Không hài lòng',1,NOW(),NOW()),
  (41,14,'Mỗi ngày',1,NOW(),NOW()),
  (42,14,'2–3 lần/tuần',1,NOW(),NOW()),
  (43,14,'1 lần/tuần',1,NOW(),NOW()),
  (44,14,'Không uống',1,NOW(),NOW()),
  (45,15,'Dưới 1 giờ',1,NOW(),NOW()),
  (46,15,'1–2 giờ',1,NOW(),NOW()),
  (47,15,'3–4 giờ',1,NOW(),NOW()),
  (48,15,'Trên 4 giờ',1,NOW(),NOW()),
  (49,16,'Dưới 1 giờ',1,NOW(),NOW()),
  (50,16,'1–2 giờ',1,NOW(),NOW()),
  (51,16,'3–5 giờ',1,NOW(),NOW()),
  (52,16,'Hầu như không tập',1,NOW(),NOW()),
  (53,17,'Dưới 5 giờ',1,NOW(),NOW()),
  (54,17,'5–6 giờ',1,NOW(),NOW()),
  (55,17,'7–8 giờ',1,NOW(),NOW()),
  (56,17,'Trên 8 giờ',1,NOW(),NOW()),
  (57,18,'Hành động',1,NOW(),NOW()),
  (58,18,'Hài',1,NOW(),NOW()),
  (59,18,'Kinh dị',1,NOW(),NOW()),
  (60,18,'Tình cảm',1,NOW(),NOW()),
  (61, 19, 'Trong nhà (Sang trọng, ấm cúng)',  1, NOW(), NOW()),
  (62, 19, 'Ngoài trời (Thoáng mát, BBQ)',  1, NOW(), NOW()),
  (63, 19, 'Resort ngoại ô',  1, NOW(), NOW()),
  (64, 20, 'Rất đẹp (5/5)', 2, NOW(), NOW()),
  (65, 20, 'Ổn (4/5)',  2, NOW(), NOW()),
  (66, 20, 'Bình thường (3/5)',  2, NOW(), NOW()),
  (67, 20, 'Chưa tốt, khó nhìn (1-2/5)',  2, NOW(), NOW()),
  (68, 21, 'Cơm tấm',  1, NOW(), NOW()),
  (69, 21, 'Bún bò Huế',  1, NOW(), NOW()),
  (70, 21, 'Pizza',  1, NOW(), NOW()),
  (71, 21, 'Gà rán',  1, NOW(), NOW()),
  (72, 21, 'Cơm văn phòng healthy', 1, NOW(), NOW());


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
  (14, 'FB014', 9, NULL, 'Khách Ẩn Danh', 5, 'Rất tốt, tôi thích khảo sát dạng này.', NOW(), NOW());

INSERT IGNORE INTO rewards (code, name, type, provider, point_cost, value, stock, image, description, giftcard_code, giftcard_serial, giftcard_expiry_date)
VALUES
('RW-CASH-20K',  'Rút tiền Banking 20.000đ',  'cash', 'bank', 2000, 20000, 1000, 'banking_20k.png', 'Rút tiền về tài khoản ngân hàng.', NULL, NULL, NULL),
('RW-CASH-200K', 'Rút tiền Banking 200.000đ', 'cash', 'bank', 20000, 200000, 1000, 'banking_200k.png', 'Rút tiền về tài khoản ngân hàng.', NULL, NULL, NULL),
('RW-CASH-500K', 'Rút tiền Banking 500.000đ', 'cash', 'bank', 50000, 500000, 1000, 'banking_500k.png', 'Rút tiền về tài khoản ngân hàng.', NULL, NULL, NULL),

('RW-MOMO-20K',  'Ví MoMo 20.000đ', 'e_wallet', 'momo', 2000, 20000, NULL, 'momo_20k.png', 'Nạp tiền vào ví MoMo.', NULL, NULL, NULL),
('RW-ZALO-100K', 'ZaloPay 100.000đ', 'e_wallet', 'zalopay', 10000, 100000, NULL, 'zalopay_100k.png', 'Nạp tiền ZaloPay.', NULL, NULL, NULL),
('RW-SHOPEE-50K', 'ShopeePay 50.000đ', 'e_wallet', 'shopeepay', 5000, 50000, NULL, 'shopeepay_50k.png', 'Nạp tiền ShopeePay.', NULL, NULL, NULL),

('RW-CARD-VIETTEL-50', 'Thẻ Viettel 50.000đ', 'giftcard', 'viettel', 6000, 50000, 100, 'viettel_50.png', 'Mã thẻ cào Viettel.', '123456789012345', 'VT-SN-2024001', '2025-12-31'),
('RW-CARD-VIETTEL-100', 'Thẻ Viettel 100.000đ', 'giftcard', 'viettel', 12000, 100000, 80, 'viettel_100.png', 'Mã thẻ cào Viettel.', '987654321098765', 'VT-SN-2024002', '2025-12-31'),
('RW-CARD-MOBI-50', 'Thẻ Mobifone 50.000đ', 'giftcard', 'mobifone', 6000, 50000, 70, 'mobifone_50.png', 'Mã thẻ Mobifone.', '111222333444555', 'MB-SN-2024001', '2025-12-31'),
('RW-CARD-VINA-100', 'Thẻ Vinaphone 100.000đ', 'giftcard', 'vinaphone', 12000, 100000, 60, 'vinaphone_100.png', 'Mã thẻ Vinaphone.', '555666777888999', 'VN-SN-2024001', '2025-12-31'),

('RW-APPLE-200', 'Apple Gift Card 200.000đ', 'giftcard', 'apple', 24000, 200000, 40, 'apple_giftcard_200.png', 'Mã Apple App Store.', 'APPLE-GC-2024-001', 'APPL-SN-2024-0001', '2025-06-30'),
('RW-GOOGLE-100', 'Google Play 100.000đ', 'giftcard', 'google', 12000, 100000, 50, 'googleplay_100.png', 'Mã Google Play nạp ứng dụng.', 'GOOGLE-GP-2024-001', 'GOOG-SN-2024-0001', '2025-06-30'),
('RW-GARENA-100', 'Garena 100.000đ', 'giftcard', 'garena', 12000, 100000 , 50, 'garena_100.png', 'Nạp Garena cho Liên Quân / FreeFire.', 'GARENA-2024-001', 'GARN-SN-2024-0001', '2025-12-31'),
('RW-GARENA-200', 'Garena 200.000đ', 'giftcard', 'garena', 24000, 200000, 30, 'garena_200.png', 'Mã thẻ Garena 200K.', NULL, NULL, NULL),

('RW-TIKI-50', 'Voucher Tiki 50.000đ', 'giftcard', 'tiki', 6000, 50000, 200, 'tiki_50.png', 'Voucher mua sắm Tiki.', NULL, NULL, NULL),
('RW-SHOPEE-100', 'Voucher Shopee 100.000đ', 'giftcard', 'shopee', 12000, 100000, 150, 'shopee_100.png', 'Voucher Shopee áp dụng toàn sàn.', NULL, NULL, NULL),
('RW-LAZADA-50', 'Voucher Lazada 50.000đ', 'giftcard', 'lazada', 6000, 50000, 180, 'lazada_50.png', 'Voucher Lazada.', NULL, NULL, NULL),

('RW-PH-SPEAKER', 'Loa Bluetooth mini', 'physical', NULL, 18000, NULL, 25, 'speaker.png', 'Loa Bluetooth âm thanh lớn.', NULL, NULL, NULL),
('RW-PH-POWERA', 'Sạc dự phòng 10.000mAh', 'physical', NULL, 22000, NULL, 20, 'powerbank.png', 'Sạc dự phòng dung lượng cao.', NULL, NULL, NULL),
('RW-PH-LAMP', 'Đèn bàn học chống cận', 'physical', NULL, 12000, NULL, 30, 'study_lamp.png', 'Đèn LED tiết kiệm điện.', NULL, NULL, NULL),

('RW-PH-BACKPACK', 'Balo chống nước', 'physical', NULL, 20000, NULL, 12, 'backpack.png', 'Balo thời trang chống nước.', NULL, NULL, NULL),
('RW-PH-NOTEBOOK', 'Sổ tay 200 trang', 'physical', NULL, 3000, NULL, 100, 'notebook.png', 'Sổ tay học tập.', NULL, NULL, NULL),
('RW-PH-PENSET', 'Bộ bút gel cao cấp', 'physical', NULL, 4000, NULL, 80, 'penset.png', 'Bút viết mực gel.', NULL, NULL, NULL),

('RW-PH-COFFEE', 'Voucher Highlands 50.000đ', 'giftcard', 'highlands', 6000, 50000, 100, 'highlands_50.png', 'Voucher mua nước Highlands.', NULL, NULL, NULL),
('RW-PH-FOOD', 'Voucher GrabFood 50.000đ', 'giftcard', 'grabfood', 6000, 50000, 100, 'grabfood_50.png', 'Voucher GrabFood.', NULL, NULL, NULL),
('RW-PH-MILKTEA', 'Voucher GongCha 50.000đ', 'giftcard', 'gongcha', 6000, 50000, 100, 'gongcha_50.png', 'Voucher trà sữa GongCha.', NULL, NULL, NULL);

INSERT IGNORE INTO reward_redemptions
(user_id, reward_id, status, note, receiver_info, bank_name, account_number, created_at, updated_at)
VALUES
(1, 3, 'pending', NULL, 'Nguyễn Văn A, 0901234567', 'momo', '0901234567', NOW(), NOW()),
(2, 1, 'completed', 'Đã chuyển khoản thành công.', 'Trần Thị B', 'bank', '0123456789', NOW(), NOW()),
(3, 9, 'processing', 'Đang chuẩn bị giao hàng.', 'Phạm Văn C - Địa chỉ: 123 Lê Lợi, Q1, TP.HCM', NULL, NULL, NOW(), NOW()),
(4, 6, 'completed', NULL, 'user4@example.com', NULL, NULL, NOW(), NOW()),
(5, 7, 'rejected', 'Mã không hợp lệ - vui lòng đổi lại.', 'user5@example.com', NULL, NULL, NOW(), NOW());

INSERT IGNORE INTO user_points (user_id, balance, total_earned, created_at, updated_at) VALUES
(1, 50, 50, NOW(), NOW()),
(2, 120, 150, NOW(), NOW()),
(3, 50000, 50000, NOW(), NOW()),     -- admin
(4, 80, 100, NOW(), NOW()),
(5, 20, 40, NOW(), NOW()),
(6, 300, 320, NOW(), NOW()),
(7, 10, 10, NOW(), NOW()),
(8, 200, 250, NOW(), NOW()),
(9, 90, 100, NOW(), NOW()),
(10, 140, 160, NOW(), NOW()),
(11, 60, 80, NOW(), NOW()),
(12, 30, 30, NOW(), NOW()),
(13, 110, 130, NOW(), NOW()),
(14, 15, 20, NOW(), NOW()),
(15, 75, 90, NOW(), NOW());

CREATE TABLE IF NOT EXISTS activity_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(50) DEFAULT NULL,
  entity_id INT UNSIGNED DEFAULT NULL,
  description TEXT DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  old_values JSON DEFAULT NULL,
  new_values JSON DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_created_at (created_at),
  INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT IGNORE INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent, old_values, new_values, created_at) VALUES
-- User 1 activities
(1, 'survey_submitted', 'survey', 1, 'Hoàn thành khảo sát: Khảo sát về thói quen đọc sách', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 1, "points_earned": 10}', DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(1, 'reward_redeemed', 'reward_redemption', 1, 'Đổi thưởng thành công: Rút tiền Banking 20.000đ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"reward_id": 1, "points_spent": 2000}', DATE_SUB(NOW(), INTERVAL 30 MINUTE)),

-- User 2 activities
(2, 'survey_submitted', 'survey', 2, 'Hoàn thành khảo sát: Khảo sát về sức khỏe cộng đồng', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 2, "points_earned": 15}', DATE_SUB(NOW(), INTERVAL 90 MINUTE)),
(2, 'participated_event', 'event', 1, 'Tham gia sự kiện: Sự kiện Khởi động Năm Mới', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 1}', DATE_SUB(NOW(), INTERVAL 2 HOUR)),

-- User 4 activities
(4, 'survey_submitted', 'survey', 4, 'Hoàn thành khảo sát: Khảo sát về ứng dụng di động', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 4, "points_earned": 12}', DATE_SUB(NOW(), INTERVAL 260 MINUTE)),
(4, 'reward_redeemed', 'reward_redemption', 2, 'Đổi thưởng thành công: Ví MoMo 20.000đ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"reward_id": 4, "points_spent": 2000}', DATE_SUB(NOW(), INTERVAL 5 HOUR)),

-- User 5 activities
(5, 'survey_submitted', 'survey', 5, 'Hoàn thành khảo sát: Khảo sát về dịch vụ khách hàng', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 5, "points_earned": 20}', DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(5, 'participated_event', 'event', 2, 'Tham gia sự kiện: Hội thảo Sức khỏe Cộng đồng', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 2}', DATE_SUB(NOW(), INTERVAL 8 HOUR)),

-- User 6 activities
(6, 'survey_submitted', 'survey', 6, 'Hoàn thành khảo sát: Khảo sát về nhu cầu giáo dục trực tuyến', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 6, "points_earned": 15}', DATE_SUB(NOW(), INTERVAL 10 HOUR)),
(6, 'reward_redeemed', 'reward_redemption', 3, 'Đổi thưởng thành công: ShopeePay 50.000đ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"reward_id": 6, "points_spent": 5000}', DATE_SUB(NOW(), INTERVAL 12 HOUR)),

-- User 7 activities
(7, 'survey_submitted', 'survey', 3, 'Hoàn thành khảo sát: Khảo sát về trang web thương mại điện tử', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 3, "points_earned": 12}', DATE_SUB(NOW(), INTERVAL 14 HOUR)),
(7, 'participated_event', 'event', 3, 'Tham gia sự kiện: Workshop về Digital Marketing 2024', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 3}', DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Admin 3 activities
(3, 'survey_created', 'survey', 7, 'Tạo khảo sát mới: Mức độ hài lòng tổng quan', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 7, "title": "Mức độ hài lòng tổng quan", "status": "pending"}', DATE_SUB(NOW(), INTERVAL 135 MINUTE)),
(3, 'event_created', 'event', 4, 'Tạo sự kiện mới: Ngày hội Công nghệ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 4, "title": "Ngày hội Công nghệ", "status": "upcoming"}', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(3, 'question_created', 'question', 13, 'Tạo câu hỏi mới: Bạn cảm thấy mức độ hài lòng hiện tại như thế nào?', '127.0.0.1', 'Mozilla/5.0', NULL, '{"question_id": 13, "type": "single_choice"}', DATE_SUB(NOW(), INTERVAL 5 HOUR)),

-- Admin 1 activities
(1, 'survey_created', 'survey', 8, 'Tạo khảo sát mới: Bạn hay uống cà phê không?', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 8, "title": "Bạn hay uống cà phê không?", "status": "pending"}', DATE_SUB(NOW(), INTERVAL 7 HOUR)),
(1, 'event_created', 'event', 1, 'Cập nhật sự kiện: Sự kiện Khởi động Năm Mới', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 1, "participants": 150}', DATE_SUB(NOW(), INTERVAL 26 HOUR)),
(1, 'question_created', 'question', 19, 'Tạo câu hỏi mới: Bạn thích tổ chức Year End Party trong nhà hay ngoài trời?', '127.0.0.1', 'Mozilla/5.0', NULL, '{"question_id": 19, "type": "single_choice"}', DATE_SUB(NOW(), INTERVAL 29 HOUR));

CREATE TABLE IF NOT EXISTS activity_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(50) DEFAULT NULL,
  entity_id INT UNSIGNED DEFAULT NULL,
  description TEXT DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  old_values JSON DEFAULT NULL,
  new_values JSON DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_created_at (created_at),
  INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT IGNORE INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent, old_values, new_values, created_at) VALUES
-- User 1 activities
(1, 'survey_submitted', 'survey', 1, 'Hoàn thành khảo sát: Khảo sát về thói quen đọc sách', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 1, "points_earned": 10}', DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(1, 'reward_redeemed', 'reward_redemption', 1, 'Đổi thưởng thành công: Rút tiền Banking 20.000đ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"reward_id": 1, "points_spent": 2000}', DATE_SUB(NOW(), INTERVAL 30 MINUTE)),

-- User 2 activities
(2, 'survey_submitted', 'survey', 2, 'Hoàn thành khảo sát: Khảo sát về sức khỏe cộng đồng', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 2, "points_earned": 15}', DATE_SUB(NOW(), INTERVAL 90 MINUTE)),
(2, 'participated_event', 'event', 1, 'Tham gia sự kiện: Sự kiện Khởi động Năm Mới', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 1}', DATE_SUB(NOW(), INTERVAL 2 HOUR)),

-- User 4 activities
(4, 'survey_submitted', 'survey', 4, 'Hoàn thành khảo sát: Khảo sát về ứng dụng di động', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 4, "points_earned": 12}', DATE_SUB(NOW(), INTERVAL 260 MINUTE)),
(4, 'reward_redeemed', 'reward_redemption', 2, 'Đổi thưởng thành công: Ví MoMo 20.000đ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"reward_id": 4, "points_spent": 2000}', DATE_SUB(NOW(), INTERVAL 5 HOUR)),

-- User 5 activities
(5, 'survey_submitted', 'survey', 5, 'Hoàn thành khảo sát: Khảo sát về dịch vụ khách hàng', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 5, "points_earned": 20}', DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(5, 'participated_event', 'event', 2, 'Tham gia sự kiện: Hội thảo Sức khỏe Cộng đồng', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 2}', DATE_SUB(NOW(), INTERVAL 8 HOUR)),

-- User 6 activities
(6, 'survey_submitted', 'survey', 6, 'Hoàn thành khảo sát: Khảo sát về nhu cầu giáo dục trực tuyến', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 6, "points_earned": 15}', DATE_SUB(NOW(), INTERVAL 10 HOUR)),
(6, 'reward_redeemed', 'reward_redemption', 3, 'Đổi thưởng thành công: ShopeePay 50.000đ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"reward_id": 6, "points_spent": 5000}', DATE_SUB(NOW(), INTERVAL 12 HOUR)),

-- User 7 activities
(7, 'survey_submitted', 'survey', 3, 'Hoàn thành khảo sát: Khảo sát về trang web thương mại điện tử', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 3, "points_earned": 12}', DATE_SUB(NOW(), INTERVAL 14 HOUR)),
(7, 'participated_event', 'event', 3, 'Tham gia sự kiện: Workshop về Digital Marketing 2024', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 3}', DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Admin 3 activities
(3, 'survey_created', 'survey', 7, 'Tạo khảo sát mới: Mức độ hài lòng tổng quan', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 7, "title": "Mức độ hài lòng tổng quan", "status": "pending"}', DATE_SUB(NOW(), INTERVAL 135 MINUTE)),
(3, 'event_created', 'event', 4, 'Tạo sự kiện mới: Ngày hội Công nghệ', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 4, "title": "Ngày hội Công nghệ", "status": "upcoming"}', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(3, 'question_created', 'question', 13, 'Tạo câu hỏi mới: Bạn cảm thấy mức độ hài lòng hiện tại như thế nào?', '127.0.0.1', 'Mozilla/5.0', NULL, '{"question_id": 13, "type": "single_choice"}', DATE_SUB(NOW(), INTERVAL 5 HOUR)),

-- Admin 1 activities
(1, 'survey_created', 'survey', 8, 'Tạo khảo sát mới: Bạn hay uống cà phê không?', '127.0.0.1', 'Mozilla/5.0', NULL, '{"survey_id": 8, "title": "Bạn hay uống cà phê không?", "status": "pending"}', DATE_SUB(NOW(), INTERVAL 7 HOUR)),
(1, 'event_created', 'event', 1, 'Cập nhật sự kiện: Sự kiện Khởi động Năm Mới', '127.0.0.1', 'Mozilla/5.0', NULL, '{"event_id": 1, "participants": 150}', DATE_SUB(NOW(), INTERVAL 26 HOUR)),
(1, 'question_created', 'question', 19, 'Tạo câu hỏi mới: Bạn thích tổ chức Year End Party trong nhà hay ngoài trời?', '127.0.0.1', 'Mozilla/5.0', NULL, '{"question_id": 19, "type": "single_choice"}', DATE_SUB(NOW(), INTERVAL 29 HOUR));

-- dữ liệu cho bảng survey_question_map
INSERT IGNORE INTO survey_question_map (idKhaoSat, idCauHoi, created_at, updated_at) VALUES
  (1, 1, NOW(), NOW()),
  (1, 2, NOW(), NOW()),
  (2, 3, NOW(), NOW()),
  (2, 4, NOW(), NOW()),
  (3, 5, NOW(), NOW()),
  (3, 6, NOW(), NOW()),
  (4, 7, NOW(), NOW()),
  (4, 8, NOW(), NOW()),
  (5, 9, NOW(), NOW()),
  (5, 10, NOW(), NOW()),
  (6, 11, NOW(), NOW()),
  (6, 12, NOW(), NOW()),
  (7,13,NOW(),NOW()),
  (8,14,NOW(),NOW()),
  (9,15,NOW(),NOW()),
  (10,16,NOW(),NOW()),
  (11,17,NOW(),NOW()),
  (12,18,NOW(),NOW()),
  (13,19,NOW(),NOW()),
  (14,20,NOW(),NOW()),
  (15,21,NOW(),NOW()),
  (16,22,NOW(),NOW());


