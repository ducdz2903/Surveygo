(function (global) {
    function ensureModalContainer() {
        let container = document.getElementById('global-modal-container');
        if (container) return container;
        container = document.createElement('div');
        container.id = 'global-modal-container';
        container.setAttribute('aria-hidden', 'true');
        document.body.appendChild(container);
        return container;
    }

    function iconFor(type) {
        switch ((type || '').toLowerCase()) {
            case 'success': return '<i class="fas fa-check-circle me-2"></i>';
            case 'warning': return '<i class="fas fa-exclamation-triangle me-2"></i>';
            case 'error':
            case 'danger': return '<i class="fas fa-times-circle me-2"></i>';
            case 'info': return '<i class="fas fa-info-circle me-2"></i>';
            case 'question': return '<i class="fas fa-question-circle me-2"></i>';
            default: return '';
        }
    }

    function headerClassFor(type) {
        switch ((type || '').toLowerCase()) {
            case 'success': return 'bg-success text-white';
            case 'warning': return 'bg-warning text-dark';
            case 'error':
            case 'danger': return 'bg-danger text-white';
            case 'info': return 'bg-info text-dark';
            case 'question': return 'bg-primary text-white';
            default: return 'bg-secondary text-white';
        }
    }

    function headerStyleFor(type) {
        // Use CSS variables declared in public/assets/css/admin.css for consistent color tokens.
        // Return an attribute string to be injected into the modal header element.
        switch ((type || '').toLowerCase()) {
            case 'success':
                return 'style="background: var(--admin-success); color: white;"';
            case 'warning':
                return 'style="background: var(--admin-warning); color: var(--text-primary);"';
            case 'error':
            case 'danger':
                return 'style="background: var(--admin-danger); color: white;"';
            case 'info':
                return 'style="background: var(--admin-info); color: var(--text-primary);"';
            case 'question':
                return 'style="background: var(--admin-primary); color: white;"';
            default:
                return 'style="background: var(--admin-primary); color: white;"';
        }
    }

    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showConfirm(options = {}) {
        try {
            const {
                title = 'Xác nhận',
                message = '',
                type = 'question',
                confirmText = 'Xác nhận',
                cancelText = 'Hủy',
                onConfirm = null,
                onCancel = null,
                isDangerous = false,
                position = 'top-middle' // 'center' or 'top-middle'
            } = options;

            const container = ensureModalContainer();
            const modalId = 'modal-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            const confirmBtnClass = isDangerous ? 'btn-danger' : 'btn-primary';

            const wrapper = document.createElement('div');
            // position: 'center' => vertically centered (default bootstrap class)
            // position: 'top-middle' => appear near top, centered horizontally
            const dialogClass = position === 'center' ? 'modal-dialog modal-dialog-centered' : 'modal-dialog';
            const dialogStyle = position === 'top-middle' ? 'style="margin: 1.5rem auto;"' : '';
            wrapper.innerHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}-label" aria-hidden="true">
                  <div class="${dialogClass}" ${dialogStyle}>
                    <div class="modal-content">
                      <div class="modal-header" ${headerStyleFor(type)}>
                        <h5 class="modal-title" id="${modalId}-label">
                            ${iconFor(type)}${escapeHtml(title)}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        ${escapeHtml(message)}
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${escapeHtml(cancelText)}</button>
                        <button type="button" class="btn ${confirmBtnClass} confirm-btn">${escapeHtml(confirmText)}</button>
                      </div>
                    </div>
                  </div>
                </div>
            `;
 
            const modalEl = wrapper.firstElementChild;
            container.appendChild(modalEl);

            const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });

            const confirmBtn = modalEl.querySelector('.confirm-btn');
            confirmBtn.addEventListener('click', function () {
                bsModal.hide();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            });

            const cancelBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
            cancelBtn.addEventListener('click', function () {
                if (typeof onCancel === 'function') {
                    onCancel();
                }
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                try { modalEl.remove(); } catch (e) { /* ignore */ }
            });

            bsModal.show();
            return bsModal;
        } catch (e) {
            console.error('Cannot show modal:', e);
            const result = confirm(options.message || options.title || 'Xác nhận?');
            if (result && typeof options.onConfirm === 'function') {
                options.onConfirm();
            } else if (!result && typeof options.onCancel === 'function') {
                options.onCancel();
            }
        }
    }

    function showAlert(options = {}) {
        try {
            const {
                title = 'Thông báo',
                message = '',
                type = 'info',
                buttonText = 'Đóng',
                onClose = null,
                position = 'top-middle' // 'center' or 'top-middle'
            } = options;

            const container = ensureModalContainer();
            const modalId = 'modal-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

            const wrapper = document.createElement('div');
            const dialogClassAlert = position === 'center' ? 'modal-dialog modal-dialog-centered' : 'modal-dialog';
            const dialogStyleAlert = position === 'top-middle' ? 'style="margin: 1.5rem auto;"' : '';
            wrapper.innerHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}-label" aria-hidden="true">
                  <div class="${dialogClassAlert}" ${dialogStyleAlert}>
                    <div class="modal-content">
                      <div class="modal-header" ${headerStyleFor(type)}>
                        <h5 class="modal-title" id="${modalId}-label">
                            ${iconFor(type)}${escapeHtml(title)}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        ${escapeHtml(message)}
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">${escapeHtml(buttonText)}</button>
                      </div>
                    </div>
                  </div>
                </div>
            `;
 
            const modalEl = wrapper.firstElementChild;
            container.appendChild(modalEl);

            const bsModal = new bootstrap.Modal(modalEl);

            modalEl.addEventListener('hidden.bs.modal', function () {
                if (typeof onClose === 'function') {
                    onClose();
                }
                try { modalEl.remove(); } catch (e) { /* ignore */ }
            });

            bsModal.show();
            return bsModal;
        } catch (e) {
            console.error('Cannot show alert modal:', e);
            alert(options.message || options.title || 'Thông báo');
            if (typeof options.onClose === 'function') {
                options.onClose();
            }
        }
    }

    global.showConfirm = showConfirm;
    global.showAlert = showAlert;
    global.ModalHelper = {
        confirm: showConfirm,
        alert: showAlert
    };
})(window);
