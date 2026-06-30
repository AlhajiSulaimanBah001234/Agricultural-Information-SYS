<?php
session_start();
$page_title = "Our Services";
include("includes/header.php");
?>

<div class="hero">
    <h1>🛠 Our Services</h1>
    <p>
        We provide digital agricultural solutions to improve farming efficiency, productivity,
        and market access.
    </p>
</div>

<section class="features">

    <div class="feature-card">
        <div class="icon">📊</div>
        <h3>Market Price System</h3>
        <p>
            Real-time agricultural prices help farmers know when and where to sell their produce.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">📚</div>
        <h3>Farming Education</h3>
        <p>
            Access articles, guides, and expert farming techniques for better productivity.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">💬</div>
        <h3>Ask Experts</h3>
        <p>
            Farmers can ask agricultural questions and receive answers from system admins.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">🌐</div>
        <h3>Digital Platform</h3>
        <p>
            A centralized system connecting farmers, administrators, and agricultural data.
        </p>
    </div>

</section>
<br>

<section class="features">

    <div class="feature-card">
        <div class="icon">🚜</div>
        <h3>Better Farming Decisions</h3>
        <p>Farmers can choose crops and timing based on data.</p>
    </div>

    <div class="feature-card">
        <div class="icon">📈</div>
        <h3>Income Improvement</h3>
        <p>Better pricing knowledge increases farmer profit.</p>
    </div>

    <div class="feature-card">
        <div class="icon">🔔</div>
        <h3>Instant Updates</h3>
        <p>Receive updates on agriculture trends and market changes.</p>
    </div>

</section>
<a href="index.php" class="btn btn-primary" style="margin-top:15px;">
    ⬅ Back to Home
</a>
<?php include("includes/footer.php"); ?>