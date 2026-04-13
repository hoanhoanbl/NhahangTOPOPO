<?php
include dirname(__DIR__,3) . "/config/connect.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm mới nhân viên</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        background-size: contain; 
        background-image: url('https://quannhautudo.com/Static/web_images/header-bg-mob.jpg');
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #333;
                }

        nav{
            background-color:pink;
            padding: 20px;
        }

        a{
            color: white;
            padding: 10px;
            text-decoration: none;
        }
    </style>
</head>
<body style="margin: 0px">
    <nav>
        <a href="index.php?action=xem">Danh sách nhân viên</a>
        <a href="index.php?action=add">Thêm nhân viên</a>
    </nav>

    <?php
        if(isset($_GET['action'])){
            switch ($_GET['action']) {
                case 'xem':
                    include __DIR__ . "/user/view.php";
                    break;
                case 'create':
                    include __DIR__ . "/user/create.php";
                    break;
                case 'update':
                    include "update.php";
                    break;
                case 'delete':
                    include "process-delete.php";
                    break;
                case 'process-update':
                    include "process-update.php";
                    break;
                default:
                    include "index.php";
                    break;
            }
        }
    ?>
</body>
</html>