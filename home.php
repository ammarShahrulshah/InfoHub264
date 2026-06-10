<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InfoHub Dashboard</title>
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

  <main class="dashboard-container">

    <section class="hero">
      <h1>Welcome to InfoHub, <?php echo $_SESSION['fullname']; ?>!</h1>
      <p>Connect · Inspire · Empower</p>

      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search announcement, news, or events...">
        <button id="searchBtn">Search →</button>
      </div>
    </section>

    <section class="quick-actions">
      <a href="submit.php" class="action-card">
        <h3>✍️ Get Published</h3>
        <p>Submit your announcement, event, or notice.</p>
      </a>

      <a href="my-submission.php" class="action-card">
        <h3>📋 My Submission</h3>
        <p>Check your submission status.</p>
      </a>

      <a href="#" class="action-card">
        <h3>📰 Latest News</h3>
        <p>View latest campus updates.</p>
      </a>
    </section>

    <section class="content-grid">

      <div class="updates-section">
        <h2>Latest Updates</h2>
        <div class="news-list" id="newsList"></div>
      </div>

      <aside class="sidebar">
        <div class="info-card">
          <h3>About InfoHub</h3>
          <p>InfoHub is a campus information system for students, lecturers, clubs and staff.</p>
        </div>

        <div class="info-card">
          <h3>User Menu</h3>
          <a href="submit.php" class="side-btn">Submit News</a>
          <a href="my-submission.php" class="side-btn">View My Submission</a>
        </div>
      
      </aside>

    </section>

  </main>

  <footer class="dashboard-footer">
    <p>© 2026 InfoHub Team.</p>
  </footer>

  <div id="articleModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle"></h2>
        <span class="close-modal">&times;</span>
      </div>

      <div class="modal-body">
        <p id="modalMeta"></p>
        <p id="modalContent"></p>
      </div>
    </div>
  </div>

  <script src="script.js"></script>

</body>
</html>