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
    $coverImage = $blog['cover_image']; // keep existing image by default

    if (empty($title) || empty($content)) {
        $errors[] = "Both title and content are required.";
    }

    // Handle "remove current image" checkbox
    if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        if (!empty($blog['cover_image']) && file_exists(__DIR__ . '/uploads/' . $blog['cover_image'])) {
            unlink(__DIR__ . '/uploads/' . $blog['cover_image']);
        }
        $coverImage = null;
    }

    // Handle new image upload (replaces existing one if provided)
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['cover_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "There was a problem uploading the image.";
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $maxSize = 5 * 1024 * 1024;

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = "Cover image must be a JPG, PNG, WEBP, or GIF file.";
            } elseif ($file['size'] > $maxSize) {
                $errors[] = "Cover image must be smaller than 5MB.";
            } else {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newCoverImage = uniqid() . '_' . time() . '.' . $extension;
                $uploadPath = __DIR__ . '/uploads/' . $newCoverImage;

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Delete the old image now that the new one is saved
                    if (!empty($blog['cover_image']) && file_exists(__DIR__ . '/uploads/' . $blog['cover_image'])) {
                        unlink(__DIR__ . '/uploads/' . $blog['cover_image']);
                    }
                    $coverImage = $newCoverImage;
                } else {
                    $errors[] = "Failed to save the uploaded image.";
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE blogPost SET title = ?, content = ?, cover_image = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $coverImage, $id, $_SESSION['user_id']]);

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

  <form method="POST" action="edit-blog.php?id=<?= $id ?>" enctype="multipart/form-data">
    <div class="form-group">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
    </div>

    <div class="form-group">
      <label for="cover_image">Cover Image</label>
      <?php if (!empty($blog['cover_image']) && file_exists(__DIR__ . '/uploads/' . $blog['cover_image'])): ?>
        <div style="margin-bottom: 10px;">
          <img src="uploads/<?= htmlspecialchars($blog['cover_image']) ?>" alt="" style="max-width: 200px; border-radius: var(--radius-sm); display: block; margin-bottom: 8px;">
          <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); font-weight: 400;">
            <input type="checkbox" name="remove_image" value="1" style="width: auto;">
            Remove current image
          </label>
        </div>
      <?php endif; ?>
      <input type="file" id="cover_image" name="cover_image" accept="image/*">
      <p class="meta" style="margin-top: 6px; color: var(--text-muted);">Choose a new file to replace the current image (optional)</p>
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