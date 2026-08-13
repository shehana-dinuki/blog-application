<?php
require 'config/database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and clean up the submitted values
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email is already taken.";
        }
    }

    // If no errors, create the account
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $hashed_password]);

        $success = true;
    }
}
?>
<?php require 'includes/header.php'; ?>

<div class="container" style="max-width: 420px; padding: 60px 0;">
  <h1 style="text-align: center; margin-bottom: 8px;">Create an account</h1>
  <p class="meta" style="text-align: center; margin-bottom: 32px; font-family: var(--font-body); color: var(--text-muted);">
    Join ByteLog and start writing
  </p>

  <?php if (!empty($errors)): ?>
    <div style="background: rgba(239, 83, 80, 0.1); border: 1px solid var(--danger); border-radius: var(--radius-sm); padding: 14px 16px; margin-bottom: 20px;">
      <?php foreach ($errors as $error): ?>
        <p style="color: var(--danger); font-size: 0.9rem;">⚠ <?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div style="background: rgba(74, 222, 128, 0.1); border: 1px solid var(--success); border-radius: var(--radius-sm); padding: 14px 16px; margin-bottom: 20px;">
      <p style="color: var(--success); font-size: 0.9rem;">✓ Account created successfully! You can now <a href="login.php" style="color: var(--success); text-decoration: underline;">sign in</a>.</p>
    </div>
  <?php endif; ?>

  <?php if (!$success): ?>
  <form method="POST" action="register.php">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
      Create Account
    </button>
  </form>
  <?php endif; ?>

  <p class="meta" style="text-align: center; margin-top: 20px; font-family: var(--font-body); color: var(--text-muted);">
    Already have an account? <a href="login.php" style="color: var(--accent);">Sign in</a>
  </p>
</div>

<?php require 'includes/footer.php'; ?>