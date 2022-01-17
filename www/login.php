<?php
$login = $_POST['login'];
$password = $_POST['password'];
$messageStart = "С возвращением,";
$messageEnd = "Уважаемый";

if ($password != null || ($_COOKIE["login"] != null && $_COOKIE["password"] != null)) {
    if ($_COOKIE["login"] != null && $_COOKIE["password"] != null) {      // Если есть куки, то инфа берется из них
        $login = $_COOKIE["login"];
        $password = $_COOKIE["password"];
    }
    check($login, $password, $messageStart, $messageEnd);
}

function check($login, $password, &$messageStart, &$messageEnd)
{
    include_once "db_connect.php";
    $sql = $pdo->prepare('SELECT * FROM account WHERE (login = ? OR email = ?) AND password = ?');     // Поиск аккаунта с такой почтой
    $sql->execute(array($login, $login, $password));
    if ($sql->rowCount() == 1) {                                                        // Если такой только 1
        $arr = $sql->fetch(PDO::FETCH_ASSOC);   // Получить строку из response
        setcookie("login", $arr["login"]);      // Сохраняем куки
        setcookie("password", $arr["password"]);
        header("Location: account.php");    
        $messageStart = "Добро";
        $messageEnd = "Пожаловать";
    } else {
        setcookie("login", "", time() - 3600, "/");             // Очищаем куки если они были(и не были, на всякий)
        setcookie("password", "", time() - 3600, "/");
        $messageStart = "Такого аккаунта ";
        $messageEnd = "Нет";
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
        Вход
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
                <form method="POST" action="login.php">
                    <input class="pl input-info" type="text" name="login" placeholder="Логин" required /> <br>
                    <input class="pl input-info" type="text" name="password" placeholder="Пароль" required /> <br>

                    <div style="height: 5vh;"></div>
                    <input class="pl submit-button" type="submit" value="Войти" />
                </form>
            </div>
            <div class="col-4 offset-1 text-center my-auto">
                <p class="hello-label"><?php echo $messageStart ?> <span><?php echo $messageEnd ?></span></p>
            </div>
        </div>
    </div>
</body>

</html>