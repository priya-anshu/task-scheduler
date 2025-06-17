// assets/auth.js

// DOM Elements
const loginForm           = document.getElementById('loginForm');
const registerForm        = document.getElementById('registerForm');
const forgotPasswordForm  = document.getElementById('forgotPasswordForm');

// Theme initialization
function initTheme() {
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }
}

// Form validation
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validatePassword(password) {
  return password.length >= 6;
}

// ——— LOGIN ———
if (loginForm) {
  loginForm.setAttribute('action', 'backend/login.php');
  loginForm.setAttribute('method', 'POST');

  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked;

    // client-side validation
    if (!validateEmail(email)) {
      alert('Please enter a valid email address');
      return;
    }
    if (!validatePassword(password)) {
      alert('Password must be at least 6 characters long');
      return;
    }

    try {
      const response = await fetch(loginForm.action, {
        method: 'POST',
        body: new FormData(loginForm)
      });
      const result = await response.json();

      if (result.success) {
        window.location.href = result.redirect;
      } else {
        alert(result.message);
      }
    } catch (err) {
      console.error('Login error:', err);
      alert('An unexpected error occurred. Please try again.');
    }
  });
}

// ——— REGISTER ———
if (registerForm) {
  registerForm.setAttribute('action', 'backend/register.php');
  registerForm.setAttribute('method', 'POST');

  registerForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const fullName        = document.getElementById('fullName').value.trim();
    const email           = document.getElementById('email').value.trim();
    const password        = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const terms           = document.getElementById('terms').checked;

    if (!fullName) {
      alert('Please enter your full name');
      return;
    }
    if (!validateEmail(email)) {
      alert('Please enter a valid email address');
      return;
    }
    if (!validatePassword(password)) {
      alert('Password must be at least 6 characters long');
      return;
    }
    if (password !== confirmPassword) {
      alert('Passwords do not match');
      return;
    }
    if (!terms) {
      alert('Please agree to the Terms of Service and Privacy Policy');
      return;
    }

    registerForm.submit();
  });
}

// ——— FORGOT PASSWORD ———
if (forgotPasswordForm) {
  forgotPasswordForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    if (!validateEmail(email)) {
      alert('Please enter a valid email address');
      return;
    }

    // simulate sending reset link
    alert('Password reset link has been sent to your email address.');
    setTimeout(() => {
      window.location.href = 'login.html';
    }, 2000);
  });
}

// Social login buttons
document.querySelectorAll('.social-button').forEach(button => {
  button.addEventListener('click', () => {
    alert('Social login functionality will be implemented here');
  });
});

// Initialize theme
initTheme();
