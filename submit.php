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

$success_msg = "";
$error_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $content = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    // Handle image upload
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
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        } else {
            $error_msg = "Image type not allowed. Allowed: " . implode(', ', $allowed);
        }
    }
    
    if (empty($error_msg)) {
        $sql = "INSERT INTO submissions (user_id, title, category, content, image_path, status) 
                VALUES ('$user_id', '$title', '$category', '$content', '$image_path', 'pending')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: my-submission.php?success=1");
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
</head>

<body>

<header class="dashboard-header">
  <div class="dashboard-navbar">
    <div class="dashboard-logo">
      <img src="logo.jpg" alt="InfoHub Logo">
    </div>

    <nav class="dashboard-links">
      <a href="home.php">Home</a>
      <a href="#">News</a>
      <a href="#">Events</a>
      <a href="submit.php">Get Published</a>
      <a href="my-submission.php">My Submission</a>
      <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
    </nav>
  </div>
</header>

<main class="submit-section">
  <div class="submit-card">
    <h1>Submit Your Announcement</h1>
    
    <?php if($success_msg): ?>
      <p style="color: green; background: #e8f5e9; padding: 10px; border-radius: 5px;">
        <?php echo $success_msg; ?>
      </p>
    <?php endif; ?>
    
    <?php if($error_msg): ?>
      <p style="color: red; background: #ffebee; padding: 10px; border-radius: 5px;">
        <?php echo $error_msg; ?>
      </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateSubmit()">
      <label>Title</label>
      <input type="text" id="title" name="title" placeholder="Enter announcement title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
      <small id="titleError"></small>

      <label>Category</label>
      <select id="category" name="category">
        <option value="">Select category</option>
        <option value="News">News</option>
        <option value="Event">Event</option>
        <option value="Notice">Notice</option>
      </select>
      <small id="categoryError"></small>

      <label>Description</label>
      <textarea id="description" name="description" rows="5" placeholder="Write your announcement details (minimum 20 characters)"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
      <small id="descriptionError"></small>

      <label>Upload Image (JPG, PNG, GIF - Max 5MB)</label>
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