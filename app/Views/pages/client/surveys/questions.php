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
    <title>Trả lời khảo sát</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/components/navbar.css') ?>">
    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/client/survey-questions.css') ?>">
    <link rel="stylesheet" href="<?= $__mk($__base, 'public/assets/css/components/footer.css') ?>">

</head>

<body>
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

    <main class="questions-container" style="margin-top: 8rem;">
        <div class="survey-content">
            <div id="survey-header" class="survey-header mb-4"></div>

            <form id="survey-form">
                <div id="questions-container"></div>

                <div class="survey-buttons">
                    <button type="button" class="btn btn-outline-secondary" id="btn-prev" disabled>
                        <i class="fas fa-arrow-left me-2"></i>Câu trước
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-next">
                        Câu tiếp theo<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <button type="submit" class="btn btn-success d-none" id="btn-submit">
                        <i class="fas fa-check me-2"></i>Nộp bài
                    </button>
                </div>
            </form>

            <div class="survey-progress">
                <div class="progress-bar-container">
                    <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
                </div>
                <div class="progress-text">
                    <span id="progress-current">1</span> / <span id="progress-total">0</span>
                </div>
            </div>
        </div>
    </main>

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script>
        let surveyData = null;
        let currentQuestion = 0;
        let answers = {};

        document.addEventListener('DOMContentLoaded', async function () {
            // Extract survey ID from URL path: /surveys/{id}/questions
            const pathParts = window.location.pathname.split('/');
            const surveyId = pathParts[2]; // /surveys/2/questions -> [0]='', [1]='surveys', [2]='2', [3]='questions'

            if (!surveyId || !surveyId.match(/^\d+$/)) {
                window.location.href = '/surveys';
                return;
            }

            await loadSurvey(surveyId);
        });

        async function loadSurvey(surveyId) {
            try {
                const response = await fetch(`/api/surveys/show?id=${surveyId}`);
                const result = await response.json();

                if (result.error) {
                    showError('Không thể tải khảo sát');
                    return;
                }

                surveyData = result.data;
                initializeSurvey();
            } catch (error) {
                console.error('Lỗi:', error);
                showError('Có lỗi xảy ra khi tải khảo sát');
            }
        }

        function initializeSurvey() {
            if (!surveyData || !surveyData.questions || surveyData.questions.length === 0) {
                showError('Khảo sát không có câu hỏi');
                return;
            }

            // Initialize answers object
            surveyData.questions.forEach(q => {
                answers[q.id] = null;
            });

            // Update progress
            document.getElementById('progress-total').textContent = surveyData.questions.length;

            // Render header
            renderHeader();

            // Render first question
            renderQuestion(0);

            // Setup event listeners
            setupEventListeners();
        }

        function renderHeader() {
            const header = document.getElementById('survey-header');
            header.innerHTML = `
                <div class="survey-title">
                    <h2>${escapeHtml(surveyData.tieuDe)}</h2>
                    <p class="text-muted">${escapeHtml(surveyData.moTa)}</p>
                </div>
            `;
        }

        function renderQuestion(index) {
            currentQuestion = index;
            const question = surveyData.questions[index];

            if (!question) return;

            const container = document.getElementById('questions-container');
            container.innerHTML = `
                <div class="question-card">
                    <div class="question-number">Câu hỏi ${index + 1}</div>
                    <div class="question-text">${escapeHtml(question.noiDungCauHoi)}</div>
                    
                    <div id="options-container" class="options-container mt-4">
                        ${renderOptions(question)}
                    </div>
                </div>
            `;

            // Update progress
            document.getElementById('progress-current').textContent = index + 1;
            document.getElementById('progress-bar').style.width = ((index + 1) / surveyData.questions.length * 100) + '%';

            // Update button states
            updateButtonStates();
        }

        function renderOptions(question) {
            // Vì hiện tại không có bảng answers trong DB
            // Tạm thời tạo mock answers dựa trên loai câu hỏi
            const mockAnswers = {
                'CH001': [
                    { id: 1, noiDungDapAn: 'Buổi sáng' },
                    { id: 2, noiDungDapAn: 'Buổi chiều' },
                    { id: 3, noiDungDapAn: 'Buổi tối' },
                    { id: 4, noiDungDapAn: 'Trước khi ngủ' }
                ],
                'CH002': [
                    { id: 5, noiDungDapAn: 'Tiểu thuyết' },
                    { id: 6, noiDungDapAn: 'Tự truyện' },
                    { id: 7, noiDungDapAn: 'Sách khoa học' },
                    { id: 8, noiDungDapAn: 'Sách triết học' }
                ]
            };

            const loaiCauHoi = question.loaiCauHoi;
            const inputType = loaiCauHoi === 'single_choice' ? 'radio' : 'checkbox';
            const questionId = question.id;

            // Lấy answers từ mock hoặc trả về empty
            const answers = mockAnswers[question.maCauHoi] || [];

            let html = '';

            if (answers && answers.length > 0) {
                answers.forEach((answer, idx) => {
                    const checked = answers[questionId] === answer.id ? 'checked' : '';
                    html += `
                        <div class="option-item">
                            <input 
                                type="${inputType}" 
                                id="answer_${answer.id}" 
                                name="question_${questionId}" 
                                value="${answer.id}"
                                class="form-check-input"
                                ${checked}
                            >
                            <label for="answer_${answer.id}" class="form-check-label">
                                ${escapeHtml(answer.noiDungDapAn)}
                            </label>
                        </div>
                    `;
                });
            }

            return html;
        }

        function setupEventListeners() {
            document.getElementById('btn-next').addEventListener('click', function (e) {
                e.preventDefault();

                // Save current answer
                saveCurrentAnswer();

                // Move to next
                if (currentQuestion < surveyData.questions.length - 1) {
                    renderQuestion(currentQuestion + 1);
                }
            });

            document.getElementById('btn-prev').addEventListener('click', function (e) {
                e.preventDefault();

                // Save current answer
                saveCurrentAnswer();

                // Move to previous
                if (currentQuestion > 0) {
                    renderQuestion(currentQuestion - 1);
                }
            });

            document.getElementById('survey-form').addEventListener('submit', function (e) {
                e.preventDefault();
                submitSurvey();
            });
        }

        function saveCurrentAnswer() {
            const question = surveyData.questions[currentQuestion];
            const loaiCauHoi = question.loaiCauHoi;
            const inputName = `question_${question.id}`;

            if (loaiCauHoi === 'single_choice') {
                const checked = document.querySelector(`input[name="${inputName}"]:checked`);
                answers[question.id] = checked ? checked.value : null;
            } else if (loaiCauHoi === 'multiple_choice') {
                const checked = document.querySelectorAll(`input[name="${inputName}"]:checked`);
                answers[question.id] = Array.from(checked).map(c => c.value);
            }
        }

        function updateButtonStates() {
            const isFirst = currentQuestion === 0;
            const isLast = currentQuestion === surveyData.questions.length - 1;

            document.getElementById('btn-prev').disabled = isFirst;
            document.getElementById('btn-next').classList.toggle('d-none', isLast);
            document.getElementById('btn-submit').classList.toggle('d-none', !isLast);
        }

        async function submitSurvey() {
            try {
                // Save last answer
                saveCurrentAnswer();

                // Extract survey ID from URL path
                const pathParts = window.location.pathname.split('/');
                const surveyId = pathParts[2];

                const response = await fetch(`/api/surveys/${surveyId}/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ answers: answers })
                });

                const result = await response.json();

                if (result.error) {
                    showError(result.message || 'Lỗi khi nộp bài');
                    return;
                }

                // Show success message
                alert('Nộp bài thành công!');
                window.location.href = '/surveys';

            } catch (error) {
                console.error('Lỗi:', error);
                showError('Có lỗi xảy ra khi nộp bài');
            }
        }

        function showError(message) {
            const container = document.getElementById('questions-container');
            container.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <strong>Lỗi:</strong> ${escapeHtml(message)}
                    <br><br>
                    <a href="/surveys" class="btn btn-primary mt-3">Quay lại danh sách</a>
                </div>
            `;
        }

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
    </script>
</body>

</html>