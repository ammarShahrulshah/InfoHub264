<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InfoHub Login</title>
  <link rel="stylesheet" href="login.css">
  <link rel="stylesheet" href="dashboard.css">
</head>

<body>

  <header class="login-navbar">
    <div class="login-logo">
      <img src="logo.jpg" alt="InfoHub Logo">
    </div>

    <nav>
      <a href="visitor.html">Home</a>
      <a href="#">News</a>
      <a href="#">Events</a>
      <button class="signin-btn">Sign In</button>
    </nav>
  </header>

  <main class="login-container">

  <div class="login-card" id="signin">
  <h1>Sign In</h1>

  <label>Email / Matric Number</label>
  <input type="text" id="loginEmail">
  <small id="loginEmailError"></small>

  <label>Password</label>
  <input type="password" id="loginPassword">
  <small id="loginPasswordError"></small>

  <button class="login-btn" onclick="return validateLogin()">Login to InfoHub</button>

  <p class="switch-text">
    Don’t have an account?
    <span onclick="showSignup()">Sign Up</span>
  </p>
</div>

    <div class="login-card" id="signup" style="display:none;">
  <h1>Sign Up</h1>

  <label>Full Name</label>
  <input type="text" id="signupName">
  <small id="signupNameError"></small>

  <label>Email</label>
  <input type="email" id="signupEmail">
  <small id="signupEmailError"></small>

  <label>Password</label>
  <input type="password" id="signupPassword">
  <small id="signupPasswordError"></small>

  <label>Confirm Password</label>
  <input type="password" id="confirmPassword">
  <small id="confirmPasswordError"></small>

  <button class="login-btn" onclick="return validateSignup()">Create Account</button>

  <p class="switch-text">
    Already have an account?
    <span onclick="showSignin()">Sign In</span>
  </p>
</div>

  </main>

  <footer class="login-footer">
    <div>
      <p><strong>About infohub:</strong> Connect · Inspire · Empower</p>
      <p>© 2026 infohub Team.</p>
    </div>

    <div>
      <p>Contact Us →</p>
      <p>Submit News via Form</p>
    </div>
  </footer>

  <script>
function showSignup() {
  document.getElementById("signin").style.display = "none";
  document.getElementById("signup").style.display = "block";
}

function showSignin() {
  document.getElementById("signup").style.display = "none";
  document.getElementById("signin").style.display = "block";
}

function validateLogin() {
  var email = document.getElementById("loginEmail").value;
  var password = document.getElementById("loginPassword").value;
  var valid = true;

  document.getElementById("loginEmailError").innerHTML = "";
  document.getElementById("loginPasswordError").innerHTML = "";

  if (email == "") {
    document.getElementById("loginEmailError").innerHTML =
      "Please enter your email or matric number.";
    valid = false;
  }

  if (password == "") {
    document.getElementById("loginPasswordError").innerHTML =
      "Please enter your password.";
    valid = false;
  }

  if (password != "" && password.length < 6) {
    document.getElementById("loginPasswordError").innerHTML =
      "Password must be at least 6 characters.";
    valid = false;
  }

  if (valid == true) {
    window.location.href = "home.html";
  }

  return false;
}

function validateSignup() {
  var name = document.getElementById("signupName").value;
  var email = document.getElementById("signupEmail").value;
  var password = document.getElementById("signupPassword").value;
  var confirm = document.getElementById("confirmPassword").value;
  var valid = true;

  document.getElementById("signupNameError").innerHTML = "";
  document.getElementById("signupEmailError").innerHTML = "";
  document.getElementById("signupPasswordError").innerHTML = "";
  document.getElementById("confirmPasswordError").innerHTML = "";

  if (name == "") {
    document.getElementById("signupNameError").innerHTML =
      "Please enter your full name.";
    valid = false;
  }

  if (email == "") {
    document.getElementById("signupEmailError").innerHTML =
      "Please enter your email address.";
    valid = false;
  } else if (email.indexOf("@") == -1) {
    document.getElementById("signupEmailError").innerHTML =
      "Please enter a valid email address.";
    valid = false;
  }

  if (password == "") {
    document.getElementById("signupPasswordError").innerHTML =
      "Please enter a password.";
    valid = false;
  } else if (password.length < 6) {
    document.getElementById("signupPasswordError").innerHTML =
      "Password must be at least 6 characters.";
    valid = false;
  }

  if (confirm == "") {
    document.getElementById("confirmPasswordError").innerHTML =
      "Please confirm your password.";
    valid = false;
  } else if (password != confirm) {
    document.getElementById("confirmPasswordError").innerHTML =
      "Password and confirm password do not match.";
    valid = false;
  }

  if (valid == true) {
    alert("Account created successfully.");
    showSignin();
  }

  return false;
}
</script>

</body>
</html>