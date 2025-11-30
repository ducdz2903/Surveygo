// Auth page script (login)
// Basic handlers for login form; replace with real logic as needed

console.log('[auth/script.js] loaded');

document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('login-form') || document.querySelector('form');
    if (!form) {
        console.log('[auth/script.js] login form not found');
        return;
    }

    var submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitBtn) submitBtn.disabled = true;

        // Collect form data (demo)
        var data = {};
        try {
            var elements = form.elements;
            for (var i = 0; i < elements.length; i++) {
                var el = elements[i];
                if (!el.name) continue;
                data[el.name] = el.value;
            }
        } catch (err) { /* ignore */ }

        console.log('[auth/script.js] submit', data);

        // Simulate network request
        setTimeout(function () {
            if (submitBtn) submitBtn.disabled = false;
            // Show a small feedback; in real app use better UI
            var notice = document.createElement('div');
            notice.className = 'alert alert-success mt-3';
            notice.textContent = 'Đăng nhập (mô phỏng) thành công.';
            form.parentNode.insertBefore(notice, form.nextSibling);
            form.reset();
        }, 800);
    });
});
