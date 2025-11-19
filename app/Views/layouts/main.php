<?php
/**
 * Layout chính (main)
 * Sử dụng khi bạn muốn bọc nội dung trang trong layout chuẩn (navbar + footer).
 *
 * Sử dụng ví dụ trong controller/view:
 *   echo $this->view('layouts/main', ['title' => 'Tiêu đề', 'content' => $this->view('pages/home/landing', $data)]);
 *
 * Biến đầu vào:
 * - $title (string) : Tiêu đề trang
 * - $content (string) : HTML của trang con
 * - $appName, $baseUrl, $urls đều có thể được truyền vào
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(rtrim((string)$baseUrl, '/'), ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
</head>
<body class="app-layout">
    <?php include BASE_PATH . '/app/Views/partials/_navbar.php'; ?>

    <main class="page-content">
        <?= $content ?>
    </main>

    <?php include BASE_PATH . '/app/Views/partials/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars(rtrim((string)$baseUrl, '/'), ENT_QUOTES, 'UTF-8') ?>/assets/js/app.js"></script>
</body>
</html>
