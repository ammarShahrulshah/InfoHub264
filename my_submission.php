<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM submissions WHERE user_id='$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Submissions</title>
    <link rel="stylesheet" href="submission.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<header class="dashboard-header">
    <div class="dashboard-navbar">
        <div class="dashboard-logo">
            <img src="logo.jpg" alt="InfoHub Logo">
        </div>
        <nav class="dashboard-links">
            <a href="home.html">Home</a>
            <a href="submit.php">New Submission</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </nav>
    </div>
</header>

<main class="submit-section">
    <div class="submit-card" style="width: 900px;">
        <h1>My Submissions</h1>
        
        <?php if($result->num_rows == 0): ?>
            <p>You haven't made any submissions yet.</p>
            <a href="submit.php">Create Submission</a>
        <?php else: ?>
            <table border="1" cellpadding="10">
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Content</th>
                    <th>Status</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo substr($row['content'], 0, 100); ?>...</td>
                    <td>
                        <?php 
                        if($row['status'] == 'pending') echo "⏳ Pending";
                        else echo "✓ Approved";
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </div>
</main>

</body>
</html>