<?php
$email = $_POST['email'];
$login = $_POST['login'];
$password1 = $_POST['password1'];
$password2 = $_POST['password2'];
$nickname = $_POST['nickname'];
if ($email != null) {
    if (($password1 == $password2) && ($password1 != null)) {
        include_once "db_connect.php";

        $arr = $pdo->prepare('SELECT * FROM account WHERE email = ? OR login = ?');     // Поиск аккаунта с такой почтой
        $arr->execute(array($email, $login));
        //echo $arr->rowCount();

        if ($arr->rowCount() == 0) {                                                    // Если таких нет
            $sql = $pdo->prepare('INSERT INTO account (`id`, `login`, `password`, `email`, `nickname`) VALUES (NULL, ?, ?, ?, ?)');
            $sql->execute(array($login, $password1, $email, $nickname));
            echo "<div id='success_message'><h2 >Аккаунт создан!</h2></div>";
        } else
            echo "<div id='error_message'><h2 >Аккаунт с таким Email уже есть!</h2></div>";
    } else
        echo "<div id='error_message'><h2 >Пароли не совпадают</h2></div>";
}
?>

<!doctype html>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>
        Регистрация
    </title>
</head>

<body>
    <div class="center">
        <h1 style="color:black">Регистрация</h1>
        <form method="POST" action="registration.php">
            <input class="pl" type="text" name="email" placeholder="Почта" required /> <br>
            <input class="pl" type="text" name="nickname" placeholder="Имя" required /> <br>
            <br>
            <input class="pl" type="text" name="login" placeholder="Логин" required /> <br>
            <input class="pl" type="text" name="password1" placeholder="Пароль" required /> <br>
            <input class="pl" type="text" name="password2" placeholder="Повторите пароль" required /> <br>


            <input class="pl" type="submit" value="Регистрация" />
        </form>
    </div>
</body>

</html>