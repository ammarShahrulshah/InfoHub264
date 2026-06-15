<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post_id = $_GET['id'];

$result = $conn->query("
    SELECT p.*, u.fullname, c.category_name 
    FROM post p
    JOIN users u ON p.User_ID = u.id
    JOIN categories c ON p.cat_ID = c.cat_ID
    WHERE p.Post_ID = '$post_id' AND p.status = 'approved'
");

$row = $result->fetch_assoc();

if (!$row) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['title']); ?> - InfoHub</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="login.css">
    <style>
        .article-page {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .article-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .article-category {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
        }
        .article-card h1 {
            font-size: 32px;
            margin-bottom: 15px;
            color: #333;
        }
        .article-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .article-image-box {
            display: flex;
             margin: 20px 10px;
             height: auto;
             justify-content: center;
             align-items: center;
             overflow: hidden;
        }
        
        .article-image {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }
        
        .article-content {
    line-height: 1.8;
    font-size: 16px;
}

.article-content p {
    margin-bottom: 15px;
}

.article-content strong {
    color: #1f6392;
}
        
        /* Navigation styles */
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
        .logout-btn {
            border: 1px solid #4000ff;
            padding: 8px 16px;
            border-radius: 20px;
            color: #2c7da0 !important;
            background: transparent;
        }
        .logout-btn:hover {
            background: #2c7da0;
            color: white !important;
        }
        
        @media (max-width: 768px) {
            .article-card h1 { font-size: 24px; }
            .article-card { padding: 20px; }
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
            .article-image {
                max-height: 250px;
            }
        }

        .sign-in-btn {
    border: 1px solid #2c7da0;
    padding: 8px 16px;
    border-radius: 20px;
    color: #2c7da0 !important;
    background: transparent;
    text-decoration: none;
}

.sign-in-btn:hover {
    background: #2c7da0;
    color: white !important;
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
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
            <?php else: ?>
                <a href="index.php" class="sign-in-btn">Sign In</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="dashboard-container">
    <section class="article-page">
        <a href="javascript:history.back()" class="back-link">← Back</a>
        <article class="article-card">
            <div class="article-category"><?php echo htmlspecialchars($row['category_name']); ?></div>
            <h1><?php echo htmlspecialchars($row['title']); ?></h1>
            <p class="article-meta">
                📅 <?php echo date('F j, Y', strtotime($row['created_at'])); ?> 
                · 👤 By: <?php echo htmlspecialchars($row['fullname']); ?>
            </p>
            
            <?php if($row['image_path'] && file_exists($row['image_path'])): ?>
                <div class="article-image-box">
                    <img src="<?php echo $row['image_path']; ?>" class="article-image" alt="Article image">
                </div>
            <?php endif; ?>
            
           <div class="article-content">
    <?php echo nl2br(htmlspecialchars($row['content'])); ?>
</div>
        </article>
    </section>
</main>

<footer class="login-footer">
    <div>
        <p><strong>About infohub:</strong> Connect · Inspire · Empower</p>
        <p>© 2026 infohub Team.</p>
    </div>
    <div>
        <p>Contact Us →</p>
        <p>Submit News via Form</p>
    </div>
</footer>

</body>
</html>