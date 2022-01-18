<?php 
if (!isset($_COOKIE['currentAccount'])) {      // Если нет куки, то ридерект обратно на login
    header("Location: login.php");
}