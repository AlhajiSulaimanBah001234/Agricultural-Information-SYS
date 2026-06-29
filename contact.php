<?php
session_start();
$page_title = "Contact Us";
include("includes/header.php");
?>

<div class="hero">
    <h1>📞 Contact Us</h1>
    <p>
        We are here to support farmers and users. Reach out anytime for help, support, or collaboration.
    </p>

    <a href="index.php" class="btn btn-primary" style="margin-top:15px;">
        ⬅ Back to Home
    </a>
</div>

<!-- ================= CONTACT INFO ================= -->
<section class="features">

    <div class="feature-card">
        <div class="icon">📍</div>
        <h3>Office Location</h3>
        <p>Freetown, Sierra Leone</p>
    </div>

    <div class="feature-card">
        <div class="icon">📧</div>
        <h3>Email Support</h3>
        <p>support@agri-system.sl</p>
    </div>

    <div class="feature-card">
        <div class="icon">📱</div>
        <h3>Phone Number</h3>
        <p>+232 33 623 944</p>
        <p>+232 75 610 878</p>
        <p>+232 30 499 392</p>
    </div>

    <div class="feature-card">
        <div class="icon">⏰</div>
        <h3>Working Hours</h3>
        <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
    </div>

    <div class="feature-card">
        <div class="icon">💬</div>
        <h3>Live Support</h3>
        <p>Fast response during working hours for farmers.</p>
    </div>

    <div class="feature-card">
        <div class="icon">🤝</div>
        <h3>Partnerships</h3>
        <p>We collaborate with NGOs, agriculture groups, and institutions.</p>
    </div>

</section>

</section>

<!-- ================= CONTACT FORM ================= -->
<div class="card" style="margin-top:20px; padding:20px;">

    <h3>Contact Form</h3>

    <form method="POST">

        <div class="form-group">
            <label>Your Name</label>
            <input type="text" name="name" placeholder="Enter your name" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label>Message</label>
            <textarea name="message" rows="4" placeholder="Write your message..." required></textarea>
        </div>

        <button class="btn btn-primary" type="submit">
            Send Message
        </button>

    </form>

</div>

<?php include("includes/footer.php"); ?>