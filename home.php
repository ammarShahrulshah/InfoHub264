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

$result = $conn->query("
    SELECT p.*, c.category_name, d.DepartmentName
    FROM post p
    JOIN categories c ON p.cat_ID = c.cat_ID
    LEFT JOIN department d ON p.Department_ID = d.Department_ID
    WHERE p.status = 'approved' 
    ORDER BY p.created_at DESC LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InfoHub Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .dashboard-links a.active {
            color: #667eea !important;
            font-weight: bold !important;
            border-bottom: 3px solid #667eea !important;
            padding-bottom: 8px !important;
        }
            
        .news-item {
            background: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            gap: 20px;
        }
        
        .news-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .news-image {
            flex-shrink: 0;
            width: 180px;
            height: 120px;
        }
        
        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .news-content {
            flex: 1;
        }
        
        .news-content h4 {
            font-size: 18px;
            font-weight: 700;
            color: #1f6392;
            margin-bottom: 8px;
        }
        
        .news-content small {
            color: #c62828 !important;
            font-size: 12px;
            display: block;
            margin-bottom: 10px;
        }
        
        .news-content p {
            color: #555;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 12px;
        }
        
        .read-more-link {
            color: #2c7da0;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }
        
        .read-more-link:hover {
            text-decoration: underline;
        }

        .default-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            border-radius: 12px;
            width: 100%;
            height: 100%;
        }
        
        .side-btn {
            display: block;
            margin-top: 12px;
            padding: 10px;
            background: #eef2f5;
            color: #1f6392;
            border-radius: 20px;
            text-decoration: none;
            text-align: center;
            transition: background 0.2s;
        }
        
        .side-btn:hover {
            background: #e0e8ed;
        }
        
        @media (max-width: 768px) {
            .news-item {
                flex-direction: column;
            }
            .news-image {
                width: 100%;
                height: 180px;
            }
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
            <a href="home.php" class="active">Home</a>
            <a href="notice.php">Notice</a>
            <a href="news.php">News</a>
            <a href="events.php">Events</a>
            <a href="submit.php">Get Published</a>
            <a href="my_submissions.php">My Submission</a>
            <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
        </nav>
    </div>
</header>

<main class="dashboard-container">
    <section class="hero">
        <h1>Welcome to InfoHub</h1>
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
        <a href="my_submissions.php" class="action-card">
            <h3>📋 My Submission</h3>
            <p>Check your submission status.</p>
        </a>
        <a href="news.php" class="action-card">
            <h3>📰 Latest News</h3>
            <p>View latest campus updates.</p>
        </a>
    </section>

    <section class="content-grid">
        <div class="updates-section">
            <h2>Latest Updates</h2>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="news-item" onclick="window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">
                        <div class="news-image">
                            <?php if($row['image_path'] && file_exists($row['image_path'])): ?>
                                <img src="<?php echo $row['image_path']; ?>" alt="Post image">
                            <?php else: ?>
                                <div class="default-image">
                                    <?php 
                                    $icon = '📰';
                                    if($row['category_name'] == 'Event') $icon = '📅';
                                    elseif($row['category_name'] == 'Notice') $icon = '📢';
                                    echo $icon;
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="news-content">
                            <small>
                                <?php echo date('F j, Y', strtotime($row['created_at'] ?? 'now')); ?> · 
                                <?php echo htmlspecialchars($row['category_name']); ?>
                                <?php if($row['DepartmentName']): ?>
                                    · 🏢 <?php echo htmlspecialchars($row['DepartmentName']); ?>
                                <?php endif; ?>
                            </small>
                            <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</p>
                            <a href="#" class="read-more-link" onclick="event.stopPropagation(); window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">Read More →</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="news-item">
                    <div class="news-content">
                        <p>No updates yet. <a href="submit.php">Be the first to submit!</a></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <aside class="sidebar">
            <div class="info-card">
                <h3>About InfoHub</h3>
                <p>InfoHub is a campus information system for students, lecturers, clubs and staff.</p>
            </div>
            <div class="info-card">
                <h3>User Menu</h3>
                <a href="submit.php" class="side-btn">Submit News</a>
                <a href="my_submissions.php" class="side-btn">View My Submission</a>
            </div>
        </aside>
    </section>
</main>

<footer class="dashboard-footer">
    <p>© 2026 InfoHub Team. Connect · Inspire · Empower</p>
</footer>

<script>
    document.getElementById('searchBtn').addEventListener('click', function() {
        var query = document.getElementById('searchInput').value.trim();
        if (query) {
            window.location.href = 'search.php?q=' + encodeURIComponent(query);
        } else {
            alert('Please enter a search term');
        }
    });
    
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('searchBtn').click();
        }
    });
</script>

</body>
</html>