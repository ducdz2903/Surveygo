document.addEventListener('DOMContentLoaded', () => {
  // Handle Login Form
  const loginForm = document.getElementById('login-form');
  if (loginForm) {
    const feedback = document.getElementById('login-feedback');
    const submitBtn = loginForm.querySelector('button[type="submit"]');

    const showMsg = (msg, type = 'danger') => {
      // Map bootstrap alert types to toast statuses
      const mapTypeToStatus = (t) => {
        switch ((t || '').toLowerCase()) {
          case 'success': return 'success';
          case 'info': return 'info';
          case 'warning': return 'warning';
          case 'danger': return 'error';
          default: return 'info';
        }
      };

      // Prefer toasts; do not write inline feedback under the form
      try {
        if (typeof window.showToast === 'function') {
          showToast(mapTypeToStatus(type), msg);
        }
      } catch (e) {
        // ignore
      }
    };

    loginForm.addEventListener('submit', async (e) => {
      // If no fetch available, let the default form POST happen
      if (typeof window.fetch !== 'function') return;
      e.preventDefault();

      try {
        submitBtn && (submitBtn.disabled = true);
        showToast('info', 'Đang đăng nhập...');

        const formData = new FormData(loginForm);
        const payload = new URLSearchParams();
        formData.forEach((v, k) => payload.append(k, v));

        const res = await fetch(loginForm.action || '/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: payload.toString(),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || data.error) {
          const msg = data.message || 'Đăng nhập thất bại.';
          showToast('error', msg);
          return;
        }

        // Persist lightweight user info for personalization
        try {
          if (data && data.data && data.data.user) {
            localStorage.setItem('app.user', JSON.stringify(data.data.user));
          }
        } catch (_) { }

        showToast('success', 'Đăng nhập thành công. Đang chuyển hướng...');
        // Redirect to /home
        setTimeout(() => {
          window.location.href = '/home';
        }, 1000);
      } catch (err) {
        showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
      } finally {
        submitBtn && (submitBtn.disabled = false);
      }
    });
  }

  // Handle Register Form
  const registerForm = document.getElementById('register-form');
  if (registerForm) {
    const feedback = document.getElementById('register-feedback');
    const submitBtn = registerForm.querySelector('button[type="submit"]');

    const showMsg = (msg, type = 'danger') => {
      // Use toast if available; do not write inline feedback under the form
      try {
        if (typeof window.showToast === 'function') {
          // map register alert types to toast statuses
          const mapTypeToStatus = (t) => {
            switch ((t || '').toLowerCase()) {
              case 'success': return 'success';
              case 'info': return 'info';
              case 'warning': return 'warning';
              case 'danger': return 'error';
              default: return 'info';
            }
          };
          showToast(mapTypeToStatus(type), msg);
        }
      } catch (e) {
        // ignore
      }
    };

    registerForm.addEventListener('submit', async (e) => {
      // If no fetch available, let the default form POST happen
      if (typeof window.fetch !== 'function') return;
      e.preventDefault();

      try {
        submitBtn && (submitBtn.disabled = true);
        showToast('info', 'Đang đăng ký...');

        const formData = new FormData(registerForm);

        // Validate password match
        const password = formData.get('password');
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password !== confirmPassword) {
          showToast('error', 'Mật khẩu không khớp.');
          return;
        }

        const payload = new URLSearchParams();
        formData.forEach((v, k) => {
          if (k !== 'confirm-password' && k !== 'terms') {
            payload.append(k, v);
          }
        });

        const res = await fetch(registerForm.action || '/api/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: payload.toString(),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || data.error) {
          const msg = data.message || 'Đăng ký thất bại.';
          showToast('error', msg);
          return;
        }
        try {
          localStorage.removeItem('app.user');
        } catch (_) { }

        setTimeout(() => {
          window.location.href = '/login';
        }, 300);
      } catch (err) {
        showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
      } finally {
        submitBtn && (submitBtn.disabled = false);
      }
    });
  }
});
