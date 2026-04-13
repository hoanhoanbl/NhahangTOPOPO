<?php
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $database = 'booking_restaurant';
    $port = '3306';

    $conn = mysqli_connect($host, $user, $pass, $database, $port);
    mysqli_set_charset($conn, "utf8mb4");
    mysqli_query($conn, "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

    // if (!$conn) {
    // die("Connection failed: " . mysqli_connect_error());
    // }
    // echo "Connected successfully";
?>

