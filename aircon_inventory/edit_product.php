<?php
session_start();

// ✅ Allow both admin and staff
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
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
$product = null;

if ($id) {
  $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $product = $stmt->get_result()->fetch_assoc();
}

if (!$product) {
  header("Location: " . ($_SESSION['role'] === 'admin' ? 'dashboard.php' : 'staff_dashboard.php'));
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $product_name = trim($_POST['product_name']);
  $category = trim($_POST['category']);
  $brand = trim($_POST['brand']);
  $description = trim($_POST['description']);
  $stock = (int)$_POST['stock'];
  $price = (float)$_POST['price'];

  $stmt = $conn->prepare("UPDATE products SET product_name=?, category=?, brand=?, description=?, stock=?, price=? WHERE id=?");
  $stmt->bind_param("ssssidi", $product_name, $category, $brand, $description, $stock, $price, $id);
  $stmt->execute();

  // ✅ Log the "Edit" action
  $log = $conn->prepare("INSERT INTO product_activity_log (product_id, product_name, action_type, performed_by) VALUES (?, ?, 'Edit', ?)");
  $log->bind_param("iss", $id, $product_name, $_SESSION['username']);
  $log->execute();

  if ($_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
  } else {
    header("Location: staff_dashboard.php");
  }
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
  </head>
  <body class="bg-gray-100 flex min-h-screen">

    <!-- Sidebar (uses role to switch styles) -->
    <?php if ($_SESSION['role'] === 'admin'): ?>
      <aside class="w-64 bg-blue-800 text-white min-h-screen p-5 flex flex-col justify-between">
        <div>
          <h2 class="text-2xl font-bold mb-8">❄️ Admin Panel</h2>
          <nav class="space-y-3">
            <a href="dashboard.php" class="block bg-blue-700 px-3 py-2 rounded">Dashboard</a>
            <button onclick="openAddModal()" class="w-full bg-green-600 py-2 rounded hover:bg-green-700 mt-3">Add Product</button>
            <a href="archived_products.php" class="block px-3 py-2 rounded hover:bg-blue-600">Archived Products</a>
            <a href="activity_log.php" class="block px-3 py-2 rounded hover:bg-blue-600">Activity Log</a>
          </nav>
        </div>
        <a href="logout.php" class="block px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-center">Logout</a>
      </aside>
    <?php else: ?>
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
    <?php endif; ?>

    <!-- Main content -->
    <main class="flex-1 p-8 overflow-x-auto">
      <div class="max-w-3xl mx-auto bg-white shadow rounded p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Edit Product</h2>

        <form method="post" action="edit_product.php?id=<?php echo (int)$id; ?>" class="space-y-4">
          <div>
            <label for="product_name" class="block font-medium">Product Name</label>
            <input type="text" id="product_name" name="product_name" required value="<?php echo htmlspecialchars($product['product_name'] ?? '', ENT_QUOTES); ?>" class="w-full border rounded px-3 py-2 mt-1">
          </div>

          <div>
            <label for="category" class="block font-medium">Category</label>
            <select id="category" name="category" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300 bg-white">
              <option value="">Select a category</option>
              <?php
                $categories = ["Window Type", "Split Type", "Cassette Type", "Ceiling Mounted"];
                foreach($categories as $cat) {
                  $selected = ($product['category'] === $cat) ? 'selected' : '';
                  echo "<option value=\"" . htmlspecialchars($cat) . "\" $selected>" . htmlspecialchars($cat) . "</option>";
                }
              ?>
            </select>
          </div>

          <div>
            <label for="brand" class="block font-medium">Brand</label>
            <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand'] ?? '', ENT_QUOTES); ?>" class="w-full border rounded px-3 py-2 mt-1">
          </div>

          <div>
            <label for="description" class="block font-medium">Description</label>
            <textarea id="description" name="description" rows="4" class="w-full border rounded px-3 py-2 mt-1"><?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="stock" class="block font-medium">Stock</label>
              <input type="number" id="stock" name="stock" min="0" value="<?php echo (int)($product['stock'] ?? 0); ?>" class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
              <label for="price" class="block font-medium">Price (₱)</label>
              <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price'] ?? '0.00', ENT_QUOTES); ?>" class="w-full border rounded px-3 py-2 mt-1">
            </div>
          </div>

          <div class="flex items-center space-x-3 mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Product</button>
            <?php $back = ($_SESSION['role'] === 'admin') ? 'dashboard.php' : 'staff_dashboard.php'; ?>
            <a href="<?php echo $back; ?>" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</a>
          </div>
        </form>
      </div>
    </main>

    <script>
      function openAddModal() { try { document.getElementById('addProductModal').classList.remove('hidden'); } catch(e){} }
    </script>
  </body>
</html>
