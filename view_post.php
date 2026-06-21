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
    SELECT p.*, u.fullname, u.email, c.category_name, d.DepartmentName
    FROM post p
    JOIN users u ON p.User_ID = u.id
    JOIN categories c ON p.cat_ID = c.cat_ID
    LEFT JOIN department d ON p.Department_ID = d.Department_ID
    WHERE p.Post_ID = '$post_id'
");

$row = $result->fetch_assoc();

// Check if user is admin
$is_admin = (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin');
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['title']); ?> - InfoHub</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fc;
        }

        .dashboard-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 2px solid #000000;
        }

        .dashboard-navbar {
            display: flex;
            justify-content: space-between; 
            align-items: center;
            padding: 12px 30px;
            width: 100%;
        }

        .dashboard-logo img {
            height: 83 px;
        }
    
        .dashboard-links {
            display: flex;
            align-items: center;
           margin-left: auto; 
        }

        .logout-btn {
            background: #dc3545;
            color: white !important;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        /* Post Container */
        .post-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .post-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }

        .post-header h1 {
            margin: 0;
            font-size: 32px;
        }

        .post-meta {
            margin-top: 15px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 14px;
            opacity: 0.9;
        }

        .post-image {
            width: 100%;
            max-height: 100%;
            object-fit: cover;
            background: #f0f0f0;
            display: block;
        }

        .post-content {
            padding: 30px;
            line-height: 1.8;
            color: #333;
            font-size: 16px;
        }

        .post-footer {
            padding: 20px 30px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-back {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: #764ba2;
        }

        .badge {
            background: rgba(255,255,255,0.3);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-approve {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-approve:hover {
            background: #218838;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-reject:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .post-header h1 { font-size: 24px; }
            .post-content { padding: 20px; }
            .dashboard-navbar { flex-direction: column; gap: 15px; }
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
            <?php if($is_logged_in): ?>
                <a href="logout.php" class="logout-btn">Logout (<?php echo $_SESSION['fullname']; ?>)</a>
            <?php else: ?>
                <a href="index.php" class="logout-btn">Sign In</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="post-container">
    <?php if ($row): ?>
        <div class="post-header">
            <h1><?php echo htmlspecialchars($row['title']); ?></h1>
            <div class="post-meta">
                <span>📅 <?php echo date('F j, Y', strtotime($row['created_at'])); ?></span>
                <span>👤 By: <?php echo htmlspecialchars($row['fullname']); ?></span>
                <span class="badge"><?php echo htmlspecialchars($row['category_name']); ?></span>
                    <?php if($row['DepartmentName']): ?>
                        <span class="badge">🏢 <?php echo htmlspecialchars($row['DepartmentName']); ?></span>
                    <?php endif; ?>
                    <span class="status-badge status-<?php echo $row['status']; ?>">
                        <?php echo ucfirst($row['status']); ?>
                </span>
            </div>
        </div>

        <?php if($row['image_path']): ?>
            <img src="<?php echo $row['image_path']; ?>" class="post-image" alt="Post image">
        <?php endif; ?>

        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($row['content'])); ?>
        </div>

        <div class="post-footer">
            <a href="javascript:history.back()" class="btn-back">← Back</a>
            
            <?php if($is_admin && $row['status'] == 'pending'): ?>
                <div class="action-buttons">
                    <button class="btn-approve" onclick="updateStatus(<?php echo $row['Post_ID']; ?>, 'approved')">✓ Approve</button>
                    <button class="btn-reject" onclick="updateStatus(<?php echo $row['Post_ID']; ?>, 'rejected')">✗ Reject</button>
                </div>
            <?php endif; ?>
            
            <small>© InfoHub Team</small>
        </div>
    <?php else: ?>
        <div class="post-content" style="text-align: center;">
            <h2>Post not found</h2>
            <a href="home.php" class="btn-back">Back to Home</a>
        </div>
    <?php endif; ?>
</div>

<script>
    function updateStatus(postId, status) {
        if(confirm(`Are you sure you want to ${status} this submission?`)) {
            window.location.href = `update_status.php?id=${postId}&status=${status}`;
        }
    }
</script>

</body>
</html>