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
    <link rel="stylesheet" href="<?= htmlspecialchars(($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
    <style><?php @include __DIR__ . '/style.css'; ?></style>
</head>
<body class="page page--auth">
    <?php include BASE_PATH . '/app/Views/partials/navbar.php'; ?>

    <main class="page-content py-5">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="glass-card">
                        <h1 class="h3 fw-bold text-gradient mb-3">Đăng nhập</h1>
                        <p class="text-secondary">
                            Nhập email và mật khẩu đã đăng ký để truy cập hệ thống.
                        </p>
                        <form id="login-form">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="user@example.com" required autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="******" required autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">Đăng nhập</button>
                            <div class="form-feedback mt-3" id="login-feedback"></div>
                        </form>
                        <div class="mt-3 text-secondary">
                            Chưa có tài khoản? <a class="link-primary text-decoration-none" href="<?= htmlspecialchars($urls['register'] ?? '/register', ENT_QUOTES, 'UTF-8') ?>">Đăng ký ngay</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include BASE_PATH . '/app/Views/partials/footer.php'; ?>

    <script>
        window.AppConfig = {
            endpoints: {
                login: '<?= htmlspecialchars(rtrim((string)($baseUrl ?? ''), '/'), ENT_QUOTES, 'UTF-8') ?>/api/login'
            }
        };
    </script>
    <script><?php @include __DIR__ . '/script.js'; ?></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
