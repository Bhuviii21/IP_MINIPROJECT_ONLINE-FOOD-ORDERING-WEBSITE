TIFFIN ROUTE - Food Ordering (HTML, CSS, JavaScript, PHP, MySQL)
================================================================

HOW TO RUN WITH XAMPP
---------------------
1. Install and open XAMPP Control Panel. Start "Apache" and "MySQL".
2. Copy the whole "tiffin-route" folder into  C:\xampp\htdocs\
3. Open http://localhost/phpmyadmin  ->  Import  ->  choose database.sql  ->  Go.
   (This creates the database "tiffin_route" with all tables and sample dishes.)
4. Open http://localhost/tiffin-route/setup_admin.php ONCE to create the admin
   account, then DELETE setup_admin.php.
       Admin login:  admin@tiffinroute.com  /  admin123
5. Open http://localhost/tiffin-route/

DATABASE CONNECTION
-------------------
config.php holds the connection (mysqli):
   host = localhost, user = root, password = (empty), database = tiffin_route
Every page includes config.php (through includes/header.php).
If your MySQL root user has a password, change $DB_PASS in config.php.

FILES
-----
database.sql       tables: users, menu_items, orders, order_items + sample data
config.php         DB connection, session start, helper functions
setup_admin.php    one-time admin creation
index.php          Home page
register.php       Registration (client + server validation, hashed password)
login.php          Login (password_verify, session)  / logout.php
menu.php           Menu page; api_menu.php returns JSON, filtered by category (fetch)
cart.php           Cart page; cart_action.php is the session cart API
checkout.php       Delivery details + payment, saves order + order_items (transaction)
order_success.php  Order confirmation
admin.php          Admin dashboard: list / add / delete dishes, recent orders
admin_edit.php     Edit a dish
assets/css/style.css, assets/js/main.js
includes/header.php, includes/footer.php

All database queries use prepared statements; output is escaped with htmlspecialchars.
