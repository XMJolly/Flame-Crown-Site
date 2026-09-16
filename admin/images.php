<?php
include 'auth.php';
include_once '../config/dbcon.php';

$success = '';
$error = '';
$edit = null;

$uploadDir = '../assets/images/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

/* Delete image record */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM Prj_SP2026_images WHERE image_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: images.php?deleted=1');
    exit;
}

/* Load image for editing */
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);

    $stmt = $conn->prepare("SELECT * FROM Prj_SP2026_images WHERE image_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $edit = $stmt->get_result()->fetch_assoc();
}

/* Add or update image */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = intval($_POST['image_id'] ?? 0);
    $image_type = trim($_POST['image_type'] ?? 'Product');
    $category_id = intval($_POST['category_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');
    $alt_text = trim($_POST['alt_text'] ?? '');

    $category_id_value = ($category_id > 0) ? $category_id : null;
    $product_id_value = ($product_id > 0) ? $product_id : null;

    $image_source = '';
    $image_is_url = 0;

    /* If editing and no new image/url is added, keep old image */
    if ($image_id > 0) {
        $oldStmt = $conn->prepare("SELECT image_source, image_is_url FROM Prj_SP2026_images WHERE image_id = ?");
        $oldStmt->bind_param('i', $image_id);
        $oldStmt->execute();
        $oldImage = $oldStmt->get_result()->fetch_assoc();

        if ($oldImage) {
            $image_source = $oldImage['image_source'];
            $image_is_url = intval($oldImage['image_is_url']);
        }
    }

    /* Upload file if selected */
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
        $originalName = basename($_FILES['image_file']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'webp', 'gif');

        if (in_array($extension, $allowedExtensions)) {
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
            $newFileName = time() . '_' . $safeName;
            $targetFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetFile)) {
                $image_source = $newFileName;
                $image_is_url = 0;
            } else {
                $error = 'Image could not be uploaded. Check your assets/images folder permissions.';
            }
        } else {
            $error = 'Only JPG, JPEG, PNG, WEBP, and GIF files are allowed.';
        }
    } elseif ($image_url != '') {
        $image_source = $image_url;
        $image_is_url = 1;
    }

    if ($image_source == '' && $error == '') {
        $error = 'Please choose an image file or enter an image URL.';
    }

    if ($error == '') {
        if ($image_id > 0) {
            $stmt = $conn->prepare(
                "UPDATE Prj_SP2026_images
                 SET image_type = ?, category_id = ?, product_id = ?, image_source = ?, image_is_url = ?, alt_text = ?
                 WHERE image_id = ?"
            );

            $stmt->bind_param(
                'siisisi',
                $image_type,
                $category_id_value,
                $product_id_value,
                $image_source,
                $image_is_url,
                $alt_text,
                $image_id
            );
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO Prj_SP2026_images
                 (image_type, category_id, product_id, image_source, image_is_url, alt_text)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                'siisis',
                $image_type,
                $category_id_value,
                $product_id_value,
                $image_source,
                $image_is_url,
                $alt_text
            );
        }

        if ($stmt->execute()) {
            /*
            If this is a product image and it was uploaded as a local file,
            also update the product's image_name so it appears on the public site.
            */
            if ($image_type == 'Product' && $product_id > 0 && $image_is_url == 0) {
                $updateProduct = $conn->prepare(
                    "UPDATE Prj_SP2026_products
                     SET image_name = ?
                     WHERE product_id = ?"
                );

                $updateProduct->bind_param('si', $image_source, $product_id);
                $updateProduct->execute();
            }

            header('Location: images.php?saved=1');
            exit;
        } else {
            $error = 'Image could not be saved.';
        }
    }
}

/* Success messages */
if (isset($_GET['saved'])) {
    $success = 'Image saved successfully.';
}

if (isset($_GET['deleted'])) {
    $success = 'Image deleted successfully.';
}

/* Dropdown data */
$categories = $conn->query(
    "SELECT category_id, category_name
     FROM Prj_SP2026_categories
     ORDER BY category_name"
);

$products = $conn->query(
    "SELECT product_id, product_name
     FROM Prj_SP2026_products
     ORDER BY product_name"
);

/* Search and limit */
$q = trim($_GET['q'] ?? '');
$limit = intval($_GET['limit'] ?? 10);

if (!in_array($limit, array(10, 25, 50, 100))) {
    $limit = 10;
}

if ($q != '') {
    $searchTerm = '%' . $q . '%';

    $stmt = $conn->prepare(
        "SELECT i.*, c.category_name, p.product_name
         FROM Prj_SP2026_images i
         LEFT JOIN Prj_SP2026_categories c ON i.category_id = c.category_id
         LEFT JOIN Prj_SP2026_products p ON i.product_id = p.product_id
         WHERE i.image_type LIKE ?
            OR c.category_name LIKE ?
            OR p.product_name LIKE ?
            OR i.alt_text LIKE ?
            OR i.image_source LIKE ?
         ORDER BY i.image_id DESC
         LIMIT ?"
    );

    $stmt->bind_param(
        'sssssi',
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $limit
    );

    $stmt->execute();
    $rows = $stmt->get_result();
} else {
    $stmt = $conn->prepare(
        "SELECT i.*, c.category_name, p.product_name
         FROM Prj_SP2026_images i
         LEFT JOIN Prj_SP2026_categories c ON i.category_id = c.category_id
         LEFT JOIN Prj_SP2026_products p ON i.product_id = p.product_id
         ORDER BY i.image_id DESC
         LIMIT ?"
    );

    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $rows = $stmt->get_result();
}

