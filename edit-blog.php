<?php
session_start();
require 'config/database.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the blog post
$stmt = $pdo->prepare("SELECT * FROM blogPost WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();

// If blog doesn't exist, go home
if (!$blog) {
    header("Location: index.php");
    exit;
}

// Authorization check: only the owner can edit
if ($blog['user_id'] != $_SESSION['user_id']) {
    header("Location: index.php");
    exit;
}

$errors = [];
$title = $blog['title'];
$content = $blog['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $errors[] = "Both title and content are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE blogPost SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $id, $_SESSION['user_id']]);

        header("Location: blog.php?id=" . $id);
        exit;
    }
}
?>
<?php require 'includes/header.php'; ?>

<div class="container" style="max-width: 700px; padding: 60px 0;">
  <h1 style="margin-bottom: 8px;">Edit post</h1>
  <p class="meta" style="margin-bottom: 32px; font-family: var(--font-body); color: var(--text-muted);">
    Update your blog post
  </p>

  <?php if (!empty($errors)): ?>
    <div style="background: rgba(239, 83, 80, 0.1); border: 1px solid var(--danger); border-radius: var(--radius-sm); padding: 14px 16px; margin-bottom: 20px;">
      <?php foreach ($errors as $error): ?>
        <p style="color: var(--danger); font-size: 0.9rem;">⚠ <?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="edit-blog.php?id=<?= $id ?>">
    <div class="form-group">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
    </div>

    <div class="form-group">
      <label for="content">Content</label>
      <textarea id="content" name="content" rows="12" required><?= htmlspecialchars($content) ?></textarea>
    </div>

    <div style="display: flex; gap: 12px;">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="blog.php?id=<?= $id ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require 'includes/footer.php'; ?>