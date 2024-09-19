<?php
// cart.php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $conn->prepare("
    SELECT cart.id AS cart_id, products.*
    FROM cart
    INNER JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = ?
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Remove item from cart
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param('ii', $cart_id, $user_id);
    $stmt->execute();
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart - Restaurant App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Your Cart</h2>
    <a href="menu.php">Back to Menu</a> | <a href="logout.php">Log Out</a>
    <div class="cart-items">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="cart-item">
            <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <h3><?php echo $row['name']; ?></h3>
            <p>$<?php echo $row['price']; ?></p>
            <a href="cart.php?remove=<?php echo $row['cart_id']; ?>">Remove</a>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
