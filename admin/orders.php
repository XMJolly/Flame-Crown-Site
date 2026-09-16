<?php
include 'auth.php';
include 'header.php';

/* Table names */
$ordersTable = "Orders_JE20936";
$orderItemsTable = "OrderItems_JE20936";

$success = '';
$error = '';

/* Delete order */
if (isset($_GET['delete'])) {
    $order_id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM $ordersTable WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);

    if ($stmt->execute()) {
        header('Location: orders.php?deleted=1');
        exit;
    } else {
        $error = 'Order could not be deleted.';
    }
}

/* Update order status */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id'] ?? 0);
    $order_status = $_POST['order_status'] ?? 'New';

    $stmt = $conn->prepare("UPDATE $ordersTable SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $order_status, $order_id);

    if ($stmt->execute()) {
        header('Location: orders.php?updated=1');
        exit;
    } else {
        $error = 'Order status could not be updated.';
    }
}

/* Success messages */
if (isset($_GET['updated'])) {
    $success = 'Order status updated successfully.';
}

if (isset($_GET['deleted'])) {
    $success = 'Order deleted successfully.';
}

/* Search/filter */
$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$whereParts = array();
$params = array();
$types = '';

if ($statusFilter != '') {
    $whereParts[] = "order_status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

if ($search != '') {
    $whereParts[] = "(customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ? OR city LIKE ? OR order_id LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sssss';
}

$whereSql = '';

if (count($whereParts) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereParts);
}

/* Get orders */
$sql = "SELECT * FROM $ordersTable $whereSql ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if ($stmt && count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

if ($stmt) {
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    $orders = false;
    $error = 'Could not load orders. Make sure the orders table exists.';
}

/* If viewing one order */
$viewOrder = null;
$orderItems = null;

if (isset($_GET['view'])) {
    $view_id = intval($_GET['view']);

    $stmt = $conn->prepare("SELECT * FROM $ordersTable WHERE order_id = ?");
    $stmt->bind_param('i', $view_id);
    $stmt->execute();
    $viewOrder = $stmt->get_result()->fetch_assoc();

    $stmt = $conn->prepare("SELECT * FROM $orderItemsTable WHERE order_id = ?");
    $stmt->bind_param('i', $view_id);
    $stmt->execute();
    $orderItems = $stmt->get_result();
}
?>

<h1>Manage Orders</h1>

<p>
    Use this page to view customer orders, check order details, update order status, and delete test orders.
</p>

<?php if ($success != ''): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error != ''): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Search / Filter -->
<div class="card p-3 mb-4 shadow-sm">
    <form method="get" class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Search Orders</label>
            <input 
                type="text" 
                name="search" 
                class="form-control"
                placeholder="Search name, email, phone, city, or order ID"
                value="<?php echo htmlspecialchars($search); ?>"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Filter by Status</label>
            <select name="status" class="form-select">
                <option value="">All Orders</option>
                <option value="New" <?php echo ($statusFilter == 'New') ? 'selected' : ''; ?>>New</option>
                <option value="Preparing" <?php echo ($statusFilter == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                <option value="Ready" <?php echo ($statusFilter == 'Ready') ? 'selected' : ''; ?>>Ready</option>
                <option value="Completed" <?php echo ($statusFilter == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="Cancelled" <?php echo ($statusFilter == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit">
                Search
            </button>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <a href="orders.php" class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </form>
</div>

<?php if ($viewOrder): ?>

    <!-- Selected Order Detail -->
    <div class="card p-4 mb-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4">Order #<?php echo $viewOrder['order_id']; ?> Details</h2>

            <a href="orders.php" class="btn btn-secondary">
                Back to Orders
            </a>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <h3 class="h5">Customer Information</h3>

                <p><strong>Name:</strong> <?php echo htmlspecialchars($viewOrder['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($viewOrder['customer_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($viewOrder['customer_phone']); ?></p>
            </div>

            <div class="col-md-6">
                <h3 class="h5">Address</h3>

                <p>
                    <?php echo htmlspecialchars($viewOrder['street']); ?><br>
                    <?php echo htmlspecialchars($viewOrder['city']); ?>,
                    <?php echo htmlspecialchars($viewOrder['state']); ?>
                    <?php echo htmlspecialchars($viewOrder['zip']); ?>
                </p>

                <p><strong>Method:</strong> <?php echo htmlspecialchars($viewOrder['shipping_method']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($viewOrder['order_status']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($viewOrder['created_at']); ?></p>
            </div>
        </div>

        <h3 class="h5 mt-4">Items Ordered</h3>

        <table class="table table-bordered table-striped">
            <tr>
                <th>Product</th>
                <th>Option</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Line Total</th>
            </tr>

            <?php if ($orderItems && $orderItems->num_rows > 0): ?>
                <?php while ($item = $orderItems->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>

                        <td>
                            <?php
                            $option = $item['item_option'] ?? '';

                            if ($option != '') {
                                echo htmlspecialchars($option);
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>

                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['line_total'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">
                        No items found for this order.
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="row justify-content-end">
            <div class="col-md-4">
                <table class="table table-bordered">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-end">$<?php echo number_format($viewOrder['subtotal'], 2); ?></td>
                    </tr>

                    <tr>
                        <td>Discount</td>
                        <td class="text-end">$<?php echo number_format($viewOrder['discount'], 2); ?></td>
                    </tr>

                    <tr>
                        <td>Tax</td>
                        <td class="text-end">$<?php echo number_format($viewOrder['tax'], 2); ?></td>
                    </tr>

                    <tr>
                        <td>Shipping</td>
                        <td class="text-end">$<?php echo number_format($viewOrder['shipping'], 2); ?></td>
                    </tr>

                    <tr>
                        <th>Total</th>
                        <th class="text-end">$<?php echo number_format($viewOrder['total'], 2); ?></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

<?php endif; ?>

<!-- Orders Table -->
<h2 class="h4">Order List</h2>

<table class="table table-bordered table-striped align-middle">
    <tr class="table-dark">
        <th>ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Method</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>

    <?php if ($orders && $orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $order['order_id']; ?></td>

                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>

                <td>
                    <a href="mailto:<?php echo htmlspecialchars($order['customer_email']); ?>">
                        <?php echo htmlspecialchars($order['customer_email']); ?>
                    </a>
                </td>

                <td><?php echo htmlspecialchars($order['shipping_method']); ?></td>

                <td>$<?php echo number_format($order['total'], 2); ?></td>

                <td>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        <input type="hidden" name="update_status" value="1">

                        <select name="order_status" class="form-select form-select-sm">
                            <option value="New" <?php echo ($order['order_status'] == 'New') ? 'selected' : ''; ?>>New</option>
                            <option value="Preparing" <?php echo ($order['order_status'] == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                            <option value="Ready" <?php echo ($order['order_status'] == 'Ready') ? 'selected' : ''; ?>>Ready</option>
                            <option value="Completed" <?php echo ($order['order_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo ($order['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>

                        <button class="btn btn-sm btn-primary" type="submit">
                            Save
                        </button>
                    </form>
                </td>

                <td><?php echo htmlspecialchars($order['created_at']); ?></td>

                <td>
                    <a 
                        href="orders.php?view=<?php echo $order['order_id']; ?>" 
                        class="btn btn-sm btn-info"
                    >
                        View
                    </a>

                    <a 
                        href="orders.php?delete=<?php echo $order['order_id']; ?>" 
                        onclick="return confirmDelete()" 
                        class="btn btn-sm btn-danger"
                    >
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="8" class="text-center">
                No orders found.
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php include 'footer.php'; ?>