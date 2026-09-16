<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/dbcon.php';

/* Get site color settings */
$settingsResult = $conn->query("SELECT * FROM SiteSettings_JE20936 LIMIT 1");
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;

$h1Color = $settings['h1_color'] ?? '#8B0000';
$h2Color = $settings['h2_color'] ?? '#D35400';
$h3Color = $settings['h3_color'] ?? '#5A2D0C';
$pColor = $settings['p_color'] ?? '#333333';
$headerColor = $settings['header_color'] ?? '#8B0000';
$bodyColor = $settings['body_color'] ?? '#FFF8E7';
$footerColor = $settings['footer_color'] ?? '#5A2D0C';

/* Get active categories for dropdown */
$categories = $conn->query("SELECT * FROM Categories_JE20936 WHERE status='Active' ORDER BY category_name");

$searchValue = $_GET['q'] ?? '';

/* Cart setup */
$cart = $_SESSION['cart'] ?? array();
$cartOptions = $_SESSION['cart_options'] ?? array();

$cartCount = 0;
$cartTotal = 0;
$cartItems = array();

foreach ($cart as $qty) {
    $cartCount += intval($qty);
}

if (!empty($cart)) {
    $ids = array_map('intval', array_keys($cart));
    $idList = implode(',', $ids);

    if ($idList != '') {
        $cartResult = $conn->query("SELECT * FROM Products_JE20936 WHERE product_id IN ($idList)");

        if ($cartResult) {
            while ($item = $cartResult->fetch_assoc()) {
                $pid = intval($item['product_id']);
                $qty = intval($cart[$pid]);
                $lineTotal = $qty * floatval($item['price']);

                $cartTotal += $lineTotal;

                $item['quantity'] = $qty;
                $item['line_total'] = $lineTotal;
                $item['item_option'] = $cartOptions[$pid] ?? '';

                $cartItems[] = $item;
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
    <title>Flame Crown Grill</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="icon" type="image/jpeg" href="../assets/images/flame_crown_logo.jpg">
    <link rel="apple-touch-icon" href="../assets/images/flame_crown_logo.jpg">

    <style>
        body {
            background-color: <?php echo htmlspecialchars($bodyColor); ?>;
        }

        h1 {
            color: <?php echo htmlspecialchars($h1Color); ?>;
        }

        h2 {
            color: <?php echo htmlspecialchars($h2Color); ?>;
        }

        h3 {
            color: <?php echo htmlspecialchars($h3Color); ?>;
        }

        p {
            color: <?php echo htmlspecialchars($pColor); ?>;
        }

        .site-header {
            background-color: <?php echo htmlspecialchars($headerColor); ?>;
        }

        .site-footer {
            background-color: <?php echo htmlspecialchars($footerColor); ?>;
        }

        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
        }

        .site-logo {
            width: 45px !important;
            height: 45px !important;
            max-width: 45px !important;
            max-height: 45px !important;
            object-fit: contain;
            background-color: white;
            border-radius: 50%;
            padding: 3px;
        }

        .btn-primary {
            background-color: #8B0000;
            border-color: #8B0000;
        }

        .btn-primary:hover {
            background-color: #5A2D0C;
            border-color: #5A2D0C;
        }

        .btn-warning {
            background-color: #FFC72C;
            border-color: #FFC72C;
            color: #5A2D0C;
            font-weight: bold;
        }

        .cart-img {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-count-badge {
            font-size: 12px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark site-header">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="home_xjolly1.php">
            <img 
                src="../assets/images/flame_crown_logo.jpg" 
                alt="Flame Crown Grill Logo" 
                class="site-logo me-2"
            >
            <span>Flame Crown Grill</span>
        </a>

        <button 
            class="navbar-toggler" 
            type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#navbarNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="home_xjolly1.php">Home</a>
                </li>

                <li class="nav-item dropdown">
                    <a 
                        class="nav-link dropdown-toggle" 
                        href="#" 
                        role="button" 
                        data-bs-toggle="dropdown"
                    >
                        Menu
                    </a>

                    <ul class="dropdown-menu">
                        <?php if ($categories && $categories->num_rows > 0): ?>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <li>
                                    <a 
                                        class="dropdown-item" 
                                        href="category.php?id=<?php echo $cat['category_id']; ?>"
                                    >
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>
                                <span class="dropdown-item text-muted">
                                    No categories available
                                </span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="search.php?q=burger">Burgers</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="search.php?q=combo">Combos</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>

            </ul>

            <form class="d-flex me-2" method="get" action="search.php">
                <input 
                    class="form-control me-2" 
                    type="search" 
                    name="q" 
                    placeholder="Search menu"
                    value="<?php echo htmlspecialchars($searchValue); ?>"
                    required
                >

                <button class="btn btn-warning" type="submit">
                    Search
                </button>
            </form>

            <button 
                class="btn btn-light position-relative" 
                type="button" 
                data-bs-toggle="offcanvas" 
                data-bs-target="#cartSidebar"
            >
                Cart

                <?php if ($cartCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count-badge">
                        <?php echo $cartCount; ?>
                    </span>
                <?php endif; ?>
            </button>

        </div>
    </div>
</nav>

<!-- Cart Slider -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar">

    <div class="offcanvas-header">
        <h2 class="offcanvas-title h4">Your Cart</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">

        <?php if (isset($_SESSION['cart_message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['cart_message']); ?>
            </div>
            <?php unset($_SESSION['cart_message']); ?>
        <?php endif; ?>

        <?php if (!empty($cartItems)): ?>

            <?php foreach ($cartItems as $item): ?>

                <?php
                $imageName = $item['image_name'];

                if ($imageName == '') {
                    $imageName = 'placeholder.jpg';
                }

                $itemOption = $item['item_option'] ?? '';
                ?>

                <div class="d-flex mb-3 border-bottom pb-3">

                    <img 
                        src="../assets/images/<?php echo htmlspecialchars($imageName); ?>" 
                        class="cart-img me-3"
                        alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                    >

                    <div class="flex-grow-1">

                        <h3 class="h6 mb-1">
                            <?php echo htmlspecialchars($item['product_name']); ?>
                        </h3>

                        <?php if ($itemOption != ''): ?>
                            <p class="mb-1 small text-muted">
                                Drink: <?php echo htmlspecialchars($itemOption); ?>
                            </p>
                        <?php endif; ?>

                        <p class="mb-1">
                            Qty: <?php echo $item['quantity']; ?>
                        </p>

                        <p class="mb-1">
                            $<?php echo number_format($item['line_total'], 2); ?>
                        </p>

                        <a 
                            href="remove_from_cart.php?id=<?php echo $item['product_id']; ?>" 
                            class="small text-danger"
                        >
                            Remove
                        </a>

                    </div>
                </div>

            <?php endforeach; ?>

            <div class="d-flex justify-content-between">
                <strong>Subtotal:</strong>
                <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
            </div>

            <div class="mt-3">
                <a href="checkout.php" class="btn btn-warning w-100 mb-2">
                    Checkout
                </a>

                <a href="clear_cart.php" class="btn btn-outline-danger w-100">
                    Clear Cart
                </a>
            </div>

        <?php else: ?>

            <div class="alert alert-info">
                Your cart is empty.
            </div>

            <a href="home_xjolly1.php" class="btn btn-primary w-100">
                Continue Shopping
            </a>

        <?php endif; ?>

    </div>
</div>