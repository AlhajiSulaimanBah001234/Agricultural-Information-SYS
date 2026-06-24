<?php
session_start();

$base_path = "../";

include("../config/db.php");
include("../includes/auth.php");

requireLogin();
requireRole("farmer");

$user_id = $_SESSION['user_id'];

$result = "";
$recommendation = "";
$image_note = "";

/* ================= IMAGE UPLOAD HANDLING ================= */
function handleImageUpload($file)
{
    if (!isset($file) || $file['error'] != 0) {
        return null;
    }

    $allowed = ["jpg", "jpeg", "png"];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return "invalid";
    }

    $newName = time() . "_" . rand(1000, 9999) . "." . $ext;
    $path = "../uploads/" . $newName;

    move_uploaded_file($file['tmp_name'], $path);

    return $newName;
}

/* ================= DETECTION ENGINE ================= */
function detectDisease($crop, $symptom)
{
    $symptom = strtolower(trim($symptom));
    $crop = strtolower(trim($crop));

    if ($crop == "rice") {

        if (str_contains($symptom, "yellow")) {
            return ["Nitrogen Deficiency", "Apply nitrogen fertilizer and improve soil nutrients."];
        }

        if (str_contains($symptom, "spots")) {
            return ["Rice Blast", "Use fungicide (Tricyclazole) and resistant varieties."];
        }
    }

    if ($crop == "tomato") {

        if (str_contains($symptom, "brown")) {
            return ["Early Blight", "Remove infected leaves and apply copper fungicide."];
        }

        if (str_contains($symptom, "wilting")) {
            return ["Bacterial Wilt", "Improve drainage and remove infected plants."];
        }
    }

    if ($crop == "maize") {

        if (str_contains($symptom, "holes")) {
            return ["Fall Armyworm", "Use pheromone traps and approved insecticides."];
        }
    }

    return ["Unknown Condition", "System cannot identify clearly. Please consult an expert."];
}

/* ================= FORM PROCESS ================= */
if (isset($_POST['detect'])) {

    $crop = trim($_POST['crop'] ?? '');

    $symptom = trim($_POST['symptom_custom'] ?? $_POST['symptom'] ?? '');

    /* IMAGE PROCESS */
    $image = handleImageUpload($_FILES['crop_image'] ?? null);

    if ($image === "invalid") {
        $image_note = "❌ Invalid image format. Please upload JPG or PNG only.";
    }

    if (empty($symptom) && $image) {
        $symptom = "image analysis (basic detection)";
    }

    list($result, $recommendation) = detectDisease($crop, $symptom);

    /* EXTRA LOGIC FOR UNCERTAIN CASES */
    if ($result == "Unknown Condition" && empty($symptom)) {
        $recommendation .= " You may need to provide more details or clear symptoms.";
    }
}

$page_title = "Pest Detection";
include("../includes/header.php");
?>

<h1 class="page-title">🐛 Pest & Disease Detection (AI Enhanced)</h1>

<p class="page-subtitle">
Upload image or describe symptoms for diagnosis
</p>

<div class="card">

<form method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label>Select Crop</label>
        <select name="crop" required>
            <option value="">Choose Crop</option>
            <option>Rice</option>
            <option>Tomato</option>
            <option>Cassava</option>
            <option>Maize</option>
        </select>
    </div>

    <div class="form-group">
        <label>Select Symptom</label>
        <select name="symptom">
            <option value="">Choose Symptom</option>
            <option>Yellow Leaves</option>
            <option>Brown Spots</option>
            <option>Leaf Curl</option>
            <option>Dry Leaves</option>
            <option>Wilting</option>
            <option>Holes in Leaves</option>
        </select>
    </div>

    <div class="form-group">
        <label>Or Describe Problem (AI Input)</label>
        <input type="text" name="symptom_custom" placeholder="Type crop issue here...">
    </div>

    <!-- IMAGE UPLOAD NOW ENABLED -->
    <div class="form-group">
        <label>Upload Crop Image</label>
        <input type="file" name="crop_image" accept="image/*">
        <small style="color:#777;">
            Upload leaf or crop image for analysis (basic system)
        </small>
    </div>

    <button type="submit" name="detect" class="btn btn-primary">
        Analyze Crop
    </button>

    <a href="dashboard.php" class="btn btn-secondary">
        Back
    </a>

</form>

</div>

<?php if ($image_note): ?>
<div class="card" style="margin-top:20px;">
    <p style="color:red;"><?php echo $image_note; ?></p>
</div>
<?php endif; ?>

<?php if ($result): ?>
<div class="card" style="margin-top:20px;">

    <h2>🔎 Diagnosis Result</h2>

    <div style="padding:15px;background:#f6fff6;border-radius:10px;">

        <h3>🌱 <?php echo htmlspecialchars($result); ?></h3>

        <p><?php echo htmlspecialchars($recommendation); ?></p>

    </div>

    <p style="margin-top:10px;color:#666;">
        ⚠ If uncertain, consult an agricultural extension officer.
    </p>

</div>
<?php endif; ?>

<div class="card" style="margin-top:20px;">
    <h3>📚 Common Crop Diseases</h3>
    <ul class="notif-list">
        <li>🍅 Early Blight (Tomato)</li>
        <li>🌾 Rice Blast</li>
        <li>🌽 Fall Armyworm (Maize)</li>
        <li>🌿 Cassava Mosaic Disease</li>
    </ul>
</div>

<?php include("../includes/footer.php"); ?>