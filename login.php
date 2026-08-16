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
        $stmt = $pdo->prepare("SELECT id, username, email, password FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
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
</div>

<?php require 'includes/footer.php'; ?>