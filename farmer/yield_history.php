<?php
session_start();

$base_path="../";

include("../config/db.php");
include("../includes/auth.php");

requireLogin();
requireRole("farmer");

$user_id=$_SESSION['user_id'];

$history=$conn->query("
SELECT *
FROM crop_yield_predictions
WHERE user_id=$user_id
ORDER BY created_at DESC
");

$page_title="Yield History";

include("../includes/header.php");
?>

<h1 class="page-title">

📊 My Yield Prediction History

</h1>

<p class="page-subtitle">

Click a prediction to view full details

</p>

<div class="card">

<?php if($history->num_rows==0): ?>

<p>No prediction history available.</p>

<?php else: ?>

<table class="data-table">

<tr>

<th>Crop</th>
<th>District</th>
<th>Farm</th>
<th>Prediction</th>
<th>Date</th>
<th>Action</th>

</tr>

<?php while($row=$history->fetch_assoc()): ?>

<tr>

<td>

<?php echo htmlspecialchars($row['crop']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['district']); ?>

</td>

<td>

<?php echo $row['farm_size']; ?> Acres

</td>

<td>

🌾

<?php echo htmlspecialchars($row['predicted_yield']); ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</td>

<td>

<a
href="yield_details.php?id=<?php echo $row['id']; ?>"
class="btn btn-primary btn-sm"
>

View

</a>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php endif; ?>

</div>

<a
href="dashboard.php"
class="btn btn-secondary"
>

Back

</a>

<?php include("../includes/footer.php"); ?>