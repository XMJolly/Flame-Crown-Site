<?php
session_start();

include '../config/dbcon.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username == '' || $password == '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare(
            "SELECT * FROM AdminUsers_JE20936 WHERE username = ?"
        );

        $stmt->bind_param('s', $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['admin_name'] = $user['full_name'];

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flame Crown Grill Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Admin Login</h3>
                </div>

                <div class="card-body">

                    <?php if ($error != ''): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input 
                                type="text" 
                                name="username" 
                                class="form-control" 
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input 
                                type="password" 
                                name="password" 
                                class="form-control" 
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                    <p class="mt-3 small text-muted">
                        Default login: admin / admin123
                    </p>

                    <p class="mt-2">
                        <a href="../public/index.php">Back to Public Site</a>
                    </p>

                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>