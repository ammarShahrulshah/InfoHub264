<?php
$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get approved news from post table
$result = $conn->query("
    SELECT p.*, c.category_name 
    FROM post p
    JOIN categories c ON p.cat_ID = c.cat_ID
    WHERE c.category_name = 'News' AND p.status = 'approved' 
    ORDER BY p.Post_ID DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InfoHub - News (Visitor)</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .news-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 20px;
            margin: 20px 0 40px;
        }
        .news-hero h1 { font-size: 48px; margin-bottom: 10px; }
        .news-hero p { font-size: 18px; opacity: 0.9; }
        .news-container { max-width: 900px; margin: 0 auto; padding: 20px; }
        
        /* News card dengan gambar di tepi - SAMA MACAM HOME */
        .news-card {
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
        
        .news-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
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
        
        .news-content {
            flex: 1;
        }
        
        .news-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
            font-size: 12px;
            color: #c62828;
        }
        
        .news-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f6392;
            margin-bottom: 10px;
        }
        
        .news-excerpt {
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
        
        .read-more:hover {
            text-decoration: underline;
        }
        
        .empty-news {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        .back-home { text-align: center; margin: 30px 0; }
        .back-home a { color: #667eea; text-decoration: none; }

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
        
        @media (max-width: 768px) {
            .news-card {
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
            <a href="visitor.php">Home</a>
            <a href="notice_visitor.php">Notices</a>
            <a href="news_visitor.php" class="active">News</a>
            <a href="events_visitor.php">Events</a>
            <a href="index.php" class="sign-in-btn">Sign In</a>
        </nav>
    </div>
</header>

<main>
    <div class="news-hero">
        <h1>📰 Latest News</h1>
        <p>Stay updated with the newest announcements and stories from InfoHub</p>
    </div>

    <div class="news-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="news-card" onclick="window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">
                    <div class="news-image">
                        <?php if($row['image_path'] && file_exists($row['image_path'])): ?>
                            <img src="<?php echo $row['image_path']; ?>" alt="News image">
                        <?php else: ?>
                            <div class="default-image">📰</div>
                        <?php endif; ?>
                    </div>
                    <div class="news-content">
                        <div class="news-meta">
                            <span>📅 <?php echo date('F j, Y', strtotime($row['created_at'] ?? 'now')); ?></span>
                            <span>🏷️ <?php echo htmlspecialchars($row['category_name']); ?></span>
                        </div>
                        <h2 class="news-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <div class="news-excerpt"><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</div>
                        <a href="#" class="read-more" onclick="event.stopPropagation(); window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">Read More →</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-news">
                <p>📭 No news articles found.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="back-home">
        <a href="visitor.php">← Back to Home</a>
    </div>
</main>

<footer class="dashboard-footer">
    <p>© 2026 InfoHub Team. Connect · Inspire · Empower</p>
</footer>

</body>
</html>