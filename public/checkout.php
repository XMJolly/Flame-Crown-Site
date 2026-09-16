<?php
include 'header.php';

$cart = $_SESSION['cart'] ?? array();
$cartItems = array();
$subtotal = 0;
$error = '';

if (!empty($cart)) {
    $ids = array_map('intval', array_keys($cart));
    $idList = implode(',', $ids);

    if ($idList != '') {
        $result = $conn->query("SELECT * FROM Prj_SP2026_products WHERE product_id IN ($idList)");

        if ($result) {
            while ($item = $result->fetch_assoc()) {
                $pid = $item['product_id'];
                $qty = intval($cart[$pid]);
                $lineTotal = $qty * floatval($item['price']);

                $item['quantity'] = $qty;
                $item['line_total'] = $lineTotal;

                $subtotal += $lineTotal;
                $cartItems[] = $item;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($cartItems)) {
        $error = 'Your cart is empty.';
    } else {
        $customer_name = trim($_POST['customer_name'] ?? '');
        $customer_email = trim($_POST['customer_email'] ?? '');
        $customer_phone = trim($_POST['customer_phone'] ?? '');
        $street = trim($_POST['street'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $zip = trim($_POST['zip'] ?? '');
        $shipping_method = $_POST['shipping_method'] ?? 'Pickup';
        $coupon_code = trim($_POST['coupon_code'] ?? '');

        if ($customer_name == '' || $customer_email == '' || $street == '' || $city == '' || $state == '' || $zip == '') {
            $error = 'Please fill out all required fields.';
        } else {
            $shipping = 0.00;

            if ($shipping_method == 'Delivery') {
                $shipping = 5.99;
            }

            $discount = 0.00;

            if (strtoupper($coupon_code) == 'SAVE10') {
                $discount = $subtotal * 0.10;
            }

            $taxableAmount = $subtotal - $discount;
            $tax = $taxableAmount * 0.065;
            $total = $taxableAmount + $tax + $shipping;

            $stmt = $conn->prepare(
                "INSERT INTO Prj_SP2026_orders
                (customer_name, customer_email, customer_phone, street, city, state, zip,
                 shipping_method, coupon_code, subtotal, discount, tax, shipping, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                'sssssssssddddd',
                $customer_name,
                $customer_email,
                $customer_phone,
                $street,
                $city,
                $state,
                $zip,
                $shipping_method,
                $coupon_code,
                $subtotal,
                $discount,
                $tax,
                $shipping,
                $total
            );

            if ($stmt->execute()) {
                $order_id = $conn->insert_id;

                foreach ($cartItems as $item) {
                    $product_id = intval($item['product_id']);
                    $product_name = $item['product_name'];
                    $price = floatval($item['price']);
                    $quantity = intval($item['quantity']);
                    $line_total = floatval($item['line_total']);

                    $itemStmt = $conn->prepare(
                        "INSERT INTO Prj_SP2026_order_items
                        (order_id, product_id, product_name, price, quantity, line_total)
                        VALUES (?, ?, ?, ?, ?, ?)"
                    );

                    $itemStmt->bind_param(
                        'iisddd',
                        $order_id,
                        $product_id,
                        $product_name,
                        $price,
                        $quantity,
                        $line_total
                    );

                    $itemStmt->execute();
                }

                unset($_SESSION['cart']);

                header("Location: order_success.php?id=" . $order_id);
                exit;
            } else {
                $error = 'Order could not be placed. Please try again.';
            }
        }
    }
}

$shippingPreview = 0.00;
$taxPreview = $subtotal * 0.065;
$totalPreview = $subtotal + $taxPreview + $shippingPreview;
?>

<div class="container my-5">

    <h1>Checkout</h1>

    <?php if ($error != ''): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>

        <div class="alert alert-info">
            Your cart is empty.
        </div>

        <a href="index.php" class="btn btn-primary">Continue Shopping</a>

    <?php else: ?>

        <form method="post">

            <div class="card p-4 shadow-sm mb-4">
                <h2 class="h4">Customer Information</h2>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input name="customer_name" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input name="customer_phone" class="form-control">
                    </div>
                </div>
            </div>

            <div class="card p-4 shadow-sm mb-4">
                <h2 class="h4">Address</h2>

                <div class="mb-3">
                    <label class="form-label">Street</label>
                    <input name="street" class="form-control" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input name="city" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input name="state" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ZIP</label>
                        <input name="zip" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="card p-4 shadow-sm mb-4">
                <h2 class="h4">Shipping Method</h2>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="shipping_method" value="Pickup" checked>
                    <label class="form-check-label">Pickup ($0.00)</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="shipping_method" value="Delivery">
                    <label class="form-check-label">Delivery ($5.99)</label>
                </div>
            </div>

            <div class="card p-4 shadow-sm mb-4">
                <h2 class="h4">Coupon</h2>

                <div class="input-group">
                    <input name="coupon_code" class="form-control" placeholder="Enter coupon code">
                    <span class="input-group-text">Try SAVE10</span>
                </div>
            </div>

            <div class="card p-4 shadow-sm mb-4">
                <h2 class="h4">Order Summary</h2>

                <table class="table table-bordered">
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($item['product_name']); ?>
                                x <?php echo $item['quantity']; ?>
                            </td>
                            <td class="text-end">
                                $<?php echo number_format($item['line_total'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td>Subtotal</td>
                        <td class="text-end">$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>

                    <tr>
                        <td>Tax Estimate (6.50%)</td>
                        <td class="text-end">$<?php echo number_format($taxPreview, 2); ?></td>
                    </tr>

                    <tr>
                        <td>Shipping</td>`
                        <td class="text-end">$<?php echo number_format($shippingPreview, 2); ?></td>
                    </tr>

                    <tr>
                        <th>Estimated Total</th>
                        <th class="text-end">$<?php echo number_format($totalPreview, 2); ?></th>
                    </tr>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-primary btn-lg">
                    Continue Shopping
                </a>

                <button type="submit" class="btn btn-warning btn-lg">
                    Place Order
                </button>
            </div>

        </form>

    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>