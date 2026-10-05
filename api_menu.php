<?php
// Returns menu items as JSON, optionally filtered by category. Used by menu.js (fetch).
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

$allowed = ['Breakfast', 'Meals', 'Curries', 'Beverages', 'Desserts'];
$cat = $_GET['category'] ?? 'all';
$rows = [];

if (in_array($cat, $allowed, true)) {
    $stmt = $conn->prepare("SELECT id,name,category,description,price,emoji FROM menu_items WHERE status='active' AND category=? ORDER BY name");
    $stmt->bind_param('s', $cat);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query("SELECT id,name,category,description,price,emoji FROM menu_items WHERE status='active' ORDER BY FIELD(category,'Breakfast','Meals','Curries','Beverages','Desserts'), name");
}
while ($r = $res->fetch_assoc()) $rows[] = $r;
echo json_encode($rows, JSON_UNESCAPED_UNICODE);
