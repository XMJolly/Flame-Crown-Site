<?php
include 'header.php';

$q = trim($_GET['q'] ?? '');
$results = null;

if ($q != '') {
    $term = "%" . $q . "%";

    $stmt = $conn->prepare(
        "SELECT p.*, c.category_name
         FROM Products_JE20936 p
         JOIN Categories_JE20936 c
         ON p.category_id = c.category_id
         WHERE p.status = 'Active'
         AND (
            p.product_name LIKE ?
            OR p.product_description LIKE ?
            OR c.category_name LIKE ?
         )
         ORDER BY p.product_name"
    );

    $stmt->bind_param('sss', $term, $term, $term);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<div class="container my-5">

    <h1>Search Results</h1>

    <?php if ($q != ''): ?>
        <p>
            Showing results for:
            <strong><?php echo htmlspecialchars($q); ?></strong>
        </p>
    <?php else: ?>
        <p>
            Type a menu item, category, or keyword in the search bar.
        </p>
    <?php endif; ?>

    <div class="row g-4 mt-2">

        <?php if ($q != '' && $results && $results->num_rows > 0): ?>

            <?php while ($row = $results->fetch_assoc()): ?>

                <?php
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

            <?php endwhile; ?>

        <?php elseif ($q != ''): ?>

            <div class="col-12">
                <div class="alert alert-warning">
                    No menu items matched your search.
                </div>
            </div>

        <?php else: ?>

            <div class="col-12">
                <div class="alert alert-info">
                    Try searching for burgers, chicken, fries, drinks, desserts, or combos.
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php include 'footer.php'; ?>