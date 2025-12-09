(function (global) {
    function ensureContainer() {
        let container = document.getElementById('global-toast-container');
        if (container) return container;
        
        container = document.createElement('div');
        container.id = 'global-toast-container';
        container.className = 'position-fixed top-0 end-0 p-3 mt-3';
        container.style.zIndex = 1080;
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'true');
        
        // Inline styles để tránh CSS conflict
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            pointer-events: none;
        `;
        
        document.body.appendChild(container);
        return container;
    }

    function getBackgroundColor(status) {
        switch ((status || '').toLowerCase()) {
            case 'success': return '#28a745';
            case 'warning': return '#ffc107';
            case 'error':
            case 'danger': return '#dc3545';
            case 'info': return '#17a2b8';
            default: return '#6c757d';
        }
    }

    function getTextColor(status) {
        switch ((status || '').toLowerCase()) {
            case 'warning': return '#000';
            default: return '#fff';
        }
    }

    function iconFor(status) {
        switch ((status || '').toLowerCase()) {
            case 'success': return '✓';
            case 'warning': return '⚠';
            case 'error':
            case 'danger': return '✕';
            case 'info': return 'ⓘ';
            default: return '●';
        }
    }

    function showToast(status, text, opts = {}) {
        try {
            const container = ensureContainer();
            
            // Tạo toast element
            const toastEl = document.createElement('div');
            toastEl.className = 'toast-item';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            
            const bgColor = getBackgroundColor(status);
            const textColor = getTextColor(status);
            const icon = iconFor(status);
            
            toastEl.style.cssText = `
                background-color: ${bgColor};
                color: ${textColor};
                padding: 16px 24px;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 300px;
                max-width: 400px;
                animation: slideInRight 0.3s ease-out;
                pointer-events: auto;
                font-size: 14px;
                font-weight: 500;
            `;
            
            // Icon
            const iconEl = document.createElement('span');
            iconEl.textContent = icon;
            iconEl.style.cssText = `
                font-size: 18px;
                font-weight: bold;
                flex-shrink: 0;
            `;
            
            // Message
            const messageEl = document.createElement('span');
            messageEl.textContent = text;
            messageEl.style.cssText = `
                flex: 1;
                white-space: normal;
                word-break: break-word;
            `;
            
            // Close button
            const closeBtn = document.createElement('button');
            closeBtn.textContent = '×';
            closeBtn.setAttribute('aria-label', 'Close');
            closeBtn.style.cssText = `
                background: none;
                border: none;
                color: ${textColor};
                font-size: 24px;
                cursor: pointer;
                padding: 0;
                margin-left: 10px;
                flex-shrink: 0;
                transition: opacity 0.2s;
                opacity: 0.7;
            `;
            
            closeBtn.addEventListener('mouseover', () => {
                closeBtn.style.opacity = '1';
            });
            closeBtn.addEventListener('mouseout', () => {
                closeBtn.style.opacity = '0.7';
            });
            
            closeBtn.addEventListener('click', () => {
                toastEl.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => toastEl.remove(), 300);
            });
            
            // Assemble toast
            toastEl.appendChild(iconEl);
            toastEl.appendChild(messageEl);
            toastEl.appendChild(closeBtn);
            
            container.appendChild(toastEl);
            
            // Auto-dismiss
            const delay = typeof opts.delay === 'number' ? opts.delay : 3000;
            const timeoutId = setTimeout(() => {
                if (toastEl.parentElement) {
                    toastEl.style.animation = 'slideOutRight 0.3s ease-out';
                    setTimeout(() => {
                        try { toastEl.remove(); } catch (e) { /* ignore */ }
                    }, 300);
                }
            }, delay);
            
            // Return object dengan method để cancel timeout
            return {
                dismiss: () => {
                    clearTimeout(timeoutId);
                    if (toastEl.parentElement) {
                        toastEl.style.animation = 'slideOutRight 0.3s ease-out';
                        setTimeout(() => {
                            try { toastEl.remove(); } catch (e) { /* ignore */ }
                        }, 300);
                    }
                }
            };
        } catch (e) {
            console.error('Toast error:', e);
            try { alert(text); } catch (er) { console.error('Cannot show alert', er); }
        }
    }

    // Add CSS animations
    if (!document.getElementById('toast-animations-style')) {
        const style = document.createElement('style');
        style.id = 'toast-animations-style';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    global.showToast = showToast;
    global.ToastHelper = { show: showToast };
    
    // Debug log
})(window);

