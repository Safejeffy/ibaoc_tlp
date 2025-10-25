<?php
// login.php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "aircon_inventory";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$error = "";

// ✅ Handle logout directly via GET parameter
if (isset($_GET['logout']) && isset($_SESSION['username'])) {
  $logout_user = $_SESSION['username'];

  // Log logout
  $log = $conn->prepare("
    INSERT INTO product_activity_log (action_type, performed_by, details)
    VALUES ('Logout', ?, 'User logged out successfully')
  ");
  $log->bind_param("s", $logout_user);
  $log->execute();

  // Destroy session and redirect
  session_unset();
  session_destroy();
  header("Location: login.php");
  exit;
}

// ✅ Redirect if already logged in
if (isset($_SESSION['role'])) {
  if ($_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
    exit;
  } elseif ($_SESSION['role'] === 'staff') {
    header("Location: staff_dashboard.php");
    exit;
  }
}

// ✅ Handle login submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
      // ✅ Valid credentials
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['username'] = $user['username'];

      // Log successful login
      $log = $conn->prepare("
        INSERT INTO product_activity_log (action_type, performed_by, details)
        VALUES ('Login', ?, 'Successful login')
      ");
      $log->bind_param("s", $_SESSION['username']);
      $log->execute();

      // Redirect based on role
      if ($user['role'] === 'admin') {
        header("Location: dashboard.php");
        exit;
      } elseif ($user['role'] === 'staff') {
        header("Location: staff_dashboard.php");
        exit;
      }
    } else {
      // ❌ Wrong password
      $error = "Incorrect password.";

      $log = $conn->prepare("
        INSERT INTO product_activity_log (action_type, performed_by, details)
        VALUES ('Failed Login', ?, 'Incorrect password')
      ");
      $log->bind_param("s", $username);
      $log->execute();
    }
  } else {
    // ❌ Username not found
    $error = "User not found.";

    $log = $conn->prepare("
      INSERT INTO product_activity_log (action_type, performed_by, details)
      VALUES ('Failed Login', ?, 'Username does not exist')
    ");
    $log->bind_param("s", $username);
    $log->execute();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Aircon Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100 flex items-center justify-center min-h-screen">

  <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-blue-700 mb-6">Aircon Inventory Login</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium">Username</label>
        <input type="text" name="username" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300">
      </div>

      <div>
        <label class="block text-gray-700 font-medium">Password</label>
        <input type="password" name="password" required class="w-full border rounded px-3 py-2 mt-1 focus:ring focus:ring-blue-300">
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        Login
      </button>
    </form>

    <p class="text-center text-gray-600 mt-4">
      Don’t have an account?
      <a href="register.php" class="text-blue-600 hover:underline">Register</a>
    </p>

    <?php if (isset($_SESSION['username'])): ?>
      <div class="text-center mt-6">
        <a href="login.php?logout=1" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
