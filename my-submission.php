<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM submissions WHERE user_id='$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Submission</title>
  <link rel="stylesheet" href="submission.css">
  <link rel="stylesheet" href="dashboard.css">
  <style>
    .status-pending {
      color: orange;
      font-weight: bold;
    }
    .status-approved {
      color: green;
      font-weight: bold;
    }
    .status-rejected {
      color: red;
      font-weight: bold;
    }
    .success-message {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
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
      <a href="#">News</a>
      <a href="#">Events</a>
      <a href="submit.php">Get Published</a>
      <a href="my-submission.php">My Submission</a>
      <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
    </nav>
  </div>
</header>

<main class="dashboard-container">
  <section class="submission-box">
    <h1>My Submissions</h1>

    <?php if(isset($_GET['success'])): ?>
      <div class="success-message">
        ✓ Submission successful! Your announcement is pending review.
      </div>
    <?php endif; ?>

    <?php if($result->num_rows == 0): ?>
      <p style="text-align: center; padding: 50px;">
        You haven't made any submissions yet.
        <br><br>
        <a href="submit.php" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Create Your First Submission</a>
      </p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
           </tr>
        </thead>

        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td class="status-<?php echo $row['status']; ?>">
              <?php 
              if($row['status'] == 'pending') {
                  echo "⏳ Pending";
              } elseif($row['status'] == 'approved') {
                  echo "✓ Approved";
              } else {
                  echo "✗ Rejected";
              }
              ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</main>

</body>
</html>