<?php
include_once '../config/dbcon.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flame Crown Grill Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            Flame Crown Grill Admin
        </a>

        <a class="btn btn-outline-light" href="logout.php">
            Logout
        </a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 bg-light admin-sidebar p-3">
            <div class="list-group">

                <a class="list-group-item list-group-item-action" href="dashboard.php">
                    Dashboard
                </a>
		
		<a class="list-group-item list-group-item-action" href="images.php">
    		    Manage Images
		</a>

                <a class="list-group-item list-group-item-action" href="categories.php">
                    Manage Categories
                </a>

		<a class="list-group-item list-group-item-action" href="orders.php">
    		    Manage Orders
		</a>

                <a class="list-group-item list-group-item-action" href="products.php">
                    Manage Products
                </a>

                <a class="list-group-item list-group-item-action" href="settings.php">
                    Manage Site Colors
                </a>

                <a class="list-group-item list-group-item-action" href="messages.php">
                    Contact Messages
                </a>

                <a class="list-group-item list-group-item-action" href="../public/home_xjolly1.php" target="_blank">
                    View Public Site
                </a>

            </div>
        </div>

        <!-- Main Content -->
        <main class="col-md-10 p-4">