<?php
include 'header.php';

$order_id = intval($_GET['id'] ?? 0);

$order = null;

if ($order_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM Orders_JE20936 WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
}
?>

<div class="container my-5">

    <?php if ($order): ?>

        <div class="card p-5 shadow-sm text-center">
            <h1>Order Placed Successfully!</h1>

            <p class="fs-5">
                Thank you, <?php echo htmlspecialchars($order['customer_name']); ?>.
                Your order has been saved.
            </p>

            <h2 class="h4">
                Order Number: #<?php echo $order['order_id']; ?>
            </h2>

            <p>
                Total: <strong>$<?php echo number_format($order['total'], 2); ?></strong>
            </p>

            <a href="home_xjolly1.php" class="btn btn-primary">
                Back to Home
            </a>
        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            Order not found.
        </div>

        <a href="index.php" class="btn btn-primary">Back to Home</a>

    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>