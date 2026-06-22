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

$error_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $category_name = $conn->real_escape_string($_POST['category']);
    $content = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    // Handle Department/Club 
    $department_input = isset($_POST['department']) ? trim($_POST['department']) : '';
    $department_id = NULL;
    
    if (!empty($department_input)) {
        $dept_name = $conn->real_escape_string($department_input);
        $check = $conn->query("SELECT Department_ID FROM department WHERE DepartmentName = '$dept_name'");
        if ($check && $check->num_rows > 0) {
            $department_id = $check->fetch_assoc()['Department_ID'];
        } else {
            $conn->query("INSERT INTO department (DepartmentName) VALUES ('$dept_name')");
            $department_id = $conn->insert_id;
        }
    }
    
    // Get cat_ID from category name
    $cat_result = $conn->query("SELECT cat_ID FROM categories WHERE category_name = '$category_name'");
    if ($cat_result && $cat_result->num_rows > 0) {
        $cat_row = $cat_result->fetch_assoc();
        $cat_id = $cat_row['cat_ID'];
    } else {
        $cat_id = 1; 
    }
    
    // Handle image upload (optional)
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = "uploads/submissions/";
        
        // Create folder if not exists
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        
        if (in_array($image_ext, $allowed)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) { 
            } else {
                $error_msg = "Failed to upload image.";
            }
        } else {
            $error_msg = "Image type not allowed. Allowed: " . implode(', ', $allowed);
        }
    }
    
    if (empty($error_msg)) {
        // Insert into post table
        $sql = "INSERT INTO post (User_ID, title, content, status, cat_ID, image_path, Department_ID) 
                VALUES ('$user_id', '$title', '$content', 'pending', '$cat_id', '$image_path', " . ($department_id ? "'$department_id'" : "NULL") . ")";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: my_submissions.php?success=1");
            exit();
        } else {
            $error_msg = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Announcement</title>
    <link rel="stylesheet" href="submit.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Navigation Active Link */
        .dashboard-links a.active {
            color: #667eea !important;
            font-weight: bold !important;
            border-bottom: 3px solid #667eea !important;
            padding-bottom: 8px !important;
        }
        
        .dashboard-links a:hover {
            color: #667eea !important;
            transition: color 0.2s ease !important;
        }
        
        textarea, input[type="text"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        .submit-card {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .submit-section {
            padding: 40px 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        small {
            color: #dc3545;
            font-size: 12px;
            display: block;
            margin-top: 3px;
        }
        button[type="submit"] {
            margin-top: 20px;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
        }
        button[type="submit"]:hover {
            background: #764ba2;
        }
        .error-msg {
            color: red;
            background: #ffebee;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
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
            <a href="submit.php" class="active">Get Published</a>
            <a href="my_submissions.php">My Submission</a>
            <a href="edit_profile.php">Edit Profile</a>
            <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
        </nav>
    </div>
</header>

<main class="submit-section">
    <div class="submit-card">
        <h1>📝 Submit Your Announcement</h1>
        
        <?php if($error_msg): ?>
            <div class="error-msg"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" onsubmit="return validateSubmit()">
            <label>Title *</label>
            <input type="text" id="title" name="title" placeholder="Enter announcement title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
            <small id="titleError"></small>

            <label>Category *</label>
            <select id="category" name="category" required>
                <option value="">Select category</option>
                <option value="News" <?php echo (isset($_POST['category']) && $_POST['category']=='News') ? 'selected' : ''; ?>>News</option>
                <option value="Event" <?php echo (isset($_POST['category']) && $_POST['category']=='Event') ? 'selected' : ''; ?>>Event</option>
                <option value="Notice" <?php echo (isset($_POST['category']) && $_POST['category']=='Notice') ? 'selected' : ''; ?>>Notice</option>
            </select>
            <small id="categoryError"></small>

            <label>Department / Club</label>
            <input type="text" id="department" name="department" placeholder="e.g.: Computer Science, Sports Club, Business Faculty" value="<?php echo isset($_POST['department']) ? htmlspecialchars($_POST['department']) : ''; ?>">
            <small id="departmentError"></small>

            <label>Description *</label>
            <textarea id="description" name="description" rows="6" placeholder="Write your announcement details (minimum 20 characters)" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            <small id="descriptionError"></small>

            <label>Upload Image (Optional - JPG, PNG, GIF)</label>
            <input type="file" id="imageUpload" name="image" accept="image/*">
            <small id="imageError"></small>

            <button type="submit" name="submit">Submit Announcement</button>
        </form>
    </div>
</main>

<script>
function validateSubmit() {
    var title = document.getElementById("title").value;
    var category = document.getElementById("category").value;
    var description = document.getElementById("description").value;
    var valid = true;

    document.getElementById("titleError").innerHTML = "";
    document.getElementById("categoryError").innerHTML = "";
    document.getElementById("departmentError").innerHTML = "";
    document.getElementById("descriptionError").innerHTML = "";
    document.getElementById("imageError").innerHTML = "";

    if (title == "") {
        document.getElementById("titleError").innerHTML = "Please enter announcement title.";
        valid = false;
    }

    if (category == "") {
        document.getElementById("categoryError").innerHTML = "Please select a category.";
        valid = false;
    }

    if (description == "") {
        document.getElementById("descriptionError").innerHTML = "Please enter announcement description.";
        valid = false;
    } else if (description.length < 20) {
        document.getElementById("descriptionError").innerHTML = "Description must be at least 20 characters.";
        valid = false;
    }

    return valid;
}
</script>

</body>
</html>