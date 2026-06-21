<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total_posts = $conn->query("SELECT COUNT(*) as count FROM post")->fetch_assoc()['count'];
$pending_posts = $conn->query("SELECT COUNT(*) as count FROM post WHERE status='pending'")->fetch_assoc()['count'];
$approved_posts = $conn->query("SELECT COUNT(*) as count FROM post WHERE status='approved'")->fetch_assoc()['count'];


$recent_posts = $conn->query("
    SELECT p.*, u.fullname, c.category_name, d.DepartmentName
    FROM post p
    JOIN users u ON p.User_ID = u.id
    JOIN categories c ON p.cat_ID = c.cat_ID
    LEFT JOIN department d ON p.Department_ID = d.Department_ID
    ORDER BY p.created_at DESC LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoHub - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .status-approved {
            background: #d4edda;
            color: #155724;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .btn-edit, .btn-view {
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .dashboard-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            flex: 1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card h3 {
            font-size: 28px;
            margin: 0 0 10px 0;
            color: #667eea;
        }
        .highlight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
        }
        .highlight-card h3 {
            color: white;
        }
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }
        .activity-table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .activity-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-center {
            margin-top: 30px;
            text-align: center;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
        }

        
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">
            <a href="admin.php">
                <img src="logo.jpg" alt="InfoHub Logo" class="logo-img">
            </a>
        </div>
        <div class="user-profile">
            <span>Hello, <?php echo $_SESSION['fullname']; ?>!</span>
            <button id="logoutBtn" class="btn-logout" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </header>

    <div class="layout-container">
        <aside class="sidebar">
            <h3>Dashboard Menu</h3>
            <ul>
                <li class="active"><a href="admin.php">Home</a></li>
                <li><a href="published.php">Published Posts</a></li>
                <li><a href="settings.php">Edit Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
                <?php if(isset($_GET['msg'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            ✓ <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
             <?php endif; ?>
            <h2>Dashboard Overview:</h2>
            <div class="dashboard-cards">
                <div class="card">
                    <h3><?php echo $total_posts; ?></h3>
                    <p>Total Published</p>
                </div>
                <div class="card highlight-card">
                    <h3><?php echo $pending_posts; ?></h3>
                    <p>Pending Approvals</p>
                </div>
                <div class="card">
                    <h3><?php echo $approved_posts; ?></h3>
                    <p>Approved</p>
                </div>
            </div>

            <h2>Recent Approval Activity:</h2>
            <table class="activity-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Department</th>
            <th>Author</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($recent_posts && $recent_posts->num_rows > 0): ?>
            <?php while($row = $recent_posts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td>
                        <?php if($row['DepartmentName']): ?>
                            <?php echo htmlspecialchars($row['DepartmentName']); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td>
                        <span class="status-<?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/y', strtotime($row['created_at'])); ?></td>
                    <td class="action-buttons">
                        <button class="btn-view" onclick="window.location.href='view_post.php?id=<?php echo $row['Post_ID']; ?>'">View</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>
        </main>
    </div>

    <script>
        function viewPost(id, title, content) {
            alert(`📖 ${title}\n\n${content}`);
        }

        function deletePost(id) {
            if(confirm('Are you sure you want to delete this post?')) {
                window.location.href = 'delete_post.php?id=' + id;
            }
        }
        
           function updateStatus(postId, status) {
        if(confirm(`Are you sure you want to ${status} this submission?`)) {
            window.location.href = `update_status.php?id=${postId}&status=${status}`;
        }
        }

        setTimeout(function() {
            location.reload();
        }, 30000);

    </script>
</body>
</html>