<?php
require_once "session_check.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p>Login successful. Your session is active.</p>
    <a href="logout.php">Logout</a>
</body>
</html>