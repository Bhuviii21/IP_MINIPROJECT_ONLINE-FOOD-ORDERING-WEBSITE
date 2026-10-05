<?php
$page = 'checkout'; $title = 'Checkout';
require 'includes/header.php';
require_login('checkout.php');

$items = cart_items($conn);
if (!$items) { header('Location: cart.php'); exit; }
$t = cart_totals($items);
$errors = [];
$name = $_SESSION['user']['name'];
$address = '';
$methods = ['Cash on Delivery', 'UPI', 'Card on Delivery'];
$method = $methods[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $method = $_POST['payment'] ?? '';
    if (strlen($name) < 2) $errors[] = 'Enter your full name.';
    if (strlen($address) < 10) $errors[] = 'Enter a complete delivery address.';
    if (!in_array($method, $methods, true)) $errors[] = 'Choose a payment method.';

    if (!$errors) {
        try {
            $conn->begin_transaction();
            $uid = (int)$_SESSION['user']['id'];
            $stmt = $conn->prepare("INSERT INTO orders (user_id,customer_name,address,payment_method,subtotal,tax,delivery_fee,total) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param('isssdddd', $uid, $name, $address, $method, $t['subtotal'], $t['tax'], $t['fee'], $t['total']);
            $stmt->execute();
            $oid = $conn->insert_id;
            $code = 'TR-' . date('Y') . str_pad($oid, 4, '0', STR_PAD_LEFT);
            $stmt = $conn->prepare("UPDATE orders SET order_code=? WHERE id=?");
            $stmt->bind_param('si', $code, $oid);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO order_items (order_id,item_id,item_name,price,qty) VALUES (?,?,?,?,?)");
            foreach ($items as $i) {
                $stmt->bind_param('iisdi', $oid, $i['id'], $i['name'], $i['price'], $i['qty']);
                $stmt->execute();
            }
            $conn->commit();
            $_SESSION['cart'] = [];
            header('Location: order_success.php?id=' . $oid);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = 'Could not place the order. Please try again.';
        }
    }
}
?>
<section class="container section">
  <div class="two-col">
    <form class="panel" method="post" novalidate>
      <h2>Delivery details</h2>
      <p class="muted">Confirm where we should deliver your order.</p>
      <?php foreach ($errors as $e): ?><div class="alert"><?= h($e) ?></div><?php endforeach; ?>
      <label>Full name<input type="text" name="name" value="<?= h($name) ?>" required></label>
      <label>Delivery address<textarea name="address" rows="3" required><?= h($address) ?></textarea></label>
      <label>Payment method
        <select name="payment">
          <?php foreach ($methods as $m): ?><option<?= $m === $method ? ' selected' : '' ?>><?= $m ?></option><?php endforeach; ?>
        </select>
      </label>
      <button class="btn block" type="submit">Place order</button>
    </form>
    <aside class="summary">
      <h3>Order summary</h3>
      <div class="line"><span>Subtotal</span><span><?= money($t['subtotal']) ?></span></div>
      <div class="line"><span>Tax (5%)</span><span><?= money($t['tax']) ?></span></div>
      <div class="line"><span>Delivery fee</span><span><?= money($t['fee']) ?></span></div>
      <div class="line total"><span>Total</span><span><?= money($t['total']) ?></span></div>
    </aside>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
