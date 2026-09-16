<?php include 'header.php'; ?>

<div class="container my-5">

    <!-- Hero Section -->
    <div class="p-5 mb-5 bg-light rounded-3 shadow-sm">
        <div class="container-fluid py-4">
            <h1 class="display-5 fw-bold">Welcome to Flame Crown Grill</h1>
            <p class="fs-5">
                Enjoy flame-grilled burgers, crispy chicken sandwiches, golden fries,
                refreshing drinks, and combo meals made fresh for every order.
            </p>
            <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
            <a href="search.php?q=burger" class="btn btn-warning btn-lg ms-2">View Burgers</a>
        </div>
    </div>

    <!-- Category Section -->
    <h2 class="mb-4">Menu Categories</h2>

    <div class="row g-4 mb-5">
        <?php
        $categoryQuery = "SELECT * FROM Prj_SP2026_categories 
                          WHERE status='Active' 
                          ORDER BY category_name";

        $categoryResult = $conn->query($categoryQuery);

        if ($categoryResult && $categoryResult->num_rows > 0):
            while ($cat = $categoryResult->fetch_assoc()):
        ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars($cat['category_description']); ?>
                            </p>

                            <a class="btn btn-outline-primary"
                               href="category.php?id=<?php echo $cat['category_id']; ?>">
                                View Items
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            endwhile;
        else:
        ?>
            <div class="col-12">
                <div class="alert alert-warning">
                    No categories are available right now.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Featured Products Section -->
    <h2 class="mb-4">Featured Menu Items</h2>

    <div class="row g-4">
        <?php
        $productQuery = "SELECT p.*, c.category_name 
                         FROM Prj_SP2026_products p
                         JOIN Prj_SP2026_categories c 
                         ON p.category_id = c.category_id
                         WHERE p.status='Active'
                         ORDER BY p.created_at DESC";

        $productResult = $conn->query($productQuery);

        if ($productResult && $productResult->num_rows > 0):
            while ($row = $productResult->fetch_assoc()):
                $imageName = $row['image_name'];

                if ($imageName == '') {
                    $imageName = 'placeholder.jpg';
                }
        ?>
                <div class="col-md-4">
                    <div class="card product-card h-100 shadow-sm">

                        <img src="../assets/images/<?php echo htmlspecialchars($imageName); ?>"
                             class="card-img-top"
                             alt="<?php echo htmlspecialchars($row['product_name']); ?>">

                        <div class="card-body">
                            <h3 class="card-title h5">
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </h3>

                            <p class="text-muted">
                                <?php echo htmlspecialchars($row['category_name']); ?>
                            </p>

                            <p>
                                <?php echo substr(htmlspecialchars($row['product_description']), 0, 100); ?>...
                            </p>

                            <strong>
                                $<?php echo number_format($row['price'], 2); ?>
                            </strong>
                        </div>

                        <div class="card-footer bg-white">
    <div class="d-flex gap-2">
        <a 
            href="product_detail.php?id=<?php echo $row['product_id']; ?>" 
            class="btn btn-outline-primary w-50"
        >
            Details
        </a>

        <form method="post" action="add_to_cart.php" class="w-50">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            <input type="hidden" name="quantity" value="1">

            <button type="submit" class="btn btn-success w-100">
                Add to Cart
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>