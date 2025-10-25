<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "aircon_inventory";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $product_name = $_POST['product_name'];
  $category = $_POST['category'];
  $brand = $_POST['brand'];
  $description = $_POST['description'];
  $stock = $_POST['stock'];
  $price = $_POST['price'];

  $stmt = $conn->prepare("INSERT INTO products (product_name, category, brand, description, stock, price) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssid", $product_name, $category, $brand, $description, $stock, $price);

  if ($stmt->execute()) {
    // ✅ Log the "Add" action
    $product_id = $stmt->insert_id;
    $log = $conn->prepare("INSERT INTO product_activity_log (product_id, product_name, action_type, performed_by) VALUES (?, ?, 'Add', ?)");
    $log->bind_param("iss", $product_id, $product_name, $_SESSION['username']);
    $log->execute();

    header("Location: dashboard.php");
    exit;
  } else {
    echo "Error adding product: " . $conn->error;
  }
}
?>
