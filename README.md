## Ứng dụng PHP MVC + Landing Page (chạy trong XAMPP)

Dự án mẫu giúp bạn khởi tạo nhanh một ứng dụng PHP thuần theo hướng MVC (không framework), có Router/Controller/View đơn giản và giao diện landing chia trang. Chạy trực tiếp trong thư mục `htdocs` của XAMPP.

### Tính năng chính
- Trang landing: Trang chủ, Tính năng, Đăng nhập, Đăng ký
- API cơ bản: Đăng ký, Đăng nhập, Health check (không dùng JWT/middleware)

### Cấu trúc thư mục
- `public/index.php`: Front controller, định tuyến mọi request vào Router
- `app/Core`: Hạ tầng lõi (Router, Request, Response, Controller, Container, Database, View)
- `app/Controllers`: Controller cho Landing (`HomeController`), Auth API (`AuthController`)
- `app/Models`: Model người dùng (`User`) và truy vấn DB tối giản
- `app/Views`: View PHP cho từng trang (+ partials navbar/footer)
- `public/assets`: CSS/JS tĩnh cho giao diện
- `config/app.php`: Cấu hình ứng dụng (app, db)

### Cài đặt trên XAMPP (Windows)
1) Sao chép mã nguồn vào C:\xampp\htdocs\Surveyon (hoặc tên thư mục bạn muốn)
2) Mở XAMPP Control Panel, bật Apache và MySQL
3) Tạo database rỗng (MySQL) và sửa config/app.php mục db cho phù hợp
4) Hệ thống sẽ tự động chạy file SQL tương ứng trong sql/ để tạo bảng và dữ liệu mẫu khi app khởi động

### Các trang giao diện
- `/` Trang chủ
- `/features` Tính năng
- `/register` Form đăng ký (gửi `POST /api/register`)
- `/login` Form đăng nhập (gửi `POST /api/login`)

### API chính
- `POST /api/register` Đăng ký tài khoản mới (trả về thông tin người dùng)
- `POST /api/login` Đăng nhập (trả về thông tin người dùng)
- `GET /api/health` Kiểm tra trạng thái API

Ví dụ payload đăng ký/đăng nhập (JSON):
```
{
  "name": "Nguyễn Văn A",
  "email": "user@example.com",
  "password": "mat-khau-bao-mat"
}
```

### Cấu hình quan trọng (`config/app.php`)
- `app.base_url`: base URL của ứng dụng. Để trống để auto-detect (hoạt động khi app nằm trong thư mục con như `/Surveyon`). Khi deploy, nên đặt giá trị rõ ràng, ví dụ `https://ten-mien`.
- `app.debug`: `true/false`. Bật để nhận thêm trace khi lỗi (chỉ nên bật local).
- `db.*`: thông tin kết nối MySQL (mặc định XAMPP: `root`/rỗng, DB `mvc_app`).

### Khắc phục sự cố
- 404 cho các route như `/login`: kiểm tra đã bật `mod_rewrite`, có `.htaccess` ở thư mục gốc, sau đó restart Apache
- Lỗi kết nối DB: kiểm tra `config/app.php` mục `db`, đảm bảo DB tồn tại và tài khoản có quyền
- Lỗi 500: bật `app.debug = true` để xem trace trong response; xem thêm log Apache `C:\xampp\apache\logs\error.log`

Chúc bạn triển khai thuận lợi! Nếu cần mở rộng giao diện hoặc thêm endpoint mới, hãy tạo issue hoặc liên hệ để mình hỗ trợ.