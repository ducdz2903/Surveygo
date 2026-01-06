<main class="page-content pt-5 mt-5 pb-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-3">Danh sách Khảo sát <span id="survey-count">(0)</span></h1>
                <p class="lead text-muted">Tham gia các khảo sát để kiếm điểm và đổi thưởng</p>
            </div>
        </div>

        <!-- bộ lọc -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="search-input" placeholder="Tìm kiếm khảo sát...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="status-filter">
                    <option value="">Tất cả</option>
                    <option value="hot">Hot 🔥</option>
                    <option value="new">Chưa hoàn thành ⏳</option>
                    <option value="old">Đã hoàn thành ✅</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary" id="btn-reset-filters">
                    <i class="fas fa-redo me-2"></i>Xóa bộ lọc
                </button>
            </div>
        </div>

        <!-- bảng khảo sát -->
        <div class="row g-4 mb-4" id="surveys-container">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        </div>

    </div>
</main>


<script>
    let currentPage = 1;
    const pageSize = 6;
    let currentFilters = {};
    const baseUrl = <?= json_encode(rtrim($baseUrl ?? '', '/')) ?>;
    const apiUrl = (path) => {
        const base = (baseUrl || '').replace(/\/+$/, '');
        const p = '/' + String(path || '').replace(/^\/+/, '');
        return base ? (base + p) : p;
    };

    function ensureEventFilterElement() {
        let select = document.getElementById('event-filter');
        if (select) {
            return select;
        }

        const statusFilter = document.getElementById('status-filter');
        if (!statusFilter) {
            return null;
        }

        const statusCol = statusFilter.closest('.col-md-3') || statusFilter.parentElement;
        if (!statusCol || !statusCol.parentElement) {
            return null;
        }

        const eventCol = document.createElement('div');
        eventCol.className = 'col-md-3';
        eventCol.innerHTML = '\
                <select class="form-select" id="event-filter">\
                    <option value=\"\">T\u1ea5t c\u1ea3 s\u1ef1 ki\u1ec7n</option>\
                    <option value=\"standalone\">Kh\u1ea3o s\u00e1t ri\u00eang 📋</option>\
                </select>\
            ';
        statusCol.insertAdjacentElement('afterend', eventCol);

        return document.getElementById('event-filter');
    }

    async function loadEventOptions(initialEventId) {
        const select = ensureEventFilterElement();
        if (!select) {
            return;
        }

        select.innerHTML = '<option value=\"\">T\u1ea5t c\u1ea3 s\u1ef1 ki\u1ec7n đã tham gia</option><option value=\"standalone\">Kh\u1ea3o s\u00e1t ri\u00eang 📋</option>';

        try {
            const params = new URLSearchParams({ page: 1, limit: 50 });
            const response = await fetch(apiUrl(`/api/events?${params.toString()}`), {
                headers: { 'Accept': 'application/json' }
            });
            const json = await response.json().catch(() => ({}));
            const allEvents = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);

            // Chỉ hiển thị các sự kiện người dùng đã tham gia
            const events = allEvents.filter(ev => ev.hasJoined === true);

            events.forEach(ev => {
                const opt = document.createElement('option');
                opt.value = ev.id;
                const code = ev.code ? ('#' + ev.code + ' - ') : '';
                opt.textContent = code + (ev.title || 'S\u1ef1 ki\u1ec7n');
                select.appendChild(opt);
            });

            if (initialEventId) {
                select.value = String(initialEventId);
            }
        } catch (error) {
            console.error('L\u1ed7i khi t\u1ea3i danh s\u00e1ch s\u1ef1 ki\u1ec7n:', error);
        }
    }

    // Tải danh sách khảo sát
    async function loadSurveys(page = 1, filters = {}) {
        try {
            // Lấy user_id từ localStorage
            const userJson = localStorage.getItem('app.user');
            let userId = null;
            if (userJson) {
                try {
                    const user = JSON.parse(userJson);
                    userId = user.id;
                } catch (e) {
                    console.warn('Cannot parse user from localStorage');
                }
            }

            const queryParams = new URLSearchParams({
                page: page,
                limit: pageSize,
                isQuickPoll: false,
                ...filters,
            });
            
            // Logic: 
            // - Nếu có filter maSuKien: lấy approved + event đó
            // - Nếu filter standalone: lấy published (không cần event)
            // - Mặc định: lấy cả approved+event VÀ published
            if (!filters.maSuKien && !filters.standalone) {
                // Mặc định: hiển thị cả 2 loại
                queryParams.set('clientView', 'true');
            } else if (filters.standalone) {
                // Khảo sát riêng: chỉ published
                queryParams.set('trangThai', 'published');
            } else if (filters.maSuKien) {
                // Theo sự kiện: approved + event
                queryParams.set('trangThai', 'approved');
            }

            // Thêm user_id vào query params nếu có
            if (userId) {
                queryParams.set('user_id', userId);
            }

            const response = await fetch(apiUrl(`/api/surveys?${queryParams}`));
            const result = await response.json();

            if (!result.error && result.data && result.meta) {
                currentPage = result.meta.page;
                currentFilters = filters;
                renderSurveys(result.data, result.meta);
            } else {
                document.getElementById('surveys-container').innerHTML =
                    '<div class="col-12 text-center"><p class="text-muted">Không có khảo sát nào.</p></div>';
            }
        } catch (error) {
            console.error('Lỗi khi tải khảo sát:', error);
            document.getElementById('surveys-container').innerHTML =
                '<div class="col-12 text-center"><p class="text-danger">Lỗi khi tải khảo sát.</p></div>';
        }
    }

    // Hiển thị các khảo sát
    function renderSurveys(surveys, meta) {
        const container = document.getElementById('surveys-container');

        if (surveys.length === 0) {
            container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted fs-5">Không có khảo sát nào phù hợp.</p></div>';
            return;
        }

        const badgeMap = {
            'hoạtĐộng': { class: 'badge-hot', icon: 'fas fa-fire', text: 'Hot' },
            'chờDuyệt': { class: '', icon: 'fas fa-star', text: 'Mới' },
        };

        container.innerHTML = surveys.map((survey) => {
            // Kiểm tra nếu user đã hoàn thành survey này
            let badge = null;
            let completedCheckmark = '';
            let buttonText = 'Bắt đầu';
            let buttonClass = 'btn btn-gradient mt-auto w-100';
            let buttonIcon = 'fas fa-play';

            if (survey.isCompleted) {
                // Đã hoàn thành - hiển thị icon dấu tích ở góc card
                completedCheckmark = '<i class="fas fa-check-circle" style="position: absolute; top: 15px; right: 15px; font-size: 24px; color: #28a745; z-index: 10;"></i>';
                buttonText = 'Xem lại';
                buttonClass = 'btn btn-outline-secondary mt-auto w-100';
                buttonIcon = 'fas fa-eye';
            } else {
                // Chưa hoàn thành - kiểm tra badge
                // Tính toán thời gian tạo
                const createdAt = new Date(survey.created_at);
                const now = new Date();
                const hoursDiff = (now - createdAt) / (1000 * 60 * 60);
                const isNew = hoursDiff < 24;

                // Ưu tiên: Hot > Mới (nếu < 24h) > Không có badge
                if (survey.trangThai === 'hoạtĐộng') {
                    badge = { class: 'badge-hot', icon: 'fas fa-fire', text: 'Hot' };
                } else if (isNew) {
                    badge = { class: '', icon: 'fas fa-star', text: 'Mới' };
                }
                // Nếu không Hot và không Mới (>24h) thì badge = null (không hiển thị)
            }

            // Chỉ hiển thị badge nếu có
            const badgeHtml = badge ? `<div class="survey-badge ${badge.class}">
                                <i class="${badge.icon} me-1"></i>${badge.text}
                            </div>` : '';

            return `
                    <div class="col-lg-4 col-md-6">
                        <div class="survey-card ${survey.isCompleted ? 'survey-completed' : ''}" style="position: relative;">
                            ${completedCheckmark}
                            ${badgeHtml}
                            <div class="survey-header">
                                <h5 class="survey-title">${survey.tieuDe}</h5>
                                <div class="survey-meta">
                                    <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+${survey.diemThuong || 50} điểm</span>
                                    <span><i class="fas fa-clock me-1"></i>~${survey.thoiLuongDuTinh || 10} phút</span>
                                </div>
                            </div>
                            <p class="survey-desc">${survey.moTa || 'Tham gia khảo sát này để kiếm điểm.'}</p>
                            <a href="/surveys/guide?id=${survey.id}" class="${buttonClass}">
                                <i class="${buttonIcon} me-1"></i>${buttonText}
                            </a>
                        </div>
                    </div>
                `;
        }).join('');

        const countEl = document.getElementById('survey-count');
        if (countEl) countEl.textContent = `(${meta.total})`;
        if (meta.totalPages > 1) {
            let pagHtml = `
                <div class="col-12 d-flex justify-content-center gap-2 mt-4">
                    ${meta.page > 1 ? `<button class="btn btn-sm btn-outline-primary" onclick="loadSurveys(${meta.page - 1})">← Trước</button>` : ''}
                    <span class="btn btn-sm btn-light disabled">Trang ${meta.page}/${meta.totalPages}</span>
                    ${meta.page < meta.totalPages ? `<button class="btn btn-sm btn-outline-primary" onclick="loadSurveys(${meta.page + 1})">Tiếp →</button>` : ''}
                </div>
            `;
            container.innerHTML += pagHtml;
        }
    }


    document.getElementById('search-input').addEventListener('input', function (e) {
        const filters = { ...currentFilters };
        if (e.target.value.trim()) {
            filters.search = e.target.value.trim();
        } else {
            delete filters.search;
        }
        loadSurveys(1, filters);
    });

    document.getElementById('status-filter').addEventListener('change', function (e) {
        const filters = { ...currentFilters };
        const value = e.target.value;

        // Reset các bộ lọc trước
        delete filters.trangThai;
        delete filters.sortBy;
        delete filters.isCompleted;
        delete filters.standalone;

        if (value === 'hot') {
            // Hot: sắp xếp theo số lượng hoàn thành (số lượng người dùng duy nhất)
            filters.sortBy = 'hot';
        } else if (value === 'new') {
            // Chưa hoàn thành: hiển thị các khảo sát chưa hoàn thành, sắp xếp theo mới nhất
            filters.isCompleted = 'false';
            filters.sortBy = 'newest';
        } else if (value === 'old') {
            // Đã hoàn thành: hiển thị các khảo sát đã hoàn thành, sắp xếp theo mới nhất
            filters.isCompleted = 'true';
            filters.sortBy = 'newest';
        }
        // Nếu giá trị rỗng (Tất cả), tất cả bộ lọc đã được xóa

        loadSurveys(1, filters);
    });

    document.getElementById('btn-reset-filters').addEventListener('click', function () {
        document.getElementById('search-input').value = '';
        document.getElementById('status-filter').value = '';
        const eventFilterEl = document.getElementById('event-filter');
        if (eventFilterEl) {
            eventFilterEl.value = '';
        }
        currentFilters = {};
        loadSurveys(1, {});
    });

    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const eventId = params.get('maSuKien');
        const initialFilters = {};
        if (eventId) {
            initialFilters.maSuKien = eventId;
        }
        currentFilters = initialFilters;

        const eventFilterEl = ensureEventFilterElement();
        loadEventOptions(eventId);

        if (eventFilterEl) {
            if (eventId) {
                eventFilterEl.value = eventId;
            }

            eventFilterEl.addEventListener('change', function (e) {
                const filters = { ...currentFilters };
                const value = e.target.value;
                
                // Reset các bộ lọc sự kiện
                delete filters.maSuKien;
                delete filters.standalone;
                
                if (value === 'standalone') {
                    // Khảo sát riêng: các khảo sát không có sự kiện (maSuKien là null)
                    filters.standalone = 'true';
                } else if (value) {
                    filters.maSuKien = value;
                }
                
                currentFilters = filters;
                loadSurveys(1, filters);
            });
        }

        loadSurveys(1, initialFilters);
    });
</script>