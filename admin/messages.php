<?php
include 'auth.php';
include 'header.php';

/* Delete message */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare(
        "DELETE FROM ContactMessages_JE20936 WHERE message_id = ?"
    );

    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: messages.php');
    exit;
}

/* Get all contact messages */
$rows = $conn->query(
    "SELECT * FROM ContactMessages_JE20936
     ORDER BY created_at DESC"
);
?>

<h1>Contact Messages</h1>

<p>
    These are messages submitted from the public contact form.
</p>

<table class="table table-bordered table-striped">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Date</th>
        <th>Action</th>
    </tr>

    <?php if ($rows && $rows->num_rows > 0): ?>
        <?php while ($r = $rows->fetch_assoc()): ?>
            <tr>
                <td><?php echo $r['message_id']; ?></td>

                <td><?php echo htmlspecialchars($r['customer_name']); ?></td>

                <td>
                    <a href="mailto:<?php echo htmlspecialchars($r['customer_email']); ?>">
                        <?php echo htmlspecialchars($r['customer_email']); ?>
                    </a>
                </td>

                <td><?php echo htmlspecialchars($r['subject']); ?></td>

                <td><?php echo nl2br(htmlspecialchars($r['message'])); ?></td>

                <td><?php echo htmlspecialchars($r['created_at']); ?></td>

                <td>
                    <a 
                        onclick="return confirmDelete()" 
                        class="btn btn-sm btn-danger" 
                        href="messages.php?delete=<?php echo $r['message_id']; ?>"
                    >
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center">
                No contact messages found.
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php include 'footer.php'; ?>