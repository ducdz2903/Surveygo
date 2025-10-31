<?php
/** @var string $appName */
/** @var array $urls */

$appName = $appName ?? 'PHP Application';
$urls = $urls ?? [];

// Ensure URLs have absolute base prefix even if controller didn't pass them.
$__base = rtrim((string)($baseUrl ?? ''), '/');
$__mk = static function (string $base, string $path): string {
    $p = '/' . ltrim($path, '/');
    return $base === '' ? $p : ($base . $p);
};
$urls['home'] = $urls['home'] ?? $__mk($__base, '/');
$urls['features'] = $urls['features'] ?? $__mk($__base, '/features');
$urls['login'] = $urls['login'] ?? $__mk($__base, '/login');
$urls['register'] = $urls['register'] ?? $__mk($__base, '/register');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
    <style><?php @include __DIR__ . '/style.css'; ?></style>
</head>
<body class="page page--home">
    <?php include BASE_PATH . '/app/Views/partials/navbar.php'; ?>

    <main class="page-content py-5">
        <section class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge rounded-pill text-bg-primary-subtle text-primary px-3 py-2 mb-3">MVC</span>
                    <h1 class="display-5 fw-bold text-gradient mb-3">Khởi đầu nhanh với ứng dụng MVC</h1>
                    <p class="lead text-secondary mb-4">
                        Dự án mẫu thuần PHP với Router đơn giản, Controller rõ ràng và giao diện landing.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a class="btn btn-outline-primary btn-lg" href="<?= htmlspecialchars($urls['features'], ENT_QUOTES, 'UTF-8') ?>">Xem tính năng</a>
                        <a class="btn btn-primary btn-lg" href="<?= htmlspecialchars($urls['login'], ENT_QUOTES, 'UTF-8') ?>">Đăng nhập</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card glass-card p-4">
                        <h2 class="h5 fw-semibold mb-3">Tổng quan</h2>
                        <ul class="list-unstyled mb-4 text-secondary">
                            <li class="mb-2">- API đăng ký/đăng nhập sẵn sàng</li>
                            <li class="mb-2">- Đặt trong <code>htdocs</code>, hỗ trợ XAMPP</li>
                        </ul>
                        <div class="d-grid gap-2">
                            <a class="btn btn-soft-primary" href="<?= htmlspecialchars($urls['register'], ENT_QUOTES, 'UTF-8') ?>">Bắt đầu sử dụng</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-body-light border-top border-bottom py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <h3 class="h5 fw-semibold mb-2">Cấu trúc rõ ràng</h3>
                                <p class="text-secondary mb-0">
                                    Thư mục <code>app/</code> tổ chức Controller, Model rõ ràng, dễ hiểu và tùy biến.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <h3 class="h5 fw-semibold mb-2">Tự động migrate</h3>
                                <p class="text-secondary mb-0">
                                    Lần khởi động đầu, hệ thống tự tạo bảng <code>users</code> và kết nối MySQL theo cấu hình.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <h3 class="h5 fw-semibold mb-2">Landing hiện đại</h3>
                                <p class="text-secondary mb-0">
                                    Trang chủ, Tính năng, Đăng ký/Đăng nhập tách riêng giúp trình bày chuyên nghiệp.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include BASE_PATH . '/app/Views/partials/footer.php'; ?>
    <script><?php @include __DIR__ . '/script.js'; ?></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>