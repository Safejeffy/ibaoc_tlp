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

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
  echo "<p class='text-center text-gray-500'>Product not found.</p>";
  exit;
}
?>

<div class="space-y-3 text-gray-700">
  <div class="flex justify-between items-center">
    <h2 class="text-xl font-bold text-blue-700"><?= htmlspecialchars($product['product_name']) ?></h2>
    <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full"><?= htmlspecialchars($product['category']) ?></span>
  </div>

  <?php if (!empty($product['brand'])): ?>
    <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand']) ?></p>
  <?php endif; ?>

  <p><strong>Stock:</strong> 
    <span class="<?= $product['stock'] <= 5 ? 'text-red-600 font-semibold' : 'text-gray-800' ?>">
      <?= htmlspecialchars($product['stock']) ?>
    </span>
    <?php if ($product['stock'] <= 5): ?>
      <span class="ml-2 text-sm bg-red-100 text-red-600 px-2 py-1 rounded">Low Stock</span>
    <?php endif; ?>
  </p>

  <p><strong>Price:</strong> 
    <span class="font-semibold text-green-700">₱<?= number_format($product['price'], 2) ?></span>
  </p>

  <?php if (!empty($product['description'])): ?>
    <div class="mt-2">
      <strong>Description:</strong>
      <p class="text-gray-600 mt-1 whitespace-pre-line"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    </div>
  <?php endif; ?>

  <!-- Optional product image -->
  <?php if (!empty($product['image'])): ?>
    <div class="mt-3">
      <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="rounded-lg w-full max-h-64 object-cover">
    </div>
  <?php endif; ?>
</div>
