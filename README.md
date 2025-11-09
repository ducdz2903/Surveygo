## Surveyon — PHP MVC Demo (XAMPP-ready)

[![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.1-777bb4?logo=php)](https://www.php.net/)
[![MySQL/MariaDB](https://img.shields.io/badge/MySQL%2FMariaDB-10.4%2B-00618a?logo=mysql)](https://mariadb.org/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952b3?logo=bootstrap)](https://getbootstrap.com/)
[![Status](https://img.shields.io/badge/Status-Alpha-orange)](#roadmapstatus--known-issues)

Dự an PHP thuần theo hướng MVC (không framework) với Router/Controller/View đơn giản, giao diện Landing, form Đăng ký/Đăng nhập, và API cơ bản. Chạy trực tiếp trong `htdocs` của XAMPP hoặc bằng PHP built‑in server.

### Mô tả ngắn
- Dự án mô phỏng trang web Surveyon nhằm mục đích học tập ngôn ngữ PHP và các framework cơ bản

### Demo / Ảnh chụp / Playground
- Trang chủ, tính năng, login/register UI có sẵn. Chạy local theo Quick Start bên dưới.

### Tính năng chính
- Giao diện: Trang chủ, Tính năng, Đăng nhập, Đăng ký, Home sau đăng nhập
- API: `POST /api/register`, `POST /api/login`, `GET /api/health`
- DB init tự động: chạy `sql/mysql/init.sql` khi app khởi động (tạo bảng + seed)
- Router/Controller/View thuần PHP; không phụ thuộc framework

### Kiến trúc / Tech stack
- PHP 8.1+, PDO MySQL, Bootstrap 5.3
- Thư mục chính:
  - `public/index.php`: Front controller + khai báo routes
  - `app/Core`: Router, Request, Response, Controller, Container, Database, View, SqlInitializer
  - `app/Controllers`: `HomeController`, `AuthController`, `SurveyController`, `QuestionController`
  - `app/Models`: `User` và các model khác (tối giản)
  - `app/Views`: View PHP + partials navbar/footer
  - `sql/mysql/init.sql`: Schema + seed mẫu (users, events, surveys, questions)
  - `config/app.php`: Cấu hình app, DB

### Yêu cầu hệ thống
- Windows/macOS/Linux
- PHP >= 8.1, ext `pdo_mysql`
- MySQL/MariaDB 10.4+
- XAMPP (khuyến nghị trên Windows) hoặc PHP CLI
- Apache `mod_rewrite` bật nếu chạy qua Apache

### Cài đặt & Quick Start
1) Sao chép mã nguồn vào `C:\xampp\htdocs\Surveyon` (hoặc thư mục bạn muốn)
2) Mở XAMPP, bật Apache và MySQL
3) Tạo database trống với tên mvc_app
4) Truy cập `http://localhost/Surveyon/` (`.htaccess` sẽ tự forward về `public/index.php`)
   - Hoặc dùng PHP built‑in server:
     ```bash
     cd path/to/Surveyon
     php -S 127.0.0.1:8000 -t public
     # Mở http://127.0.0.1:8000
     ```
5) Lần chạy đầu: `SqlInitializer` sẽ tự tạo schema và dữ liệu mẫu

Thông tin seed (ví dụ):
- User: `user1@example.com` / `password`

### Cách dùng (CLI/API/SDK) + ví dụ mã
- Đăng ký: `POST /api/register` — body `x-www-form-urlencoded` hoặc JSON
  - name, email, password
- Đăng nhập: `POST /api/login`
- Health: `GET /api/health`

Ví dụ curl đăng nhập:
```bash
curl -X POST \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=user1@example.com&password=password" \
  http://localhost/Surveyon/api/login
```

### Cấu hình & biến môi trường
- File: `config/app.php`
  - `app.base_url`: rỗng để auto-detect (hỗ trợ chạy dưới subfolder như `/Surveyon`)
  - `app.debug`: `true/false`
  - `db.{host,port,database,username,password,charset}`

### Tài liệu chi tiết
- API Testing Guide: `API_TESTING_GUIDE.md`

### Phát triển local (dev setup, scripts, hot reload)
- Sửa code trong `app/` và `public/`
- Không dùng Composer/framework — khởi động nhanh, ít phụ thuộc
- Hot reload: dùng Live Server/BrowserSync nếu cần (không tích hợp sẵn)


