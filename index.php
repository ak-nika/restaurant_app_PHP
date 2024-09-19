<?php
session_start();
include('config.php');

// Fetch menu items from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Include header -->
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Welcome to Our Restaurant!</h1>

        <div class="menu-items">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="menu-item">
                    <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                    <h3><?php echo $row['name']; ?></h3>
                    <p><?php echo $row['description']; ?></p>
                    <p>Price: £<?php echo $row['price']; ?></p>
                    <form method="POST" action="cart.php?action=add&id=<?php echo $row['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1">
                        <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>

</body>
</html>
