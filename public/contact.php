<?php
include 'header.php';

$msg = '';
$error = '';

$customer_name = '';
$customer_email = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($customer_name == '' || $customer_email == '' || $subject == '' || $message == '') {
        $error = 'Please fill out all fields.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO ContactMessages_JE20936 
            (customer_name, customer_email, subject, message) 
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'ssss',
            $customer_name,
            $customer_email,
            $subject,
            $message
        );

        if ($stmt->execute()) {
            $msg = 'Your message was submitted successfully. Thank you for contacting Flame Crown Grill!';

            $customer_name = '';
            $customer_email = '';
            $subject = '';
            $message = '';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>

<div class="container my-5">

    <div class="row justify-content-center">
        <div class="col-md-8">

            <h1>Contact Flame Crown Grill</h1>

            <p>
                Have a question about our burgers, combos, catering, or menu items?
                Send us a message and our team will get back to you.
            </p>

            <?php if ($msg != ''): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <?php if ($error != ''): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="card p-4 shadow-sm">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input 
                        type="text"
                        name="customer_name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($customer_name); ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input 
                        type="email"
                        name="customer_email"
                        class="form-control"
                        value="<?php echo htmlspecialchars($customer_email); ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input 
                        type="text"
                        name="subject"
                        class="form-control"
                        value="<?php echo htmlspecialchars($subject); ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea 
                        name="message"
                        class="form-control"
                        rows="5"
                        required
                    ><?php echo htmlspecialchars($message); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    Submit Message
                </button>

            </form>

        </div>
    </div>

</div>

<?php include 'footer.php'; ?>