(function (global) {
    function ensureContainer() {
        let container = document.getElementById('global-toast-container');
        if (container) return container;
        container = document.createElement('div');
        container.id = 'global-toast-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'true');
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = 1080;
        document.body.appendChild(container);
        return container;
    }

    function iconFor(status) {
        switch ((status || '').toLowerCase()) {
            case 'success': return '<i class="fas fa-check-circle me-2"></i>';
            case 'warning': return '<i class="fas fa-exclamation-triangle me-2"></i>';
            case 'error':
            case 'danger': return '<i class="fas fa-times-circle me-2"></i>';
            case 'info': return '<i class="fas fa-info-circle me-2"></i>';
            default: return '<i class="fas fa-bell me-2"></i>';
        }
    }

    function bgClassFor(status) {
        switch ((status || '').toLowerCase()) {
            case 'success': return 'bg-success text-white';
            case 'warning': return 'bg-warning text-dark';
            case 'error':
            case 'danger': return 'bg-danger text-white';
            case 'info': return 'bg-info text-dark';
            default: return 'bg-secondary text-white';
        }
    }

    function showToast(status, text, opts = {}) {
        try {
            const container = ensureContainer();
            const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <div id="${toastId}" class="toast align-items-center ${bgClassFor(status)} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                  <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">${iconFor(status)}<div class="toast-text">${escapeHtml(text)}</div></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                  </div>
                </div>
            `;

            const toastEl = wrapper.firstElementChild;
            container.appendChild(toastEl);

            const delay = typeof opts.delay === 'number' ? opts.delay : 3000;
            const bsToast = new bootstrap.Toast(toastEl, { delay });
            toastEl.addEventListener('hidden.bs.toast', function () {
                try { toastEl.remove(); } catch (e) { /* ignore */ }
            });

            bsToast.show();
            return bsToast;
        } catch (e) {
            try { alert(text); } catch (er) { console.error('Cannot show toast or alert', er); }
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

    global.showToast = showToast;
    global.ToastHelper = { show: showToast };
})(window);
