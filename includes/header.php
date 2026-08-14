<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ByteLog</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/blog-application/css/style.css">
</head>
<body>

<nav style="border-bottom: 1px solid var(--border); padding: 20px 0;">
  <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
    <a href="/blog-application/index.php"><h3>Byte<span class="logo-cursor">_</span>Log</h3></a>

    <!-- Desktop nav links -->
    <div class="nav-links" style="display: flex; gap: 24px; align-items: center; font-size: 0.9rem;">
      <a href="/blog-application/index.php">Home</a>
      <?php if ($isLoggedIn): ?>
        <a href="/blog-application/dashboard.php">My Blogs</a>
        <a href="/blog-application/create-blog.php" class="btn btn-secondary" style="padding: 8px 18px;">+ New Post</a>
        <a href="/blog-application/logout.php" class="btn btn-primary" style="padding: 8px 18px;">Logout</a>
      <?php else: ?>
        <a href="/blog-application/login.php" class="btn btn-secondary" style="padding: 8px 18px;">Sign In</a>
        <a href="/blog-application/register.php" class="btn btn-primary" style="padding: 8px 18px;">Sign Up</a>
      <?php endif; ?>
    </div>

    <!-- Mobile hamburger button -->
    <button id="menuToggle" class="menu-toggle" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>

  <!-- Mobile dropdown menu -->
  <div id="mobileMenu" class="mobile-menu">
    <a href="/blog-application/index.php">Home</a>
    <?php if ($isLoggedIn): ?>
      <a href="/blog-application/dashboard.php">My Blogs</a>
      <a href="/blog-application/create-blog.php">+ New Post</a>
      <a href="/blog-application/logout.php">Logout</a>
    <?php else: ?>
      <a href="/blog-application/login.php">Sign In</a>
      <a href="/blog-application/register.php">Sign Up</a>
    <?php endif; ?>
  </div>
</nav>

<script>
document.getElementById('menuToggle').addEventListener('click', function() {
  document.getElementById('mobileMenu').classList.toggle('open');
  this.classList.toggle('active');
});
</script>