<?php
session_start();
require 'config/database.php';
require 'includes/functions.php';

// Get the blog ID from the URL, e.g. blog.php?id=3
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the blog post along with its author's username
$stmt = $pdo->prepare("
    SELECT blogPost.*, user.username
    FROM blogPost
    JOIN user ON blogPost.user_id = user.id
    WHERE blogPost.id = ?
");
$stmt->execute([$id]);
$blog = $stmt->fetch();

// If no blog found with that ID, stop here
if (!$blog) {
    http_response_code(404);
    require 'includes/header.php';
    echo '<div class="container" style="padding: 60px 0; text-align: center;">
            <h2>Blog post not found</h2>
            <p class="meta" style="margin-top: 12px;"><a href="index.php" style="color: var(--accent);">Back to home</a></p>
          </div>';
    require 'includes/footer.php';
    exit;
}

// Check if the logged-in user is the owner of this post
$isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $blog['user_id'];
?>
<?php require 'includes/header.php'; ?>

<div class="container blog-page-container">
  <a href="index.php" class="meta" style="color: var(--accent);">&larr; Back to blogs</a>

  <?php if (!empty($blog['cover_image']) && file_exists(__DIR__ . '/uploads/' . $blog['cover_image'])): ?>
    <img src="uploads/<?= htmlspecialchars($blog['cover_image']) ?>" alt="" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: var(--radius); margin-top: 20px;">
  <?php endif; ?>
<h1 class="blog-title">
    <?= htmlspecialchars($blog['title']) ?>
</h1>

  <p class="meta" style="margin-top: 12px;">
    By <?= htmlspecialchars($blog['username']) ?> · <?= date('F j, Y', strtotime($blog['created_at'])) ?>
    <?php if ($blog['updated_at'] != $blog['created_at']): ?>
      · <em>updated <?= date('F j, Y', strtotime($blog['updated_at'])) ?></em>
    <?php endif; ?>
  </p>

  <?php if ($isOwner): ?>
   <div class="blog-actions">
      <a href="edit-blog.php?id=<?= $blog['id'] ?>" class="btn btn-secondary">Edit</a>
      <form method="POST" action="delete-blog.php" onsubmit="return confirm('Are you sure you want to delete this post? This cannot be undone.');">
        <input type="hidden" name="id" value="<?= $blog['id'] ?>">
        <button type="submit" class="btn btn-secondary blog-delete-btn">
    Delete
</button>
      </form>
    </div>
  <?php endif; ?>

  <div class="blog-content">
    <?= formatBlogContent($blog['content']) ?>
  </div>
</div>

<?php require 'includes/footer.php'; ?>