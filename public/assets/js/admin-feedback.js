// admin-feedback.js
// Minimal CRUD UI for admin feedbacks
(function () {
    const tableBody = document.getElementById('feedback-table-body');
    const pagination = document.getElementById('feedback-pagination');
    const totalEl = document.getElementById('total-feedbacks');
    const searchInput = document.getElementById('feedback-search') || document.getElementById('search-input');
    const btnNew = document.getElementById('btn-new-feedback');

    const modalEl = document.getElementById('feedbackModal');
    const bsModal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('feedback-form');
    const saveBtn = document.getElementById('feedback-save');

    let currentPage = 1;
    const limit = 10;

    function renderPagination(total, page, pageSize) {
        if (!pagination) return;
        const totalPages = Math.ceil(total / pageSize) || 1;
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let html = '<ul class="pagination">';
        if (page > 1) html += `<li class="page-item"><button class="page-link" data-page="${page - 1}">← Trước</button></li>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === page) html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            else html += `<li class="page-item"><button class="page-link" data-page="${i}">${i}</button></li>`;
        }
        if (page < totalPages) html += `<li class="page-item"><button class="page-link" data-page="${page + 1}">Tiếp →</button></li>`;
        html += '</ul>';
        pagination.innerHTML = html;
        pagination.querySelectorAll('button[data-page]').forEach(b => b.addEventListener('click', (e) => {
            const p = Number(e.currentTarget.getAttribute('data-page')) || 1;
            currentPage = p; loadFeedbacks();
        }));
    }

    async function loadFeedbacks() {
        if (!tableBody) return;
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>`;

        const params = new URLSearchParams();
        params.set('page', currentPage);
        params.set('limit', limit);
        const s = (searchInput?.value || '').trim();
        if (s) params.set('search', s);

        try {
            const res = await fetch('/api/feedbacks?' + params.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();
            const data = json.data || [];
            const meta = json.meta || { total: data.length, page: currentPage, limit };

            tableBody.innerHTML = data.map((f, i) => {
                const name = f.tenNguoiDung || 'Khách Ẩn Danh';
                const initials = (name.split(/\s+/).map(p => p[0] || '').slice(0, 2).join('') || 'KH').toUpperCase();
                const bg = (window.AdminHelpers && AdminHelpers.getAvatarColor) ? AdminHelpers.getAvatarColor(name) : '#34495e';
                const rating = Number(f.danhGia) || 0;
                let ratingClass = 'badge-danger';

                return `
                <tr class="slide-in">
                    <td><span class="badge badge-primary">${f.ma || ''}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width:40px;height:40px;background:${bg};display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:600;border-radius:6px;">${initials}</div>
                            <div>
                                <div class="fw-bold">${name}</div>
                                <small class="text-muted">${f.created_at ? (window.AdminHelpers ? AdminHelpers.formatDate(f.created_at) : (new Date(f.created_at)).toLocaleDateString()) : ''}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge ${ratingClass}">${rating}</span></td>
                    <td>${f.binhLuan ? f.binhLuan : ''}</td>
                    <td>${f.created_at ? (window.AdminHelpers ? AdminHelpers.formatDateTime(f.created_at) : (new Date(f.created_at)).toLocaleString()) : ''}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-edit" data-id="${f.id}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-outline-danger btn-delete" data-id="${f.id}"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');

            totalEl && (totalEl.textContent = meta.total || 0);
            renderPagination(meta.total || 0, meta.page || currentPage, meta.limit || limit);

            document.querySelectorAll('.btn-edit').forEach(b => b.addEventListener('click', onEdit));
            document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', onDelete));
        } catch (err) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-danger text-center py-4">Lỗi tải dữ liệu: ${err.message}</td></tr>`;
            console.error(err);
        }
    }

    function openModalForCreate() {
        (document.getElementById('feedback-id')).value = '';
        (document.getElementById('feedback-tenNguoiDung')).value = '';
        (document.getElementById('feedback-danhGia')).value = '5';
        (document.getElementById('feedback-binhLuan')).value = '';
        bsModal.show();
    }

    async function onEdit(e) {
        const id = e.currentTarget.getAttribute('data-id');
        try {
            const res = await fetch('/api/feedbacks/show?id=' + encodeURIComponent(id));
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();
            const f = json.data;
            document.getElementById('feedback-id').value = f.id || '';
            document.getElementById('feedback-tenNguoiDung').value = f.tenNguoiDung || '';
            document.getElementById('feedback-danhGia').value = f.danhGia ?? '0';
            document.getElementById('feedback-binhLuan').value = f.binhLuan || '';
            bsModal.show();
        } catch (err) {
            showToast('error', 'Lỗi khi lấy feedback: ' + err.message);
        }
    }

    async function onDelete(e) {
        const id = e.currentTarget.getAttribute('data-id');
        if (!confirm('Bạn có chắc muốn xóa phản hồi này?')) return;
        try {
            const res = await fetch('/api/feedbacks?id=' + encodeURIComponent(id), { method: 'DELETE' });
            const json = await res.json();
            if (json.error) throw new Error(json.message || 'Delete failed');
            loadFeedbacks();
        } catch (err) {
            showToast('error', 'Xóa thất bại: ' + err.message);
        }
    }

    async function onSave() {
        const id = (document.getElementById('feedback-id')).value || null;
        const payload = {
            tenNguoiDung: document.getElementById('feedback-tenNguoiDung').value.trim(),
            danhGia: parseInt(document.getElementById('feedback-danhGia').value || '0', 10),
            binhLuan: document.getElementById('feedback-binhLuan').value.trim() || null,
        };

        try {
            let res;
            if (id) {
                res = await fetch('/api/feedbacks?id=' + encodeURIComponent(id), {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
            } else {
                res = await fetch('/api/feedbacks', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
            }
            const json = await res.json();
            if (json.error) throw new Error(json.message || 'Save failed');
            bsModal.hide();
            loadFeedbacks();
        } catch (err) {
            showToast('error', 'Lưu thất bại: ' + err.message);
        }
    }

    // wire events
    btnNew && btnNew.addEventListener('click', openModalForCreate);
    saveBtn && saveBtn.addEventListener('click', onSave);
    searchInput && searchInput.addEventListener('input', (() => { let t; return () => { clearTimeout(t); t = setTimeout(() => { currentPage = 1; loadFeedbacks(); }, 300); }; })());

    // initial load
    document.addEventListener('DOMContentLoaded', () => loadFeedbacks());
})();
