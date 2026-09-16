<?php
session_start();

unset($_SESSION['cart']);

header('Location: home_xjolly1.php?cart=open');
exit;
?>