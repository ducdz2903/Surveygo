<?php
/** @var string $pageTitle */
/** @var string $description */

$pageTitle = $pageTitle ?? 'Khảo Sát API Dashboard';
$description = $description ?? '';
$baseUrl = $baseUrl ?? '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle . ' - ' . ($appName ?? 'PHP Application'), ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="<?= htmlspecialchars(rtrim((string) $baseUrl, '/'), ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
</head>

<body class="page page--dashboard">
    <?php include BASE_PATH . '/app/Views/components/admin/_sidebar.php'; ?>

    <main class="page-content py-5">
        <div class="container py-4">
            <h1 class="h3 fw-bold mb-2"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if ($description !== ''): ?>
                <p class="text-secondary mb-4"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <p class="text-muted mb-0">Các endpoint API dùng để kiểm thử và demo sẽ được liệt kê ở đây.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">API Endpoints</h5>
                            <ul>
                                <li><code>POST /api/register</code> — tạo người dùng</li>
                                <li><code>POST /api/login</code> — đăng nhập</li>
                                <li><code>GET /api/surveys</code> — lấy danh sách khảo sát</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include BASE_PATH . '/app/Views/partials/_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>