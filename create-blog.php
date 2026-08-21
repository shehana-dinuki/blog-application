<?php
session_start();
require 'config/database.php';

// Authorization check: block access if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$categories = ['General', 'PHP', 'MySQL', 'JavaScript', 'Web Dev', 'Tutorial'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = in_array($_POST['category'], $categories) ? $_POST['category'] : 'General';
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
        $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, category, content, cover_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $category, $content, $coverImage]);
        $newBlogId = $pdo->lastInsertId();

        header("Location: blog.php?id=" . $newBlogId);
        exit;
    }
}
?>
<?php require 'includes/header.php'; ?>

<div class="container create-page-container">
  <h1 style="margin-bottom: 8px;">Write a new post</h1>
 <p class="meta create-page-description">
    Share something with the ByteLog community
  </p>

  <?php if (!empty($errors)): ?>
    <div class="create-error-box">
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
      <label for="category">Category</label>
      <select id="category" name="category">
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat ?>" <?= (isset($category) && $category === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="cover_image">Cover Image (optional)</label>
      <input type="file" id="cover_image" name="cover_image" accept="image/*">
      <p class="meta create-help-text">JPG, PNG, WEBP or GIF — max 5MB</p>
    </div>
    
   <div class="form-group">
      <label for="content">Content</label>
      <div class="editor-toolbar">
        <button type="button" class="tb-bold" onclick="wrapSelection('content', '**', '**')" title="Bold">B</button>
        <button type="button" class="tb-italic" onclick="wrapSelection('content', '*', '*')" title="Italic">I</button>
        <button type="button" class="tb-code" onclick="wrapSelection('content', '\`', '\`')" title="Code">&lt;/&gt;</button>
        <button type="button" onclick="wrapSelection('content', '## ', '')" title="Heading">H</button>
      </div>
      <textarea id="content" name="content" rows="12" required><?= isset($content) ? htmlspecialchars($content) : '' ?></textarea>
      <p class="meta" style="margin-top: 6px; color: var(--text-muted);">Select text and click a button to format it</p>
    </div>

    <div class="create-form-actions">
      <button type="submit" class="btn btn-primary">Publish Post</button>
      <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require 'includes/footer.php'; ?>