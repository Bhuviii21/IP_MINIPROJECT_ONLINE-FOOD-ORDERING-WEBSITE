<?php
// Session cart API. POST: action=add|update|remove|clear, id, qty. Returns JSON.
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

if (in_array($action, ['add', 'update'], true)) {
    $stmt = $conn->prepare("SELECT id FROM menu_items WHERE id=? AND status='active'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if (!$stmt->get_result()->num_rows) {
        echo json_encode(['ok' => false, 'error' => 'Item not available']);
        exit;
    }
}

switch ($action) {
    case 'add':
        $_SESSION['cart'][$id] = min(20, ($_SESSION['cart'][$id] ?? 0) + max(1, $qty));
        break;
    case 'update':
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = min(20, $qty);
        break;
    case 'remove':
        unset($_SESSION['cart'][$id]);
        break;
    case 'clear':
        $_SESSION['cart'] = [];
        break;
    default:
        echo json_encode(['ok' => false, 'error' => 'Unknown action']);
        exit;
}

$totals = cart_totals(cart_items($conn));
echo json_encode(['ok' => true, 'count' => cart_count(), 'totals' => $totals]);
