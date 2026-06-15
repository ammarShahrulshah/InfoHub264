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
$success_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $category_name = $conn->real_escape_string($_POST['category']);
    $content = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    // Get cat_ID from category name
    $cat_result = $conn->query("SELECT cat_ID FROM categories WHERE category_name = '$category_name'");
    if ($cat_result && $cat_result->num_rows > 0) {
        $cat_row = $cat_result->fetch_assoc();
        $cat_id = $cat_row['cat_ID'];
    } else {
        $cat_id = 1; // Default to Uncategorized
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
                // File uploaded successfully
            } else {
                $error_msg = "Failed to upload image.";
            }
        } else {
            $error_msg = "Image type not allowed. Allowed: " . implode(', ', $allowed);
        }
    }
    
    if (empty($error_msg)) {
        // Insert into post table
        $sql = "INSERT INTO post (User_ID, title, content, status, cat_ID, image_path) 
                VALUES ('$user_id', '$title', '$content', 'pending', '$cat_id', '$image_path')";
        
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
    
    /* Sign In button */
    .dashboard-links a.sign-in-btn {
        background: transparent !important;
        border: 1.5px solid #667eea !important;
        padding: 8px 20px !important;
        border-radius: 40px !important;
        color: #667eea !important;
    }
    
    .dashboard-links a.sign-in-btn:hover {
        background: #667eea !important;
        color: white !important;
    }

    /* Make textarea paste-friendly */
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        resize: vertical;
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
      <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
    </nav>
  </div>
</header>

<main class="submit-section">
  <div class="submit-card">
    <h1>Submit Your Announcement</h1>
    
    <?php if($error_msg): ?>
      <p style="color: red; background: #ffebee; padding: 10px; border-radius: 5px;">
        <?php echo $error_msg; ?>
      </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateSubmit()">
      <label>Title</label>
      <input type="text" id="title" name="title" placeholder="Enter announcement title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
      <small id="titleError"></small>

      <label>Category</label>
      <select id="category" name="category" required>
        <option value="">Select category</option>
        <option value="News">News</option>
        <option value="Event">Event</option>
        <option value="Notice">Notice</option>
      </select>
      <small id="categoryError"></small>

      <label>Description</label>
      <textarea id="description" name="description" rows="5" placeholder="Write your announcement details (minimum 20 characters)" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
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