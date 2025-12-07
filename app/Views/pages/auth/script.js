document.addEventListener('DOMContentLoaded', () => {
  // Handle Login Form
  const loginForm = document.getElementById('login-form');
  if (loginForm) {
    const submitBtn = loginForm.querySelector('button[type="submit"]');

    const showMsg = (msg, type = 'danger') => {
      const mapTypeToStatus = (t) => {
        switch ((t || '').toLowerCase()) {
          case 'success': return 'success';
          case 'info': return 'info';
          case 'warning': return 'warning';
          case 'danger': return 'error';
          default: return 'info';
        }
      };

      window.ToastHelper?.show(mapTypeToStatus(type), msg);
    };

    loginForm.addEventListener('submit', async (e) => {
      if (typeof window.fetch !== 'function') return;
      e.preventDefault();

      try {
        submitBtn && (submitBtn.disabled = true);
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
          window.ToastHelper?.show('error', msg);
          return;
        }

        try {
          if (data?.data?.user) {
            localStorage.setItem('app.user', JSON.stringify(data.data.user));
          }
        } catch (_) {}

        window.ToastHelper?.show('success', 'Đăng nhập thành công. Đang chuyển hướng...');
        setTimeout(() => {
          window.location.href = '/home';
        }, 1000);
      } catch (err) {
        window.ToastHelper?.show('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
      } finally {
        submitBtn && (submitBtn.disabled = false);
      }
    });
  }

  // Handle Register Form
  const registerForm = document.getElementById('register-form');
  if (registerForm) {
    const submitBtn = registerForm.querySelector('button[type="submit"]');

    const showMsg = (msg, type = 'danger') => {
      const mapTypeToStatus = (t) => {
        switch ((t || '').toLowerCase()) {
          case 'success': return 'success';
          case 'info': return 'info';
          case 'warning': return 'warning';
          case 'danger': return 'error';
          default: return 'info';
        }
      };

      window.ToastHelper?.show(mapTypeToStatus(type), msg);
    };

    registerForm.addEventListener('submit', async (e) => {
      if (typeof window.fetch !== 'function') return;
      e.preventDefault();

      try {
        submitBtn && (submitBtn.disabled = true);
        window.ToastHelper?.show('info', 'Đang đăng ký...');

        const formData = new FormData(registerForm);
        const password = formData.get('password');
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password !== confirmPassword) {
          window.ToastHelper?.show('error', 'Mật khẩu không khớp.');
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
          window.ToastHelper?.show('error', msg);
          return;
        }

        try {
          localStorage.removeItem('app.user');
        } catch (_) {}

        window.ToastHelper?.show('success', 'Đăng ký thành công! Đang chuyển hướng...');

        setTimeout(() => {
          window.location.href = '/login';
        }, 300);
      } catch (err) {
        window.ToastHelper?.show('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
      } finally {
        submitBtn && (submitBtn.disabled = false);
      }
    });
  }
});
