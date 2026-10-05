<?php
$page = 'success'; $title = 'Order placed';
require 'includes/header.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$uid = (int)$_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$o = $stmt->get_result()->fetch_assoc();
if (!$o) { header('Location: menu.php'); exit; }
?>
<section class="auth-wrap">
  <div class="auth-card center">
    <div class="big-tick">✅</div>
    <h2>Order placed successfully!</h2>
    <p class="muted">Your order has been saved to the database and is now <b><?= h($o['status']) ?> confirmation</b>. You'll receive an update once the kitchen accepts it.</p>
    <table class="kv">
      <tr><td>Order ID</td><td><b>#<?= h($o['order_code']) ?></b></td></tr>
      <tr><td>Status</td><td><span class="badge pending"><?= h($o['status']) ?></span></td></tr>
      <tr><td>Total</td><td><?= money($o['total']) ?></td></tr>
      <tr><td>Estimated delivery</td><td>35–45 minutes</td></tr>
      <tr><td>Payment method</td><td><?= h($o['payment_method']) ?></td></tr>
    </table>
    <a class="btn block" href="menu.php">Order something else</a>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
