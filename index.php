<?php
session_start();
$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Sign Up
$show_signup = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $fullname = $conn->real_escape_string($_POST['signupName']);
    $email = $conn->real_escape_string($_POST['signupEmail']);
    $password = password_hash($_POST['signupPassword'], PASSWORD_DEFAULT);
    $role = 'user'; // Default role untuk user baru
    
    // Check email already registered
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    
    if ($check->num_rows > 0) {
        $signup_error = "Email already registered! Please use another email.";
        $show_signup = true;
    } else {
        $conn->query("INSERT INTO users (fullname, email, password, role) 
                      VALUES ('$fullname', '$email', '$password', '$role')");
        echo "<script>alert('Account created successfully! Please sign in.'); window.location='index.php';</script>";
        exit();
    }
}

// Handle Sign In
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin'])) {
    $email = $conn->real_escape_string($_POST['loginEmail']);
    $password = $_POST['loginPassword'];
    
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $login_error = "Wrong password!";
        }
    } else {
        $login_error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InfoHub Login</title>
  <link rel="stylesheet" href="login.css">
  <link rel="stylesheet" href="dashboard.css">
  <style>
    /* Additional styles for better display */
    .error-message {
      color: red;
      background: #ffebee;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
    }
  </style>
</head>

<body>

  <header class="login-navbar">
    <div class="login-logo">
      <img src="logo.jpg" alt="InfoHub Logo">
    </div>

    <nav>
      <a href="visitor.php">Home</a>
      <a href="notice_visitor.php">Notice</a>
      <a href="news_visitor.php">News</a>
      <a href="events_visitor.php">Events</a>
      <button class="signin-btn">Sign In</button>
    </nav>
  </header>

  <main class="login-container">

    <!-- Sign In Card -->
    <div class="login-card" id="signin" style="<?php echo ($show_signup) ? 'display:none;' : 'display:block;'; ?>">
      <h1>Sign In</h1>
      <?php if(isset($login_error)): ?>
        <div class="error-message"><?php echo $login_error; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <label>Email</label>
        <input type="text" name="loginEmail" id="loginEmail" required>
        <small id="loginEmailError"></small>

        <label>Password</label>
        <input type="password" name="loginPassword" id="loginPassword" required>
        <small id="loginPasswordError"></small>

        <button type="submit" name="signin" class="login-btn">Login to InfoHub</button>
      </form>

      <p class="switch-text">
        Don't have an account?
        <span onclick="showSignup()">Sign Up</span>
      </p>
    </div>

    <!-- Sign Up Card -->
    <div class="login-card" id="signup" style="<?php echo ($show_signup) ? 'display:block;' : 'display:none;'; ?>">
      <h1>Sign Up</h1>
      <?php if(isset($signup_error)): ?>
        <div class="error-message"><?php echo $signup_error; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <label>Full Name</label>
        <input type="text" name="signupName" id="signupName" required>
        <small id="signupNameError"></small>

        <label>Email</label>
        <input type="email" name="signupEmail" id="signupEmail" required>
        <small id="signupEmailError"></small>

        <label>Password</label>
        <input type="password" name="signupPassword" id="signupPassword" required>
        <small id="signupPasswordError"></small>

        <label>Confirm Password</label>
        <input type="password" id="confirmPassword" required>
        <small id="confirmPasswordError"></small>

        <button type="submit" name="signup" class="login-btn" onclick="return validateSignup()">Create Account</button>
      </form>

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

    function validateSignup() {
      var name = document.getElementById("signupName").value;
      var email = document.getElementById("signupEmail").value;
      var password = document.getElementById("signupPassword").value;
      var confirm = document.getElementById("confirmPassword").value;
      var valid = true;

      // Reset errors
      document.getElementById("signupNameError").innerHTML = "";
      document.getElementById("signupEmailError").innerHTML = "";
      document.getElementById("signupPasswordError").innerHTML = "";
      document.getElementById("confirmPasswordError").innerHTML = "";

      // Validate name
      if (name == "") {
        document.getElementById("signupNameError").innerHTML = "Please enter your full name.";
        valid = false;
      }

      // Validate email
      if (email == "") {
        document.getElementById("signupEmailError").innerHTML = "Please enter your email address.";
        valid = false;
      } else if (email.indexOf("@") == -1) {
        document.getElementById("signupEmailError").innerHTML = "Please enter a valid email address.";
        valid = false;
      }

      // Validate password
      if (password == "") {
        document.getElementById("signupPasswordError").innerHTML = "Please enter a password.";
        valid = false;
      } else if (password.length < 6) {
        document.getElementById("signupPasswordError").innerHTML = "Password must be at least 6 characters.";
        valid = false;
      }

      // Validate confirm password
      if (confirm == "") {
        document.getElementById("confirmPasswordError").innerHTML = "Please confirm your password.";
        valid = false;
      } else if (password != confirm) {
        document.getElementById("confirmPasswordError").innerHTML = "Password and confirm password do not match.";
        valid = false;
      }

      return valid;
    }
  </script>

</body>
</html>