<?php
require_once __DIR__ . '/../config.php';
$page = $page ?? '';
$title = $title ?? 'Tiffin Route';
$user = $_SESSION['user'] ?? null;
function nav_active($p, $page) { return $p === $page ? ' class="active"' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title) ?> | Tiffin Route</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a class="brand" href="index.php">Tiffin <span>Route</span><small><?= $page === 'admin' ? 'ADMIN PANEL' : 'FOOD ORDERING' ?></small></a>
    <nav>
      <a href="index.php"<?= nav_active('home', $page) ?>>Home</a>
      <a href="menu.php"<?= nav_active('menu', $page) ?>>Menu</a>
      <a href="cart.php"<?= nav_active('cart', $page) ?>>Cart</a>
      <?php if (is_admin()): ?><a href="admin.php"<?= nav_active('admin', $page) ?>>Admin</a><?php endif; ?>
    </nav>
    <div class="nav-right">
      <a class="cart-pill" href="cart.php">🛒 Cart <b id="cartCount"><?= cart_count() ?></b></a>
      <?php if ($user): ?>
        <a class="btn-outline" href="logout.php" title="Click to log out">Hi, <?= h(strstr($user['email'], '@', true)) ?></a>
      <?php elseif ($page === 'login'): ?>
        <a class="btn-outline" href="register.php">Create account</a>
      <?php else: ?>
        <a class="btn-outline" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main>
