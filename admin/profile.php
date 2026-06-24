```php
<?php
session_start();

$base_path = "../";

include("../config/db.php");
include("../includes/functions.php");

/* ================= SECURITY ================= */

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== "admin"
) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user = $conn
->query("
SELECT *
FROM users
WHERE id=$user_id
")
->fetch_assoc();

$msg = "";
$error = "";

/* ================= UPDATE PROFILE ================= */

if (isset($_POST['update_profile'])) {

    $old_name = $user['name'];
    $old_email = $user['email'];
    $old_phone = $user['phone'] ?? '';
    $old_image = $user['profile_image'];

    $name = escape(
        $conn,
        $_POST['name']
    );

    $email = escape(
        $conn,
        $_POST['email']
    );

    $phone = escape(
        $conn,
        $_POST['phone'] ?? ''
    );

    $profile_image =
        $user['profile_image'];

    /* IMAGE */

    if (
        !empty(
            $_FILES['profile_image']['name']
        )
    ) {

        $uploaded =
            uploadImage(
                $_FILES['profile_image'],
                "admin"
            );

        if ($uploaded === false) {

            $error =
                "Please upload valid image.";

        } else {

            $profile_image =
                $uploaded;
        }
    }

    if (!$error) {

        $check =
        $conn->query("
        SELECT id
        FROM users
        WHERE email='$email'
        AND id!=$user_id
        ");

        if (
            $check->num_rows > 0
        ) {

            $error =
                "Email already exists.";

        } else {

            $conn->query("
            UPDATE users
            SET
            name='$name',
            email='$email',
            phone='$phone',
            profile_image='$profile_image'
            WHERE id=$user_id
            ");

            /* ========= ACTIVITY ========= */

            if (
                $old_name != $name
            ) {

                logAction(
                    $conn,
                    $user_id,
                    "updated name from '$old_name' to '$name'"
                );
            }

            if (
                $old_email != $email
            ) {

                logAction(
                    $conn,
                    $user_id,
                    "updated email from '$old_email' to '$email'"
                );
            }

            if (
                $old_phone != $phone
            ) {

                logAction(
                    $conn,
                    $user_id,
                    "updated phone number"
                );
            }

            if (
                $old_image != $profile_image
            ) {

                logAction(
                    $conn,
                    $user_id,
                    "updated profile picture"
                );
            }

            $_SESSION['name'] =
                $name;

            $_SESSION['email'] =
                $email;

            $msg =
                "Profile updated successfully.";

            $user =
            $conn
            ->query("
            SELECT *
            FROM users
            WHERE id=$user_id
            ")
            ->fetch_assoc();
        }
    }
}

/* ================= PASSWORD ================= */

if (
isset(
$_POST['change_password']
)
) {

$current =
$_POST['current_password'];

$new =
$_POST['new_password'];

$confirm =
$_POST['confirm_password'];

if (
!password_verify(
$current,
$user['password']
)
) {

$error =
"Current password incorrect.";

}

elseif (
$new != $confirm
) {

$error =
"Passwords do not match.";

}

elseif (
strlen($new) < 6
) {

$error =
"Password must be at least 6 characters.";

}

else {

$hash =
password_hash(
$new,
PASSWORD_DEFAULT
);

$conn->query("
UPDATE users
SET password='$hash'
WHERE id=$user_id
");

logAction(
$conn,
$user_id,
"changed password"
);

$msg =
"Password updated.";

}

}

$page_title =
"Admin Profile";

include(
"../includes/header.php"
);

?>

<h1 class="page-title">
👤 Admin Profile
</h1>

<?php if($msg): ?>
<div class="alert alert-success">
<?= $msg ?>
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-error">
<?= $error ?>
</div>
<?php endif; ?>

<div
style="
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
">

<div class="card">

<h3>
My Profile
</h3>

<form
method="POST"
enctype="multipart/form-data"
>

<div
style="
text-align:center;
margin-bottom:20px;
">

<label>

<input
type="file"
name="profile_image"
accept="image/*"
style="display:none"
>

<?php if(
!empty(
$user['profile_image']
)
): ?>

<img
src="../uploads/<?= htmlspecialchars($user['profile_image']) ?>"
style="
width:120px;
height:120px;
border-radius:50%;
object-fit:cover;
cursor:pointer;
">

<?php else: ?>

<div
style="
width:120px;
height:120px;
border-radius:50%;
background:#eee;
display:flex;
align-items:center;
justify-content:center;
font-size:45px;
">

👨‍💼

</div>

<?php endif; ?>

</label>

</div>

<div class="form-group">

<label>
Name
</label>

<input
type="text"
name="name"
required
value="<?= htmlspecialchars($user['name']) ?>"
>

</div>

<div class="form-group">

<label>
Email
</label>

<input
type="email"
name="email"
required
value="<?= htmlspecialchars($user['email']) ?>"
>

</div>

<div class="form-group">

<label>
Phone
</label>

<input
type="text"
name="phone"
value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
>

</div>

<button
class="btn btn-primary"
name="update_profile"
>

Update Profile

</button>

</form>

</div>

<div class="card">

<h3>
Change Password
</h3>

<form method="POST">

<input
type="password"
name="current_password"
placeholder="Current Password"
required
>

<br><br>

<input
type="password"
name="new_password"
placeholder="New Password"
required
>

<br><br>

<input
type="password"
name="confirm_password"
placeholder="Confirm Password"
required
>

<br><br>

<button
class="btn btn-primary"
name="change_password"
>

Change Password

</button>

</form>

</div>

</div>

<?php include("../includes/footer.php"); ?>
```
