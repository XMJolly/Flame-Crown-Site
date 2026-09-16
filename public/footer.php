<footer class="site-footer text-white footer py-4 mt-5">
    <div class="container text-center">

        <h3 class="text-white">Flame Crown Grill</h3>

        <p class="text-white mb-2">
            Flame-grilled burgers, crispy sides, refreshing drinks, and combo meals made fresh.
        </p>

        <p class="text-white mb-2">
            <a href="home_xjolly1.php" class="text-white text-decoration-none">Home</a> |
            <a href="search.php?q=burger" class="text-white text-decoration-none">Burgers</a> |
            <a href="search.php?q=combo" class="text-white text-decoration-none">Combos</a> |
            <a href="contact.php" class="text-white text-decoration-none">Contact</a>
        </p>

        <p class="text-white mb-0">
            &copy; <?php echo date('Y'); ?> Flame Crown Grill. All rights reserved.
        </p>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>

<?php if (($_GET['cart'] ?? '') === 'open'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var cartElement = document.getElementById('cartSidebar');
    if (cartElement) {
        var cart = new bootstrap.Offcanvas(cartElement);
        cart.show();
    }
});
</script>
<?php endif; ?>

</body>
</html>