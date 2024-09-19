<?php
// menu.php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch products
$result = $conn->query("SELECT * FROM products");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    // Check if item already in cart
    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Add to cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $user_id, $product_id);
        $stmt->execute();
    }

    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu - Restaurant App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Menu</h2>
    <a href="cart.php">View Cart</a> | <a href="logout.php">Log Out</a>
    <div class="products">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product">
            <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <h3><?php echo $row['name']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p>$<?php echo $row['price']; ?></p>
            <form method="POST" action="">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
