<?php 
if ($_COOKIE["login"] == null || $_COOKIE["password"] == null) {      // Если нет куки, то ридерект обратно на login
    header("Location: login.php");
} 

function logout()
{
    setcookie("login", "", time() - 3600, "/");             // Очищаем куки если они были(и не были, на всякий)
    setcookie("password", "", time() - 3600, "/");
    header("Location: login.php");
}
?>