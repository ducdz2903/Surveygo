document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("register-form");
  const feedback = document.getElementById("register-feedback");
  if (!form) return;

  function setFeedback(type, message) {
    if (!feedback) return;
    feedback.className = `form-feedback ${type}`;
    feedback.textContent = message;
  }

  function resetFeedback() {
    if (!feedback) return;
    feedback.className = "form-feedback";
    feedback.textContent = "";
  }

  form.addEventListener("submit", (e) => {
    resetFeedback();

    const name = (form.name?.value || "").trim();
    const email = (form.email?.value || "").trim();
    const password = form.password?.value || "";

    if (!name || !email || !password) {
      e.preventDefault();
      setFeedback("error", "Vui lòng điền đầy đủ thông tin.");
      return;
    }
    if (password.length < 6) {
      e.preventDefault();
      setFeedback("error", "Mật khẩu cần tối thiểu 6 ký tự.");
      return;
    }

    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.dataset.originalText = btn.textContent || "";
      btn.textContent = "Đang gửi...";
    }
    setFeedback("success", "Đang gửi yêu cầu đăng ký...");
    // Không preventDefault ở đây -> trình duyệt sẽ POST trực tiếp tới action của form
  });
});
