<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// Get user data
$result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if email already exists (except own email)
    $check = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != '$user_id'");
    if ($check->num_rows > 0) {
        $error_msg = "Email already registered by another user!";
    } else {
        $conn->query("UPDATE users SET fullname = '$fullname', email = '$email' WHERE id = '$user_id'");
        $_SESSION['fullname'] = $fullname;
        $success_msg = "Profile updated successfully!";
        // Refresh user data
        $result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
        $user = $result->fetch_assoc();
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    if (password_verify($current, $user['password'])) {
        if ($new == $confirm) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password = '$hashed' WHERE id = '$user_id'");
            $success_msg = "Password changed successfully!";
        } else {
            $error_msg = "New passwords do not match!";
        }
    } else {
        $error_msg = "Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - InfoHub</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .profile-container h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-save {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-save:hover {
            background: #764ba2;
        }
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .divider {
            border: none;
            border-top: 2px solid #eee;
            margin: 30px 0;
        }
        h3 {
            color: #333;
            margin-bottom: 20px;
        }
        small {
            color: #dc3545;
            font-size: 12px;
            display: block;
            margin-top: 3px;
        }
        .error-text {
            color: #dc3545;
            font-size: 12px;
            display: block;
            margin-top: 3px;
        }

        .dashboard-links a.active {
            color: #667eea !important;
            font-weight: bold !important;
            border-bottom: 3px solid #667eea !important;
            padding-bottom: 8px !important;
        }
    </style>
</head>
<body>

<header class="dashboard-header">
    <div class="dashboard-navbar">
        <div class="dashboard-logo">
            <img src="logo.jpg" alt="InfoHub Logo">
        </div>
        <nav class="dashboard-links">
            <a href="home.php">Home</a>
            <a href="notice.php">Notice</a>
            <a href="news.php">News</a>
            <a href="events.php">Events</a>
            <a href="submit.php">Get Published</a>
            <a href="my_submissions.php">My Submission</a>
            <a href="edit_profile.php" class="active">Edit Profile</a>
            <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
        </nav>
    </div>
</header>

<main>
    <div class="profile-container">
        <h1>👤 Edit Profile</h1>
        
        <?php if($success_msg): ?>
            <div class="success-msg">✓ <?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if($error_msg): ?>
            <div class="error-msg">✗ <?php echo $error_msg; ?></div>
        <?php endif; ?>

        <!-- Update Profile Form -->
        <form method="POST" onsubmit="return validateProfile()">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>">
                <small id="fullnameError"></small>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                <small id="emailError"></small>
            </div>
            <button type="submit" name="update_profile" class="btn-save">Update Profile</button>
        </form>

        <hr class="divider">

        <!-- Change Password Form -->
        <h3>🔒 Change Password</h3>
        <form method="POST" onsubmit="return validatePassword()">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" id="current_password" placeholder="Enter current password">
                <small id="currentPasswordError"></small>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="Enter new password (min 6 characters)">
                <small id="newPasswordError"></small>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                <small id="confirmPasswordError"></small>
            </div>
            <button type="submit" name="change_password" class="btn-save">Change Password</button>
        </form>

        <a href="home.php" class="back-link">← Back to Home</a>
    </div>
</main>

<footer class="dashboard-footer">
    <p>© 2026 InfoHub Team. Connect · Inspire · Empower</p>
</footer>

<script>
function validateProfile() {
    var fullname = document.getElementById("fullname").value.trim();
    var email = document.getElementById("email").value.trim();
    var valid = true;

    // Reset errors
    document.getElementById("fullnameError").innerHTML = "";
    document.getElementById("emailError").innerHTML = "";

    // Validate Full Name
    if (fullname == "") {
        document.getElementById("fullnameError").innerHTML = "Please enter your full name.";
        valid = false;
    }

    // Validate Email
    if (email == "") {
        document.getElementById("emailError").innerHTML = "Please enter your email address.";
        valid = false;
    } else if (email.indexOf("@") == -1) {
        document.getElementById("emailError").innerHTML = "Please enter a valid email address (e.g., name@domain.com).";
        valid = false;
    } else if (email.indexOf(".") == -1) {
        document.getElementById("emailError").innerHTML = "Please enter a valid email address with a domain (e.g., .com, .my).";
        valid = false;
    }

    return valid;
}

function validatePassword() {
    var current = document.getElementById("current_password").value;
    var newPass = document.getElementById("new_password").value;
    var confirm = document.getElementById("confirm_password").value;
    var valid = true;

    // Reset errors
    document.getElementById("currentPasswordError").innerHTML = "";
    document.getElementById("newPasswordError").innerHTML = "";
    document.getElementById("confirmPasswordError").innerHTML = "";

    // Validate Current Password
    if (current == "") {
        document.getElementById("currentPasswordError").innerHTML = "Please enter your current password.";
        valid = false;
    }

    // Validate New Password
    if (newPass == "") {
        document.getElementById("newPasswordError").innerHTML = "Please enter a new password.";
        valid = false;
    } else if (newPass.length < 6) {
        document.getElementById("newPasswordError").innerHTML = "Password must be at least 6 characters.";
        valid = false;
    }

    // Validate Confirm Password
    if (confirm == "") {
        document.getElementById("confirmPasswordError").innerHTML = "Please confirm your new password.";
        valid = false;
    } else if (newPass != confirm) {
        document.getElementById("confirmPasswordError").innerHTML = "Passwords do not match.";
        valid = false;
    }

    return valid;
}
</script>

</body>
</html>