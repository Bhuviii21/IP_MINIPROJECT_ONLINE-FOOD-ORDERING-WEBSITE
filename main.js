// Tiffin Route - front-end behaviour (vanilla JS)
(function () {
  'use strict';

  function toast(msg) {
    let t = document.getElementById('toast');
    if (!t) { t = document.createElement('div'); t.id = 'toast'; document.body.appendChild(t); }
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 1600);
  }

  function cartCall(data) {
    return fetch('cart_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(data)
    }).then(r => r.json()).then(res => {
      if (res.ok) {
        const c = document.getElementById('cartCount');
        if (c) c.textContent = res.count;
      }
      return res;
    });
  }

  // Add-to-cart buttons (home page + menu page, including dynamically rendered ones)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.add-btn');
    if (!btn) return;
    cartCall({ action: 'add', id: btn.dataset.id, qty: 1 })
      .then(res => toast(res.ok ? 'Added to cart' : (res.error || 'Could not add')));
  });

  // ---------- Menu page: live filtering via api_menu.php ----------
  const grid = document.getElementById('menuGrid');
  if (grid) {
    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function render(items) {
      if (!items.length) { grid.innerHTML = '<p class="muted">No dishes in this category yet.</p>'; return; }
      grid.innerHTML = items.map(i => `
        <article class="card cat-${esc(i.category.toLowerCase())}">
          <div class="card-img">${esc(i.emoji)}</div>
          <div class="card-body">
            <small>${esc(i.category)}</small>
            <h4>${esc(i.name)}</h4>
            <p>${esc(i.description)}</p>
            <div class="row"><span class="price">₹${parseInt(i.price, 10)}</span>
            <button class="btn-dark add-btn" data-id="${parseInt(i.id, 10)}">Add</button></div>
          </div>
        </article>`).join('');
    }

    function load(cat) {
      fetch('api_menu.php?category=' + encodeURIComponent(cat))
        .then(r => r.json()).then(render)
        .catch(() => { grid.innerHTML = '<p class="alert">Could not load the menu.</p>'; });
    }

    document.getElementById('chips').addEventListener('click', function (e) {
      const chip = e.target.closest('.chip');
      if (!chip) return;
      document.querySelectorAll('.chip').forEach(c => c.classList.remove('on'));
      chip.classList.add('on');
      load(chip.dataset.cat);
    });

    load(grid.dataset.initial || 'all');
  }

  // ---------- Cart page: quantity + remove ----------
  document.querySelectorAll('.qty').forEach(function (box) {
    const input = box.querySelector('input');
    const id = box.dataset.id;
    function update(q) {
      cartCall({ action: 'update', id: id, qty: q }).then(() => location.reload());
    }
    box.querySelectorAll('.qty-btn').forEach(function (b) {
      b.addEventListener('click', function () {
        update(Math.max(0, parseInt(input.value, 10) + parseInt(b.dataset.d, 10)));
      });
    });
    input.addEventListener('change', function () {
      update(Math.max(0, parseInt(input.value, 10) || 1));
    });
  });
  document.querySelectorAll('.remove-btn').forEach(function (b) {
    b.addEventListener('click', function () {
      cartCall({ action: 'remove', id: b.dataset.id }).then(() => location.reload());
    });
  });

  // ---------- Registration: client-side validation ----------
  const reg = document.getElementById('registerForm');
  if (reg) {
    reg.addEventListener('submit', function (e) {
      const f = reg.elements, errs = [];
      if (f.name.value.trim().length < 2) errs.push('Please enter your full name.');
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email.value.trim())) errs.push('Enter a valid email address.');
      if (!/^[0-9]{10}$/.test(f.phone.value.trim())) errs.push('Phone number must be 10 digits.');
      if (f.password.value.length < 6) errs.push('Password must be at least 6 characters.');
      const box = reg.querySelector('.js-error');
      if (errs.length) {
        e.preventDefault();
        box.hidden = false;
        box.innerHTML = errs.map(x => '<div>' + x + '</div>').join('');
      } else {
        box.hidden = true;
      }
    });
  }
})();
