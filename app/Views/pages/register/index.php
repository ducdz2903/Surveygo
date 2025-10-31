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
    <title><?= htmlspecialchars(($appName ?? 'PHP Application') . ' - Đăng ký', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
    <style><?php @include __DIR__ . '/style.css'; ?></style>
</head>
<body class="page page--auth">
    <?php include BASE_PATH . '/app/Views/partials/navbar.php'; ?>

    <main class="page-content py-5">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="glass-card">
                        <h1 class="h3 fw-bold text-gradient mb-3">Đăng ký tài khoản</h1>
                        <p class="text-secondary">Điền thông tin bên dưới để tạo người dùng mới.</p>
                        <form id="register-form" action="<?= htmlspecialchars(rtrim((string)($baseUrl ?? ''), '/') . '/api/register', ENT_QUOTES, 'UTF-8') ?>" method="post">
                            <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ tên</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Nguyễn Văn A" required autocomplete="name">
                            </div>
                            <div class="mb-3">
                                <label for="register-email" class="form-label">Email</label>
                                <input type="email" class="form-control form-control-lg" id="register-email" name="email" placeholder="user@example.com" required autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label for="register-password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control form-control-lg" id="register-password" name="password" placeholder="******" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">Đăng ký</button>
                            <div class="form-feedback mt-3" id="register-feedback"></div>
                        </form>
                        <div class="mt-3 text-secondary">
                            Đã có tài khoản? <a class="link-primary text-decoration-none" href="<?= htmlspecialchars($urls['login'] ?? '/login', ENT_QUOTES, 'UTF-8') ?>">Đăng nhập ngay</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include BASE_PATH . '/app/Views/partials/footer.php'; ?>

    <!-- Không còn dùng REST endpoint cho đăng ký; form POST trực tiếp -->
    <script><?php @include __DIR__ . '/script.js'; ?></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
