<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'restaurant_db';

$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