include 'header.php';
?>

<h1>Manage Images</h1>

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

<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="get" class="row g-3 flex-grow-1">
        <div class="col-md-4">
            <input
                type="text"
                name="q"
                class="form-control"
                placeholder="Search type, category, product, alt text"
                value="<?php echo htmlspecialchars($q); ?>"
            >
        </div>

        <div class="col-md-2">
            <select name="limit" class="form-select">
                <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo ($limit == 25) ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo ($limit == 100) ? 'selected' : ''; ?>>100</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-dark w-100" type="submit">
                Search
            </button>
        </div>
    </form>

    <a href="dashboard.php" class="btn btn-secondary ms-3">
        Back
    </a>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header">
        Add / Edit Image
    </div>

    <div class="card-body">
        <form method="post" enctype="multipart/form-data">

            <input
                type="hidden"
                name="image_id"
                value="<?php echo htmlspecialchars($edit['image_id'] ?? ''); ?>"
            >

            <div class="row g-3">

                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="image_type" class="form-select">
                        <?php
                        $types = array('Product', 'Category', 'Slider', 'Logo', 'Other');
                        foreach ($types as $type):
                        ?>
                            <option
                                value="<?php echo $type; ?>"
                                <?php echo (($edit['image_type'] ?? 'Product') == $type) ? 'selected' : ''; ?>
                            >
                                <?php echo $type; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="0">-- Select Category --</option>

                        <?php
                        if ($categories && $categories->num_rows > 0):
                            while ($cat = $categories->fetch_assoc()):
                        ?>
                                <option
                                    value="<?php echo $cat['category_id']; ?>"
                                    <?php echo (($edit['category_id'] ?? '') == $cat['category_id']) ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                        <?php
                            endwhile;
                        endif;
                        ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="0">-- Select Product --</option>

                        <?php
                        if ($products && $products->num_rows > 0):
                            while ($prod = $products->fetch_assoc()):
                        ?>
                                <option
                                    value="<?php echo $prod['product_id']; ?>"
                                    <?php echo (($edit['product_id'] ?? '') == $prod['product_id']) ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($prod['product_name']); ?>
                                </option>
                        <?php
                            endwhile;
                        endif;
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Image File</label>
                    <input
                        type="file"
                        name="image_file"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp,.gif"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">OR URL</label>
                    <input
                        type="text"
                        name="image_url"
                        class="form-control"
                        placeholder="https://..."
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Alt Text</label>
                    <input
                        type="text"
                        name="alt_text"
                        class="form-control"
                        value="<?php echo htmlspecialchars($edit['alt_text'] ?? ''); ?>"
                    >
                </div>

                <div class="col-md-8 d-flex align-items-end">
                    <button class="btn btn-primary me-2" type="submit">
                        Save
                    </button>

                    <a href="images.php" class="btn btn-secondary">
                        Clear
                    </a>
                </div>

            </div>
        </form>

        <?php if ($edit): ?>
            <div class="mt-3">
                <strong>Editing Image:</strong>

                <?php
                if ($edit['image_is_url']) {
                    $editSrc = $edit['image_source'];
                } else {
                    $editSrc = '../assets/images/' . $edit['image_source'];
                }
                ?>

                <div class="mt-2">
                    <img
                        src="<?php echo htmlspecialchars($editSrc); ?>"
                        alt="<?php echo htmlspecialchars($edit['alt_text']); ?>"
                        style="width:120px; height:80px; object-fit:cover; border-radius:6px;"
                    >
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<h2 class="h4">Image List</h2>

<table class="table table-bordered table-striped align-middle">
    <tr class="table-dark">
        <th>ID</th>
        <th>Type</th>
        <th>Category</th>
        <th>Product</th>
        <th>Image</th>
        <th>Alt</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>

    <?php if ($rows && $rows->num_rows > 0): ?>
        <?php while ($r = $rows->fetch_assoc()): ?>

            <?php
            if ($r['image_is_url']) {
                $imageSrc = $r['image_source'];
            } else {
                $imageSrc = '../assets/images/' . $r['image_source'];
            }
            ?>

            <tr>
                <td><?php echo $r['image_id']; ?></td>

                <td><?php echo htmlspecialchars($r['image_type']); ?></td>

                <td>
                    <?php echo htmlspecialchars($r['category_name'] ?? ''); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($r['product_name'] ?? ''); ?>
                </td>

                <td>
                    <img
                        src="<?php echo htmlspecialchars($imageSrc); ?>"
                        alt="<?php echo htmlspecialchars($r['alt_text']); ?>"
                        style="width:85px; height:60px; object-fit:cover; border-radius:6px;"
                    >
                    <br>
                    <small>
                        <?php echo htmlspecialchars($r['image_source']); ?>
                    </small>
                </td>

                <td><?php echo htmlspecialchars($r['alt_text']); ?></td>

                <td><?php echo htmlspecialchars($r['created_at']); ?></td>

                <td>
                    <a
                        href="images.php?edit=<?php echo $r['image_id']; ?>"
                        class="btn btn-sm btn-warning"
                    >
                        Edit
                    </a>

                    <a
                        href="images.php?delete=<?php echo $r['image_id']; ?>"
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
                No images found.
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php include 'footer.php'; ?>