<?php
include 'auth.php';
include 'header.php';

$edit = null;

/* Delete category */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM Categories_JE20936 WHERE category_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: categories.php');
    exit;
}

/* Load category for editing */
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);

    $stmt = $conn->prepare("SELECT * FROM Categories_JE20936 WHERE category_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $edit = $stmt->get_result()->fetch_assoc();
}

/* Add or update category */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id'] ?? 0);
    $category_name = trim($_POST['category_name'] ?? '');
    $category_description = trim($_POST['category_description'] ?? '');
    $status = $_POST['status'] ?? 'Active';

    if ($category_id > 0) {
        $stmt = $conn->prepare(
            "UPDATE Categories_JE20936
             SET category_name = ?, category_description = ?, status = ?
             WHERE category_id = ?"
        );

        $stmt->bind_param(
            'sssi',
            $category_name,
            $category_description,
            $status,
            $category_id
        );
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO Categories_JE20936
             (category_name, category_description, status)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            'sss',
            $category_name,
            $category_description,
            $status
        );
    }

    $stmt->execute();

    header('Location: categories.php');
    exit;
}

/* Get all categories */
$rows = $conn->query(
    "SELECT * FROM Categories_JE20936
     ORDER BY category_id DESC"
);
?>

<h1>Manage Categories</h1>

<p>
    Use this page to add, edit, or delete menu categories for Flame Crown Grill.
</p>

<div class="card p-3 mb-4 shadow-sm">
    <h2 class="h4">
        <?php if ($edit): ?>
            Edit Category
        <?php else: ?>
            Add New Category
        <?php endif; ?>
    </h2>

    <form method="post">
        <input 
            type="hidden" 
            name="category_id" 
            value="<?php echo htmlspecialchars($edit['category_id'] ?? ''); ?>"
        >

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">Category Name</label>
                <input 
                    type="text"
                    name="category_name" 
                    class="form-control" 
                    required
                    value="<?php echo htmlspecialchars($edit['category_name'] ?? ''); ?>"
                >
            </div>

            <div class="col-md-5">
                <label class="form-label">Description</label>
                <input 
                    type="text"
                    name="category_description" 
                    class="form-control"
                    value="<?php echo htmlspecialchars($edit['category_description'] ?? ''); ?>"
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

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">
                    Save
                </button>
            </div>

        </div>
    </form>
</div>

<h2 class="h4">Category List</h2>

<table class="table table-bordered table-striped">
    <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Description</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php if ($rows && $rows->num_rows > 0): ?>
        <?php while ($r = $rows->fetch_assoc()): ?>
            <tr>
                <td><?php echo $r['category_id']; ?></td>

                <td><?php echo htmlspecialchars($r['category_name']); ?></td>

                <td><?php echo htmlspecialchars($r['category_description']); ?></td>

                <td><?php echo htmlspecialchars($r['status']); ?></td>

                <td class="table-actions">
                    <a 
                        class="btn btn-sm btn-warning" 
                        href="categories.php?edit=<?php echo $r['category_id']; ?>"
                    >
                        Edit
                    </a>

                    <a 
                        onclick="return confirmDelete()" 
                        class="btn btn-sm btn-danger" 
                        href="categories.php?delete=<?php echo $r['category_id']; ?>"
                    >
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">
                No categories found.
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php include 'footer.php'; ?>