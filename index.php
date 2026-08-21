<?php
session_start();
require 'config/database.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    // Search in title OR content
    $stmt = $pdo->prepare("
        SELECT blogpost.id, blogpost.title, blogpost.category, blogpost.content, blogpost.cover_image, blogpost.created_at, user.username
        FROM blogpost
        JOIN user ON blogpost.user_id = user.id
        WHERE blogpost.title LIKE ? OR blogpost.content LIKE ?
        ORDER BY blogpost.created_at DESC
    ");
    $likeTerm = '%' . $search . '%';
    $stmt->execute([$likeTerm, $likeTerm]);
} else {
    // No search — show everything
    $stmt = $pdo->query("
        SELECT blogpost.id, blogpost.title, blogpost.category, blogpost.content, blogpost.cover_image, blogpost.created_at, user.username
        FROM blogpost
        JOIN user ON blogpost.user_id = user.id
        ORDER BY blogpost.created_at DESC
    ");
}
$blogs = $stmt->fetchAll();

// Cycle through cover styles and symbols for visual variety
$coverStyles = ['cover-amber', 'cover-blue', 'cover-pink', 'cover-teal', 'cover-green', 'cover-violet'];
$coverSymbols = ['</>', 'DB', '{ }', 'JS', 'API', '#'];
$borderStyles = ['post-border-amber', 'post-border-blue', 'post-border-pink', 'post-border-teal', 'post-border-green', 'post-border-violet'];

$categoryTagClass = [
    'PHP' => 'tag-amber',
    'MySQL' => 'tag-blue',
    'Web Dev' => 'tag-pink',
    'JavaScript' => 'tag-teal',
    'Tutorial' => 'tag-green',
    'General' => 'tag-violet',
];
?>
<?php require 'includes/header.php'; ?>

<header class="container hero-glow" style="padding: 90px 0 60px; text-align: center;">
  <span class="tag">// welcome to bytelog</span>
  <h1 style="margin-top: 20px;">Ideas, code, and lessons<br>from the terminal</h1>
  <p class="meta" style="font-size: 1rem; margin-top: 16px; color: var(--text-muted); font-family: var(--font-body);">
    A blog for developers who like to write things down.
  </p>
</header>
<div class="container search-container">
  <form method="GET" action="index.php" class="search-form">
    <input type="text"
       name="search"
       placeholder="Search posts..."
       value="<?= htmlspecialchars($search) ?>"
       class="search-input">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($search !== ''): ?>
      <a href="index.php" class="btn btn-secondary">Clear</a>
    <?php endif; ?>
  </form>
</div>

<div class="container" style="max-width: 500px; margin: 40px auto 24px;">
  <div class="card cta-card-small">
    <span class="meta" style="font-family: var(--font-body); color: var(--text-muted);">Have something to share?</span>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="create-blog.php" class="btn btn-primary">+ Create Blog</a>
    <?php else: ?>
      <a href="register.php" class="btn btn-primary">Get Started</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($search === ''): ?>
<section class="container why-blog">
  <div class="why-blog-heading reveal">
    <h2>Your Ideas. Your Voice. Your Story.</h2>
    <p>Every developer has something worth writing about. Here's why it's worth starting today.</p>
  </div>

  <div class="feature-grid">
   <div class="card feature-card-violet reveal">
      <div class="feature-icon fi-violet">✍️</div>
      <h3>Share Your Ideas</h3>
      <p class="meta" style="margin-top: 10px; font-family: var(--font-body); color: var(--text-muted);">Turn your thoughts into meaningful stories.</p>
    </div>
    <div class="card feature-card-blue reveal reveal-delay-1">
      <div class="feature-icon fi-blue">🧠</div>
      <h3>Learn &amp; Grow</h3>
      <p class="meta" style="margin-top: 10px; font-family: var(--font-body); color: var(--text-muted);">Explore topics and deepen your knowledge.</p>
    </div>
    <div class="card feature-card-pink reveal reveal-delay-2">
      <div class="feature-icon fi-pink">💬</div>
      <h3>Connect With Others</h3>
      <p class="meta" style="margin-top: 10px; font-family: var(--font-body); color: var(--text-muted);">Find people who share your interests.</p>
    </div>
    <div class="card feature-card-teal reveal reveal-delay-3">
      <div class="feature-icon fi-teal">💼</div>
      <h3>Build Your Identity</h3>
      <p class="meta" style="margin-top: 10px; font-family: var(--font-body); color: var(--text-muted);">Showcase your knowledge, creativity, and skills.</p>
    </div>
      <div class="card feature-card-amber reveal">
      <div class="feature-icon fi-amber">🚀</div>
      <h3>Grow Through Writing</h3>
      <p class="meta" style="margin-top: 10px; font-family: var(--font-body); color: var(--text-muted);">Improve your writing with every story.</p>
    </div>
  </div>
</section>
<?php endif; ?>

<main class="container" id="latest" style="padding-bottom: 80px;">
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
               <a href="blog.php?id=<?= $blog['id'] ?>" class="card card-with-image <?= $borderStyles[$coverIndex] ?>">
          <?php if (!empty($blog['cover_image']) && file_exists(__DIR__ . '/uploads/' . $blog['cover_image'])): ?>
            <img class="card-image" src="uploads/<?= htmlspecialchars($blog['cover_image']) ?>" alt="">
          <?php else: ?>
            <div class="card-cover <?= $coverStyles[$coverIndex] ?>"><?= $coverSymbols[$coverIndex] ?></div>
          <?php endif; ?>
          <span class="tag <?= $categoryTagClass[$blog['category']] ?? 'tag-violet' ?>"><?= htmlspecialchars($blog['category']) ?></span>
          <h3 style="margin-top: 10px;"><?= htmlspecialchars($blog['title']) ?></h3>
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