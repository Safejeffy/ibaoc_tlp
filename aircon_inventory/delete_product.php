<?php
session_start();
// Only admins can permanently delete products
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "aircon_inventory";
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  $_SESSION['message'] = "⚠️ Invalid product ID.";
  header('Location: dashboard.php');
  exit;
}

// Fetch product to get the name for logging
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
  $_SESSION['message'] = "⚠️ Product not found.";
  header('Location: dashboard.php');
  exit;
}

// Begin a transaction to ensure log + delete happen together
$conn->begin_transaction();
try {
  // Insert activity log first (product exists at this point)
  $log = $conn->prepare("INSERT INTO product_activity_log (product_id, product_name, action_type, performed_by, details) VALUES (?, ?, 'Delete', ?, 'Product permanently deleted.')");
  $log->bind_param("iss", $id, $product['product_name'], $_SESSION['username']);
  $log->execute();

  // Delete any archived copies referencing this product (cleanup)
  $del_arch = $conn->prepare("DELETE FROM archived_products WHERE product_id = ?");
  $del_arch->bind_param("i", $id);
  $del_arch->execute();

  // Delete the product itself
  $del = $conn->prepare("DELETE FROM products WHERE id = ?");
  $del->bind_param("i", $id);
  $del->execute();

  $conn->commit();
  $_SESSION['message'] = "✅ Product '{$product['product_name']}' permanently deleted.";
} catch (Exception $e) {
  $conn->rollback();
  $_SESSION['message'] = "⚠️ Failed to delete product: " . $e->getMessage();
}

header('Location: dashboard.php');
exit;
?>
