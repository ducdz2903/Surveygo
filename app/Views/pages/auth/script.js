document.addEventListener('DOMContentLoaded', () => {
  // Handle Login Form
  const loginForm = document.getElementById('login-form');
  if (loginForm) {
    const feedback = document.getElementById('login-feedback');
    const submitBtn = loginForm.querySelector('button[type="submit"]');

    const showMsg = (msg, type = 'danger') => {
      if (!feedback) return;
      feedback.innerHTML = `<div class="alert alert-${type}" role="alert">${msg}</div>`;
    };

    loginForm.addEventListener('submit', async (e) => {
      // If no fetch available, let the default form POST happen
      if (typeof window.fetch !== 'function') return;
      e.preventDefault();

      try {
        submitBtn && (submitBtn.disabled = true);
        showMsg('Đang đăng nhập...', 'info');

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
          showMsg(msg, 'danger');
          return;
        }

        // Persist lightweight user info for personalization
        try {
          if (data && data.data && data.data.user) {
            localStorage.setItem('app.user', JSON.stringify(data.data.user));
          }
        } catch (_) { }

        showMsg('Đăng nhập thành công. Đang chuyển hướng...', 'success');
        // Redirect to /home
        setTimeout(() => {
          window.location.href = '/home';
        }, 800);
      } catch (err) {
        showMsg('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
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
      if (!feedback) return;
      feedback.innerHTML = `<div class="alert alert-${type}" role="alert">${msg}</div>`;
    };

    registerForm.addEventListener('submit', async (e) => {
      // If no fetch available, let the default form POST happen
      if (typeof window.fetch !== 'function') return;
      e.preventDefault();

      try {
        submitBtn && (submitBtn.disabled = true);
        showMsg('Đang đăng ký...', 'info');

        const formData = new FormData(registerForm);

        // Validate password match
        const password = formData.get('password');
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password !== confirmPassword) {
          showMsg('Mật khẩu không khớp.', 'danger');
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
          showMsg(msg, 'danger');
          return;
        }

        // Persist lightweight user info for personalization
        try {
          if (data && data.data && data.data.user) {
            localStorage.setItem('app.user', JSON.stringify(data.data.user));
          }
        } catch (_) { }

        showMsg('Đăng ký thành công. Đang chuyển hướng...', 'success');
        // Redirect to /home
        setTimeout(() => {
          window.location.href = '/home';
        }, 800);
      } catch (err) {
        showMsg('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
      } finally {
        submitBtn && (submitBtn.disabled = false);
      }
    });
  }
});
