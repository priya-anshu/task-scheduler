<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Task Scheduler</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1><i class="fa-solid fa-calendar-check"></i> Task Scheduler</h1>
        <p>Create your account to get started.</p>
      </div>
      
     <form id="registerForm" class="auth-form" action="backend/register.php" method="POST">
        <div class="form-group">
          <label for="fullName">Full Name</label>
          <div class="input-with-icon">
            <i class="fa-solid fa-user"></i>
            <input type="text" id="fullName" name="fullName" required placeholder="Enter your full name" />
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <div class="input-with-icon">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" name="email" required placeholder="Enter your email" />
          </div>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-with-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="password" name="password" required placeholder="Enter your password" />
          </div>
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <div class="input-with-icon">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm your password" />
          </div>
        </div>
        
        <div class="form-options">
          <label class="terms">
            <input type="checkbox" id="terms" name="terms" />
            <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
          </label>
        </div>
        
        <button type="submit" class="auth-button">Create Account</button>
        
        <div class="auth-divider">
          <span>or sign up with</span>
        </div>
        
        <div class="social-login">
          <button type="button" class="social-button google">
            <i class="fab fa-google"></i>
            Google
          </button>
          <button type="button" class="social-button github">
            <i class="fab fa-github"></i>
            GitHub
          </button>
        </div>
      </form>
      
      <div class="auth-footer">
        <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </div>
  </div>

  <script src="assets/auth.js"></script>
</body>
</html> 