<?php
/**
 * Layout cho trang auth (login / register)
 *
 * Biến đầu vào:
 * - $title (string)
 * - $content (string) : HTML của trang con
 * - $baseUrl, $appName có thể được truyền vào
 */
$title = $title ?? ($appName ?? 'PHP Application');
$content = $content ?? '';
$baseUrl = $baseUrl ?? '';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" href="<?= htmlspecialchars(rtrim((string) $baseUrl, '/'), ENT_QUOTES, 'UTF-8') ?>/Asset/favIconSurveyGo_16x16.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(rtrim((string)$baseUrl, '/'), ENT_QUOTES, 'UTF-8') ?>/assets/css/auth.css">
</head>
<body class="auth-layout d-flex align-items-center justify-content-center">
    <main class="container">
        <?= $content ?>
    </main>

    <?php include BASE_PATH . '/app/Views/partials/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars(rtrim((string)$baseUrl, '/'), ENT_QUOTES, 'UTF-8') ?>/assets/js/auth.js"></script>
</body>
</html>
