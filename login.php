<?php
$page = 'login'; $title = 'Login';
require 'includes/header.php';
$error = '';
$next = $_GET['next'] ?? ($_POST['next'] ?? '');
// only allow local page names as redirect targets
if (!preg_match('/^[a-z_]+\.php$/', $next)) $next = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if ($u && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['full_name'], 'email' => $u['email'], 'role' => $u['role']];
        header('Location: ' . ($next ?: ($u['role'] === 'admin' ? 'admin.php' : 'menu.php')));
        exit;
    }
    $error = 'Invalid email or password.';
}
?>
<section class="auth-wrap">
  <form class="auth-card" method="post" novalidate>
    <h2>Welcome back</h2>
    <p class="muted">Log in to reorder your favourites and track deliveries.</p>
    <?php if (isset($_GET['registered'])): ?><div class="alert ok">Account created. Please log in.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
    <input type="hidden" name="next" value="<?= h($next) ?>">
    <label>Email address<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <button class="btn" type="submit">Log in</button>
    <p class="center small">Don't have an account? <a href="register.php">Register here</a></p>
  </form>
</section>
<?php require 'includes/footer.php'; ?>
