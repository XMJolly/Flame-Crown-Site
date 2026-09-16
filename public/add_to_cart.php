<?php
session_start();
include_once '../config/dbcon.php';

$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);
$drink_choice = trim($_POST['drink_choice'] ?? '');

if ($quantity < 1) {
    $quantity = 1;
}

if ($product_id > 0) {
    $stmt = $conn->prepare(
        "SELECT product_id, product_name 
         FROM Products_JE20936 
         WHERE product_id = ? AND status = 'Active'"
    );

    if ($stmt) {
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }

            if (!isset($_SESSION['cart_options'])) {
                $_SESSION['cart_options'] = array();
            }

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }

            if ($drink_choice != '') {
                $_SESSION['cart_options'][$product_id] = $drink_choice;
            }

            $_SESSION['cart_message'] = 'Item added to cart.';
        }
    }
}

$back = $_SERVER['HTTP_REFERER'] ?? 'home_xjolly1.php';

/* Remove old cart=open if it already exists */
$back = str_replace('&cart=open', '', $back);
$back = str_replace('?cart=open', '', $back);

if (strpos($back, '?') !== false) {
    $back .= '&cart=open';
} else {
    $back .= '?cart=open';
}

header("Location: " . $back);
exit;
?>