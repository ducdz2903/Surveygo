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
    <title><?= htmlspecialchars(($appName ?? 'PHP Application') . ' - Tính năng', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
    <style><?php @include __DIR__ . '/style.css'; ?></style>
</head>
<body class="page page--features">
    <?php include BASE_PATH . '/app/Views/partials/navbar.php'; ?>

    <main class="page-content py-5">
        <div class="container py-4">
            <h1 class="display-6 fw-bold text-gradient mb-4">Bộ tính năng chính</h1>
            <p class="text-secondary fs-5 mb-5">
                Hệ thống được thiết kế để mở rộng dễ dàng. Mỗi tính năng bên dưới đều có mô tả ngắn và liên kết khu vực liên quan.
            </p>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card feature-card h-100">
                        <div class="card-body">
                            <h2 class="h4 fw-semibold mb-3">Basic Accounts</h2>
                            <p class="text-secondary">
                                Cung cap cac form dang ky/dang nhap mau (khong dung token).</p>
                            <ul class="list-unstyled text-secondary">
                                <li>- Su dung <code>password_hash</code> de bam mat khau</li>
                                <li>- Tra ve thong tin nguoi dung sau dang nhap</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card feature-card h-100">
                        <div class="card-body">
                            <h2 class="h4 fw-semibold mb-3">Routing nhẹ</h2>
                            <p class="text-secondary">
                                Hỗ trợ đầy đủ HTTP verb và pipeline middleware linh hoạt.
                            </p>
                            <ul class="list-unstyled text-secondary">
                                <li>- Định nghĩa route bằng closure hoặc controller@method</li>
                                <li>- Khởi tạo controller theo namespace <code>App\Controllers</code></li>
                                <li>- Xử lý lỗi 404/500 cơ bản</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include BASE_PATH . '/app/Views/partials/footer.php'; ?>
    <script><?php @include __DIR__ . '/script.js'; ?></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
