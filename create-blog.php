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
    $coverImage = null;

    if (empty($title) || empty($content)) {
        $errors[] = "Both title and content are required.";
    }

    // Handle image upload (optional — only validate if a file was actually chosen)
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['cover_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "There was a problem uploading the image.";
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = "Cover image must be a JPG, PNG, WEBP, or GIF file.";
            } elseif ($file['size'] > $maxSize) {
                $errors[] = "Cover image must be smaller than 5MB.";
            } else {
                // Build a safe, unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $coverImage = uniqid() . '_' . time() . '.' . $extension;
                $uploadPath = __DIR__ . '/uploads/' . $coverImage;

                if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $errors[] = "Failed to save the uploaded image.";
                    $coverImage = null;
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content, cover_image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $content, $coverImage]);
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

  <form method="POST" action="create-blog.php" enctype="multipart/form-data">
    <div class="form-group">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>
    </div>
    
    <div class="form-group">
      <label for="cover_image">Cover Image (optional)</label>
      <input type="file" id="cover_image" name="cover_image" accept="image/*">
      <p class="meta" style="margin-top: 6px; color: var(--text-muted);">JPG, PNG, WEBP or GIF — max 5MB</p>
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