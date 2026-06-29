<?php
session_start();
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

$page_title = "Home";
include("includes/header.php");
?>

<!-- HERO SECTION -->
<div class="hero">

    <div class="hero-content">

        <h1>🌾 Smart Agricultural Information System</h1>

        <p>
            Empowering farmers with knowledge,
            market information, expert guidance,
            and digital support for sustainable agriculture in Sierra Leone.
        </p>

        <!-- NAV LINKS -->
        <div style="margin:15px 0; font-size:0.95rem;">
            <a href="about.php" style="color:#fff; margin:0 10px;">About Us</a> |
            <a href="services.php" style="color:#fff; margin:0 10px;">Our Services</a> |
            <a href="contact.php" style="color:#fff; margin:0 10px;">Contact Us</a>
        </div>

        <!-- BUTTONS -->
        <div class="hero-buttons">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-outline">Register as Farmer</a>
        </div>

    </div>

</div>

<!-- FEATURES -->
<section class="features">

    <div class="feature-card">
        <div class="icon">📖</div>
        <h3>Farming Articles</h3>
        <p>Read expert guidance on crops, irrigation, soil improvement, and pest management.</p>
    </div>

    <div class="feature-card">
        <div class="icon">💰</div>
        <h3>Market Prices</h3>
        <p>Monitor agricultural market prices and improve planning.</p>
    </div>

    <div class="feature-card">
        <div class="icon">💬</div>
        <h3>Ask Experts</h3>
        <p>Submit agricultural questions and receive guidance.</p>
    </div>

    <div class="feature-card">
        <div class="icon">🌍</div>
        <h3>Digital Agriculture</h3>
        <p>Technology supporting modern agriculture and food sustainability.</p>
    </div>

</section>

<?php include("includes/footer.php"); ?>