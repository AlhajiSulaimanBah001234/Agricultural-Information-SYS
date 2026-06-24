<?php
session_start();

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

/* ================= GET MESSAGES ================= */
$messages = $conn->query("
    SELECT * FROM contact_messages
    ORDER BY created_at DESC
");

$page_title = "Contact Messages";
include("../includes/header.php");
?>

<h1 class="page-title">📩 Contact Messages</h1>

<div class="card">

<?php if ($messages->num_rows === 0): ?>

    <p>No messages received yet.</p>

<?php else: ?>

<table class="data-table">

<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Message</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php while($row = $messages->fetch_assoc()): ?>

<tr>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td><?php echo htmlspecialchars($row['message']); ?></td>
    <td>
        <?php if ($row['status'] == "unread"): ?>
            <span style="color:red;">Unread</span>
        <?php else: ?>
            <span style="color:green;">Read</span>
        <?php endif; ?>
    </td>
    <td><?php echo date("d M Y H:i", strtotime($row['created_at'])); ?></td>
</tr>

<?php endwhile; ?>

</table>

<?php endif; ?>

</div>

<?php include("../includes/footer.php"); ?>