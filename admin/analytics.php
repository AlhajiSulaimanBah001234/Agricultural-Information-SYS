<?php
session_start();

$base_path="../";

include("../config/db.php");
include("../includes/functions.php");

/* SECURITY */

if(
!isset($_SESSION['user_id']) ||
$_SESSION['role']!="admin"
){
header("Location: ../login.php");
exit();
}

/* TOTAL PREDICTIONS */

$total=$conn->query("
SELECT COUNT(*) c
FROM crop_yield_predictions
")->fetch_assoc();

/* FARMER PREDICTIONS */

$predictions=$conn->query("
SELECT
crop_yield_predictions.*,
users.name
FROM crop_yield_predictions
LEFT JOIN users
ON crop_yield_predictions.user_id=users.id
ORDER BY created_at DESC
LIMIT 50
");

$page_title="Yield Analytics";

include("../includes/header.php");
?>

<h1 class="page-title">

📊 Crop Yield Analytics

</h1>

<p class="page-subtitle">

View farmer prediction records

</p>

<div class="stats-grid">

<div class="stat-card">

<div class="stat-value">

<?php echo $total['c']; ?>

</div>

<div class="stat-label">

Total Predictions

</div>

</div>

</div>

<div class="card">

<h3>

🌾 Farmer Yield Records

</h3>

<?php if($predictions->num_rows==0): ?>

<p>

No yield prediction records available.

</p>

<?php else: ?>

<table class="data-table">

<tr>

<th>Farmer</th>
<th>Crop</th>
<th>District</th>
<th>Farm Size</th>
<th>Predicted Yield</th>
<th>Date</th>

</tr>

<?php while($row=$predictions->fetch_assoc()): ?>

<tr>

<td>

<?php
echo htmlspecialchars(
$row['name']
);
?>

</td>

<td>

<?php
echo htmlspecialchars(
$row['crop']
);
?>

</td>

<td>

<?php
echo htmlspecialchars(
$row['district']
);
?>

</td>

<td>

<?php
echo htmlspecialchars(
$row['farm_size']
);
?>

 Acres

</td>

<td>

🌾

<?php
echo htmlspecialchars(
$row['predicted_yield']
);
?>

</td>

<td>

<?php
echo date(
"d M Y",
strtotime(
$row['created_at']
)
);
?>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php endif; ?>

</div>

<a
href="dashboard.php"
class="btn btn-secondary"
style="margin-top:20px;"
>

← Back to Dashboard

</a>

<?php include("../includes/footer.php"); ?>