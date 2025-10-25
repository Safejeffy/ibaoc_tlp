<?php
session_start();
if (!isset($_SESSION['role'])) {
  header("Location: login.php");
  exit;
}

$conn = new mysqli("localhost", "root", "", "aircon_inventory");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ✅ Fetch all activity logs
$result = $conn->query("SELECT * FROM product_activity_log ORDER BY performed_at DESC");

$isAdmin = $_SESSION['role'] === 'admin';
$themeColor = $isAdmin ? 'blue' : 'yellow';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Log - Aircon Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">

  <!-- ✅ Sidebar -->
  <aside class="w-64 <?= $isAdmin ? 'bg-blue-800' : 'bg-yellow-500' ?> text-white min-h-screen p-5 flex flex-col justify-between">
    <div>
      <h2 class="text-2xl font-bold mb-8">
        <?= $isAdmin ? '❄️ Admin Panel' : '👷 Staff Panel' ?>
      </h2>
      <nav class="space-y-3">
        <a href="<?= $isAdmin ? 'dashboard.php' : 'staff_dashboard.php' ?>" class="block <?= $isAdmin ? 'bg-blue-700 hover:bg-blue-600' : 'bg-yellow-400 hover:bg-yellow-300' ?> px-3 py-2 rounded">Dashboard</a>
        <a href="archived_products.php" class="block px-3 py-2 rounded hover:bg-<?= $themeColor ?>-600">Archived Products</a>
        <a href="activity_log.php" class="block px-3 py-2 rounded bg-gray-700">Activity Log</a>
      </nav>
    </div>
    <a href="login.php?logout=1" class="block px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-center">Logout</a>
  </aside>

  <!-- ✅ Main Content -->
  <main class="flex-1 p-8 overflow-x-auto">
    <h1 class="text-3xl font-bold text-<?= $themeColor ?>-700 mb-6">🧾 System Activity Log</h1>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
      <table class="w-full border-collapse">
        <thead class="bg-gray-200">
          <tr>
            <th class="py-3 px-4 border">ID</th>
            <th class="py-3 px-4 border">Product</th>
            <th class="py-3 px-4 border">Action Type</th>
            <th class="py-3 px-4 border">Performed By</th>
            <th class="py-3 px-4 border">Details</th>
            <th class="py-3 px-4 border">Date & Time</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <?php
                // ✅ Determine color and icon by action type
                $colorClass = match($row['action_type']) {
                  'Add' => 'text-green-600 font-semibold',
                  'Edit' => 'text-yellow-600 font-semibold',
                  'Archive' => 'text-gray-600 font-semibold',
                  'Restore' => 'text-blue-600 font-semibold',
                  'Delete' => 'text-red-600 font-semibold',
                  'Login' => 'text-green-700 font-bold',
                  'Logout' => 'text-red-700 font-bold',
                  'Failed Login' => 'text-red-500 font-bold',
                  default => 'text-black'
                };

                $icon = match($row['action_type']) {
                  'Add' => '➕',
                  'Edit' => '✏️',
                  'Archive' => '📦',
                  'Restore' => '♻️',
                  'Delete' => '🗑️',
                  'Login' => '✅',
                  'Logout' => '🚪',
                  'Failed Login' => '❌',
                  default => '🔹'
                };
              ?>
              <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2 text-gray-600"><?= $row['id'] ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($row['product_name'] ?? '—') ?></td>
                <td class="border px-4 py-2 <?= $colorClass ?>"><?= $icon ?> <?= htmlspecialchars($row['action_type']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($row['performed_by'] ?? 'System') ?></td>
                <td class="border px-4 py-2 text-gray-700"><?= htmlspecialchars($row['details'] ?? '—') ?></td>
                <td class="border px-4 py-2 text-gray-500"><?= $row['performed_at'] ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-4 text-gray-500">No activity logs found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
