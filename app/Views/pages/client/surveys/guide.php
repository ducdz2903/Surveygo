<?php
/** @var string $appName */
/** @var array $urls */
/** @var string $baseUrl */

$appName = $appName ?? 'Surveygo';
$urls = $urls ?? [];
$baseUrl = $baseUrl ?? '';

$__base = rtrim((string) $baseUrl, '/');
$__mk = static function (string $base, string $path): string {
    $p = '/' . ltrim($path, '/');
    return $base === '' ? $p : ($base . $p);
};
$urls['home'] = $urls['home'] ?? $__mk($__base, '/');
$urls['login'] = $urls['login'] ?? $__mk($__base, '/login');
$urls['register'] = $urls['register'] ?? $__mk($__base, '/register');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hướng dẫn khảo sát</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/components/navbar.css') ?>">
    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/client/survey-guide.css') ?>">
    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/components/footer.css') ?>">

</head>

<body>
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

    <main class="guide-container" style="margin-top: 8rem;">
        <div id="survey-guide">
            <div class="loading">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p>Đang tải thông tin khảo sát...</p>
            </div>
        </div>
        <div id="guide-alert-container" style="display: none;"></div>
    </main>

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const params = new URLSearchParams(window.location.search);
            const surveyId = params.get('id');

            if (!surveyId) {
                document.getElementById('survey-guide').innerHTML = `
                    <div class="error-message">
                        <strong>Lỗi:</strong> Không tìm thấy khảo sát. <a href="/surveys">Quay lại</a>
                    </div>
                `;
                return;
            }

            try {
                const response = await fetch(`/api/surveys/show?id=${surveyId}`);
                const result = await response.json();

                if (result.error) {
                    document.getElementById('survey-guide').innerHTML = `
                        <div class="error-message">
                            <strong>Lỗi:</strong> ${result.message}
                        </div>
                    `;
                    return;
                }

                const survey = result.data;
                // Use thoiLuongDuTinh from API, fallback to 5 if not available
                const estimatedTime = survey.thoiLuongDuTinh || 5;

                const guideHTML = `
                    <div class="guide-header">
                        <div class="guide-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h2 class="guide-title">${escapeHtml(survey.tieuDe || 'Khảo sát')}</h2>
                            <p class="text-muted mb-0">${escapeHtml(survey.moTa || 'Khảo sát')}</p>
                        </div>
                    </div>

                    <div class="guide-info">
                        <div class="info-item">
                            <div class="info-label">Độ dài</div>
                            <div class="info-value">${estimatedTime}</div>
                            <div class="text-muted">phút</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Số câu hỏi</div>
                            <div class="info-value">${survey.soLuongCauHoi || 0}</div>
                            <div class="text-muted">câu</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Điểm thưởng</div>
                            <div class="info-value">${survey.diemThuong || 0}</div>
                            <div class="text-muted">điểm</div>
                        </div>
                    </div>

                    <div class="guide-notes">
                        <h5><i class="fas fa-info-circle"></i> Hướng dẫn</h5>
                        <p><strong>Điểm sẽ thường được cộng ngay sau khi bạn hoàn thành khảo sát.</strong></p>
                        
                        <p>Chúng tôi mong bạn thông cảm rằng, việc tham gia các cuộc khảo sát này có thể xuất hiện những khó khăn do các lỗi như gián đoạn trong quá trình tham gia, tùy thuộc vào môi trường mạng và thiết bị.</p>
                        
                        <p><strong>Lưu ý quan trọng:</strong></p>
                        <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                            <li>Nếu bạn đóng trang khảo sát giữa chừng, bạn có thể sẽ không thể tiếp tục trả lời khảo sát.</li>
                            <li>Nếu bạn không đọc kỹ câu hỏi hoặc trả lời không nhất quán, cuộc khảo sát có thể bị dừng lại hoặc phần thưởng điểm có thể bị hủy do các vấn đề về chất lượng câu trả lời.</li>
                            <li>Nếu bạn gặp bất kỳ vấn đề nào khi tham gia khảo sát, vui lòng chụp ảnh màn hình và liên hệ với bộ phận Hỗ trợ.</li>
                        </ul>
                    </div>

                    <div class="guide-actions">
                        <button class="btn btn-outline-secondary" onclick="history.back()">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </button>
                        <button class="btn btn-primary" onclick="startSurvey(${surveyId})">
                            <i class="fas fa-play me-2"></i>Bắt đầu khảo sát
                        </button>
                    </div>
                `;

                document.getElementById('survey-guide').innerHTML = guideHTML;

            } catch (error) {
                console.error('Lỗi:', error);
                document.getElementById('survey-guide').innerHTML = `
                    <div class="error-message">
                        <strong>Lỗi:</strong> Không thể tải khảo sát. Vui lòng thử lại.
                    </div>
                `;
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        function showAlert(message, type = 'info', title = '') {
            const container = document.getElementById('guide-alert-container');

            const iconMap = {
                'success': 'fas fa-check-circle',
                'danger': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            };

            const titleMap = {
                'success': 'Thành công',
                'danger': 'Lỗi',
                'warning': 'Cảnh báo',
                'info': 'Thông báo'
            };

            const alertHTML = `
                <div class="guide-alert alert-${type}">
                    <i class="${iconMap[type]}"></i>
                    <div class="guide-alert-message">
                        ${title ? `<div class="guide-alert-title">${title}</div>` : ''}
                        <p class="guide-alert-text">${escapeHtml(message)}</p>
                    </div>
                </div>
            `;

            container.innerHTML = alertHTML;
            container.style.display = 'block';
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        async function startSurvey(surveyId) {
            // Get user from localStorage
            const userRaw = localStorage.getItem('app.user');
            const user = userRaw ? JSON.parse(userRaw) : null;

            if (!user || !user.id) {
                showAlert('Vui lòng đăng nhập để bắt đầu khảo sát', 'warning');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
                return;
            }

            try {
                // Check if user already submitted this survey
                const response = await fetch(`/api/surveys/${surveyId}/check-submission?userId=${user.id}`);
                const result = await response.json();

                if (result.data && result.data.hasSubmitted) {
                    showAlert('Bạn đã thực hiện khảo sát này rồi. Mỗi người chỉ được thực hiện một khảo sát một lần.', 'warning', 'Không thể tiếp tục');
                    return;
                }

                // If no submission found, proceed to survey directly
                window.location.href = `/surveys/${surveyId}/questions`;

            } catch (error) {
                console.error('Lỗi:', error);
                showAlert('Có lỗi xảy ra. Vui lòng thử lại.', 'danger', 'Lỗi');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>