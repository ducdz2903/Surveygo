document.addEventListener("DOMContentLoaded", () => {
  const endpoints = {
    login: window.AppConfig?.endpoints?.login ?? "/api/login",
  };
  const form = document.getElementById("login-form");
  const feedback = document.getElementById("login-feedback");

  function setFeedback(type, message) {
    if (!feedback) return;
    feedback.className = `form-feedback ${type}`;
    feedback.textContent = message;
  }
  function reset() {
    if (!feedback) return;
    feedback.className = "form-feedback";
    feedback.textContent = "";
  }

  async function onSubmit(e) {
    e.preventDefault();
    reset();
    const email = form.email.value.trim();
    const password = form.password.value;
    if (!email || !password) {
      setFeedback("error", "Vui lòng nhập đầy đủ email và mật khẩu.");
      return;
    }
    setFeedback("success", "Đang gửi yêu cầu đăng nhập...");
    const btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;
    try {
      const res = await fetch(endpoints.login, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });
      const payload = await res.json();
      if (!res.ok || payload.error) {
        throw new Error(
          payload.message || "Không thể đăng nhập, vui lòng thử lại."
        );
      }
      setFeedback("success", payload.message || "Đăng nhập thành công.");
    } catch (err) {
      setFeedback("error", err.message);
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  form?.addEventListener("submit", onSubmit);
});
