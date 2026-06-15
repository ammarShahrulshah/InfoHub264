<?php
session_start();
$conn = new mysqli("localhost", "root", "", "infohub_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("
    SELECT p.*, c.category_name 
    FROM post p
    JOIN categories c ON p.cat_ID = c.cat_ID
    WHERE c.category_name = 'Notice' AND p.status = 'approved' 
    ORDER BY p.Post_ID DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InfoHub - Notices</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .notices-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 20px;
            margin: 20px 0 40px;
        }
        .notices-hero h1 { font-size: 48px; margin-bottom: 10px; }
        .notices-hero p { font-size: 18px; opacity: 0.9; }
        .notices-container { max-width: 900px; margin: 0 auto; padding: 20px; }
        
        .notice-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.2s;
            display: flex;
            gap: 20px;
            cursor: pointer;
        }
        
        .notice-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .notice-image {
            flex-shrink: 0;
            width: 180px;
            height: 120px;
        }
        
        .notice-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .default-image {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            border-radius: 12px;
        }
        
        .notice-content {
            flex: 1;
        }
        
        .notice-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
            font-size: 12px;
            color: #c62828;
        }
        
        .notice-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f6392;
            margin-bottom: 10px;
        }
        
        .notice-excerpt {
            color: #555;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 12px;
        }
        
        .read-more {
            color: #2c7da0;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }
        
        .empty-notices { text-align: center; padding: 60px 20px; background: white; border-radius: 16px; }
        .back-home { text-align: center; margin: 30px 0; }
        
        @media (max-width: 768px) {
            .notice-card { flex-direction: column; }
            .notice-image { width: 100%; height: 180px; }
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
        <div class="dashboard-logo"><img src="logo.jpg" alt="InfoHub Logo"></div>
        <nav class="dashboard-links">
            <a href="home.php">Home</a>
            <a href="notice.php" class="active">Notice</a>
            <a href="news.php">News</a>
            <a href="events.php">Events</a>
            <a href="submit.php">Get Published</a>
            <a href="my_submissions.php">My Submission</a>
            <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
        </nav>
    </div>
</header>

<main>
    <div class="notices-hero">
        <h1>📢 Official Notices</h1>
        <p>Important announcements and updates from the campus community</p>
    </div>
    <div class="notices-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="notice-card" onclick="window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">
                    <div class="notice-image">
                        <?php if($row['image_path'] && file_exists($row['image_path'])): ?>
                            <img src="<?php echo $row['image_path']; ?>" alt="Notice image">
                        <?php else: ?>
                            <div class="default-image">📢</div>
                        <?php endif; ?>
                    </div>
                    <div class="notice-content">
                        <div class="notice-meta">
                            <span>📅 <?php echo date('F j, Y', strtotime($row['created_at'] ?? 'now')); ?></span>
                            <span>🏷️ <?php echo htmlspecialchars($row['category_name']); ?></span>
                        </div>
                        <h2 class="notice-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <div class="notice-excerpt"><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</div>
                        <a href="#" class="read-more" onclick="event.stopPropagation(); window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">Read More →</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-notices"><p>📭 No notices found.</p></div>
        <?php endif; ?>
    </div>
    <div class="back-home"><a href="home.php">← Back to Home</a></div>
</main>
<footer class="dashboard-footer"><p>© 2026 InfoHub Team. Connect · Inspire · Empower</p></footer>
</body>
</html>