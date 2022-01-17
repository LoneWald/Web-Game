<?php

logout();

function logout()
{
    session_start();
    $_SESSION['FieldWidth'] = null;
    $_SESSION['FieldHeight'] = null;
    $_SESSION['Checker'] = null;
    $_SESSION['PlayerField'] = null;
    $_SESSION['game'] = null;
    $_SESSION['Difficult'] = null;
    setcookie("login", "", time() - 3600, "/");             // Очищаем куки если они были(и не были, на всякий)
    setcookie("password", "", time() - 3600, "/");
    header("Location: index.php");
}
?>