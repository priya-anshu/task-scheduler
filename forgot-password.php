<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password - Task Scheduler</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1><i class="fa-solid fa-calendar-check"></i> Task Scheduler</h1>
        <p>Reset your password</p>
      </div>
      
      <form id="forgotPasswordForm" class="auth-form">
        <div class="form-group">
          <label for="email">Email</label>
          <div class="input-with-icon">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" required placeholder="Enter your email">
          </div>
        </div>
        
        <button type="submit" class="auth-button">Send Reset Link</button>
        
        <div class="auth-footer">
          <p>Remember your password? <a href="login.php">Back to Login</a></p>
        </div>
      </form>
    </div>
  </div>

  <script src="assets/auth.js"></script>
</body>
</html> 