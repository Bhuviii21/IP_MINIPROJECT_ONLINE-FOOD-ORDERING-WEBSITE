<?php
$page = 'home'; $title = 'Home';
require 'includes/header.php';
$special = $conn->query("SELECT * FROM menu_items WHERE status='active' ORDER BY id LIMIT 1")->fetch_assoc();
$count = $conn->query("SELECT COUNT(*) c FROM menu_items WHERE status='active'")->fetch_assoc()['c'];
$popular = $conn->query("SELECT * FROM menu_items WHERE status='active' ORDER BY price DESC LIMIT 4");
?>
<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow">• Now delivering across Chennai</span>
      <h1>Home-style tiffin, <em>ordered online,</em> delivered hot.</h1>
      <p>Tiffin Route brings authentic South Indian meals, curries and thalis from our kitchen straight to your door. Browse the menu, track your order and manage everything from one dashboard.</p>
      <div class="hero-btns">
        <a class="btn" href="menu.php">Browse the menu</a>
        <?php if (!is_logged_in()): ?><a class="btn-outline" href="register.php">Create an account</a><?php endif; ?>
      </div>
      <div class="stats">
        <div><b><?= (int)$count ?>+</b><span>Dishes on the menu</span></div>
        <div><b>4.7 / 5</b><span>Average rating</span></div>
        <div><b>35 min</b><span>Average delivery</span></div>
      </div>
    </div>
    <?php if ($special): ?>
    <div class="special-card">
      <span class="tag">Today's special</span>
      <h3><?= h($special['name']) ?></h3>
      <p><?= h($special['description']) ?></p>
      <div class="row"><span class="price"><?= '₹' . (int)$special['price'] ?></span>
        <button class="btn-dark add-btn" data-id="<?= (int)$special['id'] ?>">Add to cart</button></div>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="container section">
  <h2>How ordering works</h2>
  <div class="steps">
    <div class="step"><b>1</b><h4>Create an account</h4><p>Register once with your name, email and phone.</p></div>
    <div class="step"><b>2</b><h4>Pick your dishes</h4><p>Filter the live menu by category and add to cart.</p></div>
    <div class="step"><b>3</b><h4>Checkout</h4><p>Enter your address and place the order.</p></div>
  </div>
</section>

<section class="container section">
  <h2>Popular dishes</h2>
  <div class="grid">
    <?php while ($i = $popular->fetch_assoc()): ?>
    <article class="card cat-<?= strtolower($i['category']) ?>">
      <div class="card-img"><?= h($i['emoji']) ?></div>
      <div class="card-body">
        <small><?= h($i['category']) ?></small>
        <h4><?= h($i['name']) ?></h4>
        <p><?= h($i['description']) ?></p>
        <div class="row"><span class="price">₹<?= (int)$i['price'] ?></span>
          <button class="btn-dark add-btn" data-id="<?= (int)$i['id'] ?>">Add</button></div>
      </div>
    </article>
    <?php endwhile; ?>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
