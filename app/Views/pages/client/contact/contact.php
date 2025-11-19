<?php
/** @var string $appName */
/** @var array $urls */

$appName = $appName ?? 'PHP Application';
$urls = $urls ?? [];

// Hàm trợ giúp cho URL
$url = static fn($urls_array, $key, $default) => $urls_array[$key] ?? $default;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName . ' - Liên Hệ', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/assets/css/app.css">
    <link rel="stylesheet" href="public/assets/css/components/footer.css">
    <link rel="stylesheet" href="public/assets/css/components/navbar.css">
    <link rel="stylesheet" href="public/assets/css/client/contact.css">
</head>

<body class="page page--contact">
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container py-5">
            <div class="text-center">
                <h1 class="contact-title">Liên Hệ Với Chúng Tôi</h1>
                <p class="contact-subtitle">Surveygo luôn lắng nghe và tiếp nhận mọi ý kiến đóng góp của bạn. Hãy liên
                    hệ với chúng mình bằng cách điền thông tin vào form dưới đây. Chúng mình sẽ phản hồi bạn trong thời
                    gian sớm nhất.</p>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-content">
        <div class="container py-5">
            <div class="row g-5">
                <!-- Contact Form -->
                <div class="col-lg-6">
                    <div class="contact-form-section">
                        <h2 class="form-title">Gửi Thông Tin Cho Chúng Tôi</h2>
                        <p class="form-subtitle">Vui lòng điền đầy đủ thông tin để chúng tôi có thể liên hệ lại với bạn
                            sớm nhất.</p>
                        <form id="contactForm" class="contact-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fullName" class="form-label">Họ và Tên</label>
                                        <input type="text" class="form-control" id="fullName" name="fullName"
                                            placeholder="Nhập tên đầy đủ..." required>
                                        <small class="form-text">Vui lòng nhập tên của bạn</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Nhập email..." required>
                                        <small class="form-text">Vui lòng nhập email hợp lệ</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">Số Điện Thoại</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="Nhập số điện thoại...">
                                        <small class="form-text">Tùy chọn - để lại số điện thoại để chúng tôi gọi
                                            lại</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subject" class="form-label">Chủ Đề</label>
                                        <select class="form-select" id="subject" name="subject" required>
                                            <option value="">Chọn chủ đề...</option>
                                            <option value="feedback">Phản hồi</option>
                                            <option value="bug">Báo cáo lỗi</option>
                                            <option value="feature">Đề xuất tính năng</option>
                                            <option value="support">Hỗ trợ kỹ thuật</option>
                                            <option value="partnership">Hợp tác</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label">Nội Dung</label>
                                <textarea class="form-control" id="message" name="message" rows="6"
                                    placeholder="Nhập nội dung liên hệ..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-gradient btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Thông Tin
                            </button>

                            <div id="successMessage" class="alert alert-success mt-3" style="display: none;">
                                <i class="fas fa-check-circle me-2"></i>Cảm ơn bạn! Chúng tôi đã nhận được thông tin của
                                bạn. Chúng tôi sẽ phản hồi bạn sớm nhất.
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Map -->
                <div class="col-lg-6">
                    <div class="map-section-inline">
                        <h2 class="form-title">Tìm Chúng Tôi Trên Bản Đồ</h2>
                        <div class="map-container">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.1234567890!2d106.69123456789!3d20.85678901234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31456c5c1234567%3A0x1234567890abcdef!2s484%20L%E1%BA%A0ch%20Tray%2C%20H%E1%BA%A3i%20Ph%C3%B2ng!5e0!3m2!1svi!2s!4v1234567890"
                                width="100%" height="450" style="border:0; border-radius: 12px;" allowfullscreen=""
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <!-- thông tin liên hệ -->
            <div class="contact-info-horizontal mb-5 mt-5">
                <h2 class="info-title mb-4">Thông Tin Liên Hệ</h2>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="info-item-compact">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <h3>Địa Chỉ</h3>
                                <p>484 Lạch Tray, Đổng Quốc Bình, Lê Chân, Hải Phòng 180000</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="info-item-compact">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-content">
                                <h3>Số Điện Thoại</h3>
                                <p><a href="tel:081919898908">0382116940</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="info-item-compact">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <h3>Email</h3>
                                <p><a href="mailto:contact@surveygo.com">contact@surveygo.com</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="info-item-compact">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <h3>Giờ Làm Việc</h3>
                                <p>Thứ Hai - Thứ Sáu: 9:00 - 18:00<br>Thứ Bảy: 9:00 - 12:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('contactForm');
            const successMessage = document.getElementById('successMessage');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Collect form data
                    const formData = {
                        fullName: document.getElementById('fullName').value,
                        email: document.getElementById('email').value,
                        phone: document.getElementById('phone').value,
                        subject: document.getElementById('subject').value,
                        message: document.getElementById('message').value
                    };

                    // Log data (in real app, send to server)
                    console.log('Contact form submitted:', formData);

                    // Show success message
                    successMessage.style.display = 'block';

                    // Reset form
                    form.reset();

                    // Hide success message after 5 seconds
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 5000);

                    // Scroll to message
                    successMessage.scrollIntoView({ behavior: 'smooth' });
                });
            }
        });
    </script>
</body>

</html>