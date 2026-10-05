<?php
$page = 'menu'; $title = 'Menu';
require 'includes/header.php';
$cats = ['Breakfast', 'Meals', 'Curries', 'Beverages', 'Desserts'];
$initial = in_array($_GET['category'] ?? '', $cats, true) ? $_GET['category'] : 'all';
?>
<section class="container section">
  <h1 class="page-title">Our menu</h1>
  <p class="muted">Fetched live from the <code>menu_items</code> table — filter by category, then add to your cart.</p>
  <div class="chips" id="chips">
    <button class="chip<?= $initial === 'all' ? ' on' : '' ?>" data-cat="all">All items</button>
    <?php foreach ($cats as $c): ?>
      <button class="chip<?= $initial === $c ? ' on' : '' ?>" data-cat="<?= $c ?>"><?= $c ?></button>
    <?php endforeach; ?>
  </div>
  <div class="grid" id="menuGrid" data-initial="<?= h($initial) ?>"><p class="muted">Loading menu…</p></div>
</section>
<?php require 'includes/footer.php'; ?>
