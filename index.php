<?php
session_start();
require 'config/database.php';

// Fetch all blogs with their author's username, newest first
$stmt = $pdo->query("
    SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.created_at, user.username
    FROM blogPost
    JOIN user ON blogPost.user_id = user.id
    ORDER BY blogPost.created_at DESC
");
$blogs = $stmt->fetchAll();
?>
<?php require 'includes/header.php'; ?>

<header class="container hero-glow" style="padding: 90px 0 60px; text-align: center;">
  <span class="tag">// welcome to bytelog</span>
  <h1 style="margin-top: 20px;">Ideas, code, and lessons<br>from the terminal</h1>
  <p class="meta" style="font-size: 1rem; margin-top: 16px; color: var(--text-muted); font-family: var(--font-body);">
    A blog for developers who like to write things down.
  </p>
</header>

<main class="container" style="padding-bottom: 80px;">
  <?php if (empty($blogs)): ?>
    <p style="text-align: center; color: var(--text-muted);">No blog posts yet. Be the first to write one!</p>
  <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
      <?php foreach ($blogs as $blog): ?>
        <a href="blog.php?id=<?= $blog['id'] ?>" class="card">
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