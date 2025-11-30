// login.js — handles async login submission (with regex validation)
document.getElementById("loginForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const btn = document.getElementById("loginBtn");
  const loading = document.getElementById("loading");
  const message = document.getElementById("message");

  const email = formData.get("email");
  const password = formData.get("password");

  // Regex validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!emailRegex.test(email)) {
    message.style.color = "red";
    message.textContent = "Invalid email format.";
    return;
  }

  if (password.length < 6) {
    message.style.color = "red";
    message.textContent = "Password must be at least 6 characters.";
    return;
  }

  btn.disabled = true;
  loading.style.display = "block";
  message.textContent = "";

  fetch("../actions/login_action.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      message.style.color = "green";
      message.textContent = "Login successful! Redirecting...";
      setTimeout(function() {
        window.location.href = result.redirect || "../index.php";
      }, 1500);
    } else {
      message.style.color = "red";
      message.textContent = result.error || "Login failed.";
    }
  })
  .catch(() => {
    message.style.color = "red";
    message.textContent = "Error connecting to server.";
  })
  .finally(() => {
    btn.disabled = false;
    loading.style.display = "none";
  });
});
