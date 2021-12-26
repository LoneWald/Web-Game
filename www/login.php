<?php
$login = $_POST['login'];
$password = $_POST['password'];

if ($password != null || ($_COOKIE["login"] != null && $_COOKIE["password"] != null)) {
    if ($_COOKIE["login"] != null && $_COOKIE["password"] != null) {      // Если есть куки, то инфа берется из них
        $login = $_COOKIE["login"];
        $password = $_COOKIE["password"];
    }
    check($login, $password);
}

function check($login, $password)
{
    include_once "db_connect.php";
    $sql = $pdo->prepare('SELECT * FROM account WHERE (login = ? OR email = ?) AND password = ?');     // Поиск аккаунта с такой почтой
    $sql->execute(array($login, $login, $password));
    if ($sql->rowCount() == 1) {                                                        // Если такой только 1
        $arr = $sql->fetch(PDO::FETCH_ASSOC);   // Получить строку из response
        setcookie("login", $arr["login"]);      // Сохраняем куки
        setcookie("password", $arr["password"]);
        header("Location: prepareToBattle.php");           // Переход на игру
        echo "<div id='success_message'><h2 >Добро пожаловать!</h2></div>";
    } else {
        setcookie("login", "", time() - 3600, "/");             // Очищаем куки если они были(и не были, на всякий)
        setcookie("password", "", time() - 3600, "/");
        echo "<div id='error_message'><h2 >Такого аккаунта нет!</h2></div>";
    }
}
?>

<!doctype html>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>
        Вход
    </title>
</head>

<body>
    <div class="center">
        <h1 style="color:black">Вход</h1>
        <form method="POST" action="login.php">
            <input class="pl" type="text" name="login" placeholder="Логин" required /> <br>
            <input class="pl" type="text" name="password" placeholder="Пароль" required /> <br>


            <input class="pl" type="submit" value="Войти" />
        </form>
    </div>
</body>

</html>