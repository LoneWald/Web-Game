<?php 
if ($_COOKIE["login"] == null || $_COOKIE["password"] == null) {      // Если нет куки, то ридерект обратно на login
    header("Location: login.php");
}
?>