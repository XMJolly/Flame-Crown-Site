<?php
include 'auth.php';
include_once '../config/dbcon.php';

$success = '';

/* Get current settings */
$settingsResult = $conn->query("SELECT * FROM SiteSettings_JE20936 LIMIT 1");
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;

/* If settings table is empty, use default colors */
if (!$settings) {
    $settings = array(
        'setting_id' => 0,
        'h1_color' => '#8B0000',
        'h2_color' => '#D35400',
        'h3_color' => '#5A2D0C',
        'p_color' => '#333333',
        'header_color' => '#8B0000',
        'body_color' => '#FFF8E7',
        'footer_color' => '#5A2D0C'
    );
}

/* Update colors */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $h1_color = $_POST['h1_color'] ?? '#8B0000';
    $h2_color = $_POST['h2_color'] ?? '#D35400';
    $h3_color = $_POST['h3_color'] ?? '#5A2D0C';
    $p_color = $_POST['p_color'] ?? '#333333';
    $header_color = $_POST['header_color'] ?? '#8B0000';
    $body_color = $_POST['body_color'] ?? '#FFF8E7';
    $footer_color = $_POST['footer_color'] ?? '#5A2D0C';

    if ($settings['setting_id'] > 0) {
        $stmt = $conn->prepare(
            "UPDATE SiteSettings_JE20936
             SET h1_color = ?, h2_color = ?, h3_color = ?, p_color = ?,
                 header_color = ?, body_color = ?, footer_color = ?
             WHERE setting_id = ?"
        );

        $stmt->bind_param(
            'sssssssi',
            $h1_color,
            $h2_color,
            $h3_color,
            $p_color,
            $header_color,
            $body_color,
            $footer_color,
            $settings['setting_id']
        );
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO SiteSettings_JE20936
             (h1_color, h2_color, h3_color, p_color, header_color, body_color, footer_color)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sssssss',
            $h1_color,
            $h2_color,
            $h3_color,
            $p_color,
            $header_color,
            $body_color,
            $footer_color
        );
    }

    $stmt->execute();

    $success = 'Site colors were updated successfully.';

    /* Reload settings after saving */
    $settingsResult = $conn->query("SELECT * FROM SiteSettings_JE20936 LIMIT 1");
    $settings = $settingsResult ? $settingsResult->fetch_assoc() : $settings;
}

include 'header.php';
?>

<h1>Manage Site Colors</h1>

<p>
    Use this page to change the public website colors for Flame Crown Grill.
</p>

<?php if ($success != ''): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<form method="post" class="card p-4 shadow-sm">

    <div class="row g-3">

        <div class="col-md-3">
            <label class="form-label">H1 Color</label>
            <input 
                type="color" 
                name="h1_color" 
                class="form-control form-control-color"
                value="<?php echo htmlspecialchars($settings['h1_color']); ?>"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">H2 Color</label>
            <input 
                type="color" 
                name="h2_color" 
                class="form-control form-control-color"
                value="<?php echo htmlspecialchars($settings['h2_color']); ?>"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">H3 Color</label>
            <input 
                type="color" 
                name="h3_color" 
                class="form-control form-control-color"
                value="<?php echo htmlspecialchars($settings['h3_color']); ?>"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Paragraph Color</label>
            <input 
                type="color" 
                name="p_color" 
                class="form-control form-control-color"
                value="<?php echo htmlspecialchars($settings['p_color']); ?>"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Header Color</label>
            <input 
                type="color" 
                name="header_color" 
                class="form-control form-control-color"
                value="<?php echo htmlspecialchars($settings['header_color']); ?>"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Body Background Color</label>
            <input 
                type="color" 
                name="body_color" 
                class="form-control form-control-color"
                value="<?php echo htmlspecialchars($settings['body_color']); ?>"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Footer Color</label>
            <input 
                type="color" 
                name="footer_color" 
                class="form-control form-control-color"
                value="<?php echo htmlspecialchars($settings['footer_color']); ?>"
            >
        </div>

        <div class="col-12">
            <button class="btn btn-primary" type="submit">
                Save Colors
            </button>

            <a href="../public/index.php" target="_blank" class="btn btn-warning ms-2">
                View Public Site
            </a>
        </div>

    </div>

</form>

<div class="card p-4 shadow-sm mt-4">
    <h2 class="h4">Suggested Flame Crown Grill Colors</h2>

    <p>
        A good burger restaurant theme uses dark red, orange, yellow, brown, cream, and dark text.
    </p>

    <ul>
        <li>Header: dark red</li>
        <li>Footer: dark brown</li>
        <li>Body: cream or light yellow</li>
        <li>Headings: red, orange, or brown</li>
        <li>Paragraph text: dark gray or black</li>
    </ul>
</div>

<?php include 'footer.php'; ?>