<?php
session_start();
if (!isset($_SESSION['role'])) {
  header("Location: login.php");
  exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "aircon_inventory";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['id'] ?? 0;

if ($id) {
  $stmt = $conn->prepare("SELECT * FROM archived_products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $archived = $stmt->get_result()->fetch_assoc();

  if ($archived) {
    // Restore using the original product_id so IDs remain consistent across archive/restore
    $original_id = $archived['product_id'];

    $restore = $conn->prepare("INSERT INTO products (id, product_name, category, brand, description, stock, price)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
    // types: i (id), s (product_name), s (category), s (brand), s (description), i (stock), d (price)
    $restore->bind_param(
      "issssid",
      $original_id,
      $archived['product_name'],
      $archived['category'],
      $archived['brand'],
      $archived['description'],
      $archived['stock'],
      $archived['price']
    );
    // Execute restore and only log/delete if successful
    $restore_success = $restore->execute();

    // Use the original id for logging (insert_id may be 0 if id was explicitly provided)
    $restored_id = $original_id;

    if ($restore_success) {
      // Add a short details message to the activity log
      $details = "Product restored successfully .";

      $log = $conn->prepare("INSERT INTO product_activity_log (product_id, product_name, action_type, performed_by, details) VALUES (?, ?, 'Restore', ?, ?)");
      if ($log) {
        // types: i (product_id), s (product_name), s (performed_by), s (details)
        $log->bind_param("isss", $restored_id, $archived['product_name'], $_SESSION['username'], $details);
        $log->execute();
      } else {
        // As a fallback, insert a log without product_id (NULL) if prepare failed
        $fallback = $conn->prepare("INSERT INTO product_activity_log (product_id, product_name, action_type, performed_by, details) VALUES (NULL, ?, 'Restore', ?, ?)");
        if ($fallback) {
          $fallback->bind_param("sss", $archived['product_name'], $_SESSION['username'], $details);
          $fallback->execute();
        }
      }

      $delete = $conn->prepare("DELETE FROM archived_products WHERE id = ?");
      $delete->bind_param("i", $id);
      $delete->execute();
    }
  }
}

header("Location: archived_products.php");
exit;
?>
