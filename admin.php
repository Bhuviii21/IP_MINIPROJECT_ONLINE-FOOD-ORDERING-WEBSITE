<?php
$page = 'admin'; $title = 'Admin';
require 'includes/header.php';
require_admin();

$cats = ['Breakfast', 'Meals', 'Curries', 'Beverages', 'Desserts'];
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $cat = $_POST['category'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $desc = trim($_POST['description'] ?? '');
        $emoji = trim($_POST['emoji'] ?? '') ?: '🍽️';
        $status = ($_POST['status'] ?? 'active') === 'draft' ? 'draft' : 'active';
        if ($name === '' || !in_array($cat, $cats, true) || $price <= 0) {
            $err = 'Enter a dish name, category and a price above 0.';
        } else {
            $stmt = $conn->prepare("INSERT INTO menu_items (name,category,description,price,emoji,status) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('sssdss', $name, $cat, $desc, $price, $emoji, $status);
            $stmt->execute();
            $msg = 'Dish added.';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $msg = 'Dish deleted.';
    }
}

$items = $conn->query("SELECT * FROM menu_items ORDER BY FIELD(category,'Breakfast','Meals','Curries','Beverages','Desserts'), id");
$today = $conn->query("SELECT COUNT(*) c FROM orders WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['c'];
$onMenu = $conn->query("SELECT COUNT(*) c FROM menu_items WHERE status='active'")->fetch_assoc()['c'];
$orders = $conn->query("SELECT order_code, customer_name, total, status, created_at FROM orders ORDER BY id DESC LIMIT 8");
?>
<section class="container section">
  <h1 class="page-title">Manage menu items</h1>
  <p class="muted">Add, update or remove dishes. Changes are written directly to the <code>menu_items</code> table.</p>
  <?php if ($msg): ?><div class="alert ok"><?= h($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert"><?= h($err) ?></div><?php endif; ?>
  <p><b><?= (int)$today ?> orders today</b> &nbsp;·&nbsp; <b><?= (int)$onMenu ?> items on menu</b></p>

  <div class="two-col admin-cols">
    <div>
      <table class="table dark-head">
        <thead><tr><th>Dish</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while ($i = $items->fetch_assoc()): ?>
          <tr>
            <td><?= h($i['name']) ?></td>
            <td><?= h($i['category']) ?></td>
            <td>₹<?= (int)$i['price'] ?></td>
            <td><span class="badge <?= $i['status'] ?>"><?= ucfirst($i['status']) ?></span></td>
            <td class="actions">
              <a class="btn-sm" href="admin_edit.php?id=<?= (int)$i['id'] ?>">Edit</a>
              <form method="post" onsubmit="return confirm('Delete this dish?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
                <button class="btn-sm danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>

      <h3 class="mt">Recent orders</h3>
      <table class="table">
        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php if ($orders->num_rows === 0): ?><tr><td colspan="4" class="muted">No orders yet.</td></tr><?php endif; ?>
        <?php while ($o = $orders->fetch_assoc()): ?>
          <tr><td>#<?= h($o['order_code']) ?></td><td><?= h($o['customer_name']) ?></td><td><?= money($o['total']) ?></td><td><span class="badge pending"><?= h($o['status']) ?></span></td></tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <form class="panel" method="post" novalidate>
      <h3>Add new dish</h3>
      <p class="muted small">Inserts a new row into <code>menu_items</code>.</p>
      <input type="hidden" name="action" value="add">
      <label>Dish name<input type="text" name="name" placeholder="e.g. Vada Sambar" required></label>
      <label>Category
        <select name="category"><?php foreach ($cats as $c): ?><option><?= $c ?></option><?php endforeach; ?></select>
      </label>
      <label>Price (₹)<input type="number" name="price" min="1" step="1" placeholder="99" required></label>
      <label>Description<input type="text" name="description" maxlength="255"></label>
      <label>Emoji<input type="text" name="emoji" maxlength="4" placeholder="🍛"></label>
      <label>Status
        <select name="status"><option value="active">Active</option><option value="draft">Draft</option></select>
      </label>
      <button class="btn block" type="submit">Add dish</button>
    </form>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
