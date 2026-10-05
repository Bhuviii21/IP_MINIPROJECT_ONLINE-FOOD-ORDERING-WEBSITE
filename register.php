<?php
$page = 'register'; $title = 'Create account';
require 'includes/header.php';
$errors = []; $old = ['name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = trim($_POST['name'] ?? '');
    $old['email'] = strtolower(trim($_POST['email'] ?? ''));
    $old['phone'] = trim($_POST['phone'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (strlen($old['name']) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[0-9]{10}$/', $old['phone'])) $errors[] = 'Phone number must be 10 digits.';
    if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';

    if (!$errors) {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $old['email']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows) {
            $errors[] = 'This email is already registered. Please log in.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name,email,phone,password_hash) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $old['name'], $old['email'], $old['phone'], $hash);
            $stmt->execute();
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<section class="auth-wrap">
  <form class="auth-card" method="post" id="registerForm" novalidate>
    <h2>Create your account</h2>
    <p class="muted">Register once to order, save addresses and track deliveries.</p>
    <?php foreach ($errors as $e): ?><div class="alert"><?= h($e) ?></div><?php endforeach; ?>
    <div class="alert js-error" hidden></div>

    <label>Full name<input type="text" name="name" value="<?= h($old['name']) ?>" required></label>
    <label>Email address<input type="email" name="email" value="<?= h($old['email']) ?>" required></label>
    <label>Phone number<input type="tel" name="phone" value="<?= h($old['phone']) ?>" maxlength="10" required></label>
    <label>Password<input type="password" name="password" minlength="6" required></label>
    <button class="btn" type="submit">Create account</button>
    <p class="center small">Already registered? <a href="login.php">Log in</a></p>
  </form>
</section>
<?php require 'includes/footer.php'; ?>
