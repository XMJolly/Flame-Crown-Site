<?php
include 'header.php';

$id = intval($_GET['id'] ?? 0);

$category = null;

if ($id > 0) {
    $stmt = $conn->prepare(
        "SELECT * FROM Categories_JE20936 
         WHERE category_id = ? AND status = 'Active'"
    );

    $stmt->bind_param('i', $id);
    $stmt->execute();

    $category = $stmt->get_result()->fetch_assoc();
}
?>

<div class="container my-5">

    <?php if ($category): ?>

        <h1><?php echo htmlspecialchars($category['category_name']); ?></h1>

        <p class="fs-5">
            <?php echo htmlspecialchars($category['category_description']); ?>
        </p>

        <div class="row g-4 mt-3">

            <?php
            $stmt = $conn->prepare(
                "SELECT * FROM Products_JE20936 
                 WHERE status = 'Active' AND category_id = ?
                 ORDER BY product_name"
            );

            $stmt->bind_param('i', $id);
            $stmt->execute();

            $products = $stmt->get_result();

            if ($products && $products->num_rows > 0):
                while ($row = $products->fetch_assoc()):

                    $imageName = $row['image_name'];

                    if ($imageName == '') {
                        $imageName = 'placeholder.jpg';
                    }
            ?>

                    <div class="col-md-4">
                        <div class="card product-card h-100 shadow-sm">

                            <img 
                                src="../assets/images/<?php echo htmlspecialchars($imageName); ?>" 
                                class="card-img-top" 
                                alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                            >

                            <div class="card-body">

                                <h3 class="h5">
                                    <?php echo htmlspecialchars($row['product_name']); ?>
                                </h3>

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
                                        <input 
                                            type="hidden" 
                                            name="product_id" 
                                            value="<?php echo $row['product_id']; ?>"
                                        >

                                        <input 
                                            type="hidden" 
                                            name="quantity" 
                                            value="1"
                                        >

                                        <button type="submit" class="btn btn-success w-100">
                                            Add to Cart
                                        </button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>

            <?php
                endwhile;
            else:
            ?>

                <div class="col-12">
                    <div class="alert alert-warning">
                        No menu items are available in this category right now.
                    </div>
                </div>

            <?php endif; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            Category not found.
        </div>

        <a href="home_xjolly1.php" class="btn btn-primary">
            Back to Home
        </a>

    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>