<?php
// logout.php
session_start();

// ✅ Clear all session data
session_unset();
session_destroy();

// ✅ Redirect to homepage (outside folder)
header("Location: ../index.php");
exit;
?>
