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

$user_id = $_SESSION['user_id'];

$result = $conn->query("
    SELECT p.*, c.category_name, d.DepartmentName
    FROM post p
    LEFT JOIN categories c ON p.cat_ID = c.cat_ID
    LEFT JOIN department d ON p.Department_ID = d.Department_ID
    WHERE p.User_ID = '$user_id' 
    ORDER BY p.Post_ID DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Submissions - InfoHub</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .submissions-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 20px;
            margin: 20px 0 40px;
        }
        .submissions-hero h1 { font-size: 48px; margin-bottom: 10px; }
        .submissions-hero p { font-size: 18px; opacity: 0.9; }
        .submissions-container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .submission-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .submission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        .submission-title { font-size: 22px; font-weight: 700; color: #333; margin: 0; }
        .submission-status { padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .submission-meta { display: flex; gap: 20px; margin-bottom: 15px; font-size: 14px; color: #667eea; }
        .submission-category { background: #f0f0f0; padding: 4px 12px; border-radius: 20px; color: #667eea; }
        .submission-content { color: #666; line-height: 1.6; margin-bottom: 15px; }
        .submission-image { max-width: 200px; max-height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; }
        .submission-date { color: #999; font-size: 12px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee; }
        .empty-submissions { text-align: center; padding: 60px 20px; background: white; border-radius: 16px; }
        .back-home { text-align: center; margin: 30px 0; }
        .delete-btn { background: #dc3545; color: white; border: none; padding: 8px 20px; border-radius: 8px; cursor: pointer; margin-top: 15px; }
        .delete-btn:hover { background: #c82333; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .alert-warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        
        .read-more-btn {
            background: #667eea;
            border: none;
            color: white;
            cursor: pointer;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            margin-top: 10px;
            display: inline-block;
        }
        .read-more-btn:hover {
            background: #764ba2;
        }
        
        .read-less-btn {
            background: #6c757d;
            border: none;
            color: white;
            cursor: pointer;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            margin-top: 10px;
        }
        .read-less-btn:hover {
            background: #5a6268;
        }
        
        .full-content {
            display: none;
            margin-top: 15px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            line-height: 1.8;
            color: #333;
            font-size: 15px;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            text-align: justify;
        }

        /* Navigation Active Link */
        .dashboard-links a.active {
            color: #667eea !important;
            font-weight: bold !important;
            border-bottom: 3px solid #667eea !important;
            padding-bottom: 8px !important;
        }
        .dashboard-links a:hover { color: #667eea !important; transition: color 0.2s ease !important; }
        .dashboard-links a.sign-in-btn {
            background: transparent !important;
            border: 1.5px solid #667eea !important;
            padding: 8px 20px !important;
            border-radius: 40px !important;
            color: #667eea !important;
        }
        .dashboard-links a.sign-in-btn:hover { background: #667eea !important; color: white !important; }

        .back-home { text-align: center; margin: 30px 0; }
        .back-home a { color: #667eea; text-decoration: none; }
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
            <a href="events.php">Events</a>
            <a href="submit.php">Get Published</a>
            <a href="my_submissions.php" class="active">My Submission</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
            <?php else: ?>
                <a href="index.php" class="sign-in-btn">Sign In</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
    <div class="submissions-hero">
        <h1>📋 My Submissions</h1>
        <p>Track and manage all your announcements, news, and events</p>
    </div>

    <div class="submissions-container">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success">✓ Submission successful! Your announcement is pending review.</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert-warning">✓ Submission deleted successfully!</div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="submission-card">
                    <div class="submission-header">
                        <h2 class="submission-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <span class="submission-status status-<?php echo $row['status']; ?>">
                            <?php 
                            if($row['status'] == 'pending') echo "⏳ Pending Review";
                            elseif($row['status'] == 'approved') echo "✓ Approved";
                            else echo "✗ Rejected";
                            ?>
                        </span>
                    </div>
                    <?php if($row['image_path']): ?>
                        <img src="<?php echo $row['image_path']; ?>" class="submission-image" alt="Submission image">
                    <?php endif; ?>
                    <div class="submission-meta">
                        <span>📅 <?php echo date('F j, Y', strtotime($row['created_at'] ?? 'now')); ?></span>
                        <span class="submission-category">🏷️ <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></span>
                            <?php if($row['DepartmentName']): ?>
                                <span>🏢 <?php echo htmlspecialchars($row['DepartmentName']); ?></span>
                            <?php endif; ?>
                    </div>
                    <div class="submission-content">
                        <?php 
                        $short_content = substr($row['content'], 0, 200);
                        echo nl2br(htmlspecialchars($short_content));
                        if(strlen($row['content']) > 200) echo "...";
                        ?>
                    </div>
                    
                    <button class="read-more-btn" onclick="this.style.display='none'; this.nextElementSibling.style.display='block'">📖 Read More</button>
                    <div class="full-content">
                        <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                        <br><br>
                        <button class="read-less-btn" onclick="this.parentElement.style.display='none'; this.parentElement.previousElementSibling.style.display='inline-block'">📖 Read Less</button>
                    </div>
                    
                    <div class="submission-date">Submitted: <?php echo date('F j, Y g:i A', strtotime($row['created_at'] ?? 'now')); ?></div>
                    <?php if($row['status'] == 'pending'): ?>
                        <button class="delete-btn" onclick="deleteSubmission(<?php echo $row['Post_ID']; ?>)">🗑️ Delete Submission</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-submissions">
                <p>📭 You haven't made any submissions yet.</p>
                <a href="submit.php">Create Your First Submission →</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="back-home"><a href="home.php">← Back to Home</a></div>
</main>

<footer class="dashboard-footer"><p>© 2026 InfoHub Team. Connect · Inspire · Empower</p></footer>

<script>
    function deleteSubmission(id) {
        if(confirm('Are you sure you want to delete this submission?')) {
            window.location.href = 'delete_post.php?id=' + id;
        }
    }
</script>

</body>
</html>