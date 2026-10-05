<?php
$page = 'cart'; $title = 'Cart';
require 'includes/header.php';
$items = cart_items($conn);
$t = cart_totals($items);
?>
<section class="container section">
  <h1 class="page-title">Your cart</h1>
  <p class="muted">Review your items before checking out. Totals update automatically.</p>
  <?php if (!$items): ?>
    <div class="empty">Your cart is empty. <a href="menu.php">Browse the menu</a></div>
  <?php else: ?>
  <div class="two-col">
    <table class="table cart-table">
      <thead><tr><th>Item</th><th>Quantity</th><th>Amount</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($items as $i): ?>
        <tr>
          <td><b><?= h($i['name']) ?></b><br><small class="muted"><?= h($i['category']) ?></small></td>
          <td>
            <div class="qty" data-id="<?= (int)$i['id'] ?>">
              <button type="button" class="qty-btn" data-d="-1">-</button>
              <input type="number" min="1" max="20" value="<?= (int)$i['qty'] ?>">
              <button type="button" class="qty-btn" data-d="1">+</button>
            </div>
          </td>
          <td><?= money($i['line']) ?></td>
          <td><button type="button" class="btn-sm danger remove-btn" data-id="<?= (int)$i['id'] ?>">Remove</button></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <aside class="summary">
      <h3>Order summary</h3>
      <div class="line"><span>Subtotal</span><span><?= money($t['subtotal']) ?></span></div>
      <div class="line"><span>Tax (5%)</span><span><?= money($t['tax']) ?></span></div>
      <div class="line"><span>Delivery fee</span><span><?= money($t['fee']) ?></span></div>
      <div class="line total"><span>Total</span><span><?= money($t['total']) ?></span></div>
      <a class="btn block" href="checkout.php">Proceed to checkout</a>
    </aside>
  </div>
  <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
