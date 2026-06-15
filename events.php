<?php
session_start();
$conn = new mysqli("localhost", "root", "", "infohub_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("
    SELECT p.*, c.category_name 
    FROM post p
    JOIN categories c ON p.cat_ID = c.cat_ID
    WHERE c.category_name = 'Event' AND p.status = 'approved' 
    ORDER BY p.Post_ID DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InfoHub - Events</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .events-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 20px;
            margin: 20px 0 40px;
        }
        .events-hero h1 { font-size: 48px; margin-bottom: 10px; }
        .events-hero p { font-size: 18px; opacity: 0.9; }
        .events-container { max-width: 900px; margin: 0 auto; padding: 20px; }
        
        .event-card {
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
        
        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .event-image {
            flex-shrink: 0;
            width: 180px;
            height: 120px;
        }
        
        .event-image img {
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
        
        .event-content {
            flex: 1;
        }
        
        .event-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
            font-size: 12px;
            color: #c62828;
        }
        
        .event-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f6392;
            margin-bottom: 10px;
        }
        
        .event-excerpt {
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
        
        .empty-events { text-align: center; padding: 60px 20px; background: white; border-radius: 16px; }
        .back-home { text-align: center; margin: 30px 0; }
        
        @media (max-width: 768px) {
            .event-card { flex-direction: column; }
            .event-image { width: 100%; height: 180px; }
        }
    </style>
</head>
<body>

<header class="dashboard-header">
    <div class="dashboard-navbar">
        <div class="dashboard-logo"><img src="logo.jpg" alt="InfoHub Logo"></div>
        <nav class="dashboard-links">
            <a href="home.php">Home</a>
            <a href="notice.php">Notice</a>
            <a href="news.php">News</a>
            <a href="events.php" class="active">Events</a>
            <a href="submit.php">Get Published</a>
            <a href="my_submissions.php">My Submission</a>
            <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
        </nav>
    </div>
</header>

<main>
    <div class="events-hero">
        <h1>📅 Upcoming Events</h1>
        <p>Discover and join campus events, workshops, and activities</p>
    </div>
    <div class="events-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="event-card" onclick="window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">
                    <div class="event-image">
                        <?php if($row['image_path'] && file_exists($row['image_path'])): ?>
                            <img src="<?php echo $row['image_path']; ?>" alt="Event image">
                        <?php else: ?>
                            <div class="default-image">📅</div>
                        <?php endif; ?>
                    </div>
                    <div class="event-content">
                        <div class="event-meta">
                            <span>📅 <?php echo date('F j, Y', strtotime($row['created_at'] ?? 'now')); ?></span>
                            <span>🏷️ <?php echo htmlspecialchars($row['category_name']); ?></span>
                        </div>
                        <h2 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <div class="event-excerpt"><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</div>
                        <a href="#" class="read-more" onclick="event.stopPropagation(); window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">View Event →</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-events"><p>📭 No events found.</p></div>
        <?php endif; ?>
    </div>
    <div class="back-home"><a href="home.php">← Back to Home</a></div>
</main>
<footer class="dashboard-footer"><p>© 2026 InfoHub Team. Connect · Inspire · Empower</p></footer>
</body>
</html>