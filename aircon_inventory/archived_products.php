<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "aircon_inventory";
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

// Fetch archived products
$sql = "SELECT id, product_id, product_name, category, brand, stock, price, archived_by, archived_at 
        FROM archived_products 
        ORDER BY archived_at DESC";
$result = $conn->query($sql);
if (!$result) die("Query failed: " . $conn->error);

$isAdmin = $_SESSION['role'] === 'admin';
$themeColor = $isAdmin ? 'blue' : 'yellow';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Archived Products</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">

<!-- Sidebar -->
<aside class="w-64 <?= $isAdmin ? 'bg-blue-800' : 'bg-yellow-500' ?> text-white min-h-screen p-5 flex flex-col justify-between">
  <div>
    <h2 class="text-2xl font-bold mb-8">
      <?= $isAdmin ? '❄️ Admin Panel' : '👷 Staff Panel' ?>
    </h2>
    <nav class="space-y-3">
      <a href="<?= $isAdmin ? 'dashboard.php' : 'staff_dashboard.php' ?>" class="block <?= $isAdmin ? 'bg-blue-700 hover:bg-blue-600' : 'bg-yellow-400 hover:bg-yellow-300' ?> px-3 py-2 rounded">Dashboard</a>
      <a href="archived_products.php" class="block px-3 py-2 rounded bg-gray-700">Archived Products</a>
      <a href="activity_log.php" class="block px-3 py-2 rounded hover:bg-<?= $themeColor ?>-600">Activity Log</a>
    </nav>
  </div>
  <a href="logout.php" class="block px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-center">Logout</a>
</aside>

<!-- Main Content -->
<main class="flex-1 p-8 overflow-x-auto">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-<?= $themeColor ?>-700">📦 Archived Products</h1>
    <p class="text-gray-600">Total: 
      <span class="font-semibold"><?= $result->num_rows ?></span>
    </p>
  </div>

  <!-- Table -->
  <div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <table class="w-full border-collapse">
      <thead class="bg-gray-200 text-gray-700">
        <tr>
          <th class="py-3 px-4 border">#</th>
          <th class="py-3 px-4 border">Product Name</th>
          <th class="py-3 px-4 border">Category</th>
          <th class="py-3 px-4 border">Brand</th>
          <th class="py-3 px-4 border">Stock</th>
          <th class="py-3 px-4 border">Price (₱)</th>
          <th class="py-3 px-4 border">Archived By</th>
          <th class="py-3 px-4 border">Date Archived</th>
          <th class="py-3 px-4 border text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
              <td class="py-2 px-4 border"><?= htmlspecialchars($row['id']) ?></td>
              <td class="py-2 px-4 border"><?= htmlspecialchars($row['product_name'] ?? '—') ?></td>
              <td class="py-2 px-4 border"><?= htmlspecialchars($row['category'] ?? '—') ?></td>
              <td class="py-2 px-4 border"><?= htmlspecialchars($row['brand'] ?? '—') ?></td>
              <td class="py-2 px-4 border text-center"><?= htmlspecialchars($row['stock'] ?? 0) ?></td>
              <td class="py-2 px-4 border">₱<?= number_format($row['price'] ?? 0, 2) ?></td>
              <td class="py-2 px-4 border"><?= htmlspecialchars($row['archived_by'] ?? 'Unknown') ?></td>
              <td class="py-2 px-4 border"><?= htmlspecialchars($row['archived_at'] ?? 'N/A') ?></td>
              <td class="py-2 px-4 border text-center">
                <a href="restore_product.php?id=<?= $row['id'] ?>" 
                   class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">Restore</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center py-4 text-gray-500">No archived products found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>

