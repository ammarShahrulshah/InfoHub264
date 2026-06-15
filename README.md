# 📢 InfoHub Campus Notice Board System

**CSC264: Introduction to Web and Mobile Application**  
**Group:** CDCS1104D  
**Submission Date:** 14 April 2026  

---

## 📌 Project Overview

**InfoHub** is a web-based campus notice board system designed to centralize announcements for students, faculty, and campus clubs. It replaces scattered communication channels (WhatsApp groups, emails, physical notice boards) with one organized, searchable, and easy-to-use platform.

### Key Features

| Feature | Description |
|---------|-------------|
| 🔍 **Search Announcements** | Find specific news by keyword (no login required) |
| 📝 **Submit News** | Users can submit news for admin review |
| 👑 **Admin Dashboard** | Create, manage, and delete announcements |
| ✅ **Submission Approval** | Admins can approve or reject user submissions |
| 📱 **Responsive Design** | Works on desktop, tablet, and mobile |
| 🔐 **Secure Login** | Admin authentication with session management |

---

## 👥 Team Members

| No. | Name | Metric Number |
|-----|------|---------------|
| 1. | Muhammad Faris Bin Mohd Fairuz | 2024653776 |
| 2. | Wan Muhammad Hamizan Bin Wan Ab Rahim | 2024671512 |
| 3. | Muhammad Ammar Bin Shahrulshah | 2024691276 |
| 4. | Jaya Amar Thaqif Bin Jaya Adlilamir | 2024225934 |

**Lecturer:** Mdm. Nor Asma Binti Mohd Zin

---

## 🛠️ Technologies Used

| Technology | Purpose |
|------------|---------|
| **PHP** | Backend logic, authentication, database interaction |
| **MySQL** | Store announcements, submissions, admin credentials |
| **HTML5** | Structure of web pages |
| **CSS3** | Styling and responsive design |
| **JavaScript** | Interactive features (search, modals, form validation) |
| **XAMPP / Apache** | Local development server |

---

## 📁 Project File Structure
InfoHub264/
│
├── 🏠 MAIN PAGES
│ ├── index.php # Landing page (redirects to visitor.php)
│ ├── home.php # Homepage for logged-in users
│ ├── visitor.php # Homepage for public visitors
│ ├── login.php # Admin login page
│ └── logout.php # Destroy session
│
├── 👑 ADMIN PAGES
│ ├── admin.php # Admin dashboard
│ ├── delete_post.php # Delete announcement
│ ├── update_status.php # Approve/reject submissions
│ ├── settings.php # Admin settings
│ └── session.php # Session management
│
├── 📄 CONTENT PAGES (Authenticated)
│ ├── news.php # News page (logged in)
│ ├── events.php # Events page (logged in)
│ ├── notice.php # Notices page (logged in)
│ ├── article.php # Full article view (logged in)
│ └── view_post.php # Alternative article view
│
├── 👁️ CONTENT PAGES (Visitor)
│ ├── news_visitor.php # News page (public)
│ ├── events_visitor.php # Events page (public)
│ └── notice_visitor.php # Notices page (public)
│
├── 📝 SUBMISSION PAGES
│ ├── submit.php # News submission form
│ └── my_submissions.php # View user's own submissions
│
├── 🗄️ DATABASE
│ ├── db_conn.php # Database connection
│ └── published.php # Published announcements
│
├── 🎨 CSS STYLESHEETS
│ ├── style.css # Main stylesheet
│ ├── login.css # Login page styles
│ ├── dashboard.css # Admin dashboard styles
│ └── submission.css # Submission form styles
│
├── 🖼️ IMAGES
│ ├── logo.jpg # Site logo
│ ├── exam.jpg # Example image
│ ├── sports.jpg # Example image
│ ├── iran.jpg # Example image
│ └── uploads/submissions/ # User-uploaded images
│
└── 📄 OTHER
└── submit.css # Additional submission styles


---

## 💻 Installation & Setup

### Prerequisites

- XAMPP (or WAMP/LAMP) with PHP 7.4+ and MySQL
- Web browser (Chrome, Firefox, Edge)
- Git (optional)

### Step-by-Step Installation

**Step 1: Clone or Download**

git clone https://github.com/ammarShahrulshah/InfoHub264.git

Or download the ZIP from GitHub and extract it.

**Step 2: Move to htdocs

Copy the InfoHub264 folder into:

Windows: C:\xampp\htdocs\

macOS: /Applications/XAMPP/htdocs/

Step 3: Start XAMPP

Open XAMPP Control Panel and start:

Apache (Web Server)

MySQL (Database)

Step 4: Create Database

Open phpMyAdmin: http://localhost/phpmyadmin

Create database named: infohub_db

Step 5: Configure Database Connection

Edit db_conn.php:
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "infohub_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
Step 6: Launch Website

Open browser and go to:
http://localhost/InfoHub264/

🔐 User Access Guide
Default Admin Login
Field                 	Value
Login URL     	http://localhost/InfoHub264/login.php
Username	       admin
Email	          admin@infohub.com
Password	      admin123

User Permissions
User Type	             Permissions
Visitor	         View and search announcements only
Submitter      	 View + submit news for review
Admin            Full control (create, delete, approve/reject)

🧪 How to Use

For Visitors
1.Open visitor.php
2.Browse announcements
3.Use search bar to find specific news
4.Click "Read More" to view full article

To Submit News
1.Click "Submit News via Form" in sidebar
2.Fill in your name, email, title, category, content
3.Upload image (optional)
4.Click Submit

For Admin
1.Go to login.php
2.Enter admin credentials
3.Dashboard shows posts and pending submissions
4.Click "+ Create New Post" to add announcement
5.Click "Delete" to remove posts
6.Approve or reject user submissions

