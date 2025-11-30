// register.js — handles async submission (with regex validation)
document.getElementById("registerForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const btn = document.getElementById("registerBtn");
  const loading = document.getElementById("loading");
  const message = document.getElementById("message");

  const email = formData.get("email");
  const contact = formData.get("contact");

  // Regex validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const phoneRegex = /^[0-9\-\+\s\(\)]+$/;

  if (!emailRegex.test(email)) {
    message.style.color = "red";
    message.textContent = "Invalid email format.";
    return;
  }

  if (!phoneRegex.test(contact)) {
    message.style.color = "red";
    message.textContent = "Invalid phone number format.";
    return;
  }

  btn.disabled = true;
  loading.style.display = "block";
  message.textContent = "";

  fetch("../actions/register_action.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      message.style.color = "green";
      message.textContent = "Registration successful! Redirecting...";
      setTimeout(function() {
        window.location.href = result.redirect || "login.php";
      }, 1500);
    } else {
      message.style.color = "red";
      message.textContent = result.error || "Registration failed.";
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
