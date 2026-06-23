<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admin data from users table
$user_id = $_SESSION['user_id'];
$user_result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
$user_data = $user_result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if email already exists (except own email)
    $check = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != '$user_id'");
    if ($check->num_rows > 0) {
        $error_msg = "Email already registered by another user!";
    } else {
        $conn->query("UPDATE users SET fullname='$fullname', email='$email' WHERE id='$user_id'");
        $_SESSION['fullname'] = $fullname;
        $success_msg = "Profile updated successfully!";
        // Refresh user data
        $user_result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
        $user_data = $user_result->fetch_assoc();
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password == $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$hashed_password' WHERE id='$user_id'");
            $pass_msg = "Password changed successfully!";
        } else {
            $pass_error = "New passwords do not match!";
        }
    } else {
        $pass_error = "Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoHub - Settings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tab-navigation {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .tab-btn:hover {
            background: #f0f0f0;
        }
        .tab-btn.active {
            background: #667eea;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .settings-form {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-save {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-save:hover {
            background: #764ba2;
        }
        .setting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .setting-row:last-child {
            border-bottom: none;
        }
        .text-muted {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #667eea;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .notification {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .notification.error {
            background: #f8d7da;
            color: #721c24;
        }
        .hidden {
            display: none;
        }
        .text-blue {
            color: #667eea;
        }
        .data-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn-data {
            background: #f0f0f0;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-data:hover {
            background: #e0e0e0;
        }
        small {
            color: #dc3545;
            font-size: 12px;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">
            <img src="logo.jpg" alt="InfoHub Logo" class="logo-img">
        </div>
        <div class="user-profile">
            <span>Hello, <?php echo $_SESSION['fullname']; ?>!</span>
            <button class="btn-logout" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </header>

    <div class="layout-container">
        <aside class="sidebar">
            <h3>Dashboard Menu</h3>
            <ul>
                <li><a href="admin.php">Home</a></li>
                <li><a href="published.php">Published Posts</a></li>
                <li class="active"><a href="settings.php">Edit Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>PROFILE SETTINGS</h2>
            <p style="margin-bottom: 20px; color: #666;">Configure your profile.</p>

            <!-- Success/Error Messages -->
            <?php if(isset($success_msg)): ?>
                <div class="notification">✓ <?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if(isset($error_msg)): ?>
                <div class="notification error">✗ <?php echo $error_msg; ?></div>
            <?php endif; ?>
            <?php if(isset($pass_msg)): ?>
                <div class="notification">✓ <?php echo $pass_msg; ?></div>
            <?php endif; ?>
            <?php if(isset($pass_error)): ?>
                <div class="notification error">✗ <?php echo $pass_error; ?></div>
            <?php endif; ?>

            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-btn active" data-tab="profile">👤 My Profile</button>
            </div>

            <!-- ================= TAB 1: PROFILE ================= -->
            <div id="profile" class="tab-content active">
                <h3>Admin Profile</h3>
                <form class="settings-form" method="POST" onsubmit="return validateProfile()">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user_data['fullname']); ?>" class="form-control">
                        <small id="fullnameError"></small>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" class="form-control">
                        <small id="emailError"></small>
                    </div>
                    
                    <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">
                    
                    <button type="submit" name="update_profile" class="btn-save">Save Profile Changes</button>
                </form>
                
                <!-- Change Password Form -->
                <h3 style="margin-top: 30px;">Change Password</h3>
                <form class="settings-form" method="POST" onsubmit="return validatePassword()">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="••••••••">
                        <small id="currentPasswordError"></small>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimum 6 characters">
                        <small id="newPasswordError"></small>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm new password">
                        <small id="confirmPasswordError"></small>
                    </div>
                    <button type="submit" name="change_password" class="btn-save">Change Password</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Tab switching functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.getAttribute('data-tab');
                
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                btn.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        function validateProfile() {
            var fullname = document.getElementById("fullname").value.trim();
            var email = document.getElementById("email").value.trim();
            var valid = true;

            document.getElementById("fullnameError").innerHTML = "";
            document.getElementById("emailError").innerHTML = "";

            if (fullname == "") {
                document.getElementById("fullnameError").innerHTML = "Please enter your full name.";
                valid = false;
            }

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

            document.getElementById("currentPasswordError").innerHTML = "";
            document.getElementById("newPasswordError").innerHTML = "";
            document.getElementById("confirmPasswordError").innerHTML = "";

            if (current == "") {
                document.getElementById("currentPasswordError").innerHTML = "Please enter your current password.";
                valid = false;
            }

            if (newPass == "") {
                document.getElementById("newPasswordError").innerHTML = "Please enter a new password.";
                valid = false;
            } else if (newPass.length < 6) {
                document.getElementById("newPasswordError").innerHTML = "Password must be at least 6 characters.";
                valid = false;
            }

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