<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['role']) || !isset($_SESSION['username'])) {
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

// Get product ID
$id = $_GET['id'] ?? 0;

if ($id) {
    // Fetch product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        // Insert into archived_products (archived_at will default to NOW())
        $insert = $conn->prepare("
            INSERT INTO archived_products 
            (product_id, product_name, category, brand, description, stock, price, archived_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->bind_param(
            "issssids",
            $product['id'],
            $product['product_name'],
            $product['category'],
            $product['brand'],
            $product['description'],
            $product['stock'],
            $product['price'],
            $_SESSION['username']
        );
        $insert->execute();

        // Log activity
        $log = $conn->prepare("
            INSERT INTO product_activity_log 
            (product_id, product_name, action_type, performed_by, details)
            VALUES (?, ?, 'Archive', ?, 'Product archived successfully.')
        ");
        $log->bind_param("iss", $product['id'], $product['product_name'], $_SESSION['username']);
        $log->execute();

        // Delete original product AFTER archiving
        $delete = $conn->prepare("DELETE FROM products WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();

        $_SESSION['message'] = "✅ Product '{$product['product_name']}' archived successfully!";
    } else {
        $_SESSION['message'] = "⚠️ Product not found.";
    }
} else {
    $_SESSION['message'] = "⚠️ Invalid product ID.";
}

// Redirect back to dashboard
header("Location: " . ($_SESSION['role'] === 'admin' ? 'dashboard.php' : 'staff_dashboard.php'));
exit;
