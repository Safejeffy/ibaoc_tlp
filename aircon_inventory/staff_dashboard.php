<?php
session_start();

// ✅ Only Staff Access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
  header("Location: login.php");
  exit;
}

$conn = new mysqli("localhost", "root", "", "aircon_inventory");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM products WHERE product_name LIKE ? OR category LIKE ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$likeSearch = "%$search%";
$stmt->bind_param("ss", $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard - Aircon Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">

  <!-- ✅ Sidebar -->
  <aside class="w-64 bg-yellow-500 text-white min-h-screen p-5 flex flex-col justify-between">
    <div>
      <h2 class="text-2xl font-bold mb-8">👷 Staff Panel</h2>
      <nav class="space-y-3">
        <a href="staff_dashboard.php" class="block bg-yellow-400 px-3 py-2 rounded">Dashboard</a>
        <button onclick="openAddModal()" class="w-full bg-green-600 py-2 rounded hover:bg-green-700 mt-3">Add Product</button>
        <a href="archived_products.php" class="block px-3 py-2 rounded hover:bg-yellow-400">Archived Products</a>
        <a href="activity_log.php" class="block px-3 py-2 rounded hover:bg-yellow-400">Activity Log</a>
      </nav>
    </div>
    <a href="logout.php" class="block px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-center">Logout</a>
  </aside>

  <!-- ✅ Main Content -->
  <main class="flex-1 p-8 overflow-x-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-yellow-600">Staff Dashboard</h1>

      <!-- Search Bar -->
      <form method="GET" class="flex items-center">
        <input type="text" name="search" placeholder="Search product or category..."
               value="<?= htmlspecialchars($search) ?>"
               class="border px-3 py-2 rounded-l w-64">
        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-r hover:bg-yellow-600">Search</button>
      </form>
    </div>

    <!-- ✅ Product Table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
      <table class="w-full border-collapse">
        <thead class="bg-yellow-100">
          <tr>
            <th class="py-3 px-4 border">ID</th>
            <th class="py-3 px-4 border">Product Name</th>
            <th class="py-3 px-4 border">Category</th>
            <th class="py-3 px-4 border">Stock</th>
            <th class="py-3 px-4 border">Price (₱)</th>
            <th class="py-3 px-4 border text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-gray-50">
                <td class="py-2 px-4 border"><?= $row['id'] ?></td>
                <td class="py-2 px-4 border"><?= htmlspecialchars($row['product_name']) ?></td>
                <td class="py-2 px-4 border"><?= htmlspecialchars($row['category']) ?></td>
                <td class="py-2 px-4 border"><?= $row['stock'] ?></td>
                <td class="py-2 px-4 border">₱<?= number_format($row['price'], 2) ?></td>
                <td class="py-2 px-4 border text-center space-x-2">
                  <button onclick="viewProduct(<?= $row['id'] ?>)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">View</button>
                  <a href="edit_product.php?id=<?= $row['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-sm">Edit</a>
                  <a href="archive_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Archive this product?')" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 text-sm">Archive</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center py-4 text-gray-500">No products found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- ✅ Add Product Modal -->
  <div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
      <h3 class="text-2xl font-bold mb-4 text-yellow-600">Add New Product</h3>

      <form action="add_product.php" method="POST" class="space-y-4">
        <div>
          <label class="block font-medium">Product Name</label>
          <input type="text" name="product_name" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-yellow-300">
        </div>

        <div>
          <label class="block font-medium">Category</label>
          <select name="category" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-yellow-300 bg-white">
            <option value="">Select a category</option>
            <option value="Window Type">Window Type</option>
            <option value="Split Type">Split Type</option>
            <option value="Cassette Type">Cassette Type</option>
            <option value="Ceiling Mounted">Ceiling Mounted</option>
          </select>
        </div>

        <div>
          <label class="block font-medium">Brand</label>
          <input type="text" name="brand" class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-yellow-300">
        </div>

        <div>
          <label class="block font-medium">Description</label>
          <textarea name="description" rows="3" class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-yellow-300"></textarea>
        </div>

        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block font-medium">Stock</label>
            <input type="number" name="stock" min="0" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-yellow-300">
          </div>
          <div class="flex-1">
            <label class="block font-medium">Price (₱)</label>
            <input type="number" step="0.01" name="price" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-yellow-300">
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeAddModal()" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
          <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ✅ View Product Modal -->
  <div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
      <h3 class="text-2xl font-bold mb-4 text-yellow-600">Product Details</h3>
      <div id="viewContent" class="space-y-2 text-gray-700">Loading...</div>
      <div class="flex justify-end mt-4">
        <button onclick="closeViewModal()" class="bg-gray-400 px-4 py-2 rounded hover:bg-gray-500 text-white">Close</button>
      </div>
    </div>
  </div>

  <script>
    function openAddModal() { document.getElementById('addProductModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addProductModal').classList.add('hidden'); }
    function viewProduct(id) {
      fetch('view_product.php?id=' + id)
        .then(response => response.text())
        .then(data => {
          document.getElementById('viewContent').innerHTML = data;
          document.getElementById('viewModal').classList.remove('hidden');
        });
    }
    function closeViewModal() { document.getElementById('viewModal').classList.add('hidden'); }
  </script>
</body>
</html>
