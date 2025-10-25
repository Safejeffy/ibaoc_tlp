<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "aircon_inventory";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

// ✅ Handle Registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $confirm_password = trim($_POST['confirm_password']);
  $role = $_POST['role']; // 👈 Keep role selector from form

  // ✅ Validation
  if ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    // Check for duplicate username/email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $error = "Username or email already exists.";
    } else {
      // ✅ Hash password
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // ✅ Insert new user
      $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

      if ($stmt->execute()) {
        // ✅ Log registration in activity log
        $log = $conn->prepare("
          INSERT INTO product_activity_log (action_type, performed_by, details)
          VALUES ('Register', ?, CONCAT('New user (', ?, ') registered successfully as ', ?))
        ");
        $log->bind_param("sss", $username, $username, $role);
        $log->execute();

        $success = "Registration successful! You can now log in.";
      } else {
        $error = "Registration failed. Please try again.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Aircon Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100 flex items-center justify-center min-h-screen">

  <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-blue-700 mb-6">Create Account</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php elseif (!empty($success)): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium">Username</label>
        <input type="text" name="username" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300">
      </div>

      <div>
        <label class="block text-gray-700 font-medium">Email</label>
        <input type="email" name="email" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300">
      </div>

      <div>
        <label class="block text-gray-700 font-medium">Password</label>
        <input type="password" name="password" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300">
      </div>

      <div>
        <label class="block text-gray-700 font-medium">Confirm Password</label>
        <input type="password" name="confirm_password" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300">
      </div>

      <!-- ✅ Role Selector -->
      <div>
        <label class="block text-gray-700 font-medium">Select Role</label>
        <select name="role" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300">
          <option value="">-- Choose Role --</option>
          <option value="staff">Staff 👷</option>
          <option value="admin">Admin 👑</option>
        </select>
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        Register
      </button>
    </form>

    <p class="text-center text-gray-600 mt-4">
      Already have an account?
      <a href="login.php" class="text-blue-600 hover:underline">Login</a>
    </p>
  </div>

</body>
</html>
