<?php
$page = 'admin'; $title = 'Edit dish';
require 'includes/header.php';
require_admin();

$cats = ['Breakfast', 'Meals', 'Curries', 'Beverages', 'Desserts'];
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $cat = $_POST['category'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $emoji = trim($_POST['emoji'] ?? '') ?: '🍽️';
    $status = ($_POST['status'] ?? 'active') === 'draft' ? 'draft' : 'active';
    if ($name === '' || !in_array($cat, $cats, true) || $price <= 0) {
        $err = 'Enter a dish name, category and a price above 0.';
    } else {
        $stmt = $conn->prepare("UPDATE menu_items SET name=?, category=?, description=?, price=?, emoji=?, status=? WHERE id=?");
        $stmt->bind_param('sssdssi', $name, $cat, $desc, $price, $emoji, $status, $id);
        $stmt->execute();
        header('Location: admin.php');
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$i = $stmt->get_result()->fetch_assoc();
if (!$i) { header('Location: admin.php'); exit; }
?>
<section class="auth-wrap">
  <form class="auth-card" method="post" novalidate>
    <h2>Edit dish</h2>
    <?php if ($err): ?><div class="alert"><?= h($err) ?></div><?php endif; ?>
    <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
    <label>Dish name<input type="text" name="name" value="<?= h($i['name']) ?>" required></label>
    <label>Category
      <select name="category"><?php foreach ($cats as $c): ?><option<?= $c === $i['category'] ? ' selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select>
    </label>
    <label>Price (₹)<input type="number" name="price" min="1" step="1" value="<?= (int)$i['price'] ?>" required></label>
    <label>Description<input type="text" name="description" maxlength="255" value="<?= h($i['description']) ?>"></label>
    <label>Emoji<input type="text" name="emoji" maxlength="4" value="<?= h($i['emoji']) ?>"></label>
    <label>Status
      <select name="status">
        <option value="active"<?= $i['status'] === 'active' ? ' selected' : '' ?>>Active</option>
        <option value="draft"<?= $i['status'] === 'draft' ? ' selected' : '' ?>>Draft</option>
      </select>
    </label>
    <button class="btn block" type="submit">Save changes</button>
    <p class="center small"><a href="admin.php">← Back to dashboard</a></p>
  </form>
</section>
<?php require 'includes/footer.php'; ?>
