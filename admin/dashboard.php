<?php
session_start();

$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

/* ================= SECURITY ================= */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "admin"
) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================= ADMIN INFO ================= */

$admin = $conn->query("
    SELECT * FROM users WHERE id=$user_id
")->fetch_assoc();

/* ================= STATS ================= */

$stats = [];

$stats['users'] = $conn->query("
    SELECT COUNT(*) AS c FROM users
")->fetch_assoc()['c'];

$stats['farmers'] = $conn->query("
    SELECT COUNT(*) AS c
    FROM users
    WHERE role='farmer'
")->fetch_assoc()['c'];

/* ================= FARMER STATUS ================= */

$stats['active_farmers'] = $conn->query("
SELECT COUNT(*) AS c
FROM users
WHERE role='farmer'
AND (
LOWER(COALESCE(status,'active'))='active'
)
")->fetch_assoc()['c'];

$stats['suspended_farmers'] = $conn->query("
SELECT COUNT(*) AS c
FROM users
WHERE role='farmer'
AND LOWER(COALESCE(status,''))='suspended'
")->fetch_assoc()['c'];

$stats['posts'] = $conn->query("
    SELECT COUNT(*) AS c FROM posts
")->fetch_assoc()['c'];

$stats['categories'] = $conn->query("
    SELECT COUNT(*) AS c FROM categories
")->fetch_assoc()['c'];

$stats['questions'] = $conn->query("
    SELECT COUNT(*) AS c FROM questions
")->fetch_assoc()['c'];

$stats['pending_questions'] = $conn->query("
    SELECT COUNT(*) AS c FROM questions WHERE status='Pending'
")->fetch_assoc()['c'];

$stats['prices'] = $conn->query("
    SELECT COUNT(*) AS c FROM market_prices
")->fetch_assoc()['c'];

/* ================= RECENT ACTIVITY ================= */

$recent_logs = $conn->query("
    SELECT logs.id, logs.action, logs.created_at, users.name AS admin_name
    FROM logs
    LEFT JOIN users ON logs.admin_id = users.id
    ORDER BY logs.created_at DESC
    LIMIT 10
");

/* ================= RECENT QUESTIONS ================= */

$recent_questions = $conn->query("
    SELECT questions.id, questions.question, questions.created_at, users.name AS farmer_name
    FROM questions
    JOIN users ON questions.user_id = users.id
    WHERE questions.status = 'Pending'
    ORDER BY questions.created_at DESC
    LIMIT 5
");

/* ================= DISTRICT ADOPTION ================= */

$district_stats = $conn->query("
    SELECT district, COUNT(*) AS total_users
    FROM users
    WHERE district IS NOT NULL
    GROUP BY district
    ORDER BY total_users DESC
");

$page_title = "Admin Dashboard";
include("../includes/header.php");
?>

<!-- ================= HEADER ================= -->

<h1 class="page-title">

<?php if (!empty($admin['profile_image'])): ?>
    <img src="../uploads/<?php echo htmlspecialchars($admin['profile_image']); ?>"
    style="width:45px;height:45px;border-radius:50%;vertical-align:middle;margin-right:10px;">
<?php else: ?>
    🧑‍💼
<?php endif; ?>

Admin Dashboard

</h1>

<p class="page-subtitle">
Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>
</p>

<!-- ================= STATS ================= -->

<div class="stats-grid">

<div class="stat-card">
<div class="stat-value"><?php echo $stats['users']; ?></div>
<div class="stat-label">Total Users</div>
</div>

<div class="stat-card orange">
<div class="stat-value">

<?php echo $stats['farmers']; ?>

</div>

<div class="stat-label">

Farmers

</div>

</div>


<div class="stat-card">

<div class="stat-value">

<?php echo $stats['active_farmers']; ?>

</div>

<div class="stat-label">

🟢 Active Farmers

</div>

</div>


<div class="stat-card purple">

<div class="stat-value">

<?php echo $stats['suspended_farmers']; ?>

</div>

<div class="stat-label">

⛔ Suspended Farmers

</div>

</div>

<div class="stat-card">
<div class="stat-value"><?php echo $stats['posts']; ?></div>
<div class="stat-label">Posts</div>
</div>

<div class="stat-card blue">
<div class="stat-value"><?php echo $stats['categories']; ?></div>
<div class="stat-label">Categories</div>
</div>

<div class="stat-card purple">
<div class="stat-value"><?php echo $stats['questions']; ?></div>
<div class="stat-label">Questions</div>
</div>

<div class="stat-card orange">
<div class="stat-value"><?php echo $stats['pending_questions']; ?></div>
<div class="stat-label">Pending</div>
</div>

<div class="stat-card">
<div class="stat-value"><?php echo $stats['prices']; ?></div>
<div class="stat-label">Market Prices</div>
</div>

</div>

<!-- ================= QUICK ACTIONS ================= -->

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

<div class="card">

<h3>Quick Actions</h3>

<ul class="quick-links">
<li><a href="add_category.php">➕ Add Category</a></li>
<li><a href="add_post.php">📝 Add Post</a></li>
<li><a href="users.php">👥 Manage Users</a></li>
<li><a href="questions.php">💬 Questions</a></li>
<li><a href="reports.php">📊 Reports</a></li>
<li><a href="notifications.php">🔔 Notifications</a></li>
<li><a href="profile.php">👤 My Profile</a></li>
<li><a href="analytics.php">📊 Advanced Analytics</a></li>
</li>


</div>

<!-- ================= PENDING QUESTIONS ================= -->

<div class="card">

<h3>Pending Questions</h3>

<?php if ($recent_questions->num_rows === 0): ?>
<p>No pending questions.</p>
<?php else: ?>

<ul class="notif-list">

<?php while ($q = $recent_questions->fetch_assoc()): ?>

<li>
<strong><?php echo htmlspecialchars($q['farmer_name']); ?></strong><br>
<?php echo htmlspecialchars(substr($q['question'], 0, 80)); ?>...
<div class="notif-date">
<?php echo date("d M Y h:i A", strtotime($q['created_at'])); ?>
</div>
</li>

<?php endwhile; ?>

</ul>

<?php endif; ?>

</div>

</div>

<!-- ================= DISTRICT ADOPTION ================= -->

<div class="card" style="margin-top:20px;">

<h3>📍 District Adoption Report</h3>

<table class="data-table">

<tr>
<th>District</th>
<th>Users</th>
<th>Growth</th>
<th>Status</th>
</tr>

<?php while($d = $district_stats->fetch_assoc()): ?>

<?php
$total = $stats['users'];

$percent = $total > 0
? round(($d['total_users'] / $total) * 100, 1)
: 0;

if ($percent >= 30) {
    $status = "🔥 High";
} elseif ($percent >= 15) {
    $status = "📈 Medium";
} else {
    $status = "📉 Low";
}
?>

<tr>
<td><?php echo htmlspecialchars($d['district']); ?></td>
<td><?php echo $d['total_users']; ?></td>
<td><?php echo $percent; ?>%</td>
<td><?php echo $status; ?></td>
</tr>

<?php endwhile; ?>

</table>

</div>


<!-- ================= RECENT ACTIVITY ================= -->

<div class="card" style="margin-top:20px;">

<h3>🟢 Recent System Activity</h3>

<?php if ($recent_logs->num_rows == 0): ?>

<div
style="
padding:30px;
text-align:center;
color:#777;
">
No recent activity available.
</div>

<?php else: ?>

<div
style="
display:flex;
flex-direction:column;
gap:14px;
">

<?php while ($log = $recent_logs->fetch_assoc()): ?>

<div
style="
background:#ffffff;
border:1px solid #ececec;
border-left:6px solid #2e7d32;
padding:18px;
border-radius:12px;
box-shadow:0 2px 10px rgba(0,0,0,.05);
">

<div
style="
display:flex;
justify-content:space-between;
margin-bottom:10px;
">

<div
style="
font-weight:700;
color:#1b5e20;
">

👤

<?php
echo htmlspecialchars(
$log['admin_name']
?? 'System'
);
?>

</div>

<div
style="
font-size:12px;
color:#888;
">

<?php
echo date(
"d M Y h:i A",
strtotime(
$log['created_at']
)
);
?>

</div>

</div>

<div
style="
font-size:15px;
line-height:1.7;
color:#444;
">

<?php
echo htmlspecialchars(
$log['action']
);
?>

</div>

</div>

<?php endwhile; ?>

</div>

<?php endif; ?>

</div>

<?php while ($log = $recent_logs->fetch_assoc()): ?>

<tr>

<td>
<?php echo htmlspecialchars($log['admin_name'] ?? 'System'); ?>
</td>

<td>
<?php echo htmlspecialchars($log['action']); ?>
</td>

<td>
<?php echo date("d M Y h:i A", strtotime($log['created_at'])); ?>
</td>

</tr>

<?php endwhile; ?>

</table>

</div>

<?php include("../includes/footer.php"); ?>