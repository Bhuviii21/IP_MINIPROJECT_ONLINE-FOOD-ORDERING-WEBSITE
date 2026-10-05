<?php
/**
 * Tiffin Route - database connection + shared helpers
 * XAMPP defaults: host=localhost, user=root, empty password.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'tiffin_route';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('<h2>Database connection failed</h2><p>Start Apache and MySQL in XAMPP and import <code>database.sql</code> in phpMyAdmin.</p><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}

const TAX_RATE = 0.05;
const DELIVERY_FEE = 30;

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function is_logged_in() { return !empty($_SESSION['user']); }
function is_admin() { return is_logged_in() && $_SESSION['user']['role'] === 'admin'; }

function require_login($next = '') {
    if (!is_logged_in()) {
        header('Location: login.php' . ($next ? '?next=' . urlencode($next) : ''));
        exit;
    }
}
function require_admin() {
    if (!is_admin()) {
        header('Location: login.php?next=admin.php');
        exit;
    }
}

function cart_count() {
    return array_sum($_SESSION['cart'] ?? []);
}

/** Returns cart rows with qty and line total, read from the database. */
function cart_items($conn) {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $res = $conn->query("SELECT * FROM menu_items WHERE id IN ($ids) AND status='active'");
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['qty'] = (int)$cart[$r['id']];
        $r['line'] = $r['qty'] * $r['price'];
        $rows[] = $r;
    }
    return $rows;
}

function cart_totals($items) {
    $subtotal = 0;
    foreach ($items as $i) $subtotal += $i['line'];
    $tax = round($subtotal * TAX_RATE, 2);
    $fee = $subtotal > 0 ? DELIVERY_FEE : 0;
    return ['subtotal' => $subtotal, 'tax' => $tax, 'fee' => $fee, 'total' => $subtotal + $tax + $fee];
}

function money($n) { return '₹' . number_format($n, 2); }
