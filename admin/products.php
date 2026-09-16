<?php
include 'auth.php';
include 'header.php';

$edit = null;

/* Delete product */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM Products_JE20936 WHERE product_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: products.php');
    exit;
}

/* Load product for editing */
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);

    $stmt = $conn->prepare("SELECT * FROM Products_JE20936 WHERE product_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $edit = $stmt->get_result()->fetch_assoc();
}

/* Add or update product */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $product_name = trim($_POST['product_name'] ?? '');
    $product_description = trim($_POST['product_description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $image_name = trim($_POST['image_name'] ?? '');
    $status = $_POST['status'] ?? 'Active';

    if ($image_name == '') {
        $image_name = 'placeholder.jpg';
    }

    if ($product_id > 0) {
        $stmt = $conn->prepare(
            "UPDATE Products_JE20936
             SET category_id = ?, product_name = ?, product_description = ?, price = ?, image_name = ?, status = ?
             WHERE product_id = ?"
        );

        $stmt->bind_param(
            'issdssi',
            $category_id,
            $product_name,
            $product_description,
            $price,
            $image_name,
            $status,
            $product_id
        );
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO Products_JE20936
             (category_id, product_name, product_description, price, image_name, status)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'issdss',
            $category_id,
            $product_name,
            $product_description,
            $price,
            $image_name,
            $status
        );
    }

    $stmt->execute();

    header('Location: products.php');
    exit;
}

/* Get categories for dropdown */
$categories = $conn->query(
    "SELECT * FROM Categories_JE20936
     ORDER BY category_name"
);

/* Get all products */
$rows = $conn->query(
    "SELECT p.*, c.category_name
     FROM Products_JE20936 p
     JOIN Categories_JE20936 c
     ON p.category_id = c.category_id
     ORDER BY p.product_id DESC"
);
?>

<h1>Manage Products</h1>

<p>
    Use this page to add, edit, or delete Flame Crown Grill menu items.
</p>

<div class="alert alert-info">
    Image upload is not used. Type the image filename only, then manually place that image inside
    <strong>assets/images/</strong>.
</div>

<div class="card p-3 mb-4 shadow-sm">

    <h2 class="h4">
        <?php if ($edit): ?>
            Edit Product
        <?php else: ?>
            Add New Product
        <?php endif; ?>
    </h2>

    <form method="post">

        <input 
            type="hidden" 
            name="product_id" 
            value="<?php echo htmlspecialchars($edit['product_id'] ?? ''); ?>"
        >

        <div class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Category</label>

                <select name="category_id" class="form-select" required>
                    <?php if ($categories && $categories->num_rows > 0): ?>
                        <?php while ($c = $categories->fetch_assoc()): ?>
                            <option 
                                value="<?php echo $c['category_id']; ?>"
                                <?php echo (($edit['category_id'] ?? '') == $c['category_id']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($c['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Product Name</label>

                <input 
                    type="text"
                    name="product_name" 
                    class="form-control" 
                    required
                    value="<?php echo htmlspecialchars($edit['product_name'] ?? ''); ?>"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">Price</label>

                <input 
                    type="number" 
                    step="0.01" 
                    name="price" 
                    class="form-control" 
                    required
                    value="<?php echo htmlspecialchars($edit['price'] ?? ''); ?>"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">Image Filename</label>

                <input 
                    type="text"
                    name="image_name" 
                    class="form-control" 
                    placeholder="burger.jpg"
                    value="<?php echo htmlspecialchars($edit['image_name'] ?? 'placeholder.jpg'); ?>"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>

                <select name="status" class="form-select">
                    <option value="Active"
                        <?php echo (($edit['status'] ?? '') === 'Active') ? 'selected' : ''; ?>>
                        Active
                    </option>

                    <option value="Inactive"
                        <?php echo (($edit['status'] ?? '') === 'Inactive') ? 'selected' : ''; ?>>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>

                <textarea 
                    name="product_description" 
                    class="form-control" 
                    rows="3" 
                    required
                ><?php echo htmlspecialchars($edit['product_description'] ?? ''); ?></textarea>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary" type="submit">
                    Save Product
                </button>
            </div>

        </div>
    </form>
</div>

<h2 class="h4">Product List</h2>

<table class="table table-bordered table-striped">
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php if ($rows && $rows->num_rows > 0): ?>
        <?php while ($r = $rows->fetch_assoc()): ?>
            <tr>
                <td><?php echo $r['product_id']; ?></td>

                <td>
                    <img 
                        src="../assets/images/<?php echo htmlspecialchars($r['image_name']); ?>" 
                        width="70"
                        alt="<?php echo htmlspecialchars($r['product_name']); ?>"
                    >
                </td>

                <td><?php echo htmlspecialchars($r['product_name']); ?></td>

                <td><?php echo htmlspecialchars($r['category_name']); ?></td>

                <td>$<?php echo number_format($r['price'], 2); ?></td>

                <td><?php echo htmlspecialchars($r['status']); ?></td>

                <td class="table-actions">
                    <a 
                        class="btn btn-sm btn-warning" 
                        href="products.php?edit=<?php echo $r['product_id']; ?>"
                    >
                        Edit
                    </a>

                    <a 
                        onclick="return confirmDelete()" 
                        class="btn btn-sm btn-danger" 
                        href="products.php?delete=<?php echo $r['product_id']; ?>"
                    >
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center">
                No products found.
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php include 'footer.php'; ?>