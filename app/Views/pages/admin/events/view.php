<?php
/**
 * Event detail page (admin)
 * Manage surveys attached to an event.
 */

$baseUrl = $baseUrl ?? '';
$eventId = (int) ($eventId ?? 0);

$__base = rtrim((string) $baseUrl, '/');
$__mk = static function (string $base, string $path): string {
    $p = '/' . ltrim($path, '/');
    return $base === '' ? $p : ($base . $p);
};
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary btn-sm" id="btn-back-events">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </button>
            <div>
                <p class="text-uppercase small text-muted mb-1">Chi tiết sự kiện</p>
                <h4 class="mb-0" id="event-title">Đang tải...</h4>
                <div class="text-muted small" id="event-meta">#<?= htmlspecialchars((string) $eventId, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span id="event-status-badge" class="badge bg-secondary-subtle text-secondary border">Đang tải...</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted small mb-1">Mã sự kiện</p>
                            <div class="fw-semibold" id="event-code">#<?= htmlspecialchars((string) $eventId, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Cập nhật</div>
                            <div class="fw-semibold" id="event-updated">-</div>
                        </div>
                    </div>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                        <span id="event-location">-</span>
                    </p>
                    <p class="text-muted mb-1">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        <span id="event-datetime">-</span>
                    </p>
                    <div class="d-flex flex-wrap gap-3 small mt-3">
                        <span><i class="fas fa-users me-2 text-primary"></i><span id="event-participants">0</span> tham gia</span>
                        <span><i class="fas fa-list-check me-2 text-secondary"></i><span id="event-survey-count">0</span> khảo sát</span>
                        <span><i class="fas fa-ticket me-2 text-warning"></i><span id="event-spins">0</span> lượt rút / lần</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Khảo sát thuộc sự kiện</h5>
                            <p class="text-muted small mb-0">Quản lý các khảo sát đã gắn với sự kiện này</p>
                        </div>
                        <button class="btn btn-primary d-none" type="button" id="btn-open-survey-library">
                            <i class="fas fa-plus me-1"></i>Thêm khảo sát
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3" style="width: 100px;">Mã</th>
                                    <th>Tiêu đề</th>
                                    <th style="width: 140px;">Trạng thái</th>
                                    <th style="width: 110px;">Điểm thưởng</th>
                                    <th style="width: 130px;">Ngày tạo</th>
                                    <th class="text-end pe-3" style="width: 160px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="event-surveys-body">
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal chọn khảo sát để gắn vào sự kiện -->
<div class="modal fade" id="surveyLibraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title"><i class="fas fa-list me-2"></i>Chọn khảo sát</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="survey-library-search" placeholder="Tìm kiếm khảo sát...">
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-secondary" id="btn-survey-library-reload">
                            <i class="fas fa-rotate me-1"></i>Tải lại
                        </button>
                    </div>
                </div>
                <div class="table-responsive border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 90px;">Mã</th>
                                <th>Tiêu đề</th>
                                <th style="width: 120px;">Trạng thái</th>
                                <th style="width: 120px;" class="text-end pe-3">Chọn</th>
                            </tr>
                        </thead>
                        <tbody id="survey-library-body">
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-2 mb-0">Chỉ hiển thị các khảo sát trạng thái đã duyệt / published.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= $__mk($__base, 'public/assets/js/toast-helper.js') ?>"></script>
<script src="<?= $__mk($__base, 'public/assets/js/modal-helper.js') ?>"></script>
<script>
    const eventId = <?= json_encode($eventId) ?>;
    const baseUrl = <?= json_encode($__base) ?>;
    const apiUrl = (pathWithLeadingSlash) => {
        const cleanBase = baseUrl ? baseUrl.replace(/\/+$/, '') : '';
        const cleanPath = pathWithLeadingSlash.startsWith('/') ? pathWithLeadingSlash : '/' + pathWithLeadingSlash;
        return cleanBase + cleanPath;
    };

    let isEventUpcoming = false;
    let surveyLibraryModal = null;

    // Trạng thái chỉnh sửa sự kiện (trên trang chi tiết)
    let eventEditMode = false;
    let eventCurrentStatus = 'upcoming';
    let originalStatusForConfirm = 'upcoming';
    let originalSpinsValue = 0;

    let eventEditFieldsContainer = null;
    let eventStatusSelect = null;
    let eventSpinsInput = null;
    let btnEventPrimary = null; // Chỉnh sửa / Lưu
    let btnEventCancel = null;  // Hủy chỉnh sửa

    const surveyLibraryBody = document.getElementById('survey-library-body');
    const surveyLibrarySearch = document.getElementById('survey-library-search');

    function formatDateTime(dateString) {
        if (!dateString) return '-';
        const d = new Date(dateString);
        if (Number.isNaN(d.getTime())) return dateString;
        return d.toLocaleString('vi-VN');
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const d = new Date(dateString);
        if (Number.isNaN(d.getTime())) return dateString;
        return d.toLocaleDateString('vi-VN');
    }

    function setEventStatusBadge(status) {
        const el = document.getElementById('event-status-badge');
        if (!el) return;
        let cls = 'badge ';
        let label = status;
        switch (status) {
            case 'upcoming':
                cls += 'bg-info text-dark';
                label = 'Sắp diễn ra';
                break;
            case 'ongoing':
                cls += 'bg-success';
                label = 'Đang diễn ra';
                break;
            case 'completed':
                cls += 'bg-secondary';
                label = 'Đã kết thúc';
                break;
            default:
                cls += 'bg-secondary';
        }
        el.className = cls;
        el.textContent = label;
    }

    function updateEventEditButtonsUI() {
        if (!btnEventPrimary || !btnEventCancel) return;
        btnEventCancel.classList.remove('d-none');
        if (eventEditMode) {
            btnEventPrimary.innerHTML = '<i class="fas fa-save me-1"></i>Lưu';
            btnEventCancel.disabled = false;
        } else {
            btnEventPrimary.innerHTML = '<i class="fas fa-edit me-1"></i>Chỉnh sửa';
            btnEventCancel.disabled = true;
        }
    }

    function setEventEditMode(enabled) {
        eventEditMode = !!enabled;
        if (eventEditFieldsContainer) {
            if (eventEditMode) eventEditFieldsContainer.classList.remove('d-none');
            else eventEditFieldsContainer.classList.add('d-none');
        }
        updateEventEditButtonsUI();

        if (eventEditMode && eventStatusSelect && eventSpinsInput) {
            eventStatusSelect.value = eventCurrentStatus || 'upcoming';
            eventSpinsInput.value = String(originalSpinsValue ?? 0);
            eventSpinsInput.disabled = eventCurrentStatus !== 'upcoming';
        }
    }

    function cancelEventEdit() {
        if (eventStatusSelect) {
            eventStatusSelect.value = eventCurrentStatus || 'upcoming';
        }
        if (eventSpinsInput) {
            eventSpinsInput.value = String(originalSpinsValue ?? 0);
            eventSpinsInput.disabled = eventCurrentStatus !== 'upcoming';
        }
        setEventEditMode(false);
    }

    function initEventEditUI() {
        const statusBadgeEl = document.getElementById('event-status-badge');
        if (statusBadgeEl && !btnEventPrimary) {
            const headerGroup = statusBadgeEl.parentElement;
            if (headerGroup) {
                btnEventPrimary = document.createElement('button');
                btnEventPrimary.type = 'button';
                btnEventPrimary.className = 'btn btn-outline-secondary btn-sm';
                btnEventPrimary.id = 'btn-event-edit';
                btnEventPrimary.innerHTML = '<i class="fas fa-edit me-1"></i>Chỉnh sửa';
                headerGroup.appendChild(btnEventPrimary);

                btnEventCancel = document.createElement('button');
                btnEventCancel.type = 'button';
                btnEventCancel.className = 'btn btn-outline-secondary btn-sm d-none';
                btnEventCancel.id = 'btn-event-cancel';
                btnEventCancel.innerHTML = '<i class="fas fa-times me-1"></i>Hủy';
                headerGroup.appendChild(btnEventCancel);
            }
        }

        const codeEl = document.getElementById('event-code');
        if (codeEl && !eventEditFieldsContainer) {
            let cardBody = codeEl.closest('.card-body');
            if (!cardBody) {
                let parent = codeEl.parentElement;
                while (parent && !parent.classList.contains('card-body')) {
                    parent = parent.parentElement;
                }
                cardBody = parent;
            }
            if (cardBody) {
                eventEditFieldsContainer = document.createElement('div');
                eventEditFieldsContainer.id = 'event-edit-fields';
                eventEditFieldsContainer.className = 'mt-3 d-none';
                eventEditFieldsContainer.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái sự kiện</label>
                        <select class="form-select form-select-sm" id="event-status-select">
                            <option value="upcoming">Sắp diễn ra</option>
                            <option value="ongoing">Đang diễn ra</option>
                            <option value="completed">Đã kết thúc</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Số lượt rút thăm mỗi lần tham gia</label>
                        <input type="number" class="form-control form-control-sm" id="event-spins-input" min="0" step="1">
                        <div class="form-text small text-muted">
                            Chỉ có thể thay đổi khi sự kiện đang ở trạng thái Sắp diễn ra.
                        </div>
                    </div>
                `;
                cardBody.appendChild(eventEditFieldsContainer);

                eventStatusSelect = eventEditFieldsContainer.querySelector('#event-status-select');
                eventSpinsInput = eventEditFieldsContainer.querySelector('#event-spins-input');

                if (eventStatusSelect) {
                    eventStatusSelect.addEventListener('change', () => {
                        const newStatus = eventStatusSelect.value || 'upcoming';
                        if (originalStatusForConfirm === 'upcoming' && newStatus !== 'upcoming') {
                            const confirmed = window.confirm(
                                'Sau khi thay đổi trạng thái khỏi "Sắp diễn ra", bạn sẽ không thể thay đổi được số lượt rút thăm nữa. Bạn có chắc chắn muốn tiếp tục?'
                            );
                            if (!confirmed) {
                                eventStatusSelect.value = originalStatusForConfirm || 'upcoming';
                                if (eventSpinsInput) {
                                    eventSpinsInput.disabled = originalStatusForConfirm !== 'upcoming';
                                }
                                return;
                            }
                        }
                        if (eventSpinsInput) {
                            eventSpinsInput.disabled = newStatus !== 'upcoming';
                        }
                    });
                }
            }
        }

        if (btnEventPrimary && !btnEventPrimary._bound) {
            btnEventPrimary.addEventListener('click', () => {
                if (!eventEditMode) {
                    setEventEditMode(true);
                } else {
                    handleEventSave();
                }
            });
            btnEventPrimary._bound = true;
        }

        if (btnEventCancel && !btnEventCancel._bound) {
            btnEventCancel.addEventListener('click', () => cancelEventEdit());
            btnEventCancel._bound = true;
        }

        updateEventEditButtonsUI();
    }

    async function handleEventSave() {
        if (!eventStatusSelect || !eventSpinsInput) {
            setEventEditMode(false);
            return;
        }

        const newStatus = eventStatusSelect.value || 'upcoming';
        let newSpins = parseInt(eventSpinsInput.value || '0', 10);
        if (!Number.isFinite(newSpins) || newSpins < 0) newSpins = 0;

        try {
            const res = await fetch(apiUrl('/api/events'), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    id: eventId,
                    trangThai: newStatus,
                    soLuotRutThamMoiLan: newSpins,
                }),
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok || json.error) {
                throw new Error(json.message || res.statusText);
            }
            const ev = json.data || {};

            if (typeof window.showToast === 'function') {
                window.showToast('success', 'Cập nhật sự kiện thành công.');
            }

            eventCurrentStatus = ev.status || newStatus;
            originalStatusForConfirm = eventCurrentStatus;
            originalSpinsValue = typeof ev.luckyWheelSpinsPerJoin === 'number'
                ? ev.luckyWheelSpinsPerJoin
                : newSpins;

            setEventStatusBadge(eventCurrentStatus);
            isEventUpcoming = (eventCurrentStatus === 'upcoming');

            const spinsTextEl = document.getElementById('event-spins');
            if (spinsTextEl) {
                spinsTextEl.textContent = String(originalSpinsValue);
            }

            const addBtn = document.getElementById('btn-open-survey-library');
            if (addBtn) {
                if (isEventUpcoming) addBtn.classList.remove('d-none');
                else addBtn.classList.add('d-none');
            }

            if (eventStatusSelect) eventStatusSelect.value = eventCurrentStatus;
            if (eventSpinsInput) {
                eventSpinsInput.value = String(originalSpinsValue);
                eventSpinsInput.disabled = eventCurrentStatus !== 'upcoming';
            }

            setEventEditMode(false);
        } catch (err) {
            console.error(err);
            if (typeof window.showToast === 'function') {
                window.showToast('error', 'Cập nhật sự kiện thất bại: ' + err.message);
            }
        }
    }

    function renderEventSurveys(surveys) {
        const tbody = document.getElementById('event-surveys-body');
        if (!tbody) return;

        if (!surveys || surveys.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open mb-2 display-6"></i><br>
                        Chưa có khảo sát nào được gắn với sự kiện này.
                    </td>
                </tr>`;
            document.getElementById('event-survey-count').textContent = '0';
            return;
        }

        document.getElementById('event-survey-count').textContent = surveys.length.toString();

        const statusBadgeMap = {
            approved: 'badge bg-success-subtle text-success border',
            published: 'badge bg-success-subtle text-success border',
            pending: 'badge bg-warning-subtle text-warning border',
            draft: 'badge bg-secondary-subtle text-secondary border',
            rejected: 'badge bg-danger-subtle text-danger border'
        };
        const statusTextMap = {
            approved: 'Đã duyệt',
            published: 'Đã xuất bản',
            pending: 'Chờ duyệt',
            draft: 'Nháp',
            rejected: 'Từ chối'
        };

        tbody.innerHTML = surveys.map(s => {
            const code = s.maKhaoSat || s.ma_khao_sat || s.id;
            const title = s.tieuDe || s.tieu_de || 'Không có tiêu đề';
            const status = s.trangThai || 'draft';
            const badgeCls = statusBadgeMap[status] || 'badge bg-secondary-subtle text-secondary border';
            const statusLabel = statusTextMap[status] || status;
            const points = s.diemThuong ?? s.points ?? 0;
            const created = s.created_at || s.createdAt;

            const detachBtn = isEventUpcoming
                ? `<button class="btn btn-sm btn-light text-danger" title="Gỡ khỏi sự kiện" onclick="detachSurvey(${s.id})">
                        <i class="fas fa-unlink"></i>
                   </button>`
                : '';

            return `
                <tr class="slide-in align-middle">
                    <td class="ps-3">
                        <span class="font-monospace text-dark">#${code}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-primary">${title}</div>
                    </td>
                    <td>
                        <span class="${badgeCls}">${statusLabel}</span>
                    </td>
                    <td>${points}</td>
                    <td><small class="text-muted">${formatDate(created)}</small></td>
                    <td class="text-end pe-3">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" title="Xem khảo sát" onclick="window.location.href='${apiUrl('/admin/surveys/view')}?id=${s.id}'">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${detachBtn}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function loadEventSurveys() {
        const tbody = document.getElementById('event-surveys-body');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </td>
                </tr>`;
        }
        try {
            const params = new URLSearchParams({ page: 1, limit: 50, maSuKien: eventId });
            const res = await fetch(apiUrl('/api/surveys?' + params.toString()), { headers: { 'Accept': 'application/json' } });
            const json = await res.json().catch(() => ({}));
            const data = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
            renderEventSurveys(data);
        } catch (err) {
            console.error(err);
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger">Lỗi khi tải danh sách khảo sát: ${err.message}</td></tr>`;
            }
        }
    }

    async function attachSurveyToEvent(surveyId) {
        if (!isEventUpcoming) {
            window.showToast('warning', 'Chỉ có thể thêm khảo sát khi sự kiện đang ở trạng thái Sắp diễn ra.');
            return;
        }
        try {
            const res = await fetch(apiUrl('/api/surveys'), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ id: surveyId, maSuKien: eventId }),
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok || json.error) {
                throw new Error(json.message || res.statusText);
            }
            window.showToast('success', 'Đã gắn khảo sát #' + surveyId + ' vào sự kiện.');
            loadEventSurveys();
        } catch (err) {
            console.error(err);
            window.showToast('error', 'Gắn khảo sát thất bại: ' + err.message);
        }
    }

    window.detachSurvey = async function(surveyId) {
        if (!isEventUpcoming) {
            window.showToast('warning', 'Không thể chỉnh sửa khảo sát khi sự kiện không còn ở trạng thái Sắp diễn ra.');
            return;
        }

        const doDetach = async () => {
            try {
                const res = await fetch(apiUrl('/api/surveys'), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ id: surveyId, maSuKien: null }),
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || json.error) {
                    throw new Error(json.message || res.statusText);
                }
                window.showToast('success', 'Đã gỡ khảo sát #' + surveyId + ' khỏi sự kiện.');
                loadEventSurveys();
            } catch (err) {
                console.error(err);
                window.showToast('error', 'Gỡ khảo sát thất bại: ' + err.message);
            }
        };

        if (window.ModalHelper && typeof window.ModalHelper.confirm === 'function') {
            ModalHelper.confirm({
                title: 'Gỡ khảo sát khỏi sự kiện',
                message: 'Bạn có chắc muốn gỡ khảo sát #' + surveyId + ' khỏi sự kiện?',
                type: 'warning',
                confirmText: 'Gỡ',
                cancelText: 'Hủy',
                onConfirm: doDetach,
            });
        } else if (confirm('Bạn có chắc muốn gỡ khảo sát #' + surveyId + ' khỏi sự kiện?')) {
            doDetach();
        }
    };

    async function loadSurveyLibrary() {
        if (!isEventUpcoming) {
            window.showToast('warning', 'Chỉ có thể thêm khảo sát khi sự kiện đang ở trạng thái Sắp diễn ra.');
            return;
        }

        const term = (surveyLibrarySearch?.value || '').trim();
        surveyLibraryBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </td>
            </tr>`;

        try {
            const params = new URLSearchParams({
                page: 1,
                limit: 20,
                trangThai: 'approved',
            });
            if (term) params.set('search', term);

            const res = await fetch(apiUrl('/api/surveys?' + params.toString()), {
                headers: { 'Accept': 'application/json' }
            });
            const json = await res.json().catch(() => ({}));
            const data = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);

            if (!data.length) {
                surveyLibraryBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open mb-2"></i><br>
                            Không tìm thấy khảo sát phù hợp.
                        </td>
                    </tr>`;
                return;
            }

            surveyLibraryBody.innerHTML = data.map(s => {
                const code = s.maKhaoSat || s.ma_khao_sat || s.id;
                const title = s.tieuDe || s.tieu_de || 'Không có tiêu đề';
                const status = s.trangThai || 'draft';
                const badgeCls = status === 'approved' || status === 'published'
                    ? 'badge bg-success-subtle text-success border'
                    : 'badge bg-secondary-subtle text-secondary border';
                const statusText = status === 'approved' || status === 'published' ? 'Đã duyệt' : status;

                return `
                    <tr class="align-middle">
                        <td><span class="font-monospace text-dark">#${code}</span></td>
                        <td>
                            <div class="fw-semibold text-primary">${title}</div>
                            <div class="small text-muted">
                                <span class="${badgeCls}">${statusText}</span>
                            </div>
                        </td>
                        <td>
                            <span class="${badgeCls}">${statusText}</span>
                        </td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="attachSurveyToEvent(${s.id})">
                                <i class="fas fa-link me-1"></i>Gắn vào
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (err) {
            console.error(err);
            surveyLibraryBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger">Lỗi khi tải danh sách khảo sát: ${err.message}</td></tr>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search || '');
        window.__eventDetailStartEditMode = (urlParams.get('mode') === 'edit');

        const backBtn = document.getElementById('btn-back-events');
        if (backBtn) {
            backBtn.addEventListener('click', () => {
                window.location.href = apiUrl('/admin/events');
            });
        }

        const openLibraryBtn = document.getElementById('btn-open-survey-library');
        if (openLibraryBtn) {
            openLibraryBtn.addEventListener('click', () => {
                if (!isEventUpcoming) {
                    window.showToast('warning', 'Chỉ có thể thêm khảo sát khi sự kiện đang ở trạng thái Sắp diễn ra.');
                    return;
                }
                const modalEl = document.getElementById('surveyLibraryModal');
                if (modalEl && window.bootstrap) {
                    surveyLibraryModal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                    surveyLibraryModal.show();
                    loadSurveyLibrary();
                }
            });
        }

        const reloadLibraryBtn = document.getElementById('btn-survey-library-reload');
        if (reloadLibraryBtn) {
            reloadLibraryBtn.addEventListener('click', () => loadSurveyLibrary());
        }

        if (surveyLibrarySearch) {
            let timer;
            surveyLibrarySearch.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(loadSurveyLibrary, 400);
            });
        }

        // Chuẩn bị UI chỉnh sửa sự kiện
        initEventEditUI();

        // load event detail
        (async () => {
            try {
                const res = await fetch(apiUrl('/api/events/show?id=' + eventId), { headers: { 'Accept': 'application/json' } });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || json.error || !json.data) {
                    throw new Error(json.message || res.statusText || 'Không thể tải thông tin sự kiện');
                }
                const ev = json.data;
                const titleEl = document.getElementById('event-title');
                if (titleEl) titleEl.textContent = ev.title || 'Không có tiêu đề';
                const metaEl = document.getElementById('event-meta');
                if (metaEl) metaEl.textContent = ev.code ? ('#' + ev.code) : ('ID #' + ev.id);
                const codeEl = document.getElementById('event-code');
                if (codeEl) codeEl.textContent = ev.code ? '#' + ev.code : ('ID #' + ev.id);
                const locEl = document.getElementById('event-location');
                if (locEl) locEl.textContent = ev.location || 'Online';
                const dtEl = document.getElementById('event-datetime');
                if (dtEl) {
                    const start = ev.startDate ? formatDateTime(ev.startDate) : '-';
                    const end = ev.endDate ? formatDateTime(ev.endDate) : '-';
                    dtEl.textContent = `${start}  đến  ${end}`;
                }
                const updatedEl = document.getElementById('event-updated');
                if (updatedEl) updatedEl.textContent = ev.updated_at ? formatDate(ev.updated_at) : '-';
                const participantsEl = document.getElementById('event-participants');
                if (participantsEl) participantsEl.textContent = (ev.participants ?? 0).toString();
                const spinsEl = document.getElementById('event-spins');
                if (spinsEl) spinsEl.textContent = (ev.luckyWheelSpinsPerJoin ?? 0).toString();

                eventCurrentStatus = ev.status || 'upcoming';
                originalStatusForConfirm = eventCurrentStatus;
                originalSpinsValue = typeof ev.luckyWheelSpinsPerJoin === 'number'
                    ? ev.luckyWheelSpinsPerJoin
                    : (ev.luckyWheelSpinsPerJoin ?? 0);

                setEventStatusBadge(ev.status || 'upcoming');
                isEventUpcoming = (ev.status === 'upcoming');

                const addBtn = document.getElementById('btn-open-survey-library');
                if (addBtn) {
                    if (isEventUpcoming) addBtn.classList.remove('d-none');
                    else addBtn.classList.add('d-none');
                }

                if (eventStatusSelect) {
                    eventStatusSelect.value = eventCurrentStatus;
                }
                if (eventSpinsInput) {
                    eventSpinsInput.value = String(originalSpinsValue);
                    eventSpinsInput.disabled = eventCurrentStatus !== 'upcoming';
                }

                if (window.__eventDetailStartEditMode) {
                    setEventEditMode(true);
                } else {
                    setEventEditMode(false);
                }
            } catch (err) {
                console.error(err);
                window.showToast('error', 'Không thể tải thông tin sự kiện: ' + err.message);
            } finally {
                loadEventSurveys();
            }
        })();
    });
</script>
