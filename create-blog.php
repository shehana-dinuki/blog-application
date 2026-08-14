<?php
session_start();
require 'config/database.php';

// Authorization check: block access if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $errors[] = "Both title and content are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $content]);
        $newBlogId = $pdo->lastInsertId();

        header("Location: blog.php?id=" . $newBlogId);
        exit;
    }
}
?>
<?php require 'includes/header.php'; ?>

<div class="container" style="max-width: 700px; padding: 60px 0;">
  <h1 style="margin-bottom: 8px;">Write a new post</h1>
  <p class="meta" style="margin-bottom: 32px; font-family: var(--font-body); color: var(--text-muted);">
    Share something with the ByteLog community
  </p>

  <?php if (!empty($errors)): ?>
    <div style="background: rgba(239, 83, 80, 0.1); border: 1px solid var(--danger); border-radius: var(--radius-sm); padding: 14px 16px; margin-bottom: 20px;">
      <?php foreach ($errors as $error): ?>
        <p style="color: var(--danger); font-size: 0.9rem;">⚠ <?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="create-blog.php">
    <div class="form-group">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>
    </div>

    <div class="form-group">
      <label for="content">Content</label>
      <textarea id="content" name="content" rows="12" required><?= isset($content) ? htmlspecialchars($content) : '' ?></textarea>
    </div>

    <div style="display: flex; gap: 12px;">
      <button type="submit" class="btn btn-primary">Publish Post</button>
      <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require 'includes/footer.php'; ?>