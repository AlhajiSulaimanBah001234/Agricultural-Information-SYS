<?php
session_start();
$page_title = "About Us";
include("includes/header.php");
?>

<div class="hero">
    <h1>🌾 About Our Platform</h1>
    <p>
        The Smart Agriculture Information System is a digital platform designed to empower farmers
        in Sierra Leone with modern agricultural knowledge, market access, and expert guidance.
    </p>
</div>

<section class="features">

    <div class="feature-card">
        <div class="icon">🎯</div>
        <h3>Our Mission</h3>
        <p>
            To improve agricultural productivity by giving farmers access to reliable information,
            modern techniques, and digital tools.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">🌍</div>
        <h3>Our Vision</h3>
        <p>
            To build a smart and sustainable agricultural ecosystem that supports food security
            and economic growth in Sierra Leone.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">🤝</div>
        <h3>Our Goal</h3>
        <p>
            To connect farmers, experts, and markets in one digital system for better decision making.
        </p>
    </div>

</section>
<br>

<section class="features">

    <div class="feature-card">
        <div class="icon">📊</div>
        <h3>Data Driven Farming</h3>
        <p>We provide updated market prices and agricultural insights.</p>
    </div>

    <div class="feature-card">
        <div class="icon">📚</div>
        <h3>Education Support</h3>
        <p>Farmers can learn modern farming techniques easily.</p>
    </div>

    <div class="feature-card">
        <div class="icon">🌾</div>
        <h3>Food Security</h3>
        <p>Helping increase food production across the country.</p>
    </div>

</section>

<a href="index.php" class="btn btn-primary" style="margin-top:15px;">
    ⬅ Back to Home
</a>

<?php include("includes/footer.php"); ?>