<?php
require_once("checkAuthorize.php");
include_once("db_classes.php");
session_start();
$account = json_decode($_COOKIE['currentAccount']);
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    include("header.php");
    ?>
    <div class="container">
        <div class="row">
            <div class="col-5 offset-1 d-flex justify-content-center my-auto">
                <div class="avatar">
                    <img class="profile-image" src="img/ships.png" alt="Фото">
                </div>
            </div>
            <div class="col-4 col-offset-2 d-flex justify-content-center my-auto">
                <div class="profile-name">
                    <p><?php echo $account->nickname ?></p>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-around statistics-label mt-5">
            <div class="col-3 text-center my-auto statistics">
                <div class="statistics-label">
                    <p>Всего игр:</p>
                </div>
                <div class="statistics-value">
                    <p>100</p>
                </div>
            </div>
            <div class="col-3 text-center my-auto statistics">
                <div class="statistics-label">
                    <p>Побед:</p>
                </div>
                <div class="statistics-value">
                    <p>73</p>
                </div>
            </div>
            <div class="col-3 text-center my-auto statistics">
                <div class="statistics-label">
                    <p>Поражений:</p>
                </div>
                <div class="statistics-value">
                    <p>27</p>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-center mt-5">
            <div class="col-5 text-center my-auto statistics">
                <div class="win-rate-label">
                    <p>Процент побед:</p>
                </div>
                <div class="win-rate-value">
                    <p>73%</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>