<?php
session_start();

$base_path="../";

include("../config/db.php");
include("../includes/auth.php");

requireLogin();
requireRole("farmer");

$user_id=$_SESSION['user_id'];

$id=(int)($_GET['id']??0);

$stmt=$conn->prepare("
SELECT *
FROM crop_yield_predictions
WHERE id=?
AND user_id=?
");

$stmt->bind_param(
"ii",
$id,
$user_id
);

$stmt->execute();

$result=
$stmt->get_result();

$data=
$result->fetch_assoc();

if(!$data){

die("Prediction not found.");

}

$page_title="Yield Details";

include("../includes/header.php");
?>

<h1 class="page-title">

📈 Prediction Details

</h1>

<div class="card">

<h2>

🌾

<?php echo htmlspecialchars($data['crop']); ?>

</h2>

<hr>

<p>

<strong>District:</strong>

<?php echo htmlspecialchars($data['district']); ?>

</p>

<p>

<strong>Farm Size:</strong>

<?php echo $data['farm_size']; ?>

 Acres

</p>

<p>

<strong>Rainfall:</strong>

<?php echo htmlspecialchars($data['rainfall']); ?>

</p>

<p>

<strong>Soil:</strong>

<?php echo htmlspecialchars($data['soil_quality']); ?>

</p>

<p>

<strong>Fertilizer:</strong>

<?php echo htmlspecialchars($data['fertilizer']); ?>

</p>

<p>

<strong>Predicted Yield:</strong>

🌾

<?php
echo htmlspecialchars(
$data['predicted_yield']
);
?>

</p>

<p>

<strong>Recommendation:</strong>

<?php
echo htmlspecialchars(
$data['recommendation']
);
?>

</p>

<p>

<strong>Date:</strong>

<?php
echo date(
"d M Y h:i A",
strtotime(
$data['created_at']
)
);
?>

</p>

</div>

<a
href="yield_history.php"
class="btn btn-secondary"
>

← Back

</a>

<?php include("../includes/footer.php"); ?>