<?php
include 'auth.php';
include 'header.php';

/* Get total categories */
$catResult = $conn->query("SELECT COUNT(*) AS total FROM Categories_JE20936");
$catCount = $catResult->fetch_assoc()['total'];

/* Get total products */
$prodResult = $conn->query("SELECT COUNT(*) AS total FROM Products_JE20936");
$prodCount = $prodResult->fetch_assoc()['total'];

/* Get total contact messages */
$msgResult = $conn->query("SELECT COUNT(*) AS total FROM ContactMessages_JE20936");
$msgCount = $msgResult->fetch_assoc()['total'];
?>

<h1>Admin Dashboard</h1>

<p>
    Welcome to the Flame Crown Grill admin panel. Use this dashboard to manage
    menu categories, products, site colors, and customer contact messages.
</p>

<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card text-bg-primary shadow-sm">
            <div class="card-body">
                <h3 class="text-white">Categories</h3>
                <p class="display-6 text-white">
                    <?php echo $catCount; ?>
                </p>
                <a href="categories.php" class="btn btn-light">
                    Manage Categories
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-bg-success shadow-sm">
            <div class="card-body">
                <h3 class="text-white">Products</h3>
                <p class="display-6 text-white">
                    <?php echo $prodCount; ?>
                </p>
                <a href="products.php" class="btn btn-light">
                    Manage Products
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-bg-warning shadow-sm">
            <div class="card-body">
                <h3>Messages</h3>
                <p class="display-6">
                    <?php echo $msgCount; ?>
                </p>
                <a href="messages.php" class="btn btn-dark">
                    View Messages
                </a>
            </div>
        </div>
    </div>

</div>

<div class="card p-4 shadow-sm">
    <h2 class="h4">Admin Options</h2>

    <ul>
        <li>Add, edit, or delete menu categories.</li>
        <li>Add, edit, or delete burger, chicken, side, drink, dessert, and combo products.</li>
        <li>Update public site colors for headings, paragraphs, header, body, and footer.</li>
        <li>Review and delete customer contact messages.</li>
    </ul>
</div>

<?php include 'footer.php'; ?>