<?php
/** @var string $appName */
/** @var array $urls */

$urls = $urls ?? [];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(($appName ?? 'PHP Application') . ' - Đăng nhập', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- custom css -->
    <link rel="stylesheet" href="public/assets/css/auth/login.css">
    <link rel="stylesheet" href="public/assets/css/app.css">
    <link rel="stylesheet" href="public/assets/css/components/footer.css">
    <link rel="stylesheet" href="public/assets/css/components/navbar.css">

    <style>
        <?php @include __DIR__ . '/../login/style.css'; ?>
    </style>
</head>

<body class="page page--auth">
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="auth-card">
                        <div class="text-center mb-4">
                            <div class="auth-icon mb-3">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <h2 class="auth-title">Đăng nhập</h2>
                        </div>

                        <form id="login-form"
                            action="<?= htmlspecialchars(rtrim((string) ($baseUrl ?? ''), '/') . '/api/login', ENT_QUOTES, 'UTF-8') ?>"
                            method="post">
                            <?php if (function_exists('csrf_field')) {
                                echo csrf_field();
                            } ?>

                            <!-- Tài khoản -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Tài khoản
                                </label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Nhập email hoặc số điện thoại của bạn" required autocomplete="email">
                            </div>

                            <!-- Mật khẩu -->
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Mật khẩu
                                </label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Nhập mật khẩu" required autocomplete="current-password">
                                    <button type="button" class="password-toggle" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Ghi nhớ & Quên mật khẩu -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a href="#" class="forgot-link">Quên mật khẩu?</a>
                            </div>

                            <!-- Nút đăng nhập -->
                            <button type="submit" class="btn btn-gradient w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>

                            <!-- Form feedback -->
                            <div class="form-feedback mt-3" id="login-feedback"></div>
                        </form>

                        <!-- Liên kết đăng ký -->
                        <div class="text-center mt-4">
                            <p class="mb-0 small">Chưa có tài khoản? <a
                                    href="<?= htmlspecialchars($urls['register'] ?? '/register', ENT_QUOTES, 'UTF-8') ?>"
                                    class="signup-link">Đăng ký ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include BASE_PATH . '/app/Views/partials/_footer.php'; ?>
    <div id="vanta-bg" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.net.min.js"></script>
    <script>
        // Khởi tạo Vanta Net
        window.addEventListener('load', function () {
            if (typeof VANTA !== 'undefined') {
                VANTA.NET({
                    el: "#vanta-bg",
                    mouseControls: true,
                    touchControls: true,
                    gyroControls: false,
                    minHeight: 200.00,
                    minWidth: 200.00,
                    scale: 1.00,
                    scaleMobile: 1.00,
                    color: 0x10BCD3,
                    backgroundColor: 0x0a1428,
                    points: 15.00,
                    maxDistance: 20.00,
                    spacing: 15.00
                });
            }
        });

        // Ẩn/hiện mật khẩu
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
    <script><?php @include __DIR__ . '/script.js'; ?></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>