<?php
session_start();
require 'config/database.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    } else {
        // Look up the user by email
        $stmt = $pdo->prepare("SELECT id, username, email, password FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify the password against the stored hash
        if ($user && password_verify($password, $user['password'])) {
            // Correct credentials — start the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<?php require 'includes/header.php'; ?>

<div class="container" style="max-width: 420px; padding: 60px 0;">
  <h1 style="text-align: center; margin-bottom: 8px;">Welcome back</h1>
  <p class="meta" style="text-align: center; margin-bottom: 32px; font-family: var(--font-body); color: var(--text-muted);">
    Sign in to your ByteLog account
  </p>

  <?php if (!empty($errors)): ?>
    <div style="background: rgba(239, 83, 80, 0.1); border: 1px solid var(--danger); border-radius: var(--radius-sm); padding: 14px 16px; margin-bottom: 20px;">
      <?php foreach ($errors as $error): ?>
        <p style="color: var(--danger); font-size: 0.9rem;">⚠ <?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
      Sign In
    </button>
  </form>

  <p class="meta" style="text-align: center; margin-top: 20px; font-family: var(--font-body); color: var(--text-muted);">
    Don't have an account? <a href="register.php" style="color: var(--accent);">Sign up</a>
  </p>
</div>

<?php require 'includes/footer.php'; ?>