<?php
    // Include config.php Ä‘á»ƒ sá»­ dá»¥ng hÃ m env() Ä‘á»c file .env
    require_once __DIR__ . '/config.php';
    
    // Cáº¥u hÃ¬nh database - Sá»­ dá»¥ng hÃ m env() giá»‘ng config.php
    $host = env('DB_HOST', 'localhost');
    $user = env('DB_USER', 'root');
    $pass = env('DB_PASS', '');
    $database = env('DB_NAME', 'booking_restaurant');
    $port = env('DB_PORT', '3306');

    // Káº¿t ná»‘i database
    $conn = mysqli_connect($host, $user, $pass, $database, $port);
    
    // Kiá»ƒm tra káº¿t ná»‘i trÆ°á»›c khi thá»±c hiá»‡n cÃ¡c thao tÃ¡c khÃ¡c
    if (!$conn) {
        die("Lá»—i káº¿t ná»‘i database: " . mysqli_connect_error());
    }
    
    // Thiáº¿t láº­p charset UTF-8
    mysqli_set_charset($conn, "utf8mb4");
    mysqli_query($conn, "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Thiáº¿t láº­p mÃºi giá» cho MySQL connection
    mysqli_query($conn, "SET time_zone = '+07:00'");
