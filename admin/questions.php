<?php
session_start();

$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$msg = "";
$error = "";

/* =======================
   SUBMIT ANSWER (FIXED)
======================= */
if (isset($_POST['submit_answer'])) {

    $id = (int) $_POST['question_id'];
    $answer = trim($_POST['answer_text']);

    if (!empty($answer)) {

        $stmt = $conn->prepare("
            UPDATE questions 
            SET answer = ?, status = 'Answered' 
            WHERE id = ?
        ");

        if ($stmt) {

            $stmt->bind_param("si", $answer, $id);

            if ($stmt->execute()) {
                $msg = "Answer submitted successfully!";
            } else {
                $error = "Failed to submit answer.";
            }

        } else {
            $error = "Database error.";
        }

    } else {
        $error = "Answer cannot be empty.";
    }
}

/* =======================
   FILTER
======================= */
$filter = $_GET['status'] ?? '';
$filter = trim($filter);

$where = "";

if ($filter === "Pending" || $filter === "Answered") {
    $where = "WHERE questions.status = '$filter'";
}

/* =======================
   GET QUESTIONS
======================= */
$questions = $conn->query("
    SELECT questions.*, users.name AS farmer_name, users.email AS farmer_email
    FROM questions
    JOIN users ON questions.user_id = users.id
    $where
    ORDER BY questions.created_at DESC
");

$page_title = "Farmer Questions";

include("../includes/header.php");
?>

<h1 class="page-title">💬 Farmer Questions</h1>

<p class="page-subtitle">
    Review and answer questions from farmers
</p>

<?php if ($msg): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="filter-bar">
    <a href="questions.php" class="btn btn-secondary btn-sm">All</a>
    <a href="?status=Pending" class="btn btn-secondary btn-sm">Pending</a>
    <a href="?status=Answered" class="btn btn-secondary btn-sm">Answered</a>
</div>

<?php while ($q = $questions->fetch_assoc()): ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px;">
        <div>
            <strong><?php echo htmlspecialchars($q['farmer_name']); ?></strong>
            <span style="color:#666;">
                — <?php echo htmlspecialchars($q['farmer_email']); ?>
            </span>
        </div>

        <?php echo statusBadge($q['status']); ?>
    </div>

    <p style="margin:12px 0;">
        <strong>Q:</strong><br>
        <?php echo nl2br(htmlspecialchars($q['question'])); ?>
    </p>

    <p style="font-size:0.85rem; color:#888;">
        Asked: <?php echo $q['created_at']; ?>
    </p>

    <?php if (!empty($q['answer'])): ?>

        <div class="alert alert-info" style="margin-top:12px;">
            <strong>Answer:</strong><br>
            <?php echo nl2br(htmlspecialchars($q['answer'])); ?>
        </div>

    <?php else: ?>

        <form method="POST">

            <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">

            <div class="form-group">
                <label>Your Answer</label>

                <textarea name="answer_text" required></textarea>
            </div>

            <button type="submit" name="submit_answer" class="btn btn-primary">
                Submit Answer
            </button>

        </form>

    <?php endif; ?>

</div>

<?php endwhile; ?>

<?php include("../includes/footer.php"); ?>