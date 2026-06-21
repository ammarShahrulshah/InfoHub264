<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get only approved posts
$approved_posts = $conn->query("
    SELECT p.*, u.fullname, u.email, c.category_name, d.DepartmentName
    FROM post p
    JOIN users u ON p.User_ID = u.id
    JOIN categories c ON p.cat_ID = c.cat_ID
    LEFT JOIN department d ON p.Department_ID = d.Department_ID
    WHERE p.status = 'approved'
    ORDER BY p.created_at ASC
");

// Get statistics
$total_approved = $conn->query("SELECT COUNT(*) as count FROM post WHERE status='approved'")->fetch_assoc()['count'];
$total_pending = $conn->query("SELECT COUNT(*) as count FROM post WHERE status='pending'")->fetch_assoc()['count'];
$total_posts = $conn->query("SELECT COUNT(*) as count FROM post")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Published Posts - Admin | InfoHub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            flex: 1;
            min-width: 150px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #0066ff;
        }
        .card h3 {
            font-size: 28px;
            margin: 0 0 10px 0;
            color: #667eea;
        }
        .card p {
            color: #666;
            font-size: 14px;
        }
        .card.highlight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            cursor: pointer;
        }
        .card.highlight-card h3 {
            color: white;
        }
        .card.highlight-card p {
            color: rgba(255,255,255,0.9);
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-btn {
            background: #f0f0f0;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-btn:hover, .filter-btn.active {
            background: #667eea;
            color: white;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-theme');
        }
    </script>

    <header class="top-nav">
        <div class="logo">
            <img src="logo.jpg" alt="InfoHub Logo" class="logo-img">
        </div>      
        <div class="user-profile">
            <span>Hello, <?php echo $_SESSION['fullname']; ?>!</span>
            <button class="btn-logout" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </header>

    <div class="layout-container">
        <aside class="sidebar">
            <h3>Dashboard Menu</h3>
            <ul>
                <li><a href="admin.php">Home</a></li>
                <li class="active"><a href="published.php">Published Posts</a></li>
                <li><a href="settings.php">Edit Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>Published Posts</h2>

            <div class="dashboard-cards">
                <div class="card highlight-card">
                    <h3><?php echo $total_posts; ?></h3>
                    <p>Total Posts</p>
                </div>
                <div class="card">
                    <h3><?php echo $total_pending; ?></h3>
                    <p>Pending Approvals</p>
                </div>
                <div class="card">
                    <h3><?php echo $total_approved; ?></h3>
                    <p>Approved</p>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="filter-buttons">
                <button class="filter-btn active" onclick="filterPosts('all')">All</button>
                <button class="filter-btn" onclick="filterPosts('News')">News</button>
                <button class="filter-btn" onclick="filterPosts('Event')">Events</button>
                <button class="filter-btn" onclick="filterPosts('Notice')">Notices</button>
            </div>

            <table class="activity-table" id="postsTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Department</th>
            <th>Author</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($approved_posts && $approved_posts->num_rows > 0): ?>
            <?php while($row = $approved_posts->fetch_assoc()): ?>
                <tr data-category="<?php echo $row['category_name']; ?>">
                    <td><?php echo $row['Post_ID']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><span class="status-approved"><?php echo $row['category_name']; ?></span></td>
                    <td>
                        <?php if($row['DepartmentName']): ?>
                            <?php echo htmlspecialchars($row['DepartmentName']); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo date('d/m/y', strtotime($row['created_at'])); ?></td>
                    <td class="action-buttons">
                        <button class="btn-view" onclick="window.location.href='view_post.php?id=<?php echo $row['Post_ID']; ?>'">View</button>
                        <button class="btn-delete" onclick="if(confirm('Delete this post?')) window.location.href='delete_post.php?id=<?php echo $row['Post_ID']; ?>'">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">No published posts found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
        </main>
    </div>

    <script>
        function filterPosts(category) {
            const rows = document.querySelectorAll('#postsTable tbody tr');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Update active button
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filter rows
            rows.forEach(row => {
                if (category === 'all') {
                    row.style.display = '';
                } else {
                    const rowCategory = row.getAttribute('data-category');
                    if (rowCategory === category) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    </script>
</body>
</html>