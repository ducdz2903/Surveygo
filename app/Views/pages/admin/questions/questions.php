<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Câu hỏi</h4>
            <p class="text-muted mb-0">Ngân hàng câu hỏi và quản lý liên kết khảo sát</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Tạo câu hỏi mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="search-input" class="form-control border-start-0 ps-0" placeholder="Tìm nội dung câu hỏi...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filter-type">
                        <option value="">Tất cả loại câu hỏi</option>
                        <option value="single_choice">Một lựa chọn (Radio)</option>
                        <option value="multi_choice">Nhiều lựa chọn (Checkbox)</option>
                        <option value="text">Văn bản (Text)</option>
                        <option value="rating">Đánh giá (Rating)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filter-survey">
                        <option value="">Tất cả khảo sát</option>
                        <option value="KS001">Khảo sát thói quen đọc</option>
                        <option value="KS002">Đánh giá dịch vụ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-light w-100 border" onclick="resetFilters()">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card fade-in" style="animation-delay: 0.1s">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">#ID</th>
                            <th style="width: 40%;">Nội dung câu hỏi</th>
                            <th style="width: 150px;">Loại</th>
                            <th>Thuộc khảo sát</th>
                            <th style="width: 150px;">Ngày tạo</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="questions-table-body">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <span id="showing-count">0</span> / <span id="total-count">0</span> câu hỏi
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="pagination">
                        </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tạo câu hỏi mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="question-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="2" required placeholder="Nhập câu hỏi của bạn..."></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại câu hỏi</label>
                            <select class="form-select" id="modal-type-select">
                                <option value="single_choice">Một lựa chọn (Radio)</option>
                                <option value="multi_choice">Nhiều lựa chọn (Checkbox)</option>
                                <option value="text">Văn bản (Text)</option>
                                <option value="rating">Đánh giá (Star/Scale)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gán vào khảo sát</label>
                            <select class="form-select">
                                <option value="">-- Chọn khảo sát --</option>
                                <option value="KS001">Khảo sát thói quen đọc</option>
                                <option value="KS002">Đánh giá dịch vụ</option>
                            </select>
                        </div>
                    </div>
                    <div id="options-area" class="mb-3 border rounded p-3 bg-light">
                        <label class="form-label fw-bold small text-uppercase">Các lựa chọn trả lời</label>
                        <div id="options-list">
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus me-1"></i>Thêm lựa chọn
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Lưu câu hỏi</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. MOCK DATA ---
        const mockQuestions = [
            { id: 101, content: "Bạn dành bao nhiêu thời gian đọc sách mỗi ngày?", type: "single_choice", survey: "Khảo sát thói quen đọc", created_at: "2024-03-10" },
            { id: 102, content: "Thể loại sách yêu thích của bạn là gì? (Chọn nhiều)", type: "multi_choice", survey: "Khảo sát thói quen đọc", created_at: "2024-03-10" },
            { id: 103, content: "Vui lòng cho biết lý do bạn ít đọc sách?", type: "text", survey: "Khảo sát thói quen đọc", created_at: "2024-03-11" },
            { id: 104, content: "Bạn đánh giá thế nào về chất lượng thư viện?", type: "rating", survey: "Đánh giá dịch vụ", created_at: "2024-03-12" },
            { id: 105, content: "Tần suất bạn ghé thăm thư viện?", type: "single_choice", survey: "Đánh giá dịch vụ", created_at: "2024-03-12" },
            { id: 106, content: "Góp ý thêm của bạn để cải thiện dịch vụ", type: "text", survey: "Đánh giá dịch vụ", created_at: "2024-03-13" },
        ];

        let currentData = [...mockQuestions]; // Data after filter

        // --- 2. HELPERS ---
        const getTypeBadge = (type) => {
            const map = {
                'single_choice': { class: 'bg-primary', icon: 'fa-dot-circle', text: 'Radio' },
                'multi_choice': { class: 'bg-info', icon: 'fa-check-square', text: 'Checkbox' },
                'text': { class: 'bg-secondary', icon: 'fa-align-left', text: 'Text' },
                'rating': { class: 'bg-warning text-dark', icon: 'fa-star', text: 'Rating' }
            };
            const t = map[type] || { class: 'bg-secondary', icon: 'fa-question', text: type };
            return `<span class="badge ${t.class} bg-opacity-10 text-${t.class.replace('bg-', '')} border border-${t.class.replace('bg-', '')}">
                        <i class="fas ${t.icon} me-1"></i>${t.text}
                    </span>`;
        };

        // --- 3. RENDER FUNCTION ---
        function renderTable(data) {
            const tbody = document.getElementById('questions-table-body');
            const totalEl = document.getElementById('total-count');
            const showingEl = document.getElementById('showing-count');

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">Không tìm thấy câu hỏi nào phù hợp.</td></tr>`;
                totalEl.textContent = 0;
                showingEl.textContent = 0;
                return;
            }

            tbody.innerHTML = data.map(q => `
                <tr class="slide-in align-middle">
                    <td class="ps-4"><span class="text-muted small">#${q.id}</span></td>
                    <td>
                        <div class="fw-bold text-dark text-truncate" style="max-width: 350px;" title="${q.content}">${q.content}</div>
                    </td>
                    <td>${getTypeBadge(q.type)}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-poll-h me-1 text-muted"></i>${q.survey}
                        </span>
                    </td>
                    <td><small class="text-muted">${q.created_at}</small></td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light text-primary" onclick="editQuestion(${q.id})" title="Sửa"><i class="fas fa-pen"></i></button>
                        <button class="btn btn-sm btn-light text-danger" onclick="deleteQuestion(${q.id})" title="Xóa"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');

            totalEl.textContent = mockQuestions.length;
            showingEl.textContent = data.length;
            
            // Render Pagination (Simple Mock)
            document.getElementById('pagination').innerHTML = `
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            `;
        }

        // --- 4. FILTER LOGIC ---
        function applyFilters() {
            const search = document.getElementById('search-input').value.toLowerCase();
            const type = document.getElementById('filter-type').value;
            const survey = document.getElementById('filter-survey').options[document.getElementById('filter-survey').selectedIndex].text;

            currentData = mockQuestions.filter(q => {
                const matchSearch = q.content.toLowerCase().includes(search);
                const matchType = type === '' || q.type === type;
                const matchSurvey = document.getElementById('filter-survey').value === '' || q.survey.includes(survey); // Simple include check for mock

                return matchSearch && matchType && matchSurvey;
            });

            // Simulate loading delay
            const tbody = document.getElementById('questions-table-body');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>';
            
            setTimeout(() => {
                renderTable(currentData);
            }, 300);
        }

        // --- 5. EVENT LISTENERS ---
        document.getElementById('search-input').addEventListener('input', applyFilters);
        document.getElementById('filter-type').addEventListener('change', applyFilters);
        document.getElementById('filter-survey').addEventListener('change', applyFilters);
        
        // Modal Logic (Toggle Input Area based on Type)
        document.getElementById('modal-type-select').addEventListener('change', function() {
            const type = this.value;
            const optionsArea = document.getElementById('options-area');
            if (type === 'text' || type === 'rating') {
                optionsArea.style.display = 'none';
            } else {
                optionsArea.style.display = 'block';
            }
        });

        // Global functions for buttons
        window.resetFilters = function() {
            document.getElementById('search-input').value = '';
            document.getElementById('filter-type').value = '';
            document.getElementById('filter-survey').value = '';
            applyFilters();
        };

        window.openCreateModal = function() {
            const modal = new bootstrap.Modal(document.getElementById('questionModal'));
            document.getElementById('modalTitle').textContent = 'Tạo câu hỏi mới';
            document.getElementById('question-form').reset();
            document.getElementById('options-area').style.display = 'block';
            modal.show();
        };

        window.editQuestion = function(id) {
            // Find data
            const q = mockQuestions.find(item => item.id === id);
            if(q) {
                const modal = new bootstrap.Modal(document.getElementById('questionModal'));
                document.getElementById('modalTitle').textContent = 'Cập nhật câu hỏi #' + id;
                // Mock fill data logic here...
                modal.show();
            }
        };

        window.deleteQuestion = function(id) {
            if(confirm('Bạn có chắc chắn muốn xóa câu hỏi #' + id + '?')) {
                // Remove from data
                const index = mockQuestions.findIndex(q => q.id === id);
                if (index > -1) {
                   mockQuestions.splice(index, 1);
                   applyFilters(); // Re-render
                   alert('Đã xóa thành công');
                }
            }
        };

        // Init Load
        applyFilters();
    });
</script>