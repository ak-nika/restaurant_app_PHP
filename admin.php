<?php
// admin.php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user is admin
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id=?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

if (!$is_admin) {
    echo "Access denied.";
    exit();
}

// Handle add, edit, delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or edit product
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "./images/";  // Directory to save the images
    $target_file = $target_dir . basename($image);

    // Move the uploaded image to the 'images' directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Image uploaded successfully, now insert item data into the database
        $query = "INSERT INTO menu_items (name, description, price, image) 
                  VALUES ('$item_name', '$description', '$price', '$image')";
    }

    if (isset($_POST['product_id'])) {
        // Edit product
        $product_id = $_POST['product_id'];
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
        $stmt->bind_param('ssdsi', $name, $description, $price, $image, $product_id);
    } else {
        // Add product
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssds', $name, $description, $price, $image);
    }
    $stmt->execute();
    header('Location: admin.php');
    exit();
}

// Delete product
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    header('Location: admin.php');
    exit();
}

// Fetch products
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Restaurant App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Admin Panel</h2>
    <a href="menu.php">Back to Menu</a> | <a href="logout.php">Log Out</a>

    <h3>Add / Edit Product</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <?php
        // If editing, fetch product data
        if (isset($_GET['edit'])) {
            $product_id = $_GET['edit'];
            $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            echo '<input type="hidden" name="product_id" value="'.$product['id'].'">';
        }
        ?>
        <label>Name:</label><br>
        <input type="text" name="name" required value="<?php echo $product['name'] ?? ''; ?>"><br>
        <label>Description:</label><br>
        <textarea name="description"><?php echo $product['description'] ?? ''; ?></textarea><br>
        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required value="<?php echo $product['price'] ?? ''; ?>"><br>
        <label>Image:</label><br>
        <?php if (isset($product['image'])): ?>
            <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="100"><br>
            <input type="hidden" name="existing_image" value="<?php echo $product['image']; ?>">
        <?php endif; ?>
        <input type="file" name="image"><br>
        <button type="submit"><?php echo isset($product) ? 'Update' : 'Add'; ?> Product</button>
    </form>

    <h3>Products List</h3>
    <table>
        <tr>
            <th>Name</th><th>Description</th><th>Price</th><th>Image</th><th>Actions</th>
        </tr>
        <?php while ($row = $products->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td>$<?php echo $row['price']; ?></td>
            <td><img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="50"></td>
            <td>
                <a href="admin.php?edit=<?php echo $row['id']; ?>">Edit</a> |
                <a href="admin.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
