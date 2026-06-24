<?php
session_start();

$base_path="../";

include("../config/db.php");
include("../includes/auth.php");

requireLogin();
requireRole("farmer");

$user_id=$_SESSION['user_id'];

$result="";
$advice="";

if(isset($_POST['predict'])){

$crop=trim($_POST['crop']);
$district=trim($_POST['district']);

$farm=(float)$_POST['farm_size'];

$rain=$_POST['rainfall'];

$soil=$_POST['soil'];

$fert=$_POST['fertilizer'];

/* SIMPLE PREDICTION */

$score=0;

if($farm>=5) $score+=4;
elseif($farm>=2) $score+=3;
else $score+=1;

if($rain=="High") $score+=3;
elseif($rain=="Medium") $score+=2;

if($soil=="Good") $score+=3;
elseif($soil=="Average") $score+=2;

if($fert=="Yes") $score+=2;

if($score>=10){

$result="Excellent Yield";

$yield=round($farm*2.4,1);

$advice=
"Conditions are strong. Continue monitoring irrigation and harvesting schedule.";

}

elseif($score>=7){

$result="Moderate Yield";

$yield=round($farm*1.7,1);

$advice=
"Improve fertilizer use and maintain field inspection.";

}

else{

$result="Low Yield";

$yield=round($farm*1.0,1);

$advice=
"Improve soil conditions and seek agricultural guidance.";

}

$prediction=
$yield." tons estimated";

$stmt=$conn->prepare("
INSERT INTO crop_yield_predictions
(
user_id,
crop,
district,
farm_size,
rainfall,
soil_quality,
fertilizer,
predicted_yield,
recommendation
)
VALUES
(
?,
?,
?,
?,
?,
?,
?,
?,
?
)
");

$stmt->bind_param(
"issdsssss",
$user_id,
$crop,
$district,
$farm,
$rain,
$soil,
$fert,
$prediction,
$advice
);

$stmt->execute();

}

$page_title="Crop Yield Prediction";

include("../includes/header.php");
?>

<h1 class="page-title">

📊 Crop Yield Prediction

</h1>

<p class="page-subtitle">

Estimate expected crop output

</p>

<div class="card">

<form method="POST">

<div class="form-group">

<label>Crop</label>

<select name="crop" required>

<option>Rice</option>

<option>Maize</option>

<option>Cassava</option>

<option>Tomato</option>

</select>

</div>

<div class="form-group">

<label>District</label>

<input
type="text"
name="district"
required
>

</div>

<div class="form-group">

<label>Farm Size (Acres)</label>

<input
type="number"
step="0.1"
name="farm_size"
required
>

</div>

<div class="form-group">

<label>Rainfall</label>

<select name="rainfall">

<option>High</option>

<option>Medium</option>

<option>Low</option>

</select>

</div>

<div class="form-group">

<label>Soil Quality</label>

<select name="soil">

<option>Good</option>

<option>Average</option>

<option>Poor</option>

</select>

</div>

<div class="form-group">

<label>Using Fertilizer?</label>

<select name="fertilizer">

<option>Yes</option>

<option>No</option>

</select>

</div>

<button
name="predict"
class="btn btn-primary"
>

Predict Yield

</button>

<a
href="dashboard.php"
class="btn btn-secondary"
>

Back

</a>

</form>

</div>

<?php if($result): ?>

<div
class="card"
style="margin-top:20px;"
>

<h2>

Prediction Result

</h2>

<h3>

🌾
<?php echo $result; ?>

</h3>

<p>

Estimated Yield:

<strong>

<?php echo $prediction; ?>

</strong>

</p>

<p>

<?php echo $advice; ?>

</p>

</div>

<?php endif; ?>

<?php include("../includes/footer.php"); ?>