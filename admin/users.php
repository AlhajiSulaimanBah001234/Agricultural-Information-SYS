<?php
session_start();

$base_path = "../";

include("../config/db.php");
include("../includes/functions.php");

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$msg = "";
$error = "";

/* ================= GET USER NAME HELPER ================= */

function getUserName($conn, $id) {
    $id = (int)$id;
    $res = $conn->query("SELECT name FROM users WHERE id=$id LIMIT 1");
    $row = $res->fetch_assoc();
    return $row ? $row['name'] : 'Unknown User';
}

/* ================= DELETE USER ================= */

if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    if ($id === (int) $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {

        $userName = getUserName($conn, $id);

        $conn->query("DELETE FROM users WHERE id=$id");

        logAction(
            $conn,
            $_SESSION['user_id'],
            "Deleted user: $userName"
        );

        $msg = "User deleted successfully.";
    }
}

/* ================= UPDATE STATUS ================= */

if (isset($_POST['update_user'])) {

    $id = (int) $_POST['user_id'];
    $status = escape($conn, $_POST['status']);

    if ($id === (int) $_SESSION['user_id']) {
        $error = "You cannot modify your own account.";
    } else {

        $userName = getUserName($conn, $id);

        $conn->query("
            UPDATE users
            SET status='$status'
            WHERE id=$id
        ");

        logAction(
            $conn,
            $_SESSION['user_id'],
            "Updated status for: $userName to $status"
        );

        $msg = "User status updated successfully.";
    }
}

/* ================= FILTER ================= */

$where = "WHERE 1=1";

if (!empty($_GET['role'])) {
    $role = $conn->real_escape_string($_GET['role']);
    $where .= " AND role='$role'";
}

if (!empty($_GET['district'])) {
    $district = $conn->real_escape_string($_GET['district']);
    $where .= " AND district='$district'";
}

/* ================= USERS ================= */

$users = $conn->query("
    SELECT *
    FROM users
    $where
    ORDER BY created_at DESC
");

/* ================= EDIT USER ================= */

$edit_id = (int) ($_GET['edit'] ?? 0);
$edit_user = null;

if ($edit_id) {

    if ($edit_id !== (int) $_SESSION['user_id']) {

        $edit_user = $conn->query("
            SELECT * FROM users WHERE id = $edit_id
        ")->fetch_assoc();

    } else {
        $error = "You cannot edit your own account.";
    }
}

$page_title = "Manage Users";
include("../includes/header.php");
?>

<h1 class="page-title">👥 User Control Center</h1>

<p class="page-subtitle">
Admin manages farmers and system users
</p>

<?php if ($msg): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- FILTER -->
<div class="card" style="margin-bottom:15px;">
<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;">

    <select name="role">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="farmer">Farmer</option>
    </select>

    <select name="district">
        <option value="">All Districts</option>
        <?php
        $d = $conn->query("SELECT DISTINCT district FROM users WHERE district IS NOT NULL");
        while ($row = $d->fetch_assoc()):
        ?>
            <option value="<?php echo $row['district']; ?>">
                <?php echo htmlspecialchars($row['district']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button class="btn btn-primary btn-sm">Filter</button>

</form>
</div>

<!-- EDIT PANEL -->
<?php if ($edit_user): ?>

<div class="card">

<h3>User Account Control</h3>

<p><strong>Name:</strong> <?php echo htmlspecialchars($edit_user['name']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($edit_user['email']); ?></p>
<p><strong>Phone:</strong> <?php echo htmlspecialchars($edit_user['phone'] ?? 'N/A'); ?></p>
<p><strong>District:</strong> <?php echo htmlspecialchars($edit_user['district'] ?? 'N/A'); ?></p>
<p><strong>Role:</strong> <?php echo ucfirst($edit_user['role']); ?></p>

<hr>

<form method="POST">
    <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">

    <div class="form-group">
        <label>Account Status</label>
        <select name="status" required>
            <option value="Active" <?php echo $edit_user['status']=='Active'?'selected':''; ?>>
                Active
            </option>
            <option value="Suspended" <?php echo $edit_user['status']=='Suspended'?'selected':''; ?>>
                Suspended
            </option>
        </select>
    </div>

    <button type="submit" name="update_user" class="btn btn-primary">
        Update Status
    </button>

    <a href="users.php" class="btn btn-secondary">
        Cancel
    </a>
</form>

<hr>

<!-- DELETE ONLY IN EDIT PANEL -->
<a href="?delete=<?php echo $edit_user['id']; ?>"
   class="btn btn-danger"
   onclick="return confirm('Are you sure you want to delete this user?')">
   Delete User
</a>

</div>

<?php endif; ?>

<!-- USERS TABLE -->
<table class="data-table">

<tr>
<th>Profile</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Role</th>
<th>District</th>
<th>Status</th>
<th>Joined</th>
<th>Last Seen</th>
<th>Actions</th>
</tr>

<?php while ($user = $users->fetch_assoc()): ?>

<tr>

<td>
<?php if (!empty($user['profile_image'])): ?>
<img src="../uploads/<?php echo htmlspecialchars($user['profile_image']); ?>"
style="width:40px;height:40px;border-radius:50%;">
<?php else: ?>
👨‍🌾
<?php endif; ?>
</td>

<td><?php echo htmlspecialchars($user['name']); ?></td>
<td><?php echo htmlspecialchars($user['email']); ?></td>
<td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
<td><?php echo ucfirst($user['role']); ?></td>
<td><?php echo htmlspecialchars($user['district'] ?? 'N/A'); ?></td>
<td><?php echo statusBadge($user['status']); ?></td>

<td>
<?php echo date("d M Y", strtotime($user['created_at'])); ?>
</td>

<td>
<?php
if (!empty($user['last_seen'])) {
    echo date("d M Y h:i A", strtotime($user['last_seen']));
} else {
    echo "<span style='color:gray'>Never Logged In</span>";
}
?>
</td>

<td>
<a href="?edit=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">
Edit
</a>
</td>

</tr>

<?php endwhile; ?>

</table>

<?php include("../includes/footer.php"); ?>