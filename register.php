<?php
require 'config/database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Password must contain at least one symbol (e.g. ! @ # $ %).";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email is already taken.";
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $hashed_password]);

        $success = true;
    }
}
?>
<?php require 'includes/header.php'; ?>

<div class="auth-wrapper">
  <svg class="auth-svg-bg" viewBox="0 0 1000 700" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
        <feGaussianBlur stdDeviation="12" result="blur"/>
        <feMerge>
          <feMergeNode in="blur"/>
          <feMergeNode in="SourceGraphic"/>
        </feMerge>
      </filter>
    </defs>
    <g filter="url(#glow)" opacity="0.85">
      <path class="trail trail-1" d="M-100,150 C150,50 300,300 550,180 S850,50 1100,200" stroke-width="5"/>
      <path class="trail trail-2" d="M-100,500 C200,650 400,350 650,520 S950,650 1100,450" stroke-width="4"/>
      <path class="trail trail-3" d="M-100,300 C250,180 350,450 600,320 S900,180 1100,350" stroke-width="6"/>
      <path class="trail trail-4" d="M-100,600 C150,480 450,600 600,420 S850,300 1100,480" stroke-width="3"/>
      <path class="trail trail-5" d="M-100,80 C200,220 500,20 700,180 S950,300 1100,100" stroke-width="4"/>
      <path class="trail trail-6" d="M-100,420 C300,500 400,220 650,300 S900,420 1100,250" stroke-width="3"/>
    </g>
  </svg>

  <div class="auth-card">
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
        <div class="password-wrapper">
    <input type="password" id="password" name="password" required>
    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
    </button>
</div>
        <p class="meta" style="margin-top: 6px; color: var(--text-muted);">
          At least 8 characters, with 1 number and 1 symbol
        </p>
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
</div>

<?php require 'includes/footer.php'; ?>