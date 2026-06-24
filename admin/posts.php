<?php
session_start();
$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$msg = "";

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM posts WHERE id = $id");
    logAction($conn, $_SESSION['user_id'], "Deleted post ID: $id");
    $msg = "Post deleted.";
}

if (isset($_GET['archive'])) {
    $id = (int) $_GET['archive'];
    $conn->query("UPDATE posts SET status = 'Draft' WHERE id = $id");
    logAction($conn, $_SESSION['user_id'], "Archived post ID: $id");
    $msg = "Post moved to draft.";
}

if (isset($_GET['publish'])) {
    $id = (int) $_GET['publish'];
    $conn->query("UPDATE posts SET status = 'Published' WHERE id = $id");
    logAction($conn, $_SESSION['user_id'], "Published post ID: $id");
    $msg = "Post published.";
}

$posts = $conn->query("
    SELECT posts.*, categories.name AS category_name, users.name AS author
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    LEFT JOIN users ON posts.created_by = users.id
    ORDER BY posts.created_at DESC
");

$page_title = "Manage Posts";
include("../includes/header.php");
?>

<h1 class="page-title">📋 Manage Posts</h1>
<p class="page-subtitle">Edit, publish, archive, or delete farming articles</p>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

<p style="margin-bottom:16px;"><a href="add_post.php" class="btn btn-primary">+ Add New Post</a></p>

<table class="data-table">
    <tr>
        <th>Title</th>
        <th>Category</th>
        <th>Author</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php while ($post = $posts->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($post['title']); ?></td>
        <td><?php echo htmlspecialchars($post['category_name'] ?? '—'); ?></td>
        <td><?php echo htmlspecialchars($post['author'] ?? '—'); ?></td>
        <td><?php echo statusBadge($post['status']); ?></td>
        <td><?php echo $post['created_at']; ?></td>
        <td class="actions">
            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
            <?php if ($post['status'] === 'Published'): ?>
                <a href="?archive=<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm" onclick="return confirm('Archive this post?')">Archive</a>
            <?php else: ?>
                <a href="?publish=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Publish</a>
            <?php endif; ?>
            <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete permanently?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include("../includes/footer.php"); ?>
