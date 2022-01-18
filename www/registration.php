<?php
$email = $_POST['email'];
$login = $_POST['login'];
$password1 = $_POST['password1'];
$password2 = $_POST['password2'];
$nickname = $_POST['nickname'];
$messageStart = "Я рад вас здесь";
$messageMiddle = " Приветствовать";
$messageEnd = "";

if ($email != null) {
    if (($password1 == $password2) && ($password1 != null)) {
        include_once "db_connect_pdo.php";

        $arr = $pdo->prepare('SELECT * FROM account WHERE email = ? OR login = ?');     // Поиск аккаунта с такой почтой
        $arr->execute(array($email, $login));
        //echo $arr->rowCount();

        if ($arr->rowCount() == 0) {                                                    // Если таких нет
            $sql = $pdo->prepare('INSERT INTO account (`id`, `login`, `password`, `email`, `nickname`) VALUES (NULL, ?, ?, ?, ?)');
            $sql->execute(array($login, $password1, $email, $nickname));
            $messageStart ="Аккаунт";
            $messageMiddle = " Создан";
        } else{
            $messageStart ="Аккаунт с таким";
            $messageMiddle = " Email ";
            $messageEnd = "уже есть!";
        }
    } else{
        $messageStart ="Пароли";
            $messageMiddle = " не ";
            $messageEnd = "совпадают!";
    }
}
?>

<!doctype html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <title>
        Регистрация
    </title>
</head>

<body>
    <?php
    include("header.php");
    ?>
    <div class="container">
        <div class="row" style="height: 10vh;">

        </div>
        <div class="row">
            <div class="col-4 offset-2 text-center mt-2 input-menu">
                <form method="POST" action="registration.php">
                    <input class="pl input-info" type="text" name="email" placeholder="Почта" required />
                    <input class="pl input-info" type="text" name="nickname" placeholder="Имя" required />
                    <input class="pl input-info" type="text" name="login" placeholder="Логин" required />
                    <input class="pl input-info" type="text" name="password1" placeholder="Пароль" required />
                    <input class="pl input-info" type="text" name="password2" placeholder="Повторите пароль" required />
                    <div style="height: 5vh;"></div>
                    <input class="pl submit-button" type="submit" value="Регистрация" />
                </form>
            </div>
            <div class="col-4 offset-1 text-center my-auto">
                <p class="hello-label">
                    <?php echo $messageStart ?><span><?php echo $messageMiddle ?></span><?php echo $messageEnd ?></p>
            </div>
        </div>
    </div>
</body>

</html>