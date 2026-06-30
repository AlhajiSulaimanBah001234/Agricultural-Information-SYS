
<?php
session_start();

include("config/db.php");
include("includes/functions.php");

/* =======================
   REDIRECT LOGGED USERS
======================= */

if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {

    if ($_SESSION['role'] === "admin") {
        header("Location: admin/dashboard.php");
        exit();
    }

    if ($_SESSION['role'] === "farmer") {
        header("Location: farmer/dashboard.php");
        exit();
    }
}

$msg = "";
$error = "";

$districts = [

"Western Area Urban",
"Western Area Rural",
"Bo",
"Bombali",
"Bonthe",
"Falaba",
"Kailahun",
"Kambia",
"Karene",
"Kenema",
"Koinadugu",
"Kono",
"Moyamba",
"Port Loko",
"Pujehun",
"Tonkolili"

];

if (isset($_POST['register'])) {

$name = escape($conn,$_POST['name'] ?? '');
$email = escape($conn,$_POST['email'] ?? '');
$phone = escape($conn,$_POST['phone'] ?? '');
$district = escape($conn,$_POST['district'] ?? '');

$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (
empty($name)
||
empty($email)
||
empty($phone)
||
empty($district)
||
empty($password)
||
empty($confirm)
){

$error="Please fill all fields.";

}

elseif($password !== $confirm){

$error="Passwords do not match.";

}

elseif(strlen($password)<6){

$error="Password must be at least 6 characters.";

}

else{

$check=$conn->query("
SELECT id
FROM users
WHERE email='$email'
LIMIT 1
");

if($check && $check->num_rows>0){

$error="Email already registered.";

}

else{

$hash=password_hash(
$password,
PASSWORD_DEFAULT
);

$sql="

INSERT INTO users
(
name,
email,
phone,
district,
password,
role,
status
)

VALUES
(
'$name',
'$email',
'$phone',
'$district',
'$hash',
'farmer',
'Active'
)

";

if($conn->query($sql)){

/* NEW USER ID */

$new_user_id =
$conn->insert_id;

/* REGISTER ACTIVITY */

$activity =
"New farmer registered: "
.
$name
.
" from "
.
$district;

/* SAVE TO LOGS */

$conn->query("

INSERT INTO logs
(
admin_id,
action
)

VALUES
(
$new_user_id,
'$activity'
)

");

/* SUCCESS */

$msg =
"Registration successful! Login now.";

$_POST=[];

}else{

$error =
"Registration failed.";

}

}

}

}

$page_title="Register";

include("includes/header.php");

?>

<div class="auth-page">

<div class="auth-box">

<h1>🌾 Register</h1>

<p class="subtitle">
Create your farmer account
</p>

<?php if($msg): ?>

<div class="alert alert-success">

<?= htmlspecialchars($msg) ?>

<br><br>

<a href="login.php">
Login Now
</a>

</div>

<?php endif; ?>

<?php if($error): ?>

<div class="alert alert-error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="form-group">

<label>Full Name</label>

<input
type="text"
name="name"
required
value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
>

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
required
value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
>

</div>

<div class="form-group">

<label>Phone Number</label>

<input
type="text"
name="phone"
required
value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
>

</div>

<div class="form-group">

<label>District</label>

<select name="district" required>

<option value="">
Select District
</option>

<?php foreach($districts as $d): ?>

<option
value="<?= $d ?>"
<?= (($_POST['district'] ?? '')==$d)?'selected':'' ?>
>

<?= $d ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="form-group">

<label>Password</label>

<div style="position:relative;">

<input
type="password"
id="password"
name="password"
required
style="padding-right:50px;"
>

<button
type="button"
onclick="togglePassword()"
style="
position:absolute;
right:10px;
top:50%;
transform:translateY(-50%);
background:none;
border:none;
cursor:pointer;
"
id="eye1"
>

👁️

</button>

</div>

</div>

<div class="form-group">

<label>Confirm Password</label>

<div style="position:relative;">

<input
type="password"
id="confirm"
name="confirm_password"
required
style="padding-right:50px;"
>

<button
type="button"
onclick="toggleConfirm()"
style="
position:absolute;
right:10px;
top:50%;
transform:translateY(-50%);
background:none;
border:none;
cursor:pointer;
"
id="eye2"
>

👁️

</button>

</div>

</div>

<button
type="submit"
name="register"
class="btn btn-primary"
style="width:100%;"
>

Register

</button>

</form>

<p style="text-align:center;margin-top:16px;">

Already have an account?

<a href="login.php">
Login
</a>

</p>

<p style="text-align:center;margin-top:10px;">

<a href="index.php">

← Back to Home

</a>

</p>

</div>

</div>

<script>

function togglePassword(){

let x=document.getElementById("password");
let eye=document.getElementById("eye1");

if(x.type==="password"){

x.type="text";
eye.innerHTML="🙈";

}else{

x.type="password";
eye.innerHTML="👁️";

}

}

function toggleConfirm(){

let x=document.getElementById("confirm");
let eye=document.getElementById("eye2");

if(x.type==="password"){

x.type="text";
eye.innerHTML="🙈";

}else{

x.type="password";
eye.innerHTML="👁️";

}

}

</script>

<?php
include("includes/footer.php");
?>

