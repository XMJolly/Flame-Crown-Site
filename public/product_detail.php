<?php
include 'header.php';

$id = intval($_GET['id'] ?? 0);
$product = null;

/* Get selected product */
if ($id > 0) {
    $stmt = $conn->prepare(
        "SELECT p.*, c.category_name
         FROM Products_JE20936 p
         JOIN Categories_JE20936 c
         ON p.category_id = c.category_id
         WHERE p.product_id = ? AND p.status = 'Active'"
    );

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}
?>

<div class="container my-5">

    <?php if ($product): ?>

        <?php
        $imageName = $product['image_name'];

        if ($imageName == '') {
            $imageName = 'placeholder.jpg';
        }

        /* Decide if this item should show a drink choice menu */
        $isDrinkChoiceItem = false;

        $productNameLower = strtolower($product['product_name']);
        $categoryNameLower = strtolower($product['category_name']);

        if (
            $categoryNameLower == 'drinks' ||
            $categoryNameLower == 'combos' ||
            strpos($productNameLower, 'drink') !== false ||
            strpos($productNameLower, 'combo') !== false ||
            strpos($productNameLower, 'fountain') !== false ||
            strpos($productNameLower, 'meal') !== false
        ) {
            $isDrinkChoiceItem = true;
        }
        ?>

        <div class="row g-4">

            <!-- Product Image -->
            <div class="col-md-6">
                <img 
                    class="img-fluid rounded shadow detail-img"
                    src="../assets/images/<?php echo htmlspecialchars($imageName); ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                >
            </div>

            <!-- Product Info -->
            <div class="col-md-6">

                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>

                <h2 class="h5 text-muted">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </h2>

                <p class="fs-5 mt-3">
                    <?php echo nl2br(htmlspecialchars($product['product_description'])); ?>
                </p>

                <h3 class="mt-3">
                    $<?php echo number_format($product['price'], 2); ?>
                </h3>

                <!-- Add to Cart Form -->
                <form method="post" action="add_to_cart.php" class="mt-4">

                    <input 
                        type="hidden" 
                        name="product_id" 
                        value="<?php echo $product['product_id']; ?>"
                    >

                    <?php if ($isDrinkChoiceItem): ?>
                        <div class="mb-3">
                            <label class="form-label">Choose Your Drink</label>

                            <select name="drink_choice" class="form-select" required>
                                <option value="">Select a drink</option>
                                <option value="Sprite">Sprite</option>
                                <option value="Dr Pepper">Dr Pepper</option>
                                <option value="Coca-Cola">Coca-Cola</option>
                                <option value="Diet Coke">Diet Coke</option>
				<option value="Strawberry Lemonade">Strawberry Lemonade</option>
                                <option value="Fanta Orange">Fanta Orange</option>
                                <option value="Sweet Tea">Sweet Tea</option>
                                <option value="Lemonade">Lemonade</option>
                                <option value="Water">Water</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="row g-2 align-items-end">

                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input 
                                type="number" 
                                name="quantity" 
                                class="form-control" 
                                value="1" 
                                min="1"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                Add to Cart
                            </button>
                        </div>

                        <div class="col-md-4">
                            <a href="home_xjolly1.php" class="btn btn-warning btn-lg w-100">
                                Back to Menu
                            </a>
                        </div>

                    </div>

                </form>

                <div class="mt-4">
                    <a href="contact.php" class="btn btn-outline-primary">
                        Ask About This Item
                    </a>
                </div>

            </div>

        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            Product not found.
        </div>

        <a href="home_xjolly1.php" class="btn btn-primary">
            Back to Home
        </a>

    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>