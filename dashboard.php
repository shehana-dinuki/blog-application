<?php
session_start();
require 'config/database.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only this user's blogs, newest first
$stmt = $pdo->prepare("SELECT * FROM blogPost WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$myBlogs = $stmt->fetchAll();

$totalBlogs = count($myBlogs);
?>
<?php require 'includes/header.php'; ?>

<div class="container dashboard-container">
  <h1>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?></h1>
  <p class="meta" style="margin-top: 8px; font-family: var(--font-body); color: var(--text-muted);">
    You have <?= $totalBlogs ?> blog post<?= $totalBlogs !== 1 ? 's' : '' ?>
  </p>

  <a href="create-blog.php" class="btn btn-primary" style="margin-top: 24px;">+ Create New Blog</a>

  <div style="margin-top: 40px;">
    <?php if (empty($myBlogs)): ?>
      <p style="color: var(--text-muted);">You haven't written any blogs yet. Click "Create New Blog" to get started.</p>
    <?php else: ?>
      <?php
      $coverStyles = ['cover-amber', 'cover-blue', 'cover-pink', 'cover-teal', 'cover-green', 'cover-violet'];
      $coverSymbols = ['</>', 'DB', '{ }', 'JS', 'API', '#'];
      ?>
      <?php foreach ($myBlogs as $blog): ?>
        <?php $coverIndex = $blog['id'] % count($coverStyles); ?>
        <div class="card dashboard-blog-card">
          <div class="dashboard-blog-info">
            <?php if (!empty($blog['cover_image']) && file_exists(__DIR__ . '/uploads/' . $blog['cover_image'])): ?>
              <img src="uploads/<?= htmlspecialchars($blog['cover_image']) ?>" alt="" style="width: 90px; height: 70px; object-fit: cover; border-radius: var(--radius-sm); flex-shrink: 0;">
            <?php else: ?>
              <div class="card-cover <?= $coverStyles[$coverIndex] ?>" style="width: 90px; height: 70px; margin-bottom: 0; font-size: 1.4rem; flex-shrink: 0;"><?= $coverSymbols[$coverIndex] ?></div>
            <?php endif; ?>
            <div class="dashboard-blog-details">
  <h3><?= htmlspecialchars($blog['title']) ?></h3>
              <p class="meta" style="margin-top: 8px;">
                <?= date('F j, Y', strtotime($blog['created_at'])) ?>
                <?php if ($blog['updated_at'] != $blog['created_at']): ?>
                  · <em>updated <?= date('F j, Y', strtotime($blog['updated_at'])) ?></em>
                <?php endif; ?>
              </p>
            </div>
          </div>
          <div class="dashboard-blog-actions">
            <a href="blog.php?id=<?= $blog['id'] ?>" class="btn btn-secondary">View</a>
            <a href="edit-blog.php?id=<?= $blog['id'] ?>" class="btn btn-secondary">Edit</a>
            <form method="POST" action="delete-blog.php" onsubmit="return confirm('Delete this post? This cannot be undone.');">
              <input type="hidden" name="id" value="<?= $blog['id'] ?>">
              <button type="submit" class="btn btn-secondary" style="border-color: var(--danger); color: var(--danger);">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require 'includes/footer.php'; ?>