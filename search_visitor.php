<?php
$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

$result = $conn->query("
    SELECT p.*, c.category_name, u.fullname
    FROM post p
    JOIN categories c ON p.cat_ID = c.cat_ID
    JOIN users u ON p.User_ID = u.id
    WHERE p.status = 'approved' 
    AND (p.title LIKE '%$search%' 
    OR p.content LIKE '%$search%'
    OR c.category_name LIKE '%$search%')
    ORDER BY p.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results - InfoHub Visitor</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #f4f7fc;
            font-family: Arial, sans-serif;
        }
        .dashboard-container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            padding-bottom: 20px;
            width: 100%;
        }
        .search-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 20px;
            margin: 20px 0 40px;
        }
        .search-hero h1 { font-size: 48px; margin-bottom: 10px; }
        .search-hero p { font-size: 18px; opacity: 0.9; margin-bottom: 25px; }
        .hero-search-box {
            max-width: 500px;
            margin: 0 auto;
            display: flex;
            background: white;
            border-radius: 50px;
            overflow: hidden;
        }
        .hero-search-box input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            outline: none;
            font-size: 16px;
        }
        .hero-search-box button {
            padding: 0 25px;
            border: none;
            background: #667eea;
            color: white;
            cursor: pointer;
            font-weight: 600;
        }
        .hero-search-box button:hover { background: #764ba2; }
        .search-container { max-width: 900px; margin: 0 auto; padding: 20px; }
        .results-count {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .results-count span { font-weight: bold; color: #667eea; }
        .result-card {
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
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        .result-image {
            flex-shrink: 0;
            width: 180px;
            height: 120px;
        }
        .result-image img {
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
        .result-content { flex: 1; }
        .result-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
            font-size: 12px;
            color: #c62828;
        }
        .result-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f6392;
            margin-bottom: 10px;
        }
        .result-excerpt {
            color: #555;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        .result-author { color: #999; font-size: 12px; }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .dashboard-header {
            background: white;
            border-bottom: 1px solid #ddd;
        }
        .dashboard-navbar {
            width: 100%;        
            padding: 15px 40px;   
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }
        .dashboard-logo img { height: 50px; width: auto; }
        .dashboard-links {
            display: flex;
            gap: 24px;
            align-items: center;
            margin-left: auto;
        }
        .dashboard-links a {
            text-decoration: none;
            color: #1a2c3e;
            font-weight: 500;
        }
        .dashboard-links a:hover { color: #667eea; }
        .sign-in-btn {
            border: 1px solid #2c7da0;
            padding: 8px 16px;
            border-radius: 20px;
            color: #2c7da0 !important;
            background: transparent;
        }
        .sign-in-btn:hover {
            background: #2c7da0;
            color: white !important;
        }
        .dashboard-footer {
            text-align: center;
            padding: 20px;
            background: white;
            border-top: 1px solid #ddd;
            margin-top: auto;
            width: 100%;
        }
        @media (max-width: 768px) {
            .result-card { flex-direction: column; }
            .result-image { width: 100%; height: 180px; }
            .search-hero h1 { font-size: 32px; }
            .dashboard-navbar {
                flex-direction: column;
                height: auto;
                gap: 10px;
                padding: 15px 20px;
            }
            .dashboard-links {
                flex-wrap: wrap;
                justify-content: center;
                margin-left: 0;
            }
        }

        .dashboard-logo img {
            height: 80px !important;
            width: auto;
            max-height: 80px;
            object-fit: contain;
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
            <a href="notice_visitor.php">Notice</a>
            <a href="news_visitor.php">News</a>
            <a href="events_visitor.php">Events</a>
            <a href="index.php" class="sign-in-btn">Sign In</a>
        </nav>
    </div>
</header>

<main class="dashboard-container">
    <div class="search-hero">
        <h1>🔍 Search Results</h1>
        <?php if(!empty($search)): ?>
            <p>Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong></p>
        <?php endif; ?>
        <div class="hero-search-box">
            <form action="search_visitor.php" method="get" style="display: flex; width: 100%;">
                <input type="text" name="q" placeholder="Search announcements, news, or events..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search →</button>
            </form>
        </div>
    </div>

    <div class="search-container">
        <?php if (!empty($search)): ?>
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="results-count">Found <span><?php echo $result->num_rows; ?></span> result(s)</div>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="result-card" onclick="window.location.href='article.php?id=<?php echo $row['Post_ID']; ?>'">
                        <div class="result-image">
                            <?php if($row['image_path'] && file_exists($row['image_path'])): ?>
                                <img src="<?php echo $row['image_path']; ?>" alt="Image">
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
                        <div class="result-content">
                            <div class="result-meta">
                                <span>📅 <?php echo date('F j, Y', strtotime($row['created_at'] ?? 'now')); ?></span>
                                <span>🏷️ <?php echo htmlspecialchars($row['category_name']); ?></span>
                            </div>
                            <h2 class="result-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                            <p class="result-excerpt"><?php echo htmlspecialchars(substr($row['content'], 0, 120)); ?>...</p>
                            <p class="result-author">👤 By: <?php echo htmlspecialchars($row['fullname']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>😔 No results found for "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
                    <p>Try searching with different keywords or check your spelling.</p>
                    <a href="visitor.php" class="back-link">← Back to Home</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <p>🔍 Please enter a search term</p>
                <a href="visitor.php" class="back-link">← Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer class="dashboard-footer">
    <p>© 2026 InfoHub Team. Connect · Inspire · Empower</p>
</footer>

</body>
</html>