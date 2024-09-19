<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="user/cart.php">Cart</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="user/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="user/login.php">Login</a></li>
                <li><a href="user/signup.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
