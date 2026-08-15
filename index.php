<?php
session_start();
require 'config/database.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    // Search in title OR content
    $stmt = $pdo->prepare("
        SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.cover_image, blogPost.created_at, user.username
        FROM blogPost
        JOIN user ON blogPost.user_id = user.id
        WHERE blogPost.title LIKE ? OR blogPost.content LIKE ?
        ORDER BY blogPost.created_at DESC
    ");
    $likeTerm = '%' . $search . '%';
    $stmt->execute([$likeTerm, $likeTerm]);
} else {
    // No search — show everything
    $stmt = $pdo->query("
        SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.cover_image, blogPost.created_at, user.username
        FROM blogPost
        JOIN user ON blogPost.user_id = user.id
        ORDER BY blogPost.created_at DESC
    ");
}
$blogs = $stmt->fetchAll();

// Cycle through cover styles and symbols for visual variety
$coverStyles = ['cover-amber', 'cover-blue', 'cover-pink', 'cover-teal', 'cover-green', 'cover-violet'];
$coverSymbols = ['</>', 'DB', '{ }', 'JS', 'API', '#'];
?>
<?php require 'includes/header.php'; ?>

<header class="container hero-glow" style="padding: 90px 0 60px; text-align: center;">
  <span class="tag">// welcome to bytelog</span>
  <h1 style="margin-top: 20px;">Ideas, code, and lessons<br>from the terminal</h1>
  <p class="meta" style="font-size: 1rem; margin-top: 16px; color: var(--text-muted); font-family: var(--font-body);">
    A blog for developers who like to write things down.
  </p>
</header>

<div class="container" style="max-width: 500px; margin: 0 auto 20px;">
  <form method="GET" action="index.php" style="display: flex; gap: 10px;">
    <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>" style="flex: 1; padding: 12px 14px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text); font-family: var(--font-body); font-size: 0.95rem;">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($search !== ''): ?>
      <a href="index.php" class="btn btn-secondary">Clear</a>
    <?php endif; ?>
  </form>
</div>

<main class="container" style="padding-bottom: 80px;">
 <?php if (empty($blogs)): ?>
    <div class="card" style="text-align: center; padding: 60px 20px; max-width: 480px; margin: 0 auto;">
      <?php if ($search !== ''): ?>
        <h3>No results found</h3>
        <p class="meta" style="margin-top: 10px; color: var(--text-muted); font-family: var(--font-body);">
          Nothing matches "<?= htmlspecialchars($search) ?>". Try a different search term.
        </p>
        <a href="index.php" class="btn btn-secondary" style="margin-top: 20px;">Clear search</a>
      <?php else: ?>
        <h3>No posts yet</h3>
        <p class="meta" style="margin-top: 10px; color: var(--text-muted); font-family: var(--font-body);">
          This is where new posts will appear. Be the first to write one.
        </p>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="create-blog.php" class="btn btn-primary" style="margin-top: 20px;">Write a Post</a>
        <?php else: ?>
          <a href="register.php" class="btn btn-primary" style="margin-top: 20px;">Sign Up to Get Started</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
      <?php foreach ($blogs as $blog): ?>
        <?php $coverIndex = $blog['id'] % count($coverStyles); ?>
        <a href="blog.php?id=<?= $blog['id'] ?>" class="card card-with-image">
          <?php if (!empty($blog['cover_image']) && file_exists(__DIR__ . '/uploads/' . $blog['cover_image'])): ?>
            <img class="card-image" src="uploads/<?= htmlspecialchars($blog['cover_image']) ?>" alt="">
          <?php else: ?>
            <div class="card-cover <?= $coverStyles[$coverIndex] ?>"><?= $coverSymbols[$coverIndex] ?></div>
          <?php endif; ?>
          <h3><?= htmlspecialchars($blog['title']) ?></h3>
          <p class="meta" style="margin-top: 12px;">
            By <?= htmlspecialchars($blog['username']) ?> · <?= date('M j, Y', strtotime($blog['created_at'])) ?>
          </p>
          <p style="margin-top: 12px; color: var(--text-muted); font-size: 0.9rem;">
            <?= htmlspecialchars(substr($blog['content'], 0, 100)) ?>...
          </p>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php require 'includes/footer.php'; ?>