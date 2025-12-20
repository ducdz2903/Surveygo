<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý sự kiện</h4>
            <p class="text-muted mb-0">Danh sách các sự kiện đang diễn ra và sắp tới</p>
        </div>
        <button class="btn btn-primary" type="button" onclick="openCreateEventModal()">
            <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Nhập tên sự kiện...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái</label>
                    <select class="form-select" id="filter-type">
                        <option value="">Tất cả trạng thái</option>
                        <option value="upcoming">Sắp diễn ra</option>
                        <option value="ongoing">Đang diễn ra</option>
                        <option value="completed">Đã kết thúc</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-light w-100 border" id="reset-filters" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>Đặt lại bộ lọc
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
                            <th class="ps-4" style="width: 100px;">Mã</th>
                            <th style="min-width: 250px;">Thông tin sự kiện</th>
                            <th style="width: 220px;">Thời gian</th>
                            <th style="width: 220px;">Người tạo</th>
                            <th class="text-center" style="width: 120px;">Khảo sát</th>
                            <th class="text-end pe-4" style="width: 180px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="events-table-body">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <span id="total-events">0</span> kết quả
                </div>
                <div id="events-pagination"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo / chỉnh sửa / xem chi tiết sự kiện -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Tạo sự kiện mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="event-form">
                    <input type="hidden" id="event-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên sự kiện</label>
                            <input type="text" class="form-control" id="event-title" placeholder="Nhập tên sự kiện" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Địa điểm</label>
                            <input type="text" class="form-control" id="event-location" placeholder="Nhập địa điểm (Online nếu để trống)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thời gian bắt đầu</label>
                            <input type="datetime-local" class="form-control" id="event-start">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thời gian kết thúc</label>
                            <input type="datetime-local" class="form-control" id="event-end">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select" id="event-status">
                                <option value="upcoming">Sắp diễn ra</option>
                                <option value="ongoing">Đang diễn ra</option>
                                <option value="completed">Đã kết thúc</option>
                            </select>
                        </div>
                        <!-- S? kh?o s�t li�n quan du?c t�nh t? d?ng t? b?ng surveys (maSuKien = eventId), kh�ng c?n nh?p tay -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="event-modal-save-btn" onclick="saveEvent()">Lưu</button>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/admin-helpers.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let eventsCurrentPage = 1;
        const itemsPerPage = 10;
        const totalEventsEl = document.getElementById('total-events');
        let eventModalMode = 'create'; // 'create' | 'edit' | 'view'

          const toast = typeof window.showToast === 'function'
              ? window.showToast
              : function(_, text) { try { alert(text); } catch(e) { console.log(text); } };

          const BootstrapModal = window.bootstrap ? window.bootstrap.Modal : null;

          // Lưu trạng thái ban đầu của sự kiện để cảnh báo khi đổi từ upcoming sang trạng thái khác
          let originalEventStatus = 'upcoming';
          const eventStatusField = document.getElementById('event-status');
          const eventSpinsField = document.getElementById('event-spins');

          if (eventStatusField) {
              eventStatusField.addEventListener('change', function() {
                  const newStatus = this.value || 'upcoming';

                  // Nếu đang từ upcoming chuyển sang trạng thái khác thì cảnh báo
                  if (originalEventStatus === 'upcoming' && newStatus !== 'upcoming') {
                      const confirmed = window.confirm(
                          'Sau khi thay đổi trạng thái khỏi \"Sắp diễn ra\", bạn sẽ không thể thay đổi được số lượt rút thăm nữa. Bạn có chắc chắn muốn tiếp tục?'
                      );
                      if (!confirmed) {
                          this.value = originalEventStatus || 'upcoming';
                          if (eventSpinsField) {
                              eventSpinsField.disabled = originalEventStatus !== 'upcoming';
                          }
                          return;
                      }
                  }

                  if (eventSpinsField) {
                      eventSpinsField.disabled = newStatus !== 'upcoming';
                  }
              });
          }

        function getInitials(name) {
            if (!name) return 'EV';
            const parts = name.trim().split(/\s+/).filter(Boolean);
            if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        function toDatetimeLocalValue(dateTimeString) {
            if (!dateTimeString) return '';
            const d = new Date(dateTimeString);
            if (Number.isNaN(d.getTime())) return '';
            const pad = (n) => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        }

        function setEventFormDisabled(disabled) {
            ['event-title', 'event-location', 'event-start', 'event-end', 'event-status', 'event-spins', 'event-surveys'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.disabled = disabled;
            });
        }

        async function fetchEventDetail(id) {
            const res = await fetch(`/api/events/show?id=${id}`, { headers: { 'Accept': 'application/json' } });
            const json = await res.json().catch(() => ({}));
            if (!res.ok || json.error || !json.data) {
                throw new Error(json.message || res.statusText || 'Không thể tải thông tin sự kiện');
            }
            return json.data;
        }

          window.openCreateEventModal = function() {
              eventModalMode = 'create';
              const form = document.getElementById('event-form');
              if (form) form.reset();
              const idInput = document.getElementById('event-id');
              if (idInput) idInput.value = '';
              const statusSelect = document.getElementById('event-status');
              if (statusSelect) statusSelect.value = 'upcoming';
              originalEventStatus = 'upcoming';
            const spinsInput = document.getElementById('event-spins');
            if (spinsInput) {
                spinsInput.value = 0;
                spinsInput.disabled = false;
            }
            const surveysInput = document.getElementById('event-surveys');
            if (surveysInput) surveysInput.value = 0;
            const titleEl = document.getElementById('eventModalLabel');
            if (titleEl) titleEl.textContent = 'Tạo sự kiện mới';
            const saveBtn = document.getElementById('event-modal-save-btn');
            if (saveBtn) saveBtn.style.display = '';
            setEventFormDisabled(false);
            const modalEl = document.getElementById('eventModal');
            if (modalEl && BootstrapModal) {
                const modal = BootstrapModal.getOrCreateInstance(modalEl);
                modal.show();
            }
        };

        window.viewEvent = async function(id) {
            try {
                const data = await fetchEventDetail(id);
                eventModalMode = 'view';

                const idInput = document.getElementById('event-id');
                if (idInput) idInput.value = data.id;
                const titleInput = document.getElementById('event-title');
                if (titleInput) titleInput.value = data.title || '';
                const locInput = document.getElementById('event-location');
                if (locInput) locInput.value = data.location || '';
                const startInput = document.getElementById('event-start');
                if (startInput) startInput.value = toDatetimeLocalValue(data.startDate);
                const endInput = document.getElementById('event-end');
                if (endInput) endInput.value = toDatetimeLocalValue(data.endDate);
                const statusSelect = document.getElementById('event-status');
                if (statusSelect) statusSelect.value = data.status || 'upcoming';
                originalEventStatus = statusSelect ? (statusSelect.value || 'upcoming') : 'upcoming';
                const spinsInput = document.getElementById('event-spins');
                if (spinsInput) spinsInput.value = data.luckyWheelSpinsPerJoin ?? 0;
                const surveysInput = document.getElementById('event-surveys');
                if (surveysInput) surveysInput.value = data.surveys ?? 0;

                const titleEl = document.getElementById('eventModalLabel');
                if (titleEl) titleEl.textContent = 'Sự kiện';
                const saveBtn = document.getElementById('event-modal-save-btn');
                if (saveBtn) saveBtn.style.display = 'none';
                setEventFormDisabled(true);

                const modalEl = document.getElementById('eventModal');
                if (modalEl && BootstrapModal) {
                    const modal = BootstrapModal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            } catch (err) {
                console.error(err);
                toast('error', 'Không thể tải thông tin sự kiện: ' + err.message);
            }
        };

        window.editEvent = async function(id) {
            try {
                const data = await fetchEventDetail(id);
                eventModalMode = 'edit';

                const idInput = document.getElementById('event-id');
                if (idInput) idInput.value = data.id;
                const titleInput = document.getElementById('event-title');
                if (titleInput) titleInput.value = data.title || '';
                const locInput = document.getElementById('event-location');
                if (locInput) locInput.value = data.location || '';
                const startInput = document.getElementById('event-start');
                if (startInput) startInput.value = toDatetimeLocalValue(data.startDate);
                const endInput = document.getElementById('event-end');
                if (endInput) endInput.value = toDatetimeLocalValue(data.endDate);
                const statusSelect = document.getElementById('event-status');
                if (statusSelect) statusSelect.value = data.status || 'upcoming';
                const spinsInput = document.getElementById('event-spins');
                if (spinsInput) {
                    spinsInput.value = data.luckyWheelSpinsPerJoin ?? 0;
                    spinsInput.disabled = data.status && data.status !== 'upcoming';
                }
                const surveysInput = document.getElementById('event-surveys');
                if (surveysInput) surveysInput.value = data.surveys ?? 0;

                const titleEl = document.getElementById('eventModalLabel');
                if (titleEl) titleEl.textContent = 'Chỉnh sửa sự kiện';
                const saveBtn = document.getElementById('event-modal-save-btn');
                if (saveBtn) saveBtn.style.display = '';
                setEventFormDisabled(false);

                const modalEl = document.getElementById('eventModal');
                if (modalEl && BootstrapModal) {
                    const modal = BootstrapModal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            } catch (err) {
                console.error(err);
                toast('error', 'Không thể tải thông tin sự kiện: ' + err.message);
            }
        };

        window.saveEvent = async function() {
            const idInput = document.getElementById('event-id');
            const titleInput = document.getElementById('event-title');
            const locInput = document.getElementById('event-location');
            const startInput = document.getElementById('event-start');
            const endInput = document.getElementById('event-end');
            const statusSelect = document.getElementById('event-status');
            const spinsInput = document.getElementById('event-spins');
            const surveysInput = document.getElementById('event-surveys');

            const eventId = idInput?.value ? Number(idInput.value) : null;
            const title = (titleInput?.value || '').trim();
            if (!title) {
                toast('error', 'Vui lòng nhập tên sự kiện.');
                return;
            }

            const payload = {
                tenSuKien: title,
                diaDiem: (locInput?.value || '').trim() || null,
                thoiGianBatDau: startInput?.value ? startInput.value.replace('T', ' ') + ':00' : null,
                thoiGianKetThuc: endInput?.value ? endInput.value.replace('T', ' ') + ':00' : null,
                trangThai: statusSelect?.value || 'upcoming',
                soLuotRutThamMoiLan: spinsInput ? Math.max(0, parseInt(spinsInput.value || '0', 10) || 0) : 0,
                soKhaoSat: surveysInput ? Math.max(0, parseInt(surveysInput.value || '0', 10) || 0) : 0,
            };

            if (!eventId) {
                const maNguoiTao = (function() {
                    const raw = localStorage.getItem('app.user');
                    if (!raw) return 1;
                    try {
                        const user = JSON.parse(raw);
                        return Number(user.id ?? user.maNguoiTao ?? 1) || 1;
                    } catch (e) {
                        return 1;
                    }
                })();
                payload.maNguoiTao = maNguoiTao;
            } else {
                payload.id = eventId;
            }

            const isEdit = !!eventId;
            const method = isEdit ? 'PUT' : 'POST';
            const url = '/api/events';

            try {
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || json.error) {
                    throw new Error(json.message || res.statusText);
                }
                toast('success', isEdit ? 'Cập nhật sự kiện thành công.' : 'Tạo sự kiện thành công.');
                const modalEl = document.getElementById('eventModal');
                if (modalEl && BootstrapModal) {
                    const modal = BootstrapModal.getInstance(modalEl) || new BootstrapModal(modalEl);
                    modal.hide();
                }
                eventsCurrentPage = 1;
                loadEvents();
            } catch (err) {
                console.error(err);
                toast('error', (isEdit ? 'Cập nhật' : 'Tạo') + ' sự kiện thất bại: ' + err.message);
            }
        };

        // table renderer
        function renderEventsTable(events) {
            const tbody = document.getElementById('events-table-body');

            if (!events || events.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times mb-2 display-6"></i><br>
                            Không tìm thấy sự kiện nào
                        </td>
                    </tr>`;
                return;
            }

            const Helpers = window.AdminHelpers || {
                getStatusBadge: (s) => 'badge bg-secondary',
                getStatusText: (s) => s,
                formatDateTime: (d) => d,
                getAvatarColor: () => '#6c757d'
            };

            tbody.innerHTML = events.map(ev => `
                <tr class="slide-in">
                    <td class="ps-4">
                        <span class="font-monospace text-dark">#${ev.code || ev.id || 'N/A'}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-primary mb-1 text-truncate" style="max-width: 300px;">
                            ${ev.title || 'Không có tiêu đề'}
                        </div>
                        <div class="small text-muted mb-1">
                            <i class="fas fa-map-marker-alt me-1"></i>${ev.location || 'Online'}
                        </div>
                        <div>
                            <span class="badge ${Helpers.getStatusBadge(ev.status)}">${Helpers.getStatusText(ev.status)}</span>
                            <span class="ms-2 small text-muted"><i class="fas fa-users me-1"></i>${ev.participants || 0} tham gia</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <div class="small text-muted">
                                <i class="fas fa-clock me-1"></i>${Helpers.formatDateTime(ev.startDate)}
                            </div>
                            <div class="small text-muted">
                                <i class="fas fa-flag-checkered me-1"></i>${Helpers.formatDateTime(ev.endDate)}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center"
                                 style="width:32px; height:32px; font-size:0.8rem; background:${Helpers.getAvatarColor(ev.creator)}">
                                ${getInitials(ev.creator)}
                            </div>
                            <div class="d-flex flex-column" style="line-height: 1.2;">
                                <span class="fw-bold small">${ev.creator || 'Ẩn danh'}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="text-dark">${ev.surveys || 0}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" title="Xem chi tiết" onclick="viewEvent(${ev.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-success" title="Chỉnh sửa" onclick="editEvent(${ev.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-danger" title="Xóa" onclick="deleteEvent(${ev.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('events-pagination');
            if (!container) return;

            const totalPages = Math.ceil(total / pageSize) || 1;
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';

            html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page - 1})"><i class="fas fa-chevron-left"></i></button>
                     </li>`;

            const startPage = Math.max(1, page - 1);
            const endPage = Math.min(totalPages, page + 1);

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
                if (startPage > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === page ? 'active' : ''}">
                            <button class="page-link" onclick="changePage(${i})">${i}</button>
                         </li>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                html += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;
            }

            html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page + 1})"><i class="fas fa-chevron-right"></i></button>
                     </li>`;

            html += '</ul>';
            container.innerHTML = html;
        }

        async function loadEvents() {
            const tbody = document.getElementById('events-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>`;

            const params = new URLSearchParams();
            params.set('page', eventsCurrentPage);
            params.set('limit', itemsPerPage);

            const searchVal = document.getElementById('filter-search')?.value.trim();
            if (searchVal) params.set('search', searchVal);

            const type = document.getElementById('filter-type')?.value;
            if (type) {
                params.set('trangThai', type);
            }

            try {
                const res = await fetch('/api/events?' + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error(`Lỗi server: ${res.status}`);

                const json = await res.json();

                const data = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
                const meta = json.meta || { total: data.length, page: eventsCurrentPage, limit: itemsPerPage, totalPages: 1 };

                renderEventsTable(data);
                renderPagination(meta.total || 0, meta.page || eventsCurrentPage, meta.limit || itemsPerPage);

                if (totalEventsEl) totalEventsEl.textContent = meta.total || 0;

            } catch (err) {
                console.error('API Error:', err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Không thể tải dữ liệu: ${err.message}
                </td></tr>`;
                const pag = document.getElementById('events-pagination');
                if (pag) pag.innerHTML = '';
            }
        }

        window.changePage = function(page) {
            eventsCurrentPage = page;
            loadEvents();
        };

        window.deleteEvent = function(id) {
            const doDelete = async () => {
                try {
                    const res = await fetch(`/api/events?id=${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json' } });
                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || json.error) {
                        throw new Error(json.message || res.statusText);
                    }
                    toast('success', 'Đã xóa sự kiện #' + id);
                    loadEvents();
                } catch (err) {
                    console.error(err);
                    toast('error', 'Xóa sự kiện thất bại: ' + err.message);
                }
            };

            if (window.ModalHelper && typeof window.ModalHelper.confirm === 'function') {
                ModalHelper.confirm({
                    title: 'Xóa sự kiện',
                    message: 'Bạn có chắc chắn muốn xóa sự kiện #' + id + '?',
                    type: 'danger',
                    confirmText: 'Xóa',
                    cancelText: 'Hủy',
                    isDangerous: true,
                    onConfirm: doDelete,
                });
            } else if (confirm('Bạn có chắc chắn muốn xóa sự kiện #' + id + '?')) {
                doDelete();
            }
        };

        function debounce(fn, wait = 300) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const debouncedLoad = debounce(() => {
            eventsCurrentPage = 1;
            loadEvents();
        });

        const statusFilter = document.getElementById('filter-type');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                eventsCurrentPage = 1;
                loadEvents();
            });
        }

        const searchInput = document.getElementById('filter-search');
        if (searchInput) {
            searchInput.addEventListener('input', debouncedLoad);
        }

        window.loadEvents = loadEvents;
        window.resetFilters = function() {
            const ft = document.getElementById('filter-type');
            const fs = document.getElementById('filter-search');
            if (ft) ft.value = '';
            if (fs) fs.value = '';
            eventsCurrentPage = 1;
            loadEvents();
        };

        // Chỉnh sửa: chuyển tới trang chi tiết sự kiện ở chế độ chỉnh sửa
        window.editEvent = function(id) {
            window.location.href = '/admin/events/view?id=' + id + '&mode=edit';
        };

        // Điều hướng sang trang chi tiết sự kiện
        window.viewEvent = function(id) {
            window.location.href = '/admin/events/view?id=' + id;
        };

        // Initial Load
        loadEvents();
    });
</script>



